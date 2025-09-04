@extends('layouts.' . (auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin'))

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Chat Partners Sidebar -->
        <div class="col-md-4 col-lg-3 p-0">
            <div class="chat-sidebar h-100 d-flex flex-column">
                <!-- Header -->
                <div class="chat-header p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Chat
                    </h5>
                </div>
                </div>

                <!-- Search -->
                <div class="p-3 border-bottom">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchPartners" placeholder="Search {{ auth()->user()->isSuperAdmin() ? 'admins' : 'superadmins' }}...">
                    </div>
                </div>
                <!-- Chat Partners List -->
                <div class="chat-partners flex-grow-1 overflow-auto" style="max-height: calc(100vh - 200px);">
                    @if($chatPartners->count() > 0)
                        @foreach($chatPartners as $partner)
                            <div class="chat-partner-item" data-partner-id="{{ $partner->id }}" data-partner-name="{{ $partner->name }}">
                                <div class="d-flex align-items-center p-3 border-bottom chat-partner-link" style="cursor: pointer;">
                                    <div class="avatar me-3">
                                        <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr($partner->name, 0, 1)) }}
                                        </div>
                                        @if($partner->unread_messages_count > 0)
                                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                                                {{ $partner->unread_messages_count }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 fw-semibold">{{ $partner->name }}</h6>
                                                <small class="text-muted">{{ $partner->role_with_organization }}</small>
                                            </div>
                                            <div class="text-end">
                                                @if(isset($recentConversations[$partner->id]) && $recentConversations[$partner->id]->count() > 0)
                                                    <small class="text-muted">
                                                        {{ $recentConversations[$partner->id]->last()->created_at->diffForHumans() }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        @if(isset($recentConversations[$partner->id]) && $recentConversations[$partner->id]->count() > 0)
                                            <div class="last-message text-muted small mt-1">
                                                @php
                                                    $lastMessage = $recentConversations[$partner->id]->last();
                                                @endphp
                                                @if($lastMessage->sender_id === auth()->id())
                                                    <i class="bi bi-check2 text-success me-1"></i>
                                                @endif
                                                {{ Str::limit($lastMessage->message, 30) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-4">
                            <i class="bi bi-people display-4 text-muted"></i>
                            <p class="text-muted mt-2">No {{ auth()->user()->isSuperAdmin() ? 'admins' : 'superadmins' }} available for chat</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8 col-lg-9 p-0">
            <div class="chat-area h-100 d-flex flex-column">
                <!-- Chat Header -->
                <div class="chat-header p-3 border-bottom d-none" id="chatHeader">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                <span id="partnerInitial">U</span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-semibold" id="partnerName">Select a user to start chatting</h6>
                            <small class="text-muted" id="partnerRole">Click on a user from the sidebar</small>
                        </div>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="chat-welcome flex-grow-1 d-flex align-items-center justify-content-center" id="chatWelcome">
                    <div class="text-center">
                        <i class="bi bi-chat-dots display-1 text-muted"></i>
                        <h4 class="text-muted mt-3">Welcome to Chat</h4>
                        <p class="text-muted">Select a user from the sidebar to start a conversation</p>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="messages-area flex-grow-1 overflow-auto d-none" id="messagesArea">
                    <div id="messagesContainer">
                        <!-- Messages will be loaded here -->
                    </div>
                    <div class="scroll-indicator" id="scrollIndicator">
                        <i class="bi bi-arrow-down"></i> New messages
                    </div>
                    <button class="btn btn-primary btn-sm scroll-to-bottom" id="scrollToBottom" style="position: absolute; bottom: 20px; right: 20px; border-radius: 50%; width: 45px; height: 45px; display: none; z-index: 10; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                        <i class="bi bi-arrow-down"></i>
                    </button>
                </div>
                
                <!-- Context Menu -->
                <div class="context-menu" id="contextMenu">
                    <div class="context-menu-item" data-action="delete">
                        <i class="bi bi-trash"></i>
                        Delete Message
                    </div>
                    <div class="context-menu-item" data-action="copy">
                        <i class="bi bi-copy"></i>
                        Copy Text
                    </div>
                    <div class="context-menu-item" data-action="cancel">
                        <i class="bi bi-x"></i>
                        Cancel
                    </div>
                </div>

                <!-- Delete Message Modal -->
                <div class="modal fade" id="deleteMessageModal" tabindex="-1" aria-labelledby="deleteMessageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title" id="deleteMessageModalLabel">
                                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                    Delete Message
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body pt-0">
                                <p class="mb-3">Are you sure you want to delete this message?</p>
                                <div class="alert alert-light border-start border-3 border-warning">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-chat-quote text-muted me-2 mt-1"></i>
                                        <div>
                                            <small class="text-muted">Message content:</small>
                                            <div class="mt-1 p-2 bg-light rounded" id="deleteMessagePreview">
                                                <!-- Message content will be inserted here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <small>This action cannot be undone.</small>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Cancel
                                </button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteMessage">
                                    <i class="bi bi-trash me-1"></i>
                                    Delete Message
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="message-input p-3 border-top d-none" id="messageInput">
                    <form id="messageForm" class="d-flex gap-2">
                        <input type="hidden" id="currentPartnerId" name="recipient_id">
                        <div class="flex-grow-1">
                            <textarea class="form-control" id="messageText" name="message" rows="1" 
                                      placeholder="Type your message..." maxlength="1000" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" id="sendButton">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                    <div class="text-end mt-1">
                        <small class="text-muted">
                            <span id="charCount">0</span>/1000 characters
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dynamic color scheme based on user role */
@if(auth()->user()->isSuperAdmin())
    :root {
        --primary-color: #dc2626;
        --primary-light: #b91c1c;
        --primary-dark: #991b1b;
        --primary-gradient: linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #991b1b 100%);
    }
@else
    :root {
        --primary-color: #1e40af;
        --primary-light: #1d4ed8;
        --primary-dark: #3b82f6;
        --primary-gradient: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #3b82f6 100%);
    }
@endif

.chat-sidebar {
    background-color: #ffffff;
    border-right: 1px solid #e9ecef;
    box-shadow: 2px 0 8px rgba(0,0,0,0.05);
}

.chat-area {
    background-color: #ffffff;
}

/* Chat header styling */
.chat-header {
    background-color: #ffffff;
    color: #333;
    border-bottom: 1px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.chat-header h5 {
    font-weight: 600;
    margin: 0;
    color: #333;
}

.chat-header .avatar-circle {
    width: 40px;
    height: 40px;
    font-size: 16px;
}

/* Override Bootstrap primary button colors */
.btn-primary {
    background: var(--primary-gradient) !important;
    border-color: var(--primary-color) !important;
}

.btn-primary:hover {
    background: var(--primary-dark) !important;
    border-color: var(--primary-dark) !important;
}

.avatar {
    position: relative;
}

.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 20px;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.chat-partner-link:hover .avatar-circle {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.bg-primary {
    background: var(--primary-gradient) !important;
}

/* Unread message badge styling */
.badge.bg-danger {
    background: linear-gradient(135deg, #dc3545, #c82333) !important;
    border: 2px solid white;
    font-size: 11px;
    font-weight: 600;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.chat-partner-link {
    transition: all 0.3s ease;
    position: relative;
    border-radius: 0;
}

.chat-partner-link:hover {
    background-color: #f8f9fa !important;
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.chat-partner-link.active {
    background: var(--primary-gradient) !important;
    color: white !important;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-left: 4px solid rgba(255,255,255,0.3) !important;
}

.chat-partner-link.active .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

.chat-partner-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: rgba(255,255,255,0.3);
    border-radius: 0 2px 2px 0;
}

/* Chat partner text styling */
.chat-partner-link h6 {
    font-weight: 600;
    margin-bottom: 2px;
    font-size: 15px;
}

.chat-partner-link .text-muted {
    font-size: 13px;
    font-weight: 400;
}

.chat-partner-link .small {
    font-size: 12px;
    font-weight: 500;
}

/* Better spacing for chat partner content */
.chat-partner-link .flex-grow-1 {
    min-width: 0;
}

.chat-partner-link .text-end {
    flex-shrink: 0;
}

/* Delete Message Modal Styling */
#deleteMessageModal .modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

#deleteMessageModal .modal-header {
    background: var(--primary-gradient);
    color: white;
    border-radius: 12px 12px 0 0;
}

#deleteMessageModal .modal-title {
    font-weight: 600;
}

#deleteMessageModal .btn-close {
    filter: invert(1);
}

#deleteMessageModal .modal-body {
    padding: 1.5rem;
}

#deleteMessageModal .modal-footer {
    padding: 1rem 1.5rem 1.5rem;
}

#deleteMessageModal .btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    font-weight: 500;
}

#deleteMessageModal .btn-danger:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

#deleteMessageModal .btn-secondary {
    background: #6c757d;
    border: none;
    font-weight: 500;
}

#deleteMessageModal .btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

#deleteMessagePreview {
    max-height: 100px;
    overflow-y: auto;
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
}

