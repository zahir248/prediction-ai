<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    /**
     * Display inbox for admin/superadmin
     */
    public function index(): View
    {
        $user = auth()->user();
        
        // Get received messages with sender information
        $messages = Message::with('sender')
            ->forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get unread count
        $unreadCount = $user->getUnreadMessagesCount();

        return view('messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Display sent messages
     */
    public function sent(): View
    {
        $user = auth()->user();
        
        // Get sent messages with recipient information
        $messages = Message::with('recipient')
            ->fromUser($user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('messages.sent', compact('messages'));
    }

    /**
     * Show the form for creating a new message
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Get available recipients based on user role
        if ($user->isSuperAdmin()) {
            // Superadmin can message all admins
            $recipients = User::where('role', 'admin')->get();
        } else {
            // Admin can only message superadmins
            $recipients = User::where('role', 'superadmin')->get();
        }

        return view('messages.create', compact('recipients'));
    }

    /**
     * Store a newly created message
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $user = auth()->user();
        $recipient = User::findOrFail($request->recipient_id);

        // Validate that the recipient is appropriate for the sender's role
        if ($user->isSuperAdmin() && !$recipient->isAdmin()) {
            return redirect()->back()->with('error', 'You can only send messages to admins.');
        }

        if ($user->isAdmin() && !$recipient->isSuperAdmin()) {
            return redirect()->back()->with('error', 'You can only send messages to superadmins.');
        }

        Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        $routePrefix = $user->isSuperAdmin() ? 'superadmin' : 'admin';
        return redirect()->route($routePrefix . '.messages.index')
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Display the specified message
     */
    public function show(Message $message): View
    {
        $user = auth()->user();

        // Ensure user can view this message
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'You are not authorized to view this message.');
        }

        // Mark as read if user is the recipient
        if ($message->recipient_id === $user->id && !$message->is_read) {
            $message->markAsRead();
        }

        return view('messages.show', compact('message'));
    }

    /**
     * Mark message as read (AJAX endpoint)
     */
    public function markAsRead(Message $message): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        // Ensure user is the recipient
        if ($message->recipient_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Delete a message
     */
    public function destroy(Message $message): RedirectResponse
    {
        $user = auth()->user();

        // Ensure user can delete this message (only sender or recipient)
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'You are not authorized to delete this message.');
        }

        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully.');
    }

    /**
     * Get unread messages count (AJAX endpoint)
     */
    public function unreadCount(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $count = $user->getUnreadMessagesCount();

        return response()->json(['count' => $count]);
    }
}
