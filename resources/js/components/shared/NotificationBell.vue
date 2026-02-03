<template>
    <div class="notification-bell-container" ref="container">
        <button 
            class="notification-bell" 
            @click="toggleDropdown"
            :class="{ active: showDropdown }"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
        </button>

        <!-- Dropdown -->
        <Teleport to="body">
            <div 
                v-if="showDropdown" 
                class="notification-dropdown" 
                :style="dropdownStyle"
                @click.stop
            >
                    <div class="notification-dropdown-header">
                        <h3>Notifications</h3>
                        <div class="notification-header-actions">
                            <button 
                                v-if="unreadCount > 0" 
                                @click="markAllAsRead" 
                                class="mark-all-read-btn"
                                :disabled="markingAllAsRead"
                            >
                                {{ markingAllAsRead ? 'Marking...' : 'Mark all as read' }}
                            </button>
                            <button @click="closeDropdown" class="close-dropdown-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="notification-dropdown-body">
                        <div v-if="loading" class="notification-loading">
                            <div class="spinner"></div>
                            <p>Loading notifications...</p>
                        </div>
                        <div v-else-if="notifications.length === 0" class="notification-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                            <p>No notifications</p>
                        </div>
                        <div v-else class="notification-list">
                            <div
                                v-for="notification in notifications"
                                :key="notification.id"
                                class="notification-item"
                                :class="{ unread: !notification.read_at }"
                                @click="handleNotificationClick(notification)"
                            >
                                <div class="notification-item-content">
                                    <div class="notification-title">{{ notification.title }}</div>
                                    <div class="notification-message">{{ notification.message }}</div>
                                    <div class="notification-time">{{ formatTime(notification.created_at) }}</div>
                                </div>
                                <div v-if="!notification.read_at" class="notification-unread-dot"></div>
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
    name: 'NotificationBell',
    data() {
        return {
            showDropdown: false,
            notifications: [],
            unreadCount: 0,
            loading: false,
            markingAllAsRead: false,
            dropdownStyle: {},
        };
    },
    mounted() {
        if (this.isAuthenticated()) {
            this.loadUnreadCount();
            this.loadNotifications();
        // Poll for new notifications every 30 seconds (only when authenticated)
        this.pollInterval = setInterval(() => {
            if (this.isAuthenticated()) {
                this.loadUnreadCount();
                if (this.showDropdown) {
                    this.loadNotifications();
                }
            }
        }, 30000);
        }
        // Close dropdown when clicking outside
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        isAuthenticated() {
            const token = localStorage.getItem('token');
            return !!token;
        },
        async loadUnreadCount() {
            if (!this.isAuthenticated()) return;
            try {
                const response = await api.get('/notifications/unread-count');
                this.unreadCount = response.data.count;
            } catch (error) {
                // Silently fail if unauthorized (user not logged in)
                if (error.response?.status !== 401) {
                    console.error('Failed to load unread count:', error);
                }
            }
        },
        async loadNotifications() {
            if (!this.isAuthenticated()) return;
            this.loading = true;
            try {
                const response = await api.get('/notifications', {
                    params: { per_page: 20 }
                });
                // Handle paginated response from Laravel
                if (response.data.data) {
                    this.notifications = response.data.data;
                } else if (Array.isArray(response.data)) {
                    this.notifications = response.data;
                } else {
                    this.notifications = [];
                }
            } catch (error) {
                // Silently fail if unauthorized (user not logged in)
                if (error.response?.status !== 401) {
                    console.error('Failed to load notifications:', error);
                }
            } finally {
                this.loading = false;
            }
        },
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            if (this.showDropdown) {
                this.loadNotifications();
                this.updateDropdownPosition();
            }
        },
        closeDropdown() {
            this.showDropdown = false;
        },
        updateDropdownPosition() {
            this.$nextTick(() => {
                const bell = this.$refs.container?.querySelector('.notification-bell');
                if (bell) {
                    const rect = bell.getBoundingClientRect();
                    const windowWidth = window.innerWidth;
                    const windowHeight = window.innerHeight;
                    const dropdownWidth = 420;
                    const dropdownHeight = 600;
                    const spaceOnRight = windowWidth - rect.right;
                    const spaceBelow = windowHeight - rect.bottom;
                    
                    // Calculate optimal position
                    let top, right, left;
                    
                    // Check if dropdown fits below the bell
                    if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
                        // Position above the bell if there's more space above
                        top = `${rect.top - dropdownHeight - 10}px`;
                    } else {
                        // Position below the bell
                        top = `${rect.bottom + 10}px`;
                    }
                    
                    // Position horizontally - align right edge with bell's right edge, with some offset
                    if (spaceOnRight >= dropdownWidth - 50) {
                        // Enough space, align with bell's right edge minus small offset
                        right = `${windowWidth - rect.right - 10}px`;
                        left = 'auto';
                    } else {
                        // Not enough space, position from right edge with margin
                        right = '1rem';
                        left = 'auto';
                    }
                    
                    this.dropdownStyle = {
                        top,
                        right,
                        left,
                        animation: 'slideDown 0.2s ease',
                    };
                }
            });
        },
        handleClickOutside(event) {
            if (this.showDropdown) {
                const container = this.$refs.container;
                const dropdown = document.querySelector('.notification-dropdown');
                if (container && !container.contains(event.target) && 
                    dropdown && !dropdown.contains(event.target)) {
                    this.closeDropdown();
                }
            }
        },
        async handleNotificationClick(notification) {
            if (!this.isAuthenticated()) return;
            if (!notification.read_at) {
                try {
                    await api.post(`/notifications/${notification.id}/read`);
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch (error) {
                    if (error.response?.status !== 401) {
                        console.error('Failed to mark notification as read:', error);
                    }
                }
            }

            // Navigate based on notification type and data
            if (notification.data) {
                if (notification.data.job_id) {
                    if (notification.type.startsWith('application_')) {
                        this.$router.push('/student/applications');
                    } else if (notification.type === 'new_application') {
                        this.$router.push(`/client/jobs/${notification.data.job_id}`);
                    }
                } else if (notification.data.invoice_id) {
                    this.$router.push(`/client/invoices/${notification.data.invoice_id}`);
                } else if (notification.type === 'verification_approved' || notification.type === 'verification_rejected') {
                    this.$router.push('/student/profile');
                }
            }

            this.closeDropdown();
        },
        async markAllAsRead() {
            if (!this.isAuthenticated()) return;
            this.markingAllAsRead = true;
            try {
                await api.post('/notifications/read-all');
                this.notifications.forEach(n => n.read_at = new Date().toISOString());
                this.unreadCount = 0;
            } catch (error) {
                if (error.response?.status !== 401) {
                    console.error('Failed to mark all as read:', error);
                }
            } finally {
                this.markingAllAsRead = false;
            }
        },
        formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            return date.toLocaleDateString();
        },
    },
};
</script>

