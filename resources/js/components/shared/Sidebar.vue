<template>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <div class="navbar-title">UniJob</div>
            </div>
            <div class="navbar-nav">
                <router-link v-if="userRole === 'student'" to="/student/dashboard" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span>Dashboard</span>
                </router-link>
                <router-link v-if="userRole === 'student'" to="/student/jobs" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                    <span>Browse Jobs</span>
                </router-link>
                <router-link v-if="userRole === 'student'" to="/student/applications" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <span>My Applications</span>
                </router-link>
                <router-link v-if="userRole === 'student'" to="/student/invoices" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <span>Invoices</span>
                </router-link>
                <router-link v-if="userRole === 'student'" to="/student/profile" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Profile</span>
                </router-link>
                <router-link v-if="userRole === 'admin'" to="/admin/dashboard" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span>Dashboard</span>
                </router-link>
                <router-link v-if="userRole === 'admin'" to="/admin/users" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Users</span>
                </router-link>
                <router-link v-if="userRole === 'admin'" to="/admin/jobs" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                    <span>Jobs History</span>
                </router-link>
                <router-link v-if="userRole === 'admin'" to="/admin/invoices" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <span>Invoices</span>
                </router-link>
                <router-link v-if="userRole === 'client'" to="/client/dashboard" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span>Dashboard</span>
                </router-link>
                <router-link v-if="userRole === 'client'" to="/client/jobs" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <span>My Jobs</span>
                </router-link>
                <router-link v-if="userRole === 'client'" to="/client/invoices" class="nav-item" active-class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <span>Invoices</span>
                </router-link>
            </div>
            <div class="navbar-right">
                <Chat />
                <NotificationBell />
                <HelpChatbot v-if="userRole !== 'admin'" :userRole="userRole" />
                <button @click="handleLogout" class="nav-item logout-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </nav>
</template>

<script>
import api from '../../api.js';
import Chat from './Chat.vue';
import NotificationBell from './NotificationBell.vue';
import HelpChatbot from './HelpChatbot.vue';

export default {
    name: 'Sidebar',
    components: {
        Chat,
        NotificationBell,
        HelpChatbot,
    },
    data() {
        return {
            user: JSON.parse(localStorage.getItem('user') || '{}'),
        };
    },
    computed: {
        userRole() {
            return this.user?.role;
        },
    },
    methods: {
        async handleLogout() {
            try {
                await api.post('/logout');
            } catch (error) {
                console.error('Logout error:', error);
            }
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            // Use window.location for a full page navigation to clear all state
            window.location.href = '/login';
        },
    },
};
</script>

<style scoped>
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 70px;
    background: #000000;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    z-index: 100;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    transform: translateZ(0);
    backface-visibility: hidden;
    will-change: transform;
    overflow: hidden;
}

.navbar-container {
    max-width: 100%;
    height: 100%;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    position: relative;
    z-index: 1;
}

.navbar-brand {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    min-width: 200px;
    width: 200px;
}

.navbar-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #FFFFFF;
    letter-spacing: -0.5px;
    white-space: nowrap;
    width: 100%;
}

.navbar-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
    justify-content: center;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.2s ease, color 0.2s ease;
    font-weight: 500;
    font-size: 0.9375rem;
    white-space: nowrap;
    background: transparent;
    border: none;
    cursor: pointer;
    position: relative;
    min-height: 40px;
    box-sizing: border-box;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #FFFFFF;
}

.nav-item.active {
    background: rgba(31, 106, 225, 0.2);
    color: #FFFFFF;
    font-weight: 600;
}

.nav-item.active svg {
    color: #FFFFFF;
}

.nav-item svg {
    width: 18px;
    height: 18px;
    min-width: 18px;
    min-height: 18px;
    flex-shrink: 0;
    color: rgba(255, 255, 255, 0.8);
}

.nav-item span {
    white-space: nowrap;
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-left: auto;
}

.logout-btn {
    color: rgba(255, 255, 255, 0.8);
}

.logout-btn:hover {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

/* Responsive design */
@media (max-width: 768px) {
    .navbar-container {
        padding: 0 1rem;
        gap: 1rem;
    }

    .navbar-nav {
        gap: 0.25rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .navbar-nav::-webkit-scrollbar {
        display: none;
    }

    .nav-item span {
        font-size: 0.875rem;
    }

    .nav-item {
        padding: 0.5rem 0.75rem;
        gap: 0.375rem;
    }

    .navbar-title {
        font-size: 1.25rem;
    }
}

@media (max-width: 640px) {
    .navbar {
        height: 60px;
    }

    .nav-item span {
        display: none;
    }

    .nav-item {
        padding: 0.5rem;
        min-width: 40px;
        justify-content: center;
    }

    .navbar-title {
        font-size: 1.125rem;
    }
}
</style>
