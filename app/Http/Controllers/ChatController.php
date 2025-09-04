<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Display the chat interface
     */
    public function index(): View
    {
        $user = auth()->user();
        
        // Get available chat partners based on user role
        if ($user->isSuperAdmin()) {
            // Superadmin can chat with all admins
            $chatPartners = User::where('role', 'admin')->get();
        } else {
            // Admin can only chat with superadmins
            $chatPartners = User::where('role', 'superadmin')->get();
        }

        // Add unread message count for each partner
        foreach ($chatPartners as $partner) {
            $partner->unread_messages_count = Message::where('sender_id', $partner->id)
                ->where('recipient_id', $user->id)
                ->where('is_read', false)
                ->count();
        }

        // Get recent conversations (last 20 messages for each partner)
        $recentConversations = [];
        foreach ($chatPartners as $partner) {
            $conversation = Message::where(function ($query) use ($user, $partner) {
                $query->where('sender_id', $user->id)->where('recipient_id', $partner->id);
            })->orWhere(function ($query) use ($user, $partner) {
                $query->where('sender_id', $partner->id)->where('recipient_id', $user->id);
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->reverse()
            ->values();

            if ($conversation->count() > 0) {
                $recentConversations[$partner->id] = $conversation;
            }
        }

        return view('chat.index', compact('chatPartners', 'recentConversations'));
    }

    /**
     * Get messages between current user and a specific partner
     */
    public function getMessages(User $partner): JsonResponse
    {
        $user = auth()->user();

        // Validate that the partner is appropriate for the user's role
        if ($user->isSuperAdmin() && !$partner->isAdmin()) {
            return response()->json(['error' => 'Invalid chat partner'], 403);
        }

        if ($user->isAdmin() && !$partner->isSuperAdmin()) {
            return response()->json(['error' => 'Invalid chat partner'], 403);
        }

        // Get messages between the two users
        $messages = Message::where(function ($query) use ($user, $partner) {
            $query->where('sender_id', $user->id)->where('recipient_id', $partner->id);
        })->orWhere(function ($query) use ($user, $partner) {
            $query->where('sender_id', $partner->id)->where('recipient_id', $user->id);
        })
        ->with(['sender', 'recipient'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages as read where current user is recipient
        Message::where('sender_id', $partner->id)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Add debugging information
        \Log::info('Chat messages request', [
            'user_id' => $user->id,
            'partner_id' => $partner->id,
            'messages_count' => $messages->count(),
            'sample_message' => $messages->first() ? [
                'id' => $messages->first()->id,
                'sender_id' => $messages->first()->sender_id,
                'recipient_id' => $messages->first()->recipient_id,
                'sender_id_type' => gettype($messages->first()->sender_id),
                'user_id_type' => gettype($user->id)
            ] : null
        ]);

        return response()->json([
            'messages' => $messages,
            'partner' => $partner,
            'debug' => [
                'current_user_id' => $user->id,
                'current_user_id_type' => gettype($user->id)
            ]
        ]);
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $recipient = User::findOrFail($request->recipient_id);

        // Validate that the recipient is appropriate for the sender's role
        if ($user->isSuperAdmin() && !$recipient->isAdmin()) {
            return response()->json(['error' => 'You can only send messages to admins.'], 403);
        }

        if ($user->isAdmin() && !$recipient->isSuperAdmin()) {
            return response()->json(['error' => 'You can only send messages to superadmins.'], 403);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $recipient->id,
            'subject' => 'Chat Message', // Default subject for chat messages
            'message' => $request->message,
        ]);

        $message->load(['sender', 'recipient']);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Get unread message count for a specific partner
     */
    public function getUnreadCount(User $partner): JsonResponse
    {
        $user = auth()->user();
        
        $unreadCount = Message::where('sender_id', $partner->id)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(User $partner): JsonResponse
    {
        $user = auth()->user();
        
        Message::where('sender_id', $partner->id)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a message
     */
    public function deleteMessage(Message $message): JsonResponse
    {
        $user = auth()->user();

        \Log::info('Delete message request', [
            'message_id' => $message->id,
            'sender_id' => $message->sender_id,
            'user_id' => $user->id,
            'user_role' => $user->role
        ]);

        // Ensure user can delete this message (only sender can delete)
        if ($message->sender_id !== $user->id) {
            \Log::warning('User tried to delete message they did not send', [
                'message_id' => $message->id,
                'sender_id' => $message->sender_id,
                'user_id' => $user->id
            ]);
            return response()->json(['error' => 'You can only delete your own messages.'], 403);
        }

        $message->delete();

        \Log::info('Message deleted successfully', ['message_id' => $message->id]);

        return response()->json(['success' => true]);
    }
}