<style scoped>
.notification-bell-container {
    position: relative;
}


.notification-bell {
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

.notification-bell:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #FFFFFF;
    border-color: rgba(255, 255, 255, 0.2);
}

.notification-bell.active {
    background: rgba(31, 106, 225, 0.2);
    color: #FFFFFF;
    border-color: rgba(31, 106, 225, 0.3);
}

.notification-bell svg {
    width: 18px;
    height: 18px;
}

.notification-badge {
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

/* Dropdown */
.notification-dropdown {
    position: fixed;
    width: 420px;
    max-width: calc(100vw - 2rem);
    max-height: 600px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(0, 0, 0, 0.05);
    z-index: 10001;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-dropdown-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
}

.notification-dropdown-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.025em;
}

.notification-header-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.mark-all-read-btn {
    padding: 0.5rem 0.875rem;
    background: transparent;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #4b5563;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.mark-all-read-btn:hover:not(:disabled) {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #111827;
}

.mark-all-read-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.close-dropdown-btn {
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

.close-dropdown-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.close-dropdown-btn svg {
    width: 18px;
    height: 18px;
}

.notification-dropdown-body {
    flex: 1;
    overflow-y: auto;
    max-height: 520px;
    background: white;
}

.notification-loading,
.notification-empty {
    padding: 4rem 2rem;
    text-align: center;
    color: #9ca3af;
}

.notification-empty p {
    margin-top: 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
}

.notification-loading .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e2e8f0;
    border-top-color: #6366f1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.notification-empty svg {
    width: 48px;
    height: 48px;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.notification-list {
    display: flex;
    flex-direction: column;
}

.notification-item {
    padding: 1.125rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    transition: all 0.2s ease;
    position: relative;
    background: white;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background: #f9fafb;
    transform: translateX(-2px);
}

.notification-item.unread {
    background: linear-gradient(to right, #eff6ff 0%, #ffffff 4%);
    border-left: 3px solid #6366f1;
    padding-left: calc(1.5rem - 3px);
}

.notification-item.unread:hover {
    background: linear-gradient(to right, #dbeafe 0%, #f9fafb 4%);
}

.notification-item-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    font-size: 0.9375rem;
    color: #111827;
    margin-bottom: 0.375rem;
    line-height: 1.4;
    letter-spacing: -0.01em;
}

.notification-item.unread .notification-title {
    color: #1e40af;
}

.notification-message {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.5;
    margin-bottom: 0.625rem;
    word-wrap: break-word;
}

.notification-time {
    font-size: 0.75rem;
    color: #9ca3af;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.notification-unread-dot {
    width: 8px;
    height: 8px;
    background: #6366f1;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 0.5rem;
    box-shadow: 0 0 0 2px white, 0 2px 4px rgba(99, 102, 241, 0.3);
}

/* Scrollbar styling */
.notification-dropdown-body::-webkit-scrollbar {
    width: 8px;
}

.notification-dropdown-body::-webkit-scrollbar-track {
    background: transparent;
}

.notification-dropdown-body::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 4px;
    border: 2px solid transparent;
    background-clip: padding-box;
}

.notification-dropdown-body::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
    background-clip: padding-box;
}

@media (max-width: 768px) {
    .notification-dropdown {
        width: calc(100vw - 2rem);
        max-height: 80vh;
        left: 1rem !important;
        right: 1rem !important;
        transform: none !important;
    }

    .notification-dropdown-body {
        max-height: 60vh;
    }
}
</style>

