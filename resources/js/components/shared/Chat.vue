<template>
    <div class="chat-container" ref="container">
        <!-- Chat Button -->
        <button 
            class="chat-button" 
            @click="toggleChat"
            :class="{ active: showChat }"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <span v-if="unreadCount > 0" class="chat-badge">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
        </button>

        <!-- Chat Window -->
        <Teleport to="body">
            <div 
                v-if="showChat" 
                class="chat-window" 
                :style="windowStyle"
                @click.stop
            >
                <div class="chat-header">
                    <h3>Messages</h3>
                    <div class="chat-header-actions">
                        <button 
                            v-if="!selectedConversation"
                            @click="toggleNewChat" 
                            class="new-chat-btn"
                            title="New Message"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                        <button @click="closeChat" class="close-chat-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="chat-content">
                    <!-- New Chat Modal -->
                    <div v-if="showNewChat && !selectedConversation" class="new-chat-modal">
                        <div class="new-chat-header">
                            <h4>Start New Conversation</h4>
                            <button @click="showNewChat = false" class="close-btn">×</button>
                        </div>
                        <div class="available-users-list">
                            <div v-if="loadingUsers" class="loading-users">Loading...</div>
                            <div v-else-if="!availableUsers || availableUsers.length === 0" class="no-users">No users available</div>
                            <div 
                                v-else-if="availableUsers && availableUsers.length > 0"
                                v-for="user in availableUsers"
                                :key="user.id"
                                class="user-item"
                                @click="startConversation(user)"
                            >
                                <div class="user-avatar">
                                    <img v-if="user.profile_picture" :src="getImageUrl(user.profile_picture)" :alt="user.name" />
                                    <span v-else>{{ getUserInitials(user.name) }}</span>
                                </div>
                                <div class="user-info">
                                    <div class="user-name">{{ user.name }}</div>
                                    <div class="user-role">{{ user.role }}</div>
                                </div>
                                <div v-if="user.conversation_id" class="existing-conversation-badge">Existing</div>
                            </div>
                        </div>
                    </div>

                    <!-- Conversations List -->
                    <div v-else-if="!selectedConversation" class="conversations-list">
                        <div v-if="loadingConversations && !isPolling" class="loading-conversations">Loading conversations...</div>
                        <div v-else-if="!conversations || conversations.length === 0" class="no-conversations">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <p>No conversations yet</p>
                            <button @click="toggleNewChat" class="start-chat-btn">Start a conversation</button>
                        </div>
                        <div 
                            v-else-if="conversations && conversations.length > 0"
                            v-for="conversation in conversations"
                            :key="conversation.id"
                            class="conversation-item"
                            :class="{ active: selectedConversation?.id === conversation.id, unread: conversation.unread_count > 0 }"
                            @click="selectConversation(conversation)"
                        >
                            <div class="conversation-avatar">
                                <img v-if="conversation.other_user.profile_picture" :src="getImageUrl(conversation.other_user.profile_picture)" :alt="conversation.other_user.name" />
                                <span v-else>{{ getUserInitials(conversation.other_user.name) }}</span>
                            </div>
                            <div class="conversation-info">
                                <div class="conversation-header">
                                    <div class="conversation-name">{{ conversation.other_user.name }}</div>
                                    <div class="conversation-time">{{ formatTime(conversation.last_message_at || conversation.created_at) }}</div>
                                </div>
                                <div class="conversation-preview">
                                    <span v-if="conversation.latest_message">{{ conversation.latest_message.message }}</span>
                                    <span v-else class="no-messages">No messages yet</span>
                                </div>
                            </div>
                            <div v-if="conversation.unread_count > 0" class="conversation-unread">{{ conversation.unread_count }}</div>
                        </div>
                    </div>

                    <!-- Chat Window for Selected Conversation -->
                    <div v-else class="chat-messages-container">
                        <div class="chat-messages-header">
                            <button @click="selectedConversation = null" class="back-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="15 18 9 12 15 6"/>
                                </svg>
                            </button>
                            <div class="chat-messages-user-info">
                                <div class="chat-messages-avatar">
                                    <img v-if="selectedConversation.other_user.profile_picture" :src="getImageUrl(selectedConversation.other_user.profile_picture)" :alt="selectedConversation.other_user.name" />
                                    <span v-else>{{ getUserInitials(selectedConversation.other_user.name) }}</span>
                                </div>
                                <div class="chat-messages-name">{{ selectedConversation.other_user.name }}</div>
                            </div>
                        </div>

                        <div class="messages-list" ref="messagesList">
                            <div v-if="loadingMessages && !isPolling" class="loading-messages">Loading messages...</div>
                            <template v-else>
                                <!-- Load older messages button -->
                                <div v-if="hasMoreOlderMessages && !loadingOlderMessages" class="load-older-messages">
                                    <button @click="loadOlderMessages" class="load-older-btn">
                                        Load older messages
                                    </button>
                                </div>
                                <div v-if="loadingOlderMessages" class="loading-older-messages">
                                    Loading older messages...
                                </div>
                                <template v-for="(item, index) in messagesWithSeparators" :key="item.type === 'separator' ? `sep-${index}` : item.id">
                                    <div 
                                        v-if="item.type === 'separator'"
                                        class="date-separator"
                                    >
                                        <span class="date-separator-text">{{ formatDate(item.date) }}</span>
                                    </div>
                                    <div 
                                        v-else
                                        class="message-item"
                                        :class="{ 'own-message': item.sender.id === currentUser.id }"
                                    >
                                        <div class="message-content">
                                            <div class="message-text">{{ item.message }}</div>
                                            <div class="message-time">{{ formatTime(item.created_at) }}</div>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <div class="chat-input-container">
                            <textarea 
                                v-model="newMessage"
                                @keydown.enter.exact.prevent="sendMessage"
                                @keydown.enter.shift.exact="newMessage += '\n'"
                                placeholder="Type a message..."
                                class="chat-input"
                                rows="1"
                            ></textarea>
                            <button @click="sendMessage" :disabled="!newMessage.trim() || sending" class="send-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script>
