<template>
    <Layout>
        <div class="dashboard-wrapper" v-show="!loading">
            <!-- Animated Background Orbs -->
            <div class="dashboard-orbs">
                <div class="dashboard-orb orb-1"></div>
                <div class="dashboard-orb orb-2"></div>
                <div class="dashboard-orb orb-3"></div>
                <div class="dashboard-orb orb-4"></div>
            </div>
            
            <!-- Main Content Area -->
            <div class="dashboard-main">
                <!-- Welcome Header -->
                <div class="dashboard-header-wrapper">
                    <div class="header-picture-container">
                        <div v-if="user?.profile_picture" class="header-avatar">
                            <img :src="getImageUrl(user.profile_picture)" alt="Profile" />
                        </div>
                        <div v-else class="header-avatar placeholder">
                            {{ userInitials }}
                        </div>
                    </div>
                    <div class="header-greetings-container">
                        <h1 class="header-title">{{ greetingMessage }}</h1>
                        <p class="header-subtitle">Manage your applications and discover opportunities</p>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="stats-section">
                    <h2 class="section-title">Overview</h2>
                    <div class="stats-cards">
                        <div class="stat-card" data-stat="total">
                            <div class="stat-icon-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Total Applications</div>
                                <div class="stat-value">{{ animatedStats.total }}</div>
                            </div>
                        </div>
                        <div class="stat-card" data-stat="pending">
                            <div class="stat-icon-wrapper pulse-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Pending</div>
                                <div class="stat-value">{{ animatedStats.pending }}</div>
                            </div>
                        </div>
                        <div class="stat-card" data-stat="accepted">
                            <div class="stat-icon-wrapper success-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Accepted</div>
                                <div class="stat-value">{{ animatedStats.accepted }}</div>
                            </div>
                        </div>
                        <div class="stat-card" data-stat="rejected">
                            <div class="stat-icon-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Rejected</div>
                                <div class="stat-value">{{ animatedStats.rejected }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applications List -->
                <div class="applications-section">
                    <div class="section-header-row">
                        <h2 class="section-title">My Applications</h2>
                        <router-link to="/student/applications" class="view-all-link">
                            View All
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </router-link>
                    </div>
                    <div v-if="applications?.data?.length > 0" class="applications-grid">
                        <div v-for="application in applications.data.slice(0, 6)" :key="application.id" class="application-card">
                            <div class="application-card-header">
                                <div class="application-title-row">
                                    <router-link :to="`/student/jobs/${application.job.id}`" class="application-job-title">
                                        {{ application.job.title }}
                                    </router-link>
                                    <span class="status-badge" :class="getStatusClass(application.status)">
                                        {{ capitalize(application.status) }}
                                    </span>
                                </div>
                                <div class="application-company">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    {{ application.job.client?.name || 'N/A' }}
                                </div>
                            </div>
                            <div class="application-card-body">
                                <div class="application-meta-grid">
                                    <div class="application-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        <span>{{ formatDate(application.created_at) }}</span>
                                    </div>
                                    <div class="application-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="1" x2="12" y2="23"/>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                        <span>{{ formatMoney(application.job.budget) }} TND</span>
                                    </div>
                                </div>
                            </div>
                            <div class="application-card-footer">
                                <router-link :to="`/student/jobs/${application.job.id}`" class="view-details-btn">
                                    View Details
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state">
                        <div class="empty-state-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <p>No applications yet.</p>
                        <router-link to="/student/jobs" class="browse-jobs-btn">Browse Jobs</router-link>
                    </div>
                </div>

                <!-- Recommended for You Section -->
                <div class="recommended-jobs-section">
                    <div class="section-header-row">
                        <h2 class="section-title">Recommended for You</h2>
                        <router-link to="/student/jobs" class="view-all-link">
                            View All
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </router-link>
                    </div>
                    <div v-if="loadingJobs" class="loading-jobs">
                        <div class="loading-spinner"></div>
                        <p>Loading recommended jobs...</p>
                    </div>
                    <div v-else-if="recommendedJobs?.length > 0" class="recommended-jobs-grid">
                        <div v-for="job in recommendedJobs.slice(0, 6)" :key="job.id" class="recommended-job-card">
                            <div class="recommended-job-card-header">
                                <div class="company-avatar-large">
                                    <span>{{ getCompanyInitials(job.client?.name || 'Company') }}</span>
                                </div>
                                <div class="recommended-job-card-content">
                                    <router-link :to="`/student/jobs/${job.id}`" class="recommended-job-card-title">
                                        {{ job.title }}
                                    </router-link>
                                    <div class="recommended-job-card-meta">
                                        <span class="job-card-company">{{ job.client?.name || 'Unknown Company' }}</span>
                                        <span class="job-card-separator">•</span>
                                        <span class="job-card-budget">{{ formatMoney(job.budget) }} TND</span>
                                    </div>
                                </div>
                            </div>
                            <div class="recommended-job-card-footer">
                                <div class="recommended-job-card-tags">
                                    <span class="job-tag" v-if="job.category">
                                        {{ job.category }}
                                    </span>
                                    <span class="job-tag">
                                        {{ capitalizeWorkType(job.work_type || 'remote') }}
                                    </span>
                                </div>
                                <router-link :to="`/student/jobs/${job.id}`" class="job-card-view-link">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-recommended-jobs">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <p>No recommended jobs available</p>
                        <router-link to="/student/jobs" class="browse-jobs-btn">Browse All Jobs</router-link>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Sidebar Column -->
            <div class="dashboard-bottom-row">
                <!-- Right Sidebar Column -->
                <div class="dashboard-sidebar-column">
                    <!-- Quick Actions -->
                    <div class="sidebar-widget">
                        <div class="widget-header">
                            <h3 class="widget-title">Quick Actions</h3>
                        </div>
                        <div class="quick-actions-list">
                            <router-link to="/student/jobs" class="quick-action-item">
                                <div class="quick-action-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="M21 21l-4.35-4.35"/>
                                    </svg>
                                </div>
                                <div class="quick-action-content">
                                    <div class="quick-action-title">Browse Jobs</div>
                                    <div class="quick-action-subtitle">Find new opportunities</div>
                                </div>
                                <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </router-link>
                            <router-link to="/student/profile" class="quick-action-item">
                                <div class="quick-action-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div class="quick-action-content">
                                    <div class="quick-action-title">Edit Profile</div>
                                    <div class="quick-action-subtitle">Update your information</div>
                                </div>
                                <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </router-link>
                            <router-link to="/student/applications" class="quick-action-item">
                                <div class="quick-action-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                </div>
                                <div class="quick-action-content">
                                    <div class="quick-action-title">My Applications</div>
                                    <div class="quick-action-subtitle">View your applications</div>
                                </div>
                                <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </router-link>
                        </div>
                    </div>

                    <!-- Success Rate Card -->
                    <div class="sidebar-widget success-rate-widget">
                        <div class="widget-header">
                            <h3 class="widget-title">Success Rate</h3>
                        </div>
                        <div class="success-rate-content">
                            <div class="success-rate-circle">
                                <svg class="success-rate-svg" viewBox="0 0 120 120">
                                    <circle
                                        class="success-rate-bg"
                                        cx="60"
                                        cy="60"
                                        r="54"
                                        fill="none"
                                        stroke="#e5e7eb"
                                        stroke-width="12"
                                    />
                                    <circle
                                        class="success-rate-progress"
                                        cx="60"
                                        cy="60"
                                        r="54"
                                        fill="none"
                                        :stroke="getSuccessRateColor()"
                                        stroke-width="12"
                                        stroke-linecap="round"
                                        :stroke-dasharray="circumference"
                                        :stroke-dashoffset="circumference - (successRatePercentage / 100) * circumference"
                                        transform="rotate(-90 60 60)"
                                    />
                                </svg>
                                <div class="success-rate-text">
                                    <span class="success-rate-value">{{ getSuccessRate() }}%</span>
                                    <span class="success-rate-label">Accepted</span>
                                </div>
                            </div>
                            <div class="success-rate-stats">
                                <div class="success-rate-stat">
                                    <span class="stat-label">Total</span>
                                    <span class="stat-value">{{ stats?.total || 0 }}</span>
                                </div>
                                <div class="success-rate-stat">
                                    <span class="stat-label">Accepted</span>
                                    <span class="stat-value success">{{ stats?.accepted || 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="sidebar-widget tips-widget">
                        <div class="widget-header">
                            <h3 class="widget-title">Quick Tips</h3>
                        </div>
                        <div class="tips-list">
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 16v-4M12 8h.01"/>
                                    </svg>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Complete Profile</div>
                                    <div class="tip-text">A complete profile increases your chances of getting accepted</div>
                                </div>
                            </div>
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Write Cover Letters</div>
                                    <div class="tip-text">Personalized cover letters make your applications stand out</div>
                                </div>
                            </div>
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Apply Early</div>
                                    <div class="tip-text">Apply to jobs as soon as they're posted for better chances</div>
                                </div>
                            </div>
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Build Your Network</div>
                                    <div class="tip-text">Connect with professionals and expand your opportunities</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motivational Card - Full Width -->
            <div class="dashboard-motivational-row">
                <div class="sidebar-widget motivational-card-full">
                    <div class="motivational-content-wrapper">
                        <div class="motivational-icon-sidebar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <div class="motivational-message-center">
                            <transition name="message-fade" mode="out-in">
                                <p :key="currentMessageIndex" class="motivational-message-sidebar" v-html="formatMessageWithEmojis(encouragingMessage)"></p>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from '../shared/Layout.vue';
import api from '../../api.js';

export default {
    name: 'StudentDashboard',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            loadingJobs: false,
            user: JSON.parse(localStorage.getItem('user') || '{}'),
            stats: null,
            applications: null,
            recommendedJobs: [],
            alertMessage: null,
            alertType: 'success',
            animatedStats: {
                total: 0,
                pending: 0,
                accepted: 0,
                rejected: 0,
            },
            encouragingMessages: [
                "Every job application is a step closer to your dream career! 💼",
                "Your persistence will pay off. Keep applying! ✨",
                "Success comes to those who keep trying. You've got this! 🌟",
                "Each application is an opportunity. Make the most of it! 🚀",
                "Your future is bright. Keep pushing forward! ⭐",
                "Great things take time. Your perfect job is waiting! 💪",
                "Stay focused and positive. Opportunities are coming! 🎯",
                "You're building your future one application at a time! 🏆",
                "Believe in yourself. Your potential is limitless! 🌈",
                "Keep going! The right opportunity is just around the corner! 🔥",
                "Your hard work today will shape your tomorrow! 🌱",
                "Every 'no' brings you closer to your 'yes'! 🎪",
                "You're stronger than any challenge. Keep moving forward! 💎",
                "Dream big, work hard, and never give up! 🌠",
                "Your journey to success starts with a single step! 👣",
                "Turn your setbacks into comebacks! 🔄",
                "The best is yet to come. Stay motivated! 🎁",
                "You're capable of amazing things. Show the world! 🌍",
                "Progress, not perfection. Every step counts! 📈",
                "Your dedication will open doors you never imagined! 🚪",
                "Chase your dreams with passion and determination! 🏃",
                "You're writing your success story, one application at a time! 📖",
                "Stay positive, stay persistent, stay powerful! ⚡",
                "Your efforts today are investments in your future! 💰",
                "The only way to fail is to stop trying. Keep going! 🎲",
                "You have the power to create your own opportunities! 🎨",
                "Believe in your abilities and watch them grow! 🌳",
                "Success is a journey, and you're on the right path! 🗺️",
                "Your potential is unlimited. Keep exploring it! 🔍",
                "Every expert was once a beginner. You're learning! 📚",
            ],
            currentMessageIndex: 0,
            messageRotationInterval: null,
            mousePosition: { x: 0, y: 0 },
            orbPosition: { x: 0, y: 0 },
        };
    },
    computed: {
        userInitials() {
            if (!this.user?.name) return '?';
            return this.user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        },
        greetingMessage() {
            const hour = new Date().getHours();
            const firstName = this.user?.name?.split(' ')[0] || 'Student';
            
            if (hour < 12) {
                return `Good Morning, ${firstName}!`;
            } else if (hour < 17) {
                return `Good Afternoon, ${firstName}!`;
            } else {
                return `Good Evening, ${firstName}!`;
            }
        },
        encouragingMessage() {
            return this.encouragingMessages[this.currentMessageIndex];
        },
        successRatePercentage() {
            return this.getSuccessRate();
        },
        circumference() {
            return 2 * Math.PI * 54; // radius is 54
        },
    },
    async mounted() {
        await this.loadUserProfile();
        await this.loadData();
        await this.loadRecommendedJobs();
        if (this.stats) {
            this.animateStats();
        }
        this.startMessageRotation();
    },
    beforeUnmount() {
        if (this.messageRotationInterval) {
            clearInterval(this.messageRotationInterval);
        }
    },
    methods: {
        async loadUserProfile() {
            try {
                const response = await api.get('/student/profile');
                this.user = response.data;
                // Update localStorage with fresh user data
                localStorage.setItem('user', JSON.stringify(response.data));
            } catch (error) {
                console.error('Failed to load user profile:', error);
                // Keep using localStorage data as fallback
            }
        },
        async loadData() {
            try {
                const response = await api.get('/student/dashboard');
                this.stats = response.data.stats;
                this.applications = response.data.applications;
            } catch (error) {
                this.alertMessage = 'Failed to load dashboard data';
                this.alertType = 'error';
            } finally {
                this.loading = false;
            }
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        },
        formatMoney(amount) {
            const num = Number(amount) || 0;
            return num.toFixed(2);
        },
        formatMessageWithEmojis(message) {
            // Split message into text and emojis, wrap text in span for gradient
            const emojiRegex = /(\p{Emoji_Presentation}|\p{Emoji}\uFE0F?)/gu;
            const parts = message.split(emojiRegex);
            return parts.map(part => {
                if (emojiRegex.test(part)) {
                    return `<span class="emoji-text">${part}</span>`;
                } else if (part.trim()) {
                    return `<span class="gradient-text">${part}</span>`;
                }
                return part;
            }).join('');
        },
        capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        getStatusClass(status) {
            const classes = {
                pending: 'status-pending',
                accepted: 'status-accepted',
                rejected: 'status-rejected',
                withdrawn: 'status-withdrawn',
            };
            return classes[status] || 'status-withdrawn';
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
        getProgressPercentage(statType) {
            if (!this.stats || this.stats.total === 0) return 0;
            const value = this.animatedStats[statType] || 0;
            return Math.min((value / this.stats.total) * 100, 100);
        },
        getSuccessRate() {
            if (!this.stats || this.stats.total === 0) return 0;
            return Math.round((this.stats.accepted / this.stats.total) * 100);
        },
        getSuccessRateColor() {
            const rate = this.getSuccessRate();
            if (rate >= 70) return '#34A853'; // green
            if (rate >= 40) return '#1F6AE1'; // primary blue
            if (rate >= 20) return '#f59e0b'; // amber
            return '#EA4335'; // red
        },
        async loadRecommendedJobs() {
            this.loadingJobs = true;
            try {
                const response = await api.get('/student/jobs', {
                    params: { per_page: 5 }
                });
                
                console.log('Recommended jobs response:', response.data);
                
                // Handle different response structures - based on JobsBrowse.vue, response.data.jobs is the structure
                let jobs = [];
                
                if (response.data) {
                    if (response.data.jobs) {
                        // If jobs is a paginated object with data property
                        if (response.data.jobs.data && Array.isArray(response.data.jobs.data)) {
                            jobs = response.data.jobs.data;
                        } 
                        // If jobs is directly an array
                        else if (Array.isArray(response.data.jobs)) {
                            jobs = response.data.jobs;
                        }
                    } else if (response.data.data && Array.isArray(response.data.data)) {
                        jobs = response.data.data;
                    } else if (Array.isArray(response.data)) {
                        jobs = response.data;
                    }
                }
                
                this.recommendedJobs = jobs;
                console.log('Recommended jobs set to:', this.recommendedJobs, 'Length:', this.recommendedJobs.length);
            } catch (error) {
                console.error('Failed to load recommended jobs:', error);
                this.recommendedJobs = [];
            } finally {
                this.loadingJobs = false;
            }
        },
        truncateDescription(text, maxLength = 150) {
            if (!text) return '';
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength).trim() + '...';
        },
        getCompanyInitials(name) {
            if (!name) return '?';
            return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        },
        capitalizeWorkType(type) {
            if (!type) return 'Remote';
            return type.charAt(0).toUpperCase() + type.slice(1);
        },
        animateStats() {
            const duration = 1500;
            const steps = 60;
            const interval = duration / steps;
            
            const animateValue = (start, end, callback) => {
                const increment = (end - start) / steps;
                let current = start;
                let step = 0;
                
                const timer = setInterval(() => {
                    step++;
                    current = start + (increment * step);
                    if (step >= steps) {
                        current = end;
                        clearInterval(timer);
                    }
                    callback(Math.floor(current));
                }, interval);
            };
            
            animateValue(0, this.stats.total || 0, (val) => { this.animatedStats.total = val; });
            animateValue(0, this.stats.pending || 0, (val) => { this.animatedStats.pending = val; });
            animateValue(0, this.stats.accepted || 0, (val) => { this.animatedStats.accepted = val; });
            animateValue(0, this.stats.rejected || 0, (val) => { this.animatedStats.rejected = val; });
        },
        startMessageRotation() {
            // Rotate messages every 5 seconds
            this.messageRotationInterval = setInterval(() => {
                this.currentMessageIndex = (this.currentMessageIndex + 1) % this.encouragingMessages.length;
            }, 5000);
        },
    },
    watch: {
        stats: {
            handler() {
                if (this.stats) {
                    this.$nextTick(() => {
                        this.animateStats();
                    });
                }
            },
            deep: true,
        },
    },
};
</script>

<style scoped>
.dashboard-wrapper {
    display: grid;
    grid-template-columns: 1fr 340px;
    grid-template-rows: 1fr auto;
    gap: 2rem;
    min-height: calc(100vh - 70px);
    padding: 2rem;
    max-width: 1600px;
    margin: 0 auto;
    background: transparent;
    position: relative;
    overflow: hidden;
    align-items: stretch;
}

.dashboard-bottom-row {
    grid-column: 2;
    grid-row: 1;
    display: flex;
    flex-direction: column;
    align-self: stretch;
    min-height: 0;
    height: 100%;
}

.dashboard-sidebar-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    align-items: stretch;
    justify-content: flex-start;
    flex: 1;
    min-height: 0;
}

