@extends('layouts.' . (auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-plus-circle me-2"></i>New Message
            </h1>
            <p class="text-muted mb-0">Send a message to {{ auth()->user()->isSuperAdmin() ? 'an admin' : 'a superadmin' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Inbox
            </a>
        </div>
    </div>

    <!-- Message Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-envelope-plus me-2"></i>Compose Message
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.store') }}" method="POST" id="messageForm">
                        @csrf
                        
                        <!-- Recipient Selection -->
                        <div class="mb-4">
                            <label for="recipient_id" class="form-label fw-semibold">
                                <i class="bi bi-person me-2"></i>To
                            </label>
                            <select class="form-select @error('recipient_id') is-invalid @enderror" id="recipient_id" name="recipient_id" required>
                                <option value="">Select {{ auth()->user()->isSuperAdmin() ? 'Admin' : 'Superadmin' }}</option>
                                @foreach($recipients as $recipient)
                                    <option value="{{ $recipient->id }}" {{ old('recipient_id') == $recipient->id ? 'selected' : '' }}>
                                        {{ $recipient->name }} - {{ $recipient->role_with_organization }}
                                    </option>
                                @endforeach
                            </select>
                            @error('recipient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div class="mb-4">
                            <label for="subject" class="form-label fw-semibold">
                                <i class="bi bi-tag me-2"></i>Subject
                            </label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject') }}" 
                                   placeholder="Enter message subject" required maxlength="255">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 255 characters</div>
                        </div>

                        <!-- Message Content -->
                        <div class="mb-4">
                            <label for="message" class="form-label fw-semibold">
                                <i class="bi bi-chat-text me-2"></i>Message
                            </label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="8" 
                                      placeholder="Type your message here..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Be clear and concise in your message</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    This message will be sent to the selected {{ auth()->user()->isSuperAdmin() ? 'admin' : 'superadmin' }}
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.messages.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="sendBtn">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Tips -->
    <div class="row justify-content-center mt-4">
        <div class="col-lg-8">
            <div class="card border-info">
                <div class="card-header bg-info bg-opacity-10 border-info">
                    <h6 class="card-title mb-0 text-info">
                        <i class="bi bi-lightbulb me-2"></i>Message Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 text-muted">
                        <li>Be clear and specific about your request or question</li>
                        <li>Include relevant context or background information</li>
                        <li>Use a descriptive subject line</li>
                        <li>Be professional and respectful in your tone</li>
                        <li>Double-check the recipient before sending</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus,
.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
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

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const sendBtn = document.getElementById('sendBtn');
    const subjectInput = document.getElementById('subject');
    const messageTextarea = document.getElementById('message');
    const recipientSelect = document.getElementById('recipient_id');

    // Form submission handling
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            // Basic validation
            if (!recipientSelect.value) {
                e.preventDefault();
                alert('Please select a recipient');
                recipientSelect.focus();
                return;
            }

            if (!subjectInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a subject');
                subjectInput.focus();
                return;
            }

            if (!messageTextarea.value.trim()) {
                e.preventDefault();
                alert('Please enter a message');
                messageTextarea.focus();
                return;
            }

            // Disable button and show loading state
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
        });
    }

    // Character counter for subject
    if (subjectInput) {
        subjectInput.addEventListener('input', function() {
            const maxLength = 255;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            // Update character count display if it exists
            let counter = document.getElementById('subject-counter');
            if (!counter) {
                counter = document.createElement('div');
                counter.id = 'subject-counter';
                counter.className = 'form-text text-end';
                subjectInput.parentNode.appendChild(counter);
            }
            
            counter.textContent = `${currentLength}/${maxLength} characters`;
            
            if (remaining < 50) {
                counter.className = 'form-text text-end text-warning';
            } else if (remaining < 0) {
                counter.className = 'form-text text-end text-danger';
            } else {
                counter.className = 'form-text text-end';
            }
        });
    }

    // Auto-resize textarea
    if (messageTextarea) {
        messageTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    }
});
</script>
@endsection
