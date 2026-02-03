<template>
    <Layout>
        <div v-if="loading" class="loading">Loading...</div>
        <div v-else class="dashboard-wrapper">
            <!-- Animated Background Orbs -->
            <div class="dashboard-orbs">
                <div class="dashboard-orb orb-1"></div>
                <div class="dashboard-orb orb-2"></div>
                <div class="dashboard-orb orb-3"></div>
                <div class="dashboard-orb orb-4"></div>
            </div>
            
            <div class="dashboard-main">
                <!-- Welcome Header -->
                <div class="dashboard-header">
                    <div class="header-content">
                        <div v-if="user?.profile_picture" class="header-avatar">
                            <img :src="getImageUrl(user.profile_picture)" alt="Profile" />
                        </div>
                        <div v-else class="header-avatar placeholder">
                            {{ userInitials }}
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">{{ greetingMessage }}</h1>
                            <p class="header-subtitle">Manage your job postings and track your opportunities</p>
                        </div>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="stats-section">
                    <div class="stats-cards">
                        <div class="stat-card">
                            <div class="stat-icon-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Total Jobs</div>
                                <div class="stat-value">{{ stats?.total || 0 }}</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon-wrapper open-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4M12 8h.01"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Open</div>
                                <div class="stat-value">{{ stats?.open || 0 }}</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon-wrapper progress-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">In Progress</div>
                                <div class="stat-value">{{ stats?.in_progress || 0 }}</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon-wrapper completed-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Completed</div>
                                <div class="stat-value">{{ stats?.completed || 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jobs Section -->
                <div class="jobs-section">
                    <div class="section-header-row">
                        <h2 class="section-title">My Jobs</h2>
                        <div class="header-actions">
                            <router-link to="/client/invoices" class="view-all-link">
                                View Invoices
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </router-link>
                            <router-link to="/client/jobs/create" class="post-job-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Post New Job
                            </router-link>
                        </div>
                    </div>

                    <div v-if="jobs?.data?.length > 0" class="jobs-list">
                        <div v-for="job in jobs.data" :key="job.id" class="job-card">
                            <div class="job-card-header">
                                <div class="job-title-section">
                                    <router-link :to="`/client/jobs/${job.id}`" class="job-title-link">
                                        <h3 class="job-title">{{ job.title }}</h3>
                                    </router-link>
                                    <div class="job-meta-row">
                                        <span class="status-badge" :class="getStatusClass(job.status)">
                                            {{ capitalize(job.status) }}
                                        </span>
                                        <span v-if="job.category" class="job-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                            </svg>
                                            {{ job.category }}
                                        </span>
                                        <span v-if="job.budget" class="job-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="12" y1="1" x2="12" y2="23"/>
                                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                            </svg>
                                            {{ formatMoney(job.budget) }} TND
                                        </span>
                                    </div>
                                </div>
                                <div class="job-actions">
                                    <router-link :to="`/client/jobs/${job.id}/edit`" class="action-btn edit-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Edit
                                    </router-link>
                                    <button v-if="job.status === 'open'" @click="openDeleteModal(job)" class="action-btn delete-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                            <p v-if="job.description" class="job-description">{{ truncate(job.description, 150) }}</p>
                        </div>
                    </div>
                    <div v-else class="empty-state">
                        <div class="empty-state-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                        </div>
                        <p>No jobs posted yet.</p>
                        <router-link to="/client/jobs/create" class="browse-jobs-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Create Your First Job
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Job Modal -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="modal-overlay" @click.self="closeDeleteModal">
                <div class="modal-content delete-modal">
                    <div class="modal-header">
                        <h2>Delete Job</h2>
                        <button @click="closeDeleteModal" class="close-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this job? This action cannot be undone.</p>
                        <p v-if="selectedJobForDeletion" class="job-title-warning"><strong>{{ selectedJobForDeletion.title }}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeDeleteModal" class="btn btn-secondary">Cancel</button>
                        <button @click="confirmDelete" :disabled="deleting" class="btn btn-danger">
                            {{ deleting ? 'Deleting...' : 'Delete Job' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </Layout>
</template>

<script>
import Layout from '../shared/Layout.vue';
import api from '../../api.js';
import { Teleport } from 'vue';

export default {
    name: 'ClientDashboard',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            stats: null,
            jobs: null,
            user: null,
            showDeleteModal: false,
            selectedJobForDeletion: null,
            deleting: false,
        };
    },
    computed: {
        userInitials() {
            if (!this.user?.name) return 'C';
            const names = this.user.name.split(' ');
            if (names.length >= 2) {
                return (names[0][0] + names[1][0]).toUpperCase();
            }
            return names[0][0].toUpperCase();
        },
        greetingMessage() {
            const hour = new Date().getHours();
            const firstName = this.user?.name?.split(' ')[0] || 'Client';
            
            if (hour < 12) {
                return `Good Morning, ${firstName}!`;
            } else if (hour < 17) {
                return `Good Afternoon, ${firstName}!`;
            } else {
                return `Good Evening, ${firstName}!`;
            }
        },
    },
    async mounted() {
        await this.loadUserProfile();
        await this.loadData();
    },
    methods: {
        async loadUserProfile() {
            try {
                const response = await api.get('/user');
                this.user = response.data;
                // Update localStorage with fresh user data
                localStorage.setItem('user', JSON.stringify(response.data));
            } catch (error) {
                console.error('Failed to load user profile:', error);
                // Keep using localStorage data as fallback
                const userStr = localStorage.getItem('user');
                if (userStr) {
                    this.user = JSON.parse(userStr);
                }
            }
        },
        async loadData() {
            try {
                const response = await api.get('/client/dashboard');
                this.stats = response.data.stats;
                this.jobs = response.data.jobs;
            } catch (error) {
                console.error('Failed to load dashboard:', error);
            } finally {
                this.loading = false;
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
        openDeleteModal(job) {
            this.selectedJobForDeletion = job;
            this.showDeleteModal = true;
        },
        closeDeleteModal() {
            this.showDeleteModal = false;
            this.selectedJobForDeletion = null;
        },
        async confirmDelete() {
            if (!this.selectedJobForDeletion) return;
            this.deleting = true;
            try {
                await api.delete(`/client/jobs/${this.selectedJobForDeletion.id}`);
                this.closeDeleteModal();
                await this.loadData();
            } catch (error) {
                alert(error.response?.data?.message || 'Failed to delete job');
            } finally {
                this.deleting = false;
            }
        },
        formatMoney(amount) {
            const num = Number(amount) || 0;
            return num.toFixed(2);
        },
        capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ');
        },
        truncate(text, length) {
            if (!text) return '';
            return text.length > length ? text.substring(0, length) + '...' : text;
        },
        getStatusClass(status) {
            const classes = {
                open: 'badge-success',
                in_progress: 'badge-warning',
                completed: 'badge-info',
                cancelled: 'badge-danger',
            };
            return classes[status] || 'badge-secondary';
        },
    },
};
</script>

<style scoped>
.dashboard-wrapper {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    min-height: calc(100vh - 70px);
    padding: 1.5rem;
    max-width: 1600px;
    margin: 0 auto;
    background: transparent;
    position: relative;
    overflow: hidden;
}

.dashboard-orbs {
    position: fixed;
    top: 70px;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: calc(100vh - 70px);
    pointer-events: none;
    z-index: 0;
    overflow: visible;
}

.dashboard-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    will-change: transform;
}

.dashboard-orb.orb-1 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.4) 0%, rgba(99, 102, 241, 0.2) 50%, rgba(99, 102, 241, 0) 80%);
    top: 10%;
    left: -10%;
    animation: float-dashboard-orb-1 40s ease-in-out infinite;
}