.dashboard-motivational-row {
    grid-column: 1 / -1;
    grid-row: 2;
}

.motivational-card-full {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.95) 100%);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-radius: 24px;
    padding: 3rem 4rem;
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.08),
        0 8px 24px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    min-height: 180px;
}

.motivational-card-full::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(
        circle at 30% 50%,
        rgba(31, 106, 225, 0.08) 0%,
        transparent 50%
    ),
    radial-gradient(
        circle at 70% 50%,
        rgba(59, 130, 246, 0.08) 0%,
        transparent 50%
    ),
    radial-gradient(
        circle at 50% 80%,
        rgba(236, 72, 153, 0.06) 0%,
        transparent 50%
    );
    animation: motivational-bg-float 15s ease-in-out infinite;
    pointer-events: none;
    z-index: 0;
}

@keyframes motivational-bg-float {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 1;
    }
    33% {
        transform: translate(5%, -5%) scale(1.05);
        opacity: 0.9;
    }
    66% {
        transform: translate(-5%, 5%) scale(0.95);
        opacity: 0.95;
    }
}

.motivational-card-full .motivational-content-wrapper {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 2rem;
    width: 100%;
    position: relative;
    z-index: 2;
}

.motivational-message-center {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    flex: 1;
}

.motivational-card-full .motivational-icon-sidebar {
    width: 72px;
    height: 72px;
    border-radius: 18px;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    box-shadow: 
        0 8px 24px rgba(31, 106, 225, 0.3),
        0 4px 12px rgba(31, 106, 225, 0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.motivational-card-full:hover .motivational-icon-sidebar {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 
        0 12px 32px rgba(31, 106, 225, 0.4),
        0 6px 16px rgba(31, 106, 225, 0.3);
}

.motivational-card-full .motivational-icon-sidebar svg {
    width: 32px;
    height: 32px;
    stroke-width: 2.5;
}

.motivational-card-full .motivational-message-sidebar {
    font-size: 2rem;
    font-weight: 800;
    margin: 0;
    letter-spacing: -0.03em;
    line-height: 1.2;
    height: auto;
    min-height: auto;
    max-height: none;
    -webkit-line-clamp: none;
    display: block;
    overflow: visible;
    text-align: center;
}

.motivational-card-full .motivational-message-sidebar :deep(.gradient-text) {
    background: linear-gradient(135deg, #000000 0%, #374151 50%, #1F6AE1 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.motivational-card-full .motivational-message-sidebar :deep(.emoji-text) {
    -webkit-text-fill-color: initial;
    background: none;
    -webkit-background-clip: initial;
    background-clip: initial;
    display: inline;
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
    background: radial-gradient(circle, rgba(31, 106, 225, 0.4) 0%, rgba(31, 106, 225, 0.2) 50%, rgba(31, 106, 225, 0) 80%);
    top: 10%;
    left: -10%;
    animation: float-dashboard-orb-1 40s ease-in-out infinite;
}

.dashboard-orb.orb-2 {
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.4) 0%, rgba(37, 99, 235, 0.2) 50%, rgba(37, 99, 235, 0) 80%);
    top: 30%;
    right: -10%;
    animation: float-dashboard-orb-2 45s ease-in-out infinite;
}

.dashboard-orb.orb-3 {
    width: 480px;
    height: 480px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.4) 0%, rgba(59, 130, 246, 0.2) 50%, rgba(59, 130, 246, 0) 80%);
    bottom: 10%;
    left: -5%;
    animation: float-dashboard-orb-3 50s ease-in-out infinite;
}