.messages-area {
    background-color: #f8f9fa;
    min-height: 400px;
    max-height: 70vh;
    overflow-y: auto;
    padding: 20px;
    scroll-behavior: smooth;
}

.message {
    margin-bottom: 15px;
    display: flex;
    align-items: flex-end;
}

.message.sent {
    justify-content: flex-end;
}

.message.received {
    justify-content: flex-start;
}

.message-bubble {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 18px;
    position: relative;
    word-wrap: break-word;
}

.message.sent .message-bubble {
    background: var(--primary-gradient);
    color: white;
    border-bottom-right-radius: 5px;
}

.message.received .message-bubble {
    background-color: white;
    color: #333;
    border: 1px solid #dee2e6;
    border-bottom-left-radius: 5px;
}

.message-time {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 5px;
}

.message-status {
    font-size: 12px;
    margin-left: 5px;
}

.message-input textarea {
    resize: none;
    border-radius: 20px;
    padding: 10px 15px;
}

.message-input textarea:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

#sendButton {
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-partner-item.hidden {
    display: none;
}

/* Enhanced Scrollbar styling */
.chat-partners::-webkit-scrollbar,
.messages-area::-webkit-scrollbar {
    width: 8px;
}

.chat-partners::-webkit-scrollbar-track,
.messages-area::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 4px;
}