.dashboard-orb.orb-2 {
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.4) 0%, rgba(16, 185, 129, 0.2) 50%, rgba(16, 185, 129, 0) 80%);
    top: 30%;
    right: -10%;
    animation: float-dashboard-orb-2 45s ease-in-out infinite;
}

.dashboard-orb.orb-3 {
    width: 480px;
    height: 480px;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.4) 0%, rgba(139, 92, 246, 0.2) 50%, rgba(139, 92, 246, 0) 80%);
    bottom: 10%;
    left: -5%;
    animation: float-dashboard-orb-3 50s ease-in-out infinite;
}

.dashboard-orb.orb-4 {
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.35) 0%, rgba(16, 185, 129, 0.18) 50%, rgba(16, 185, 129, 0) 80%);
    top: 60%;
    left: 50%;
    transform: translateX(-50%);
    animation: float-dashboard-orb-4 42s ease-in-out infinite;
}

@keyframes float-dashboard-orb-1 {
    0% {
        transform: translate(0, 0) scale(1);
    }
    25% {
        transform: translate(calc(50vw + 100px), 30vh) scale(1.15);
    }
    50% {
        transform: translate(calc(90vw + 200px), -20vh) scale(0.95);
    }
    75% {
        transform: translate(calc(60vw + 150px), 50vh) scale(1.1);
    }
    100% {
        transform: translate(0, 0) scale(1);
    }
}