.dashboard-orb.orb-4 {
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.35) 0%, rgba(59, 130, 246, 0.18) 50%, rgba(59, 130, 246, 0) 80%);
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

.dashboard-main,
.dashboard-sidebar {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
}

.dashboard-main {
    gap: 2rem;
    grid-column: 1;
    grid-row: 1;
    align-self: stretch;
    min-height: 0;
}

/* Dashboard Header */
.dashboard-header-wrapper {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.header-picture-container {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-radius: 24px;
    padding: 0;
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.06),
        0 2px 8px rgba(0, 0, 0, 0.03),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 150px;
    height: 150px;
}

.header-picture-container::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(31, 106, 225, 0.06) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.header-greetings-container {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-radius: 24px;
    padding: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.06),
        0 2px 8px rgba(0, 0, 0, 0.03),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex: 1;
}

.header-greetings-container::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(31, 106, 225, 0.06) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
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
    position: relative;
    z-index: 1;
}

.header-subtitle {
    font-size: 1.0625rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
    letter-spacing: -0.01em;
    position: relative;
    z-index: 1;
}

.header-avatar {
    width: 100%;
    height: 100%;
    border-radius: 24px;
    overflow: hidden;
    flex-shrink: 0;
    border: none;
    box-shadow: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1;
}