.chat-partners::-webkit-scrollbar-thumb,
.messages-area::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #6c757d, #495057);
    border-radius: 4px;
    border: 1px solid #f8f9fa;
}

.chat-partners::-webkit-scrollbar-thumb:hover,
.messages-area::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #495057, #343a40);
}

.chat-partners::-webkit-scrollbar-thumb:active,
.messages-area::-webkit-scrollbar-thumb:active {
    background: linear-gradient(180deg, #343a40, #212529);
}

/* Firefox scrollbar styling */
.chat-partners,
.messages-area {
    scrollbar-width: thin;
    scrollbar-color: #6c757d #f8f9fa;
}

/* Smooth scrolling for all browsers */
.chat-partners,
.messages-area {
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch; /* iOS smooth scrolling */
}

/* Message container styling */
#messagesContainer {
    padding-bottom: 20px;
}

/* Auto-scroll indicator */
.scroll-indicator {
    position: absolute;
    bottom: 80px;
    right: 20px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    z-index: 10;
}

.scroll-indicator.show {
    opacity: 1;
}

/* Loading indicator for messages */
.messages-loading {
    text-align: center;
    padding: 20px;
    color: #6c757d;
}

.messages-loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Context menu styling */
.context-menu {
    position: absolute;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 8px 0;
    z-index: 1000;
    min-width: 150px;
    display: none;
}

.context-menu-item {
    padding: 8px 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    color: #495057;
    transition: background-color 0.2s;
}

.context-menu-item:hover {
    background-color: #f8f9fa;
}

.context-menu-item.danger {
    color: #dc3545;
}

.context-menu-item.danger:hover {
    background-color: #f8d7da;
}

.context-menu-item[data-action="delete"] {
    color: #dc3545;
}

.context-menu-item[data-action="delete"]:hover {
    background-color: #f8d7da;
}

.context-menu-item i {
    margin-right: 8px;
    width: 16px;
}

/* Message hover effect for context menu */
.message-bubble {
    cursor: context-menu;
    transition: background-color 0.2s;
}

