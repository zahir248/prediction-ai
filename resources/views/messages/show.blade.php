@extends('layouts.' . (auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-envelope-open me-2"></i>Message Details
            </h1>
            <p class="text-muted mb-0">View and manage your message</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Inbox
            </a>
            <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Message
            </a>
        </div>
    </div>

    <!-- Message Details -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-envelope-open me-2"></i>{{ $message->subject }}
                        </h5>
                        <div class="d-flex gap-2">
                            <form action="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.destroy', $message) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Message Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-person-fill text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-semibold">
                                        @if($message->sender_id === auth()->id())
                                            You
                                        @else
                                            {{ $message->sender->name }}
                                        @endif
                                    </h6>
                                    <small class="text-muted">
                                        @if($message->sender_id === auth()->id())
                                            Sent to {{ $message->recipient->name }}
                                        @else
                                            {{ $message->sender->role_with_organization }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="text-muted">
                                <div class="fw-semibold">{{ $message->created_at->format('F d, Y') }}</div>
                                <small>{{ $message->created_at->format('h:i A') }}</small>
                                @if($message->read_at)
                                    <div class="mt-1">
                                        <small class="text-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Read {{ $message->read_at->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="message-content">
                        <div class="bg-light rounded p-4">
                            <div class="message-text">
                                {!! nl2br(e($message->message)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Message Actions -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    @if($message->sender_id === auth()->id())
                                        This message was sent to {{ $message->recipient->name }}
                                    @else
                                        This message was sent by {{ $message->sender->name }}
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                @if($message->sender_id !== auth()->id())
                                    <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.create') }}?reply_to={{ $message->sender_id }}&subject={{ urlencode('Re: ' . $message->subject) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-reply me-2"></i>Reply
                                    </a>
                                @endif
                                <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Inbox
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Messages (if any) -->
    @if($message->sender_id !== auth()->id())
        @php
            $relatedMessages = \App\Models\Message::where(function($query) use ($message) {
                $query->where('sender_id', $message->sender_id)
                      ->where('recipient_id', auth()->id());
            })->orWhere(function($query) use ($message) {
                $query->where('sender_id', auth()->id())
                      ->where('recipient_id', $message->sender_id);
            })->where('id', '!=', $message->id)
              ->orderBy('created_at', 'desc')
              ->limit(5)
              ->get();
        @endphp

        @if($relatedMessages->count() > 0)
            <div class="row justify-content-center mt-4">
                <div class="col-lg-10">
                    <div class="card border-info">
                        <div class="card-header bg-info bg-opacity-10 border-info">
                            <h6 class="card-title mb-0 text-info">
                                <i class="bi bi-chat-dots me-2"></i>Related Messages
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($relatedMessages as $relatedMessage)
                                    <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.show', $relatedMessage) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $relatedMessage->subject }}</h6>
                                                <p class="mb-1 text-muted">{{ Str::limit(strip_tags($relatedMessage->message), 100) }}</p>
                                                <small class="text-muted">
                                                    @if($relatedMessage->sender_id === auth()->id())
                                                        You to {{ $relatedMessage->recipient->name }}
                                                    @else
                                                        {{ $relatedMessage->sender->name }} to you
                                                    @endif
                                                </small>
                                            </div>
                                            <small class="text-muted">{{ $relatedMessage->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

<style>
.avatar-lg {
    width: 60px;
    height: 60px;
}

.message-content {
    line-height: 1.6;
}

.message-text {
    font-size: 1rem;
    color: #333;
}

.card {
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    background-color: #f8f9fa;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.list-group-item:last-child {
    border-bottom: none;
}

.list-group-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-mark as read if user is the recipient and message is unread
    @if($message->recipient_id === auth()->id() && !$message->is_read)
        // The message is already marked as read in the controller
        // This is just for any additional client-side handling if needed
    @endif
});
</script>
@endsection