.header-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.header-avatar.placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: #FFFFFF;
    font-size: 3rem;
    font-weight: 700;
}

.header-avatar::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 24px;
    background: linear-gradient(135deg, #1F6AE1, #2563EB, #3B82F6);
    background-size: 200% 200%;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.header-avatar:hover {
    transform: scale(1.02);
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
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    background-size: 200% 200%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 2.25rem;
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


@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(31, 106, 225, 0.05) 0%, transparent 70%);
    border-radius: 50%;
}

.banner-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
    width: 100%;
    gap: 2rem;
}

.banner-user-section {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex: 1;
    min-width: 0;
}

.banner-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 3px solid white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.banner-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.banner-avatar.placeholder {
    background: linear-gradient(135deg, #1F6AE1 0%, #0B3C8A 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
}

.banner-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: #111827;
    cursor: default;
}

.banner-subtitle {
    font-size: 1.125rem;
    margin: 1rem 0 0 0;
    color: #6b7280;
    min-height: 1.5rem;
}

/* Fade transition for encouraging messages */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease, transform 0.3s ease;
}

.fade-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

.banner-illustration {
    width: 150px;
    height: 150px;
    opacity: 1;
    animation: float 6s ease-in-out infinite;
    position: relative;
    flex-shrink: 0;
    cursor: pointer;
}

.illustration-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(52, 168, 83, 0.2) 0%, rgba(52, 168, 83, 0.08) 40%, transparent 70%);
    border-radius: 50%;
    animation: pulse-glow 3s ease-in-out infinite;
    pointer-events: none;
    filter: blur(30px);
    -webkit-filter: blur(30px);
}

/* Morphing Blob Container */
.blob-container {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto;
    z-index: 3;
}

.morphing-blob {
    width: 100%;
    height: 100%;
    transform: translate(25px, 25px) scale(0.75);
    filter: blur(1px);
}

.blob-path {
    transform-origin: center;
    animation: blobRotate 20s linear infinite;
}

@keyframes blobRotate {
    0% {
        transform: rotate(0deg) scale(1);
    }
    50% {
        transform: rotate(180deg) scale(1.1);
    }
    100% {
        transform: rotate(360deg) scale(1);
    }
}

.blob-inner-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    background: radial-gradient(circle, rgba(52, 168, 83, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    animation: innerPulse 4s ease-in-out infinite;
    filter: blur(20px);
    pointer-events: none;
}

@keyframes innerPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.6;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.3);
        opacity: 0.9;
    }
}

/* Flowing Particles */
.flowing-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.flow-particle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    box-shadow: 
        0 0 10px rgba(52, 168, 83, 0.8),
        0 0 20px rgba(52, 168, 83, 0.6);
    animation: flowPath 6s ease-in-out infinite;
}

.particle-1 {
    animation-delay: 0s;
    animation-duration: 6s;
}

.particle-2 {
    animation-delay: -0.75s;
    animation-duration: 7s;
}

.particle-3 {
    animation-delay: -1.5s;
    animation-duration: 6.5s;
}

.particle-4 {
    animation-delay: -2.25s;
    animation-duration: 7.5s;
}

.particle-5 {
    animation-delay: -3s;
    animation-duration: 6.8s;
}

.particle-6 {
    animation-delay: -3.75s;
    animation-duration: 7.2s;
}

.particle-7 {
    animation-delay: -4.5s;
    animation-duration: 6.3s;
}

.particle-8 {
    animation-delay: -5.25s;
    animation-duration: 7.8s;
}