@keyframes float-dashboard-orb-2 {
    0% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(calc(-40vw - 100px), 40vh) scale(1.2);
    }
    66% {
        transform: translate(calc(-80vw - 200px), -30vh) scale(0.9);
    }
    100% {
        transform: translate(0, 0) scale(1);
    }
}

@keyframes float-dashboard-orb-3 {
    0% {
        transform: translate(0, 0) scale(1);
    }
    20% {
        transform: translate(calc(40vw + 150px), -25vh) scale(1.18);
    }
    40% {
        transform: translate(calc(85vw + 250px), 35vh) scale(0.92);
    }
    60% {
        transform: translate(calc(25vw + 100px), 70vh) scale(1.12);
    }
    80% {
        transform: translate(calc(-15vw - 50px), 25vh) scale(1.05);
    }
    100% {
        transform: translate(0, 0) scale(1);
    }
}

@keyframes float-dashboard-orb-4 {
    0% {
        transform: translate(-50%, 0) scale(1);
    }
    25% {
        transform: translate(calc(-50% + 35vw + 100px), -40vh) scale(1.22);
    }
    50% {
        transform: translate(calc(-50% - 25vw - 100px), 30vh) scale(0.88);
    }
    75% {
        transform: translate(calc(-50% + 45vw + 150px), 20vh) scale(1.15);
    }
    100% {
        transform: translate(-50%, 0) scale(1);
    }
}

.dashboard-main {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Dashboard Header */
.dashboard-header {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.08),
        0 2px 8px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.06) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    position: relative;
    z-index: 1;
}