import api from '../../api.js';

export default {
    name: 'Chat',
    data() {
        return {
            showChat: false,
            showNewChat: false,
            conversations: [],
            selectedConversation: null,
            messages: [],
            availableUsers: [],
            unreadCount: 0,
            loadingConversations: false,
            loadingMessages: false,
            loadingUsers: false,
            loadingOlderMessages: false,
            hasMoreOlderMessages: true, // Assume there are more messages until we know otherwise
            sending: false,
            newMessage: '',
            currentUser: JSON.parse(localStorage.getItem('user') || '{}'),
            pollInterval: null,
            windowStyle: {},
            isPolling: false, // Track if we're in a polling update
        };
    },
    mounted() {
        if (this.isAuthenticated()) {
            this.loadUnreadCount();
            // Don't load conversations or start polling until chat is opened
        }

        // Close chat when clicking outside
        document.addEventListener('click', this.handleClickOutside);
        window.addEventListener('resize', this.updateWindowPosition);
    },
    beforeUnmount() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
        document.removeEventListener('click', this.handleClickOutside);
        window.removeEventListener('resize', this.updateWindowPosition);
    },
    computed: {
        messagesWithSeparators() {
            if (!this.messages || this.messages.length === 0) {
                return [];
            }
            
            const result = [];
            let lastDate = null;
            
            for (const message of this.messages) {
                const messageDate = new Date(message.created_at);
                const messageDateStr = messageDate.toDateString();
                
                // Add date separator if date changed
                if (lastDate !== messageDateStr) {
                    result.push({
                        type: 'separator',
                        date: message.created_at
                    });
                    lastDate = messageDateStr;
                }
                
                // Add the message
                result.push(message);
            }
            
            return result;
        }
    },
    methods: {
        isAuthenticated() {
            const token = localStorage.getItem('token');
            return !!token;
        },
        async loadConversations(isPolling = false) {
            if (!this.isAuthenticated()) return;
            // Only show loading indicator on initial load, not during polling
            if (!isPolling) {
                this.loadingConversations = true;
            }
            try {
                const response = await api.get('/chat/conversations');
                this.conversations = response.data.conversations || [];
            } catch (error) {
                if (error.response?.status !== 401) {
                    console.error('Failed to load conversations:', error);
                }
                this.conversations = [];
            } finally {
                this.loadingConversations = false;
            }
        },
        async loadMessages(isPolling = false) {
            if (!this.selectedConversation || !this.isAuthenticated()) return;
            // Only show loading indicator on initial load, not during polling
            if (!isPolling) {
                this.loadingMessages = true;
            }
            try {
                const response = await api.get(`/chat/conversations/${this.selectedConversation.id}/messages`, {
                    params: { per_page: 10 } // Load last 10 messages initially
                });
                // API returns messages in ascending order (oldest first, newest last)
                let newMessages = response.data.data || [];
                
                // Update hasMoreOlderMessages based on response
                this.hasMoreOlderMessages = response.data.has_more === true;
                
                // During polling, append only new messages at the bottom
                if (isPolling && this.messages.length > 0) {
                    const existingIds = new Set(this.messages.map(m => m.id));
                    const newOnes = newMessages.filter(m => !existingIds.has(m.id));
                    if (newOnes.length > 0) {
                        // New messages are already in correct order from API
                        this.messages.push(...newOnes);
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    }
                } else {
                    // Initial load - replace all messages (remove duplicates)
                    this.messages = this.removeDuplicates(newMessages);
                    // Check if there are older messages (messages before the oldest one we just loaded)
                    this.hasMoreOlderMessages = response.data.has_more === true;
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                }
            } catch (error) {
                if (error.response?.status !== 401) {
                    console.error('Failed to load messages:', error);
                }
            } finally {
                this.loadingMessages = false;
            }
        },
        async loadOlderMessages() {
            if (!this.selectedConversation || !this.isAuthenticated() || this.loadingOlderMessages) return;
            if (this.messages.length === 0) return; // No messages to load older ones before
            
            this.loadingOlderMessages = true;
            const scrollElement = this.$refs.messagesList;
            const previousScrollHeight = scrollElement ? scrollElement.scrollHeight : 0;
            
            try {
                // Get the oldest message ID to load messages before it
                const oldestMessage = this.messages[0];
                const response = await api.get(`/chat/conversations/${this.selectedConversation.id}/messages`, {
                    params: {
                        per_page: 20, // Load 20 older messages at a time
                        before_id: oldestMessage.id
                    }
                });
                
                const olderMessages = response.data.data || [];
                
                // Update hasMoreOlderMessages based on response
                this.hasMoreOlderMessages = response.data.has_more === true;
                
                if (olderMessages.length > 0) {
                    // Filter out duplicates before prepending
                    const existingIds = new Set(this.messages.map(m => m.id));
                    const newOlderMessages = olderMessages.filter(m => !existingIds.has(m.id));
                    
                    if (newOlderMessages.length > 0) {
                        // Prepend older messages to the beginning
                        this.messages = [...newOlderMessages, ...this.messages];
                        
                        // Restore scroll position after prepending
                        this.$nextTick(() => {
                            if (scrollElement) {
                                const newScrollHeight = scrollElement.scrollHeight;
                                const scrollDiff = newScrollHeight - previousScrollHeight;
                                scrollElement.scrollTop = scrollDiff;
                            }
                        });
                    }
                } else {
                    // No more older messages
                    this.hasMoreOlderMessages = false;
                }
            } catch (error) {
                if (error.response?.status !== 401) {
                    console.error('Failed to load older messages:', error);
                }
            } finally {
                this.loadingOlderMessages = false;
            }
        },
        async loadAvailableUsers() {
            if (!this.isAuthenticated()) return;
            this.loadingUsers = true;
            try {
                const response = await api.get('/chat/available-users');
                console.log('Available users response:', response.data);
                this.availableUsers = response.data.users || [];
                console.log('Available users set to:', this.availableUsers);
            } catch (error) {
                console.error('Failed to load available users:', error);
                if (error.response) {
                    console.error('Error response:', error.response.data);
                    console.error('Error status:', error.response.status);
                }
                this.availableUsers = [];
            } finally {
                this.loadingUsers = false;
            }
        },
        async loadUnreadCount() {
            if (!this.isAuthenticated()) return;
            try {
                const response = await api.get('/chat/unread-count');
                this.unreadCount = response.data.count;
            } catch (error) {
                // Silently fail if unauthorized
                if (error.response?.status !== 401) {
                    console.error('Failed to load unread count:', error);
                }
            }
        },
        async startConversation(user) {
            try {
                let conversationId = user.conversation_id;
                
                if (!conversationId) {
                    // Create new conversation
                    const response = await api.post('/chat/conversations', {
                        user_id: user.id
                    });
                    conversationId = response.data.conversation.id;
                }

                // Find conversation in list or load it
                let conversation = this.conversations.find(c => c.id === conversationId);
                if (!conversation) {
                    await this.loadConversations();
                    conversation = this.conversations.find(c => c.id === conversationId);
                }

                if (conversation) {
                    this.selectedConversation = conversation;
                    this.showNewChat = false;
                    await this.loadMessages(false); // Initial load, show loading
                }
            } catch (error) {
                console.error('Failed to start conversation:', error);
                alert('Failed to start conversation. ' + (error.response?.data?.error || 'Please try again.'));
            }
        },
        selectConversation(conversation) {
            this.selectedConversation = conversation;
            this.loadMessages(false); // Not a polling update, show loading
        },
        async sendMessage() {
            if (!this.newMessage.trim() || !this.selectedConversation || this.sending) return;

            this.sending = true;
            const messageText = this.newMessage.trim();
            this.newMessage = '';

            try {
                const response = await api.post(`/chat/conversations/${this.selectedConversation.id}/messages`, {
                    message: messageText
                });

                // Add message to list (new messages go at the bottom)
                const newMessage = response.data.message;
                // Check if message already exists (might have been added by polling)
                const existingIds = new Set(this.messages.map(m => m.id));
                if (!existingIds.has(newMessage.id)) {
                    this.messages.push(newMessage);
                }
                this.$nextTick(() => {
                    this.scrollToBottom();
                });

                // Update conversation list
                await this.loadConversations();
            } catch (error) {
                console.error('Failed to send message:', error);
                this.newMessage = messageText; // Restore message on error
                alert('Failed to send message. Please try again.');
            } finally {
                this.sending = false;
            }
        },
        toggleChat() {
            this.showChat = !this.showChat;
            if (this.showChat) {
                this.loadConversations(false); // Initial load, show loading
                this.startPolling();
                this.$nextTick(() => {
                    this.updateWindowPosition();
                });
            } else {
                this.stopPolling();
            }
        },
        startPolling() {
            // Clear any existing interval
            this.stopPolling();
            // Poll for new messages every 10 seconds (only when chat is open)
            // 10 seconds is a good balance between responsiveness and server load
            this.pollInterval = setInterval(() => {
                if (this.isAuthenticated() && this.showChat) {
                    this.isPolling = true;
                    this.loadConversations(true); // Pass true to indicate this is a polling update
                    if (this.selectedConversation) {
                        this.loadMessages(true); // Pass true to indicate this is a polling update
                    }
                    this.loadUnreadCount();
                    this.isPolling = false;
                }
            }, 10000); // 10 seconds = optimal balance for chat
        },
        stopPolling() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
        },
        toggleNewChat() {
            this.showNewChat = !this.showNewChat;
            // Only load available users when opening the "New Chat" modal
            if (this.showNewChat && this.availableUsers.length === 0) {
                this.loadAvailableUsers();
            }
        },
        closeChat() {
            this.showChat = false;
            this.selectedConversation = null;
            this.showNewChat = false;
            this.stopPolling();
        },
        handleClickOutside(event) {
            if (this.showChat) {
                const container = this.$refs.container;
                const window = document.querySelector('.chat-window');
                if (container && !container.contains(event.target) && 
                    window && !window.contains(event.target)) {
                    this.closeChat();
                }
            }
        },
        updateWindowPosition() {
            this.$nextTick(() => {
                const button = this.$refs.container?.querySelector('.chat-button');
                const chatWidth = 420;
                const chatHeight = 600;
                const offset = 10; // Distance from button
                const navbarHeight = 70; // Navbar height

                if (button) {
                    const rect = button.getBoundingClientRect();
                    const windowWidth = window.innerWidth;
                    const windowHeight = window.innerHeight;
                    const spaceOnRight = windowWidth - rect.right;
                    const spaceBelow = windowHeight - rect.bottom;
                    const spaceAbove = rect.top;

                    let right, top, bottom;

                    // Horizontal positioning - align right edge with button's right edge, with offset
                    if (spaceOnRight >= chatWidth - 50) {
                        // Enough space on right, align with button
                        right = `${windowWidth - rect.right + offset}px`;
                    } else {
                        // Not enough space, position from right edge with margin
                        right = '1rem';
                    }

                    // Vertical positioning - below button, or above if not enough space
                    if (spaceBelow >= chatHeight) {
                        // Position below button
                        top = `${rect.bottom + offset}px`;
                        bottom = 'auto';
                    } else if (spaceAbove >= chatHeight) {
                        // Position above button
                        bottom = `${windowHeight - rect.top + offset}px`;
                        top = 'auto';
                    } else {
                        // Not enough space above or below, position at bottom of screen
                        top = 'auto';
                        bottom = '1rem';
                    }

                    this.windowStyle = {
                        right,
                        top,
                        bottom,
                        width: `${chatWidth}px`,
                        height: `${chatHeight}px`,
                    };
                } else {
                    // Fallback position (bottom right corner)
                    this.windowStyle = {
                        right: '1rem',
                        bottom: `${navbarHeight + 10}px`,
                        top: 'auto',
                        width: `${chatWidth}px`,
                        height: `${chatHeight}px`,
                    };
                }
            });
        },
        scrollToBottom() {
            const messagesList = this.$refs.messagesList;
            if (messagesList) {
                messagesList.scrollTop = messagesList.scrollHeight;
            }
        },
        getImageUrl(path) {
            if (!path) return '';
            // If already a full URL, return as is
            if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/storage/')) {
                return path;
            }
            // Otherwise, prepend storage URL
            return `/storage/${path}`;
        },
        getUserInitials(name) {
            if (!name) return '?';
            return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        },
        formatTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            // Only show time for messages
            return date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: true 
            });
        },
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const now = new Date();
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            
            // Check if it's today
            if (date.toDateString() === now.toDateString()) {
                return 'Today';
            }
            // Check if it's yesterday
            if (date.toDateString() === yesterday.toDateString()) {
                return 'Yesterday';
            }
            // Otherwise show the date
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
            });
        },
        removeDuplicates(messages) {
            const seen = new Set();
            return messages.filter(message => {
                if (seen.has(message.id)) {
                    return false;
                }
                seen.add(message.id);
                return true;
            });
        },
    },
};
</script>