@keyframes flowPath {
    0% {
        transform: translate(75px, 75px) rotate(0deg) translateX(45px) rotate(0deg);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    50% {
        transform: translate(75px, 75px) rotate(180deg) translateX(45px) rotate(-180deg);
        opacity: 0.8;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translate(75px, 75px) rotate(360deg) translateX(45px) rotate(-360deg);
        opacity: 0;
    }
}


.orb-smile {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 70px;
    height: 70px;
    margin-top: 28px;
    pointer-events: none;
}

.smile-path {
    stroke: rgba(5, 150, 105, 0.9);
    stroke-width: 5;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

/* Particle Canvas */
.particle-canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

/* Floating Geometric Shapes */
.floating-shape {
    position: absolute;
    pointer-events: none;
    z-index: 2;
    opacity: 0.4;
}

.shape-1 {
    width: 20px;
    height: 20px;
    background: rgba(52, 168, 83, 0.3);
    border-radius: 50%;
    top: 10%;
    left: 10%;
    animation: floatShape1 8s ease-in-out infinite;
    box-shadow: 0 0 20px rgba(52, 168, 83, 0.4);
}

.shape-2 {
    width: 0;
    height: 0;
    border-left: 12px solid transparent;
    border-right: 12px solid transparent;
    border-bottom: 20px solid rgba(31, 106, 225, 0.3);
    top: 20%;
    right: 15%;
    animation: floatShape2 10s ease-in-out infinite;
    filter: drop-shadow(0 0 15px rgba(31, 106, 225, 0.4));
}

.shape-3 {
    width: 16px;
    height: 16px;
    background: rgba(59, 130, 246, 0.3);
    transform: rotate(45deg);
    bottom: 15%;
    left: 12%;
    animation: floatShape3 12s ease-in-out infinite;
    box-shadow: 0 0 18px rgba(59, 130, 246, 0.4);
}

.shape-4 {
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-bottom: 18px solid rgba(236, 72, 153, 0.3);
    transform: rotate(180deg);
    bottom: 20%;
    right: 10%;
    animation: floatShape4 9s ease-in-out infinite;
    filter: drop-shadow(0 0 15px rgba(236, 72, 153, 0.4));
}

@keyframes floatShape1 {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    25% {
        transform: translate(15px, -20px) scale(1.1);
    }
    50% {
        transform: translate(-10px, 15px) scale(0.9);
    }
    75% {
        transform: translate(20px, 10px) scale(1.05);
    }
}

@keyframes floatShape2 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    33% {
        transform: translate(-20px, 25px) rotate(120deg);
    }
    66% {
        transform: translate(25px, -15px) rotate(240deg);
    }
}

@keyframes floatShape3 {
    0%, 100% {
        transform: translate(0, 0) rotate(45deg) scale(1);
    }
    50% {
        transform: translate(20px, -25px) rotate(225deg) scale(1.2);
    }
}

@keyframes floatShape4 {
    0%, 100% {
        transform: translate(0, 0) rotate(180deg);
    }
    25% {
        transform: translate(-15px, -20px) rotate(270deg);
    }
    50% {
        transform: translate(20px, 15px) rotate(360deg);
    }
    75% {
        transform: translate(-10px, 25px) rotate(450deg);
    }
}

/* Graduation Cap */
.graduation-cap {
    position: absolute;
    top: -8px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
    pointer-events: none;
}

.cap-top {
    width: 50px;
    height: 20px;
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border-radius: 50% 50% 0 0;
    position: relative;
    box-shadow: 
        0 4px 8px rgba(0, 0, 0, 0.3),
        inset 0 2px 4px rgba(255, 255, 255, 0.1);
}

.cap-top::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 15px;
    background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    border-radius: 50% 50% 0 0;
}

.cap-tassel {
    position: absolute;
    top: 10px;
    right: -8px;
    width: 3px;
    height: 25px;
    background: linear-gradient(180deg, #fbbf24 0%, #f59e0b 100%);
    border-radius: 2px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.cap-tassel::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 50%;
    transform: translateX(-50%);
    width: 8px;
    height: 8px;
    background: #fbbf24;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Cheeks */
.orb-cheek {
    position: absolute;
    width: 16px;
    height: 12px;
    background: radial-gradient(circle, rgba(251, 113, 133, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    top: 58%;
    pointer-events: none;
}

.orb-cheek-left {
    left: 20%;
}

.orb-cheek-right {
    right: 20%;
}

/* Sparkles */
.sparkle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    pointer-events: none;
    animation: sparkleFloat 3s ease-in-out infinite;
    box-shadow: 
        0 0 8px rgba(255, 255, 255, 0.8),
        0 0 12px rgba(52, 168, 83, 0.6);
}

.sparkle::before,
.sparkle::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 2px;
    background: rgba(255, 255, 255, 0.8);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-radius: 2px;
    box-shadow: 0 0 4px rgba(255, 255, 255, 0.6);
}

.sparkle::after {
    transform: translate(-50%, -50%) rotate(90deg);
}

.sparkle-1 {
    top: 15%;
    left: 20%;
    animation-delay: 0s;
}

.sparkle-2 {
    top: 25%;
    right: 15%;
    animation-delay: 0.5s;
}

.sparkle-3 {
    bottom: 20%;
    left: 15%;
    animation-delay: 1s;
}

.sparkle-4 {
    bottom: 15%;
    right: 25%;
    animation-delay: 1.5s;
}

@keyframes sparkleFloat {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0.6;
    }
    50% {
        transform: translate(8px, -8px) scale(1.2);
        opacity: 1;
    }
}

.orb-eyes {
    position: absolute;
    top: 45%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    gap: 24px;
    align-items: center;
}

.orb-eye {
    width: 32px;
    height: 40px;
    background: rgba(255, 255, 255, 1);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    border-radius: 50%;
    position: relative;
    overflow: hidden;
    box-shadow: 
        inset 0 2px 8px rgba(0, 0, 0, 0.2),
        0 3px 10px rgba(0, 0, 0, 0.15),
        0 0 0 2px rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(255, 255, 255, 0.9);
}

.eye-pupil {
    width: 20px;
    height: 20px;
    background: radial-gradient(circle at 25% 25%, #4b5563 0%, #1f2937 60%, #000000 100%);
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transition: transform 0.15s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    box-shadow: 
        0 3px 6px rgba(0, 0, 0, 0.4),
        inset -3px -3px 6px rgba(0, 0, 0, 0.5),
        inset 2px 2px 3px rgba(255, 255, 255, 0.2);
}

@keyframes pulse-glow {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 0.6;
    }
}


@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stats Section */
.stats-section {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-radius: 24px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.06),
        0 2px 8px rgba(0, 0, 0, 0.03),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
}

.section-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.375rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0 0 1.5rem 0;
    letter-spacing: -0.01em;
}

.time-selector {
    padding: 0.5rem 1rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    font-size: 0.875rem;
    color: #111827;
    cursor: pointer;
    transition: all 0.2s;
}

.time-selector:hover {
    border-color: rgba(0, 0, 0, 0.2);
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
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

.stat-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid rgba(229, 231, 235, 0.5);
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    position: relative;
}