.message-bubble:hover {
    opacity: 0.9;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .col-md-4, .col-md-8 {
        height: 50vh;
    }
    
    .chat-sidebar {
        border-right: none;
        border-bottom: 1px solid #dee2e6;
    }
    
    .message-bubble {
        max-width: 85%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPartnerId = null;
    let messagePollingInterval = null;
    
    // Elements
    const searchInput = document.getElementById('searchPartners');
    const chatWelcome = document.getElementById('chatWelcome');
    const chatHeader = document.getElementById('chatHeader');
    const messagesArea = document.getElementById('messagesArea');
    const messageInput = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('messagesContainer');
    const messageForm = document.getElementById('messageForm');
    const messageText = document.getElementById('messageText');
    const charCount = document.getElementById('charCount');
    const sendButton = document.getElementById('sendButton');
    const currentPartnerIdInput = document.getElementById('currentPartnerId');
    const partnerName = document.getElementById('partnerName');
    const partnerRole = document.getElementById('partnerRole');
    const partnerInitial = document.getElementById('partnerInitial');
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const partnerItems = document.querySelectorAll('.chat-partner-item');
        
        partnerItems.forEach(item => {
            const partnerName = item.dataset.partnerName.toLowerCase();
            if (partnerName.includes(searchTerm)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
    
    // Chat partner selection
    document.querySelectorAll('.chat-partner-link').forEach(link => {
        link.addEventListener('click', function() {
            const partnerItem = this.closest('.chat-partner-item');
            const partnerId = partnerItem.dataset.partnerId;
            const partnerNameText = partnerItem.dataset.partnerName;
            
            // Update active state
            document.querySelectorAll('.chat-partner-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Load chat with this partner
            loadChat(partnerId, partnerNameText);
        });
    });
    
    // Load chat with a partner
    function loadChat(partnerId, partnerNameText) {
        currentPartnerId = partnerId;
        currentPartnerIdInput.value = partnerId;
        
        // Update UI
        chatWelcome.classList.add('d-none');
        chatHeader.classList.remove('d-none');
        messagesArea.classList.remove('d-none');
        messageInput.classList.remove('d-none');
        
        // Update header
        partnerName.textContent = partnerNameText;
        partnerInitial.textContent = partnerNameText.charAt(0).toUpperCase();
        
        // Load messages
        loadMessages(partnerId);
        
        // Start polling for new messages
        startMessagePolling(partnerId);
        
        // Mark messages as read
        markMessagesAsRead(partnerId);
    }
    
    // Load messages for a partner
    function loadMessages(partnerId) {
        const baseUrl = '{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.chat.messages', 'PARTNER_ID') }}';
        const url = baseUrl.replace('PARTNER_ID', partnerId);
        
        // Show loading state
        showLoadingState();
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.messages) {
                    displayMessages(data.messages);
                }
                hideLoadingState();
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                hideLoadingState();
            });
    }
    
    // Show loading state
    function showLoadingState() {
        const container = document.getElementById('messagesContainer');
        if (container.children.length === 0) {
            container.innerHTML = '<div class="messages-loading"><i class="bi bi-hourglass-split"></i> Loading messages...</div>';
        }
    }
    
    // Hide loading state
    function hideLoadingState() {
        const loading = document.querySelector('.messages-loading');
        if (loading) {
            loading.remove();
        }
    }
    
    // Display messages
    function displayMessages(messages) {
        messagesContainer.innerHTML = '';
        
        messages.forEach(message => {
            const messageElement = createMessageElement(message);
            messagesContainer.appendChild(messageElement);
        });
        
        // Smooth scroll to bottom
        messagesArea.scrollTo({
            top: messagesArea.scrollHeight,
            behavior: 'smooth'
        });
    }
    
    // Create message element
    function createMessageElement(message) {
        // Get current user ID and ensure proper type comparison
        const currentUserId = {{ auth()->id() }};
        const senderId = parseInt(message.sender_id);
        
        // Multiple comparison methods to handle different data types
        const isSent = (senderId === currentUserId) || 
                      (senderId == currentUserId) || 
                      (String(senderId) === String(currentUserId));
        
        console.log('Message alignment check:', {
            messageId: message.id,
            senderId: senderId,
            currentUserId: currentUserId,
            isSent: isSent,
            senderIdType: typeof senderId,
            currentUserIdType: typeof currentUserId,
            stringComparison: String(senderId) === String(currentUserId),
            looseComparison: senderId == currentUserId,
            strictComparison: senderId === currentUserId
        });
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        messageDiv.dataset.messageId = message.id;
        
        const time = new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        messageDiv.innerHTML = `
            <div class="message-bubble" data-message-id="${message.id}">
                <div class="message-content">${escapeHtml(message.message)}</div>
                <div class="message-time">
                    ${time}
                    ${isSent ? '<span class="message-status"><i class="bi bi-check2"></i></span>' : ''}
                </div>
            </div>
        `;
        
        // Add right-click event listener
        const messageBubble = messageDiv.querySelector('.message-bubble');
        messageBubble.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            showContextMenu(e, message.id, message.message, isSent);
        });
        
        return messageDiv;
    }
    
    // Send message
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageText.value.trim();
        if (!message || !currentPartnerId) return;
        
        // Disable form
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        
        // Send message
        fetch('{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.chat.send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                recipient_id: currentPartnerId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add message to UI
                const messageElement = createMessageElement(data.message);
                messagesContainer.appendChild(messageElement);
                
                // Clear input
                messageText.value = '';
                updateCharCount();
                
                // Smooth scroll to bottom
                messagesArea.scrollTo({
                    top: messagesArea.scrollHeight,
                    behavior: 'smooth'
                });
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Failed to send message. Please try again.');
        })
        .finally(() => {
            // Re-enable form
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="bi bi-send"></i>';
        });
    });
    
    // Character count
    messageText.addEventListener('input', function() {
        updateCharCount();
        autoResizeTextarea();
    });
    
    function updateCharCount() {
        const count = messageText.value.length;
        charCount.textContent = count;
        
        if (count > 900) {
            charCount.className = 'text-warning';
        } else if (count > 1000) {
            charCount.className = 'text-danger';
        } else {
            charCount.className = 'text-muted';
        }
    }
    
    // Auto-resize textarea
    function autoResizeTextarea() {
        messageText.style.height = 'auto';
        messageText.style.height = Math.min(messageText.scrollHeight, 120) + 'px';
    }
    
    // Start polling for new messages
    function startMessagePolling(partnerId) {
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
        }
        
        messagePollingInterval = setInterval(() => {
            if (currentPartnerId === partnerId) {
                loadMessages(partnerId);
            }
        }, 3000); // Poll every 3 seconds
    }
    
    // Mark messages as read
    function markMessagesAsRead(partnerId) {
        const baseUrl = '{{ route((auth()->user()->isSuperAdmin() ? 'superadmin' : 'admin') . '.chat.mark-read', 'PARTNER_ID') }}';
        const url = baseUrl.replace('PARTNER_ID', partnerId);
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update unread count in sidebar
            updateUnreadCount(partnerId, 0);
        })
        .catch(error => {
            console.error('Error marking messages as read:', error);
        });
    }
    
    // Update unread count in sidebar
    function updateUnreadCount(partnerId, count) {
        const partnerItem = document.querySelector(`[data-partner-id="${partnerId}"]`);
        if (partnerItem) {
            const badge = partnerItem.querySelector('.badge');
            if (count > 0) {
                if (badge) {
                    badge.textContent = count;
                } else {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'badge bg-danger position-absolute top-0 start-100 translate-middle';
                    newBadge.textContent = count;
                    partnerItem.querySelector('.avatar').appendChild(newBadge);
                }
            } else {
                if (badge) {
                    badge.remove();
                }
            }
        }
    }
    
    // Utility function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Context menu functionality
    let selectedMessageId = null;
    let selectedMessageText = '';
    
    function showContextMenu(event, messageId, messageText, isOwnMessage) {
        event.preventDefault();
        event.stopPropagation();
        
        selectedMessageId = messageId;
        selectedMessageText = messageText;
        
        console.log('Context menu shown for message:', messageId, 'Text:', messageText, 'Is own message:', isOwnMessage);
        
        const contextMenu = document.getElementById('contextMenu');
        const deleteItem = contextMenu.querySelector('[data-action="delete"]');
        
        // Show/hide delete option based on whether it's the user's own message
        if (isOwnMessage) {
            deleteItem.style.display = 'block';
        } else {
            deleteItem.style.display = 'none';
        }
        
        contextMenu.style.display = 'block';
        
        // Position the context menu relative to the viewport
        const rect = contextMenu.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        
        let left = event.clientX;
        let top = event.clientY;
        
        // Adjust position if menu would go off screen
        if (left + rect.width > viewportWidth) {
            left = viewportWidth - rect.width - 10;
        }
        if (top + rect.height > viewportHeight) {
            top = viewportHeight - rect.height - 10;
        }
        
        contextMenu.style.left = left + 'px';
        contextMenu.style.top = top + 'px';
        contextMenu.style.position = 'fixed';
        
        // Hide context menu when clicking elsewhere
        setTimeout(() => {
            document.addEventListener('click', hideContextMenu);
        }, 100);
    }
    
    function hideContextMenu() {
        const contextMenu = document.getElementById('contextMenu');
        contextMenu.style.display = 'none';
        document.removeEventListener('click', hideContextMenu);
    }
    
    // Use event delegation for context menu clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('#contextMenu')) {
            e.stopPropagation();
            e.preventDefault();
            
            const menuItem = e.target.closest('.context-menu-item');
            if (menuItem) {
                const action = menuItem.getAttribute('data-action');
                console.log('Context menu item clicked via delegation:', action, menuItem.textContent.trim());
                
                switch(action) {
                    case 'delete':
                        deleteMessage();
                        break;
                    case 'copy':
                        copyMessage();
                        break;
                    case 'cancel':
                        hideContextMenu();
                        break;
                }
            }
        }
    });
    
    // Add click event listeners to context menu items
    function setupContextMenuListeners() {
        const contextMenu = document.getElementById('contextMenu');
        if (contextMenu) {
            console.log('Setting up context menu listeners');
            // Add click listeners to each menu item
            const menuItems = contextMenu.querySelectorAll('.context-menu-item');
            console.log('Found menu items:', menuItems.length);
            
            menuItems.forEach((item, index) => {
                console.log(`Setting up listener for item ${index}:`, item.textContent.trim());
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    
                    const action = this.getAttribute('data-action');
                    console.log('Context menu item clicked:', action, this.textContent.trim());
                    
                    switch(action) {
                        case 'delete':
                            deleteMessage();
                            break;
                        case 'copy':
                            copyMessage();
                            break;
                        case 'cancel':
                            hideContextMenu();
                            break;
                    }
                });
            });
        } else {
            console.error('Context menu not found!');
        }
    }
    
    // Setup listeners when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupContextMenuListeners);
    } else {
        setupContextMenuListeners();
    }
    
    // Add event listener for confirm delete button using event delegation
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'confirmDeleteMessage') {
            e.preventDefault();
            console.log('Confirm delete button clicked via delegation!');
            confirmDeleteMessage();
        }
    });
    
    // Also try to add the listener when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const confirmDeleteBtn = document.getElementById('confirmDeleteMessage');
        console.log('Looking for confirm delete button:', confirmDeleteBtn);
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Confirm delete button clicked!');
                confirmDeleteMessage();
            });
            console.log('Event listener added to confirm delete button');
        } else {
            console.error('Confirm delete button not found!');
        }
    });
    
    function deleteMessage() {
        console.log('Delete message called for message:', selectedMessageId);
        
        if (selectedMessageId && selectedMessageText) {
            // Show the message content in the modal preview
            document.getElementById('deleteMessagePreview').textContent = selectedMessageText;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('deleteMessageModal'));
            modal.show();
        } else {
            console.error('No message selected for deletion');
        }
        hideContextMenu();
    }
    
    function confirmDeleteMessage() {
        console.log('confirmDeleteMessage function called');
        console.log('selectedMessageId:', selectedMessageId);
        
        if (!selectedMessageId) {
            console.error('No message ID selected');
            return;
        }
        
        // Build URL manually to avoid route generation issues
        const prefix = '{{ auth()->user()->isSuperAdmin() ? "superadmin" : "admin" }}';
        const url = `/${prefix}/chat/message/${selectedMessageId}`;
        
        console.log('Deleting message:', selectedMessageId);
        console.log('URL:', url);
        
        // Disable the delete button and show loading state
        const deleteBtn = document.getElementById('confirmDeleteMessage');
        const originalText = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';
        
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Delete response:', data);
            if (data.success) {
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteMessageModal'));
                modal.hide();
                
                // Remove message from UI
                const messageElement = document.querySelector(`[data-message-id="${selectedMessageId}"]`);
                if (messageElement) {
                    messageElement.remove();
                }
                
                // Reload messages to ensure consistency
                if (currentPartnerId) {
                    loadMessages(currentPartnerId);
                }
                
                // Show success message
                showToast('Message deleted successfully', 'success');
            } else {
                throw new Error(data.error || 'Failed to delete message');
            }
        })
        .catch(error => {
            console.error('Error deleting message:', error);
            showToast('Failed to delete message. Please try again.', 'error');
        })
        .finally(() => {
            // Re-enable the delete button
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = originalText;
        });
    }
    
    function copyMessage() {
        console.log('Copy message called with text:', selectedMessageText);
        
        if (selectedMessageText) {
            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                console.log('Using modern clipboard API');
                navigator.clipboard.writeText(selectedMessageText).then(() => {
                    console.log('Text copied successfully');
                    showCopyFeedback();
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                    fallbackCopyText(selectedMessageText);
                });
            } else {
                console.log('Using fallback copy method');
                // Fallback for older browsers
                fallbackCopyText(selectedMessageText);
            }
        } else {
            console.error('No message text selected');
        }
        hideContextMenu();
    }
    
    function fallbackCopyText(text) {
        console.log('Using fallback copy for text:', text);
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const success = document.execCommand('copy');
            console.log('Fallback copy result:', success);
            if (success) {
                showCopyFeedback();
            } else {
                alert('Failed to copy text to clipboard');
            }
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            alert('Failed to copy text to clipboard');
        }
        
        document.body.removeChild(textArea);
    }
    
    function showCopyFeedback() {
        const contextMenu = document.getElementById('contextMenu');
        const copyItem = contextMenu.querySelector('[data-action="copy"]');
        if (copyItem) {
            const originalText = copyItem.innerHTML;
            copyItem.innerHTML = '<i class="bi bi-check"></i> Copied!';
            copyItem.style.color = '#28a745';
            
            setTimeout(() => {
                copyItem.innerHTML = originalText;
                copyItem.style.color = '';
            }, 1000);
        }
    }
    
    function showToast(message, type = 'info') {
        // Create alert element matching the layout style
        const alert = document.createElement('div');
        alert.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show`;
        alert.setAttribute('role', 'alert');
        alert.style.position = 'fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '9999';
        alert.style.minWidth = '300px';
        alert.style.maxWidth = '400px';
        alert.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        
        alert.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Add to page
        document.body.appendChild(alert);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.classList.remove('show');
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.remove();
                    }
                }, 150); // Wait for fade animation
            }
        }, 3000);
        
        // Handle manual close
        alert.addEventListener('closed.bs.alert', () => {
            if (alert && alert.parentNode) {
                alert.remove();
            }
        });
    }
    
    
    // Scroll detection for showing/hiding scroll indicator and button
    messagesArea.addEventListener('scroll', function() {
        const scrollIndicator = document.getElementById('scrollIndicator');
        const scrollToBottomBtn = document.getElementById('scrollToBottom');
        const isAtBottom = this.scrollTop + this.clientHeight >= this.scrollHeight - 10;
        
        if (isAtBottom) {
            scrollIndicator.classList.remove('show');
            scrollToBottomBtn.style.display = 'none';
        } else {
            scrollIndicator.classList.add('show');
            scrollToBottomBtn.style.display = 'block';
        }
    });
    
    // Scroll to bottom button functionality
    document.getElementById('scrollToBottom').addEventListener('click', function() {
        messagesArea.scrollTo({
            top: messagesArea.scrollHeight,
            behavior: 'smooth'
        });
    });
    
    // Auto-hide scroll indicator after 3 seconds
    let scrollIndicatorTimeout;
    messagesArea.addEventListener('scroll', function() {
        const scrollIndicator = document.getElementById('scrollIndicator');
        if (scrollIndicator.classList.contains('show')) {
            clearTimeout(scrollIndicatorTimeout);
            scrollIndicatorTimeout = setTimeout(() => {
                scrollIndicator.classList.remove('show');
            }, 3000);
        }
    });
    
    // Clean up polling when page unloads
    window.addEventListener('beforeunload', function() {
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
        }
    });
});
</script>
@endsection