.header-avatar {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    overflow: hidden;
    flex-shrink: 0;
    border: 4px solid rgba(255, 255, 255, 1);
    box-shadow: 
        0 8px 24px rgba(0, 0, 0, 0.12),
        0 0 0 1px rgba(99, 102, 241, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.header-avatar::before {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 20px;
    padding: 4px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #6366f1);
    background-size: 200% 200%;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.header-avatar:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 12px 32px rgba(99, 102, 241, 0.2),
        0 0 0 1px rgba(99, 102, 241, 0.2);
}

.header-avatar:hover::before {
    opacity: 1;
    animation: gradientBorder 3s ease infinite;
}

@keyframes gradientBorder {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.header-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.header-avatar.placeholder {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #6366f1 100%);
    background-size: 200% 200%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 2rem;
    animation: gradientShift 4s ease infinite;
}

@keyframes gradientShift {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.header-text {
    flex: 1;
}

.header-title {
    font-size: 2.25rem;
    font-weight: 800;
    background: linear-gradient(135deg, #111827 0%, #374151 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.03em;
    line-height: 1.2;
}

.header-subtitle {
    font-size: 1.0625rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
    letter-spacing: -0.01em;
}

/* Stats Section */
.stats-section {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(229, 231, 235, 0.5);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.stat-card:hover {
    border-color: rgba(99, 102, 241, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

.stat-icon-wrapper svg {
    width: 24px;
    height: 24px;
}

.stat-icon-wrapper.open-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.stat-icon-wrapper.progress-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
}

.stat-icon-wrapper.completed-icon {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

.stat-info {
    flex: 1;
    min-width: 0;
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
    letter-spacing: -0.5px;
}

@media (max-width: 1200px) {
    .stats-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-cards {
        grid-template-columns: 1fr;
    }
}

/* Jobs Section */
.jobs-section {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.section-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.view-all-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #6366f1;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
}

.view-all-link svg {
    width: 16px;
    height: 16px;
    transition: transform 0.2s ease;
}

.view-all-link:hover {
    color: #4f46e5;
}

.view-all-link:hover svg {
    transform: translateX(4px);
}

.jobs-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.job-card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(229, 231, 235, 0.5);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.job-card:hover {
    border-color: rgba(99, 102, 241, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.job-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.job-title-section {
    flex: 1;
    min-width: 0;
}

.job-title-link {
    text-decoration: none;
    display: block;
    margin-bottom: 0.75rem;
}

.job-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
    transition: color 0.2s ease;
}

.job-title-link:hover .job-title {
    color: #6366f1;
}

.job-meta-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.status-badge {
    padding: 0.375rem 0.875rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.status-badge.badge-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.status-badge.badge-info {
    background: rgba(99, 102, 241, 0.1);
    color: #4f46e5;
}

.status-badge.badge-danger {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

.job-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.job-meta-item svg {
    width: 16px;
    height: 16px;
}

.job-description {
    color: #6b7280;
    font-size: 0.9375rem;
    line-height: 1.6;
    margin: 0;
}

.job-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.action-btn svg {
    width: 16px;
    height: 16px;
}

.action-btn.edit-btn {
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
}

.action-btn.edit-btn:hover {
    background: rgba(99, 102, 241, 0.15);
    transform: translateY(-1px);
}

.action-btn.delete-btn {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.action-btn.delete-btn:hover {
    background: rgba(239, 68, 68, 0.15);
    transform: translateY(-1px);
}

.post-job-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9375rem;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    transition: all 0.2s ease;
}

.post-job-btn svg {
    width: 18px;
    height: 18px;
}

.post-job-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
}

.post-job-btn:hover svg {
    transform: rotate(90deg);
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6b7280;
}

.empty-state-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1.5rem;
    color: #d1d5db;
}

.empty-state-icon svg {
    width: 100%;
    height: 100%;
}

.empty-state p {
    font-size: 1rem;
    margin: 0 0 1.5rem 0;
}

.browse-jobs-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.5rem;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    transition: all 0.2s ease;
}

.browse-jobs-btn svg {
    width: 20px;
    height: 20px;
}

.browse-jobs-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
}

.loading {
    text-align: center;
    padding: 4rem;
    color: #64748b;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.loading::before {
    content: '';
    width: 48px;
    height: 48px;
    border: 4px solid #e2e8f0;
    border-top-color: #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.5;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

/* Delete Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.delete-modal {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 500px;
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}

.close-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    transition: color 0.2s;
    border-radius: 8px;
}

.close-btn:hover {
    color: #0f172a;
    background: #f1f5f9;
}

.close-btn svg {
    width: 24px;
    height: 24px;
}

.modal-body {
    padding: 2rem;
}

.modal-body p {
    margin: 0;
    color: #374151;
    font-size: 1rem;
    line-height: 1.6;
}

.job-title-warning {
    margin-top: 1rem;
    padding: 1rem;
    background: #fef2f2;
    border-radius: 8px;
    color: #991b1b;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1.5rem 2rem;
    border-top: 1px solid #e5e7eb;
}

.modal-footer .btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}

.modal-footer .btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.modal-footer .btn-secondary:hover {
    background: #e5e7eb;
}

.modal-footer .btn-danger {
    background: #dc2626;
    color: white;
}

.modal-footer .btn-danger:hover {
    background: #b91c1c;
}

.modal-footer .btn-danger:disabled {
    background: #fca5a5;
    cursor: not-allowed;
}
</style>