.stat-card:hover {
    border-color: rgba(31, 106, 225, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.stat-card:hover .stat-icon-wrapper {
    transform: scale(1.05);
}

.stat-icon-wrapper.success-icon {
    background: linear-gradient(135deg, #34A853 0%, #2d8f47 100%);
}

.stat-icon-wrapper.pulse-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-card[data-stat="rejected"] .stat-icon-wrapper {
    background: linear-gradient(135deg, #EA4335 0%, #d32f2f 100%) !important;
}

.stat-icon-wrapper svg {
    width: 24px;
    height: 24px;
}

.stat-info {
    flex: 1;
    min-width: 0;
}

.stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
    letter-spacing: -0.02em;
}

/* Applications Section */
.applications-section {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-radius: 24px;
    padding: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.06),
        0 2px 8px rgba(0, 0, 0, 0.03),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
    position: relative;
    overflow: hidden;
}

/* Motivational Card */
.motivational-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 28px;
    padding: 3rem 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 
        0 10px 40px rgba(31, 106, 225, 0.12),
        0 2px 8px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 2rem;
    transition: all 0.3s ease;
    min-height: 180px;
}

.motivational-card:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 12px 48px rgba(31, 106, 225, 0.18),
        0 4px 12px rgba(0, 0, 0, 0.06),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
}


@keyframes gradientShift {
    0%, 100% {
        opacity: 0.6;
    }
    50% {
        opacity: 0.8;
    }
}

.motivational-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(31, 106, 225, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(52, 168, 83, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.02) 0%, transparent 50%);
    pointer-events: none;
    opacity: 0.5;
}

.motivational-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    box-shadow: 
        0 8px 24px rgba(31, 106, 225, 0.35),
        0 2px 8px rgba(31, 106, 225, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.motivational-card:hover .motivational-icon {
    transform: scale(1.05) rotate(5deg);
    box-shadow: 
        0 12px 32px rgba(31, 106, 225, 0.4),
        0 4px 12px rgba(31, 106, 225, 0.25),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
}

.motivational-icon svg {
    width: 40px;
    height: 40px;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.motivational-content {
    flex: 1;
    position: relative;
    z-index: 2;
}

.motivational-title {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #000000 0%, #1F6AE1 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 1rem 0;
    letter-spacing: -0.02em;
    line-height: 1.2;
}

.motivational-message {
    font-size: 1.125rem;
    color: #374151;
    margin: 0;
    line-height: 1.7;
    font-weight: 500;
    letter-spacing: -0.01em;
    display: block;
    min-height: 1.9em;
}

/* Message Transition Animation */
.message-fade-enter-active {
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.message-fade-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.message-fade-enter-from {
    opacity: 0;
    transform: translateY(10px) scale(0.95);
}

.message-fade-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.message-fade-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.message-fade-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
}

/* Motivational Card in Sidebar */
.motivational-card-sidebar {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 1.5rem;
    height: 320px;
    min-height: 320px;
    max-height: 320px;
    box-sizing: border-box;
    flex-shrink: 0;
}


.motivational-content-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1.25rem;
    width: 100%;
    flex: 1;
    min-height: 0;
}

.motivational-icon-sidebar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(31, 106, 225, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1F6AE1;
    flex-shrink: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.motivational-card-sidebar:hover .motivational-icon-sidebar {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    transform: scale(1.1);
}

.motivational-icon-sidebar svg {
    width: 24px;
    height: 24px;
}

.motivational-content-sidebar {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    gap: 0.75rem;
    flex: 1;
    min-height: 0;
}

.motivational-title-sidebar {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    letter-spacing: -0.02em;
    line-height: 1.3;
    flex-shrink: 0;
    height: 1.3em;
}

.motivational-message-sidebar {
    font-size: 0.9375rem;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
    font-weight: 500;
    letter-spacing: -0.01em;
    height: 3em;
    min-height: 3em;
    max-height: 3em;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    flex-shrink: 0;
    width: 100%;
    text-align: center;
}

.motivational-decoration {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
    z-index: 1;
}

.motivational-sparkle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: radial-gradient(circle, rgba(31, 106, 225, 0.8) 0%, rgba(31, 106, 225, 0.4) 50%, transparent 100%);
    border-radius: 50%;
    animation: sparkleFloat 4s ease-in-out infinite;
    box-shadow: 
        0 0 12px rgba(31, 106, 225, 0.8),
        0 0 20px rgba(59, 130, 246, 0.6);
}

.motivational-sparkle.sparkle-1 {
    top: 15%;
    left: 10%;
    animation-delay: 0s;
    width: 10px;
    height: 10px;
}

.motivational-sparkle.sparkle-2 {
    top: 25%;
    right: 15%;
    animation-delay: 0.8s;
    width: 6px;
    height: 6px;
}

.motivational-sparkle.sparkle-3 {
    bottom: 20%;
    left: 75%;
    animation-delay: 1.6s;
    width: 8px;
    height: 8px;
}

.motivational-sparkle.sparkle-4 {
    top: 50%;
    left: 5%;
    animation-delay: 2.4s;
    width: 7px;
    height: 7px;
}

.motivational-sparkle.sparkle-5 {
    bottom: 15%;
    right: 10%;
    animation-delay: 3.2s;
    width: 9px;
    height: 9px;
}

@keyframes sparkleFloat {
    0%, 100% {
        transform: translate(0, 0) scale(1) rotate(0deg);
        opacity: 0.5;
    }
    25% {
        transform: translate(10px, -10px) scale(1.2) rotate(90deg);
        opacity: 1;
    }
    50% {
        transform: translate(-5px, -15px) scale(1.1) rotate(180deg);
        opacity: 0.8;
    }
    75% {
        transform: translate(-10px, 5px) scale(1.15) rotate(270deg);
        opacity: 0.9;
    }
}


.section-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 1;
}

.view-all-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.view-all-link:hover {
    gap: 0.75rem;
    filter: brightness(1.1);
}

.view-all-link svg {
    width: 16px;
    height: 16px;
}

.applications-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
    margin-top: 1.25rem;
    position: relative;
    z-index: 1;
}

.application-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(249, 250, 251, 0.8) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.8);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: 
        0 4px 12px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    position: relative;
    overflow: hidden;
}

.application-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #1F6AE1, #2563EB, #3B82F6);
    background-size: 200% 100%;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}

.application-card:hover::before {
    transform: scaleX(1);
    animation: gradientMove 2s ease infinite;
}

.application-card:hover {
    border-color: rgba(31, 106, 225, 0.2);
    box-shadow: 
        0 12px 32px rgba(31, 106, 225, 0.15),
        0 4px 12px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 1);
    transform: translateY(-4px);
    background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(249, 250, 251, 0.95) 100%);
}

.application-card-header {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.application-title-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.application-job-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    text-decoration: none;
    flex: 1;
    line-height: 1.4;
    transition: color 0.2s ease;
}

.application-job-title:hover {
    color: #1F6AE1;
}

.application-company {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.application-company svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.application-card-body {
    padding-top: 0.5rem;
}

.application-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.application-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.application-meta-item svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    color: #9ca3af;
}

.application-card-footer {
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
    margin-top: auto;
}

.view-details-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.75rem 1.25rem;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    color: #374151;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.view-details-btn:hover {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    gap: 0.75rem;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(31, 106, 225, 0.3);
    border-color: transparent;
}