<style scoped>
.chat-container {
    position: relative;
}

.chat-button {
    position: relative;
    width: 36px;
    height: 36px;
    border: none;
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    transition: all 0.2s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.chat-button:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #FFFFFF;
    border-color: rgba(255, 255, 255, 0.2);
}

.chat-button.active {
    background: rgba(31, 106, 225, 0.2);
    color: #FFFFFF;
    border-color: rgba(31, 106, 225, 0.3);
}

.chat-button svg {
    width: 18px;
    height: 18px;
}

.chat-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 5px;
    border-radius: 10px;
    min-width: 18px;
    height: 18px;
    text-align: center;
    line-height: 14px;
    border: 2px solid white;
    z-index: 1;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-window {
    position: fixed;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    z-index: 10001;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    max-width: calc(100vw - 2rem);
    max-height: calc(100vh - 2rem);
}

.chat-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
}

.chat-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
}

.chat-header-actions {
    display: flex;
    gap: 0.5rem;
}

.new-chat-btn,
.close-chat-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.new-chat-btn:hover,
.close-chat-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.new-chat-btn svg,
.close-chat-btn svg {
    width: 18px;
    height: 18px;
}

.chat-content {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* New Chat Modal */
.new-chat-modal {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.new-chat-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.new-chat-header h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.close-btn {
    width: 24px;
    height: 24px;
    border: none;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    font-size: 1.5rem;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.available-users-list {
    flex: 1;
    overflow-y: auto;
    padding: 0.5rem 0;
}

.loading-users,
.no-users {
    padding: 2rem;
    text-align: center;
    color: #9ca3af;
}

.user-item {
    padding: 0.875rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: background 0.2s ease;
}

.user-item:hover {
    background: #f9fafb;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.user-avatar span {
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-weight: 600;
    font-size: 0.9375rem;
    color: #111827;
}

.user-role {
    font-size: 0.8125rem;
    color: #6b7280;
    text-transform: capitalize;
}

.existing-conversation-badge {
    font-size: 0.75rem;
    color: #6366f1;
    font-weight: 500;
}

/* Conversations List */
.conversations-list {
    flex: 1;
    overflow-y: auto;
}

.loading-conversations,
.no-conversations {
    padding: 3rem 2rem;
    text-align: center;
    color: #9ca3af;
}

.no-conversations svg {
    width: 48px;
    height: 48px;
    margin-bottom: 1rem;
    color: #cbd5e1;
}

.no-conversations p {
    margin: 0.5rem 0 1rem;
    font-size: 0.875rem;
}

.start-chat-btn {
    padding: 0.5rem 1rem;
    background: #6366f1;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
}

.start-chat-btn:hover {
    background: #4f46e5;
}

.conversation-item {
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: background 0.2s ease;
    border-bottom: 1px solid #f3f4f6;
}

.conversation-item:hover {
    background: #f9fafb;
}

.conversation-item.active {
    background: #eff6ff;
}

.conversation-item.unread {
    background: linear-gradient(to right, #eff6ff 0%, #ffffff 4%);
    border-left: 3px solid #6366f1;
    padding-left: calc(1.5rem - 3px);
}

.conversation-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.conversation-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.conversation-avatar span {
    font-size: 1rem;
    font-weight: 600;
    color: white;
}

.conversation-info {
    flex: 1;
    min-width: 0;
}

.conversation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
}

.conversation-name {
    font-weight: 600;
    font-size: 0.9375rem;
    color: #111827;
}

.conversation-time {
    font-size: 0.75rem;
    color: #9ca3af;
}

.conversation-preview {
    font-size: 0.875rem;
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.conversation-preview .no-messages {
    font-style: italic;
    color: #9ca3af;
}

.conversation-unread {
    background: #6366f1;
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    min-width: 20px;
    text-align: center;
}

/* Chat Messages Container */
.chat-messages-container {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.chat-messages-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: white;
}

.back-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.back-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.back-btn svg {
    width: 18px;
    height: 18px;
}

.chat-messages-user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
}

.chat-messages-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.chat-messages-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.chat-messages-avatar span {
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
}

.chat-messages-name {
    font-weight: 600;
    font-size: 0.9375rem;
    color: #111827;
}

.messages-list {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.loading-messages {
    padding: 2rem;
    text-align: center;
    color: #9ca3af;
}

.message-item {
    display: flex;
    align-items: flex-end;
    gap: 0.5rem;
}

.message-item.own-message {
    flex-direction: row-reverse;
}

.message-content {
    max-width: 70%;
    background: #f3f4f6;
    padding: 0.625rem 0.875rem;
    border-radius: 12px;
}

.message-item.own-message .message-content {
    background: #6366f1;
    color: white;
}

.message-text {
    font-size: 0.9375rem;
    line-height: 1.5;
    word-wrap: break-word;
    margin-bottom: 0.25rem;
}

.message-time {
    font-size: 0.75rem;
    opacity: 0.7;
}

.date-separator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 1rem 0;
    position: relative;
}

.date-separator::before,
.date-separator::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e5e7eb;
}

.date-separator-text {
    padding: 0 0.75rem;
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
    background: #f9fafb;
}

.load-older-messages {
    display: flex;
    justify-content: center;
    padding: 0.75rem;
}

.load-older-btn {
    padding: 0.5rem 1rem;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.load-older-btn:hover {
    background: #e5e7eb;
    color: #111827;
}

.loading-older-messages {
    padding: 0.75rem;
    text-align: center;
    color: #9ca3af;
    font-size: 0.875rem;
}

.chat-input-container {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 0.75rem;
    align-items: flex-end;
    background: white;
}

.chat-input {
    flex: 1;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.625rem 0.875rem;
    font-size: 0.9375rem;
    font-family: inherit;
    resize: none;
    max-height: 120px;
    min-height: 40px;
}

.chat-input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.send-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: #6366f1;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.send-btn:hover:not(:disabled) {
    background: #4f46e5;
}

.send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.send-btn svg {
    width: 18px;
    height: 18px;
}

/* Scrollbar styling */
.conversations-list::-webkit-scrollbar,
.available-users-list::-webkit-scrollbar,
.messages-list::-webkit-scrollbar {
    width: 8px;
}

.conversations-list::-webkit-scrollbar-track,
.available-users-list::-webkit-scrollbar-track,
.messages-list::-webkit-scrollbar-track {
    background: transparent;
}

.conversations-list::-webkit-scrollbar-thumb,
.available-users-list::-webkit-scrollbar-thumb,
.messages-list::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 4px;
}

.conversations-list::-webkit-scrollbar-thumb:hover,
.available-users-list::-webkit-scrollbar-thumb:hover,
.messages-list::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}

@media (max-width: 768px) {
    .chat-window {
        width: calc(100vw - 2rem) !important;
        height: calc(100vh - 2rem) !important;
        right: 1rem !important;
        bottom: 1rem !important;
    }
}
</style>

