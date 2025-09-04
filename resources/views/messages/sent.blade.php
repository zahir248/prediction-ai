@extends('layouts.' . (auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-send me-2"></i>Sent Messages
            </h1>
            <p class="text-muted mb-0">Messages you have sent to {{ auth()->user()->isSuperAdmin() ? 'admins' : 'superadmins' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-inbox me-2"></i>Inbox
            </a>
            <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Message
            </a>
        </div>
    </div>

    <!-- Sent Messages List -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0">
                <i class="bi bi-send me-2"></i>Sent Messages
            </h5>
        </div>
        <div class="card-body p-0">
            @if($messages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th width="25%">To</th>
                                <th width="40%">Subject</th>
                                <th width="20%">Date</th>
                                <th width="10%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input message-checkbox" type="checkbox" value="{{ $message->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="bi bi-person-fill text-success"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $message->recipient->name }}</h6>
                                                <small class="text-muted">{{ $message->recipient->role_with_organization }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.show', $message) }}" class="text-decoration-none text-dark">
                                            <div class="d-flex align-items-center">
                                                <span class="fw-semibold">{{ $message->subject }}</span>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                {{ Str::limit(strip_tags($message->message), 60) }}
                                            </small>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="text-muted">
                                            <div>{{ $message->created_at->format('M d, Y') }}</div>
                                            <small>{{ $message->created_at->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.show', $message) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form action="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.destroy', $message) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $messages->firstItem() }} to {{ $messages->lastItem() }} of {{ $messages->total() }} messages
                        </div>
                        <div>
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-send display-1 text-muted"></i>
                    </div>
                    <h5 class="text-muted">No sent messages</h5>
                    <p class="text-muted mb-4">You haven't sent any messages to {{ auth()->user()->isSuperAdmin() ? 'admins' : 'superadmins' }} yet.</p>
                    <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Send Your First Message
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}

.table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin: 0 1px;
}

.btn-group .btn:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-group .btn:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const messageCheckboxes = document.querySelectorAll('.message-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            messageCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Update select all when individual checkboxes change
    messageCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.message-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === messageCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < messageCheckboxes.length;
        });
    });
});
</script>
@endsection