.view-details-btn svg {
    width: 16px;
    height: 16px;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
    color: #6b7280;
    flex: 1;
    min-height: 400px;
}

.empty-state-icon {
    width: 64px;
    height: 64px;
    color: #d1d5db;
    margin-bottom: 1.5rem;
}

.empty-state-icon svg {
    width: 100%;
    height: 100%;
}

.empty-state p {
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

.browse-jobs-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.browse-jobs-btn:hover {
    background: linear-gradient(135deg, #0B3C8A 0%, #1F6AE1 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(31, 106, 225, 0.4);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-accepted {
    background: #d1fae5;
    color: #065f46;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.status-withdrawn {
    background: #f1f5f9;
    color: #475569;
}

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #6b7280;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-btn:hover {
    background: #f9fafb;
    color: #111827;
}

.action-btn svg {
    width: 20px;
    height: 20px;
}

/* Right Sidebar */
.dashboard-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    align-items: stretch;
}

.dashboard-sidebar > .sidebar-widget:not(.motivational-card-sidebar) {
    flex: 0 0 auto;
}

.dashboard-sidebar > .motivational-card-sidebar {
    flex: 0 0 auto;
    margin-top: auto;
}


.sidebar-widget {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-radius: 24px;
    padding: 1.75rem;
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.06),
        0 2px 8px rgba(0, 0, 0, 0.03),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-widget:hover {
    box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.08),
        0 4px 12px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
}



.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.view-all-link-small {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    color: #1F6AE1;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.view-all-link-small:hover {
    color: #0B3C8A;
    gap: 0.5rem;
}

.view-all-link-small svg {
    width: 14px;
    height: 14px;
}

.widget-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    letter-spacing: -0.02em;
}


.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1F6AE1 0%, #0B3C8A 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.activity-icon svg {
    width: 18px;
    height: 18px;
}

.activity-content {
    flex: 1;
}

.activity-text {
    font-size: 0.875rem;
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.25rem;
}

.activity-time {
    font-size: 0.75rem;
    color: #6b7280;
}

.empty-state,
.empty-activity {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
    font-size: 0.9375rem;
}

/* Success Rate Widget */
.success-rate-widget {
    background: linear-gradient(135deg, rgba(249, 250, 251, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.8);
}

.success-rate-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.success-rate-circle {
    position: relative;
    width: 140px;
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.success-rate-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.success-rate-bg {
    opacity: 0.2;
}

.success-rate-progress {
    transition: stroke-dashoffset 1s ease, stroke 0.3s ease;
}

.success-rate-text {
    position: absolute;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.success-rate-value {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.success-rate-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
    margin-top: 0.25rem;
}

.success-rate-stats {
    display: flex;
    gap: 2rem;
    width: 100%;
    justify-content: center;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.success-rate-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.success-rate-stat .stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}

.success-rate-stat .stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
}

.success-rate-stat .stat-value.success {
    color: #34A853;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    background: linear-gradient(135deg, rgba(249, 250, 251, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    color: #374151;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    width: 100%;
    min-width: 0;
}

.quick-action-btn span {
    flex: 1;
    min-width: 0;
    white-space: normal;
    line-height: 1.4;
}

.quick-action-btn:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(249, 250, 251, 0.95) 100%);
    border-color: rgba(31, 106, 225, 0.3);
    color: #1F6AE1;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.15);
}

.quick-actions-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quick-action-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, rgba(249, 250, 251, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.quick-action-item:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(249, 250, 251, 0.95) 100%);
    border-color: rgba(31, 106, 225, 0.3);
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.15);
}

.quick-action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(31, 106, 225, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1F6AE1;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.quick-action-item:hover .quick-action-icon {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    transform: scale(1.1);
}

.quick-action-icon svg {
    width: 20px;
    height: 20px;
}

.quick-action-content {
    flex: 1;
    min-width: 0;
}

.quick-action-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
    transition: color 0.3s ease;
}

.quick-action-item:hover .quick-action-title {
    color: #1F6AE1;
}

.quick-action-subtitle {
    font-size: 0.8125rem;
    color: #6b7280;
    transition: color 0.3s ease;
}

.quick-action-item:hover .quick-action-subtitle {
    color: #1F6AE1;
}

.quick-action-arrow {
    width: 20px;
    height: 20px;
    color: #9ca3af;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.quick-action-item:hover .quick-action-arrow {
    color: #1F6AE1;
    transform: translateX(4px);
}

.quick-action-btn svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

/* Tips Widget */
.tips-widget {
    display: flex;
    flex-direction: column;
    padding: 2.4rem;
}

.tips-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.875rem;
    background: linear-gradient(135deg, rgba(249, 250, 251, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.tip-item:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(249, 250, 251, 0.95) 100%);
    border-color: rgba(31, 106, 225, 0.3);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.15);
    transform: translateX(2px);
}

.tip-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(31, 106, 225, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1F6AE1;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.tip-item:hover .tip-icon {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    transform: scale(1.1);
}

.tip-icon svg {
    width: 18px;
    height: 18px;
}

.tip-content {
    flex: 1;
    min-width: 0;
}

.tip-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
    transition: color 0.3s ease;
}

.tip-item:hover .tip-title {
    color: #1F6AE1;
}

.tip-text {
    font-size: 0.75rem;
    color: #6b7280;
    line-height: 1.5;
    transition: color 0.3s ease;
}

.tip-item:hover .tip-text {
    color: #4b5563;
}

/* Recommended Jobs Section */
.recommended-jobs-section {
    margin-bottom: 0;
}

.loading-jobs {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem;
    gap: 1rem;
    color: #6b7280;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    border-top-color: #1F6AE1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.recommended-jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.25rem;
    position: relative;
    z-index: 1;
}

.recommended-jobs-section {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-radius: 24px;
    padding: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.06),
        0 2px 8px rgba(0, 0, 0, 0.03),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
    position: relative;
    overflow: hidden;
}


.recommended-job-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px) saturate(180%);
    -webkit-backdrop-filter: blur(10px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    padding: 1.75rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    box-shadow: 
        0 4px 16px rgba(0, 0, 0, 0.04),
        0 1px 4px rgba(0, 0, 0, 0.02),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    position: relative;
    overflow: hidden;
}

.recommended-job-card:hover {
    box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.08),
        0 4px 16px rgba(0, 0, 0, 0.06),
        inset 0 1px 0 rgba(255, 255, 255, 1);
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 1);
}

.recommended-job-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.recommended-job-company {
    display: flex;
    gap: 1rem;
    flex: 1;
    min-width: 0;
}

.company-avatar {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.9375rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.25);
}

.company-info {
    flex: 1;
    min-width: 0;
}

.recommended-job-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.375rem 0;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    letter-spacing: -0.01em;
}

.recommended-job-company-name {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.recommended-job-budget {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    flex-shrink: 0;
}

.budget-amount {
    font-size: 1.25rem;
    font-weight: 800;
    background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
}

.budget-label {
    font-size: 0.75rem;
    color: #9ca3af;
}

.recommended-job-description {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.7;
    margin: 0;
    flex: 1;
    font-weight: 400;
}

.recommended-job-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.recommended-job-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.job-tag {
    padding: 0.5rem 0.875rem;
    background: linear-gradient(135deg, rgba(31, 106, 225, 0.12) 0%, rgba(59, 130, 246, 0.08) 100%);
    color: #1F6AE1;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 600;
    border: 1px solid rgba(31, 106, 225, 0.15);
}

/* Recommended Job Card Styles (Main Content) */
.recommended-job-card-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.company-avatar-large {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.25);
}

.recommended-job-card-content {
    flex: 1;
    min-width: 0;
}

.recommended-job-card-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.5rem 0;
    line-height: 1.4;
    display: block;
    text-decoration: none;
    transition: color 0.2s ease;
    letter-spacing: -0.01em;
}

.recommended-job-card-title:hover {
    color: #1F6AE1;
}

.recommended-job-card-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.job-card-company {
    font-weight: 600;
}

.job-card-separator {
    color: #d1d5db;
}

.job-card-budget {
    color: #FF6B35;
    font-weight: 600;
}

.recommended-job-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 0, 0, 0.06);
}

.recommended-job-card-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    flex: 1;
}

.job-card-view-link {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(31, 106, 225, 0.1) 0%, rgba(59, 130, 246, 0.08) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1F6AE1;
    text-decoration: none;
    transition: all 0.3s ease;
    flex-shrink: 0;
    border: 1px solid rgba(31, 106, 225, 0.15);
}

.job-card-view-link:hover {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    transform: translateX(2px);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.job-card-view-link svg {
    width: 18px;
    height: 18px;
}

.recommended-job-apply-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.recommended-job-apply-btn:hover {
    background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(31, 106, 225, 0.4);
}

.recommended-job-apply-btn svg {
    width: 16px;
    height: 16px;
}

.empty-recommended-jobs {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
    color: #6b7280;
    background: rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    border: 1px dashed rgba(0, 0, 0, 0.1);
}

.empty-recommended-jobs svg {
    width: 64px;
    height: 64px;
    color: #d1d5db;
    margin-bottom: 1.5rem;
}

.empty-recommended-jobs p {
    font-size: 1rem;
    margin-bottom: 1.5rem;
    color: #9ca3af;
}

.browse-jobs-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.browse-jobs-link:hover {
    background: linear-gradient(135deg, #0B3C8A 0%, #1F6AE1 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(31, 106, 225, 0.4);
}

/* Recommended Jobs Sidebar Styles */
.loading-jobs-sidebar {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    gap: 0.75rem;
    color: #6b7280;
}

.loading-spinner-small {
    width: 32px;
    height: 32px;
    border: 3px solid #e5e7eb;
    border-top-color: #1F6AE1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-jobs-sidebar p {
    font-size: 0.875rem;
    margin: 0;
}

.recommended-jobs-sidebar {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.recommended-job-item-sidebar {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.recommended-job-item-sidebar:hover {
    border-color: rgba(31, 106, 225, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.recommended-job-item-header {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.company-avatar-sidebar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(31, 106, 225, 0.25);
}

.recommended-job-item-content {
    flex: 1;
    min-width: 0;
}

.recommended-job-item-title {
    font-size: 0.9375rem;
    font-weight: 700;
    color: #111827;
    text-decoration: none;
    display: block;
    margin: 0 0 0.375rem 0;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    transition: color 0.2s ease;
}

.recommended-job-item-title:hover {
    color: #1F6AE1;
}

.recommended-job-item-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    font-size: 0.75rem;
    color: #6b7280;
}

.job-item-company {
    font-weight: 500;
}

.job-item-separator {
    color: #d1d5db;
}

.job-item-budget {
    font-weight: 600;
    color: #1F6AE1;
}

.recommended-job-item-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e7eb;
}

.recommended-job-item-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
    flex: 1;
}

.job-tag-sidebar {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.6875rem;
    font-weight: 600;
    background: rgba(31, 106, 225, 0.1);
    color: #1F6AE1;
    white-space: nowrap;
}

.job-item-view-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: rgba(31, 106, 225, 0.1);
    color: #1F6AE1;
    text-decoration: none;
    transition: all 0.2s ease;
    flex-shrink: 0;
    margin-left: 0.5rem;
}

.job-item-view-link:hover {
    background: rgba(31, 106, 225, 0.2);
    transform: translateX(2px);
}

.job-item-view-link svg {
    width: 16px;
    height: 16px;
}

.empty-recommended-jobs-sidebar {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    text-align: center;
    color: #6b7280;
}

.empty-recommended-jobs-sidebar svg {
    width: 48px;
    height: 48px;
    color: #d1d5db;
    margin-bottom: 0.75rem;
}

.empty-recommended-jobs-sidebar p {
    font-size: 0.875rem;
    margin: 0 0 1rem 0;
    color: #9ca3af;
}

.browse-jobs-link-sidebar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.8125rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(31, 106, 225, 0.3);
}

.browse-jobs-link-sidebar:hover {
    background: linear-gradient(135deg, #0B3C8A 0%, #1F6AE1 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.4);
}

.loading {
    display: none;
}

@media (max-width: 1200px) {
    .recommended-jobs-grid {
        grid-template-columns: 1fr;
    }
}

.loading::before {
    content: '';
    width: 40px;
    height: 40px;
    border: 3px solid #e5e7eb;
    border-top-color: #111827;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 1400px) {
    .dashboard-wrapper {
        grid-template-columns: 1fr 320px;
        align-items: stretch;
    }
}

@media (max-width: 1280px) {
    .dashboard-wrapper {
        grid-template-columns: 1fr 280px;
        align-items: stretch;
    }
    
    .dashboard-bottom-row {
        align-self: stretch;
    }
    
    .dashboard-main {
        align-self: stretch;
    }
}

@media (max-width: 1100px) {
    .dashboard-wrapper {
        gap: 1.5rem;
        padding: 1.5rem;
    }
}

@media (max-width: 1024px) {
    .dashboard-wrapper {
        grid-template-columns: 1fr;
        grid-template-rows: auto auto;
        align-items: stretch;
        gap: 1.5rem;
    }
    
    .dashboard-main {
        grid-column: 1;
        grid-row: 1;
        align-self: stretch;
        min-height: auto;
    }
    
    .dashboard-bottom-row {
        grid-column: 1;
        grid-row: 2;
        align-self: stretch;
        height: auto;
    }
    
    .dashboard-sidebar {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .dashboard-sidebar-column {
        gap: 1.5rem;
        flex: none;
        height: auto;
        min-height: 0;
    }
}

@media (max-width: 768px) {
    .stats-cards {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .applications-grid {
        grid-template-columns: 1fr;
    }
    
    .welcome-banner {
        padding: 2rem;
    }
    
    .banner-title {
        font-size: 1.5rem;
    }
}
</style>
