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
                <div class="profile-page">
            <div v-if="alertMessage" class="alert" :class="alertType" @click="alertMessage = null">
                <span>{{ alertMessage }}</span>
                <button class="alert-close">×</button>
            </div>

            <div class="profile-header-section">
                <button @click="$router.push('/student/dashboard')" class="back-button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back to Dashboard
                </button>
                <router-link to="/student/profile/edit" class="edit-profile-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit Profile
                </router-link>
            </div>

            <div class="profile-content">
                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-avatar-section">
                        <div v-if="!user.profile_picture" class="profile-avatar">
                            {{ userInitials }}
                        </div>
                        <img v-else :src="getImageUrl(user.profile_picture)" alt="Profile" class="profile-avatar" />
                        <div class="profile-basic-info">
                            <div class="profile-name-row">
                                <h1 class="profile-name">{{ user.name }}</h1>
                                <div class="profile-badges">
                                    <div v-if="user.verification_status === 'approved'" class="verification-badge verified">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                        <span>Verified</span>
                                    </div>
                                    <div v-if="user.average_rating" class="rating-badge">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="star-icon">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <span class="rating-value">{{ user.average_rating }}</span>
                                        <span class="rating-count">({{ user.total_ratings }})</span>
                                    </div>
                                </div>
                            </div>
                            <p class="profile-email">{{ user.email }}</p>
                            <div class="profile-badge">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                {{ capitalize(user.role) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Cards -->
                <div class="info-cards-grid">
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="info-card-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                            </div>
                            <h3 class="info-card-title">Personal Information</h3>
                        </div>
                        <div class="info-card-content">
                            <div class="info-item">
                                <span class="info-label">Identity Card Number</span>
                                <span class="info-value">{{ user.identity_card_number || 'Not provided' }}</span>
                            </div>
                            <div class="info-item" v-if="user.address">
                                <span class="info-label">Address</span>
                                <span class="info-value">{{ user.address }}</span>
                            </div>
                            <div class="info-item" v-if="user.university">
                                <span class="info-label">University</span>
                                <span class="info-value">{{ user.university }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="info-card-icon documents-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                            </div>
                            <h3 class="info-card-title">Documents</h3>
                        </div>
                        <div class="info-card-content">
                            <div class="document-item" v-if="user.cv_file">
                                <div class="document-info">
                                    <div class="document-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="document-name">CV / Resume</div>
                                        <div class="document-status">Uploaded</div>
                                    </div>
                                </div>
                                <a :href="getImageUrl(user.cv_file)" target="_blank" class="document-action">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                        <polyline points="15 3 21 3 21 9"/>
                                        <line x1="10" y1="14" x2="21" y2="3"/>
                                    </svg>
                                    View
                                </a>
                            </div>
                            <div class="document-item" v-if="user.identity_card">
                                <div class="document-info">
                                    <div class="document-icon identity-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                            <line x1="7" y1="8" x2="17" y2="8"/>
                                            <line x1="7" y1="12" x2="17" y2="12"/>
                                            <line x1="7" y1="16" x2="12" y2="16"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="document-name">Identity Card</div>
                                        <div class="document-status">Uploaded</div>
                                    </div>
                                </div>
                                <a :href="getImageUrl(user.identity_card)" target="_blank" class="document-action">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                        <polyline points="15 3 21 3 21 9"/>
                                        <line x1="10" y1="14" x2="21" y2="3"/>
                                    </svg>
                                    View
                                </a>
                            </div>
                            <div class="document-item" v-if="user.student_card">
                                <div class="document-info">
                                    <div class="document-icon student-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                            <path d="M6 12v5c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2v-5"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="document-name">Student Card</div>
                                        <div class="document-status">Uploaded</div>
                                    </div>
                                </div>
                                <a :href="getImageUrl(user.student_card)" target="_blank" class="document-action">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                        <polyline points="15 3 21 3 21 9"/>
                                        <line x1="10" y1="14" x2="21" y2="3"/>
                                    </svg>
                                    View
                                </a>
                            </div>
                            <div v-if="!user.cv_file && !user.identity_card && !user.student_card" class="no-documents">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                                <p>No documents uploaded yet</p>
                                <router-link to="/student/profile/edit" class="upload-link">Upload Documents</router-link>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Status Card -->
                    <div v-if="user.verification_status && (user.identity_card || user.student_card)" class="info-card">
                        <div class="info-card-header">
                            <div class="info-card-icon verification-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <h3 class="info-card-title">Verification Status</h3>
                        </div>
                        <div class="info-card-content">
                            <div class="verification-status-display" :class="'status-' + user.verification_status">
                                <div class="verification-status-badge">
                                    <svg v-if="user.verification_status === 'approved'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <svg v-else-if="user.verification_status === 'rejected'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                    </svg>
                                    <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span>{{ getVerificationStatusText() }}</span>
                                </div>
                                <p v-if="user.verification_status === 'approved'" class="verification-message">
                                    Your account has been verified by an administrator.
                                </p>
                                <p v-else-if="user.verification_status === 'pending'" class="verification-message">
                                    Your documents are under review. You'll be notified once the verification is complete.
                                </p>
                                <div v-if="user.verification_status === 'rejected' && user.rejection_reason" class="rejection-reason-section">
                                    <div class="rejection-reason-header">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="15" y1="9" x2="9" y2="15"/>
                                            <line x1="9" y1="9" x2="15" y2="15"/>
                                        </svg>
                                        <span>Rejection Reason</span>
                                    </div>
                                    <p class="rejection-reason-text">{{ user.rejection_reason }}</p>
                                    <p class="rejection-reason-hint">Please correct the issues mentioned above and upload new documents to resubmit for verification.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ratings Section -->
                <div v-if="user.total_ratings > 0" class="ratings-section">
                    <div class="section-header">
                        <h2 class="section-title">Reviews & Ratings</h2>
                        <div class="ratings-summary">
                            <div class="average-rating-display">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="star-icon-large">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <span class="average-rating-value">{{ user.average_rating }}</span>
                                <span class="total-ratings-text">based on {{ user.total_ratings }} review{{ user.total_ratings !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="ratings-list">
                        <div v-for="rating in ratings" :key="rating.id" class="rating-card">
                            <div class="rating-card-header">
                                <div class="rating-client-info">
                                    <div class="client-avatar">
                                        {{ getInitials(rating.client?.name) }}
                                    </div>
                                    <div class="client-details">
                                        <div class="client-name">{{ rating.client?.name }}</div>
                                        <div class="rating-date">{{ formatDate(rating.created_at) }}</div>
                                    </div>
                                </div>
                                <div class="rating-stars">
                                    <svg
                                        v-for="i in 5"
                                        :key="i"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="star"
                                        :class="{ 'star-filled': i <= rating.rating, 'star-empty': i > rating.rating }"
                                    >
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="rating-job-info">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span>{{ rating.job?.title }}</span>
                            </div>
                            <p v-if="rating.comment" class="rating-comment">{{ rating.comment }}</p>
                            <p v-else class="rating-comment no-comment">No comment provided</p>
                        </div>
                    </div>
                    <div v-if="ratings.length === 0 && !loadingRatings" class="empty-ratings">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <p>No ratings yet</p>
                    </div>
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
    name: 'StudentProfile',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            user: {},
            alertMessage: null,
            alertType: 'success',
            ratings: [],
            loadingRatings: false,
        };
    },
    computed: {
        userInitials() {
            if (!this.user?.name) return '?';
            return this.user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        },
    },
    async mounted() {
        await this.loadProfile();
    },
    methods: {
        async loadProfile() {
            try {
                const response = await api.get('/student/profile');
                this.user = response.data;
                // Load ratings if the user has any
                if (this.user.total_ratings > 0) {
                    await this.loadRatings();
                }
            } catch (error) {
                this.alertMessage = 'Failed to load profile';
                this.alertType = 'error';
            } finally {
                this.loading = false;
            }
        },
        async loadRatings() {
            this.loadingRatings = true;
            try {
                const response = await api.get('/student/ratings');
                this.ratings = response.data;
            } catch (error) {
                console.error('Failed to load ratings:', error);
            } finally {
                this.loadingRatings = false;
            }
        },
        capitalize(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
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
        getVerificationStatusText() {
            if (this.user.verification_status === 'approved') {
                return 'Verified';
            } else if (this.user.verification_status === 'rejected') {
                return 'Rejected';
            }
            return 'Pending';
        },
        getInitials(name) {
            if (!name) return '?';
            return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        },
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
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

.dashboard-main {
    position: relative;
    z-index: 1;
}

.profile-page {
    padding: 2rem;
    padding-bottom: 4rem;
    width: 100%;
}

.alert {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.alert:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.alert-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: inherit;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.alert-close:hover {
    opacity: 1;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.profile-header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    gap: 1rem;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    color: #64748b;
    text-decoration: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.9375rem;
}

.back-button:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #111827;
}

.back-button svg {
    width: 18px;
    height: 18px;
}

.edit-profile-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #1F6AE1;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    font-size: 0.9375rem;
}

.edit-profile-btn:hover {
    background: #0B3C8A;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.edit-profile-btn svg {
    width: 18px;
    height: 18px;
}

.profile-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* Profile Card */
.profile-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.profile-avatar-section {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 700;
    color: white;
    box-shadow: 0 8px 24px rgba(31, 106, 225, 0.25);
    border: 4px solid white;
    flex-shrink: 0;
}

.profile-basic-info {
    flex: 1;
}

.profile-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.profile-badges {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.profile-name {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.verification-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
}

.verification-badge.verified {
    background: #d1fae5;
    color: #059669;
}

.verification-badge svg {
    width: 16px;
    height: 16px;
}

.rating-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #fbbf24;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.rating-badge .star-icon {
    width: 18px;
    height: 18px;
    fill: #fbbf24;
}

.rating-badge .rating-value {
    font-weight: 700;
}

.rating-badge .rating-count {
    font-weight: 500;
    opacity: 0.8;
}

.profile-email {
    font-size: 1rem;
    color: #6b7280;
    margin: 0 0 1rem 0;
}

.profile-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(31, 106, 225, 0.1);
    color: #1F6AE1;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
}

.profile-badge svg {
    width: 16px;
    height: 16px;
}

/* Info Cards Grid */
.info-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.info-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.info-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.info-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.info-card-icon.documents-icon {
    background: linear-gradient(135deg, #34A853 0%, #2d8f47 100%);
}

.info-card-icon svg {
    width: 24px;
    height: 24px;
}

.info-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.info-card-content {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    color: #111827;
    font-weight: 500;
}

/* Documents */
.document-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem;
    background: white;
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.document-item:hover {
    border-color: rgba(31, 106, 225, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.document-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.document-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.document-icon.identity-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.document-icon.student-icon {
    background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
}

.document-icon svg {
    width: 20px;
    height: 20px;
}

.document-name {
    font-weight: 600;
    color: #111827;
    font-size: 0.9375rem;
    margin-bottom: 0.25rem;
}

.document-status {
    font-size: 0.8125rem;
    color: #34A853;
    font-weight: 500;
}

.document-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f3f4f6;
    color: #374151;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.document-action:hover {
    background: #1F6AE1;
    color: white;
}

.document-action svg {
    width: 16px;
    height: 16px;
}

.no-documents {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem;
    text-align: center;
    color: #6b7280;
}

.no-documents svg {
    width: 64px;
    height: 64px;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.no-documents p {
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

.upload-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #1F6AE1;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.upload-link:hover {
    background: #0B3C8A;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem;
    color: #6b7280;
    gap: 1rem;
}

.loading::before {
    content: '';
    width: 48px;
    height: 48px;
    border: 4px solid #e5e7eb;
    border-top-color: #1F6AE1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .profile-page {
        padding: 1.5rem;
        padding-bottom: 4rem;
    }

    .profile-header-section {
        flex-direction: column;
        align-items: stretch;
    }

    .back-button,
    .edit-profile-btn {
        width: 100%;
        justify-content: center;
    }

    .info-cards-grid {
        grid-template-columns: 1fr;
    }

    .profile-avatar-section {
        flex-direction: column;
        text-align: center;
    }

    .profile-basic-info {
        text-align: center;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .rating-card-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .rating-stars {
        align-self: flex-start;
    }
}

/* Verification Status */
.info-card-icon.verification-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.verification-status-display {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.verification-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9375rem;
}

.verification-status-display.status-approved .verification-status-badge {
    background: #d1fae5;
    color: #059669;
}

.verification-status-display.status-rejected .verification-status-badge {
    background: #fee2e2;
    color: #dc2626;
}

.verification-status-display.status-pending .verification-status-badge {
    background: #fef3c7;
    color: #d97706;
}

.verification-status-badge svg {
    width: 18px;
    height: 18px;
}

.verification-message {
    color: #6b7280;
    font-size: 0.9375rem;
    line-height: 1.6;
    margin: 0;
}

.rejection-reason-section {
    margin-top: 1rem;
    padding: 1.25rem;
    background: rgba(239, 68, 68, 0.1);
    border-radius: 12px;
    border-left: 4px solid #EA4335;
}

.rejection-reason-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.rejection-reason-header svg {
    width: 18px;
    height: 18px;
    color: #dc2626;
    flex-shrink: 0;
}

.rejection-reason-header span {
    font-size: 0.9375rem;
    font-weight: 700;
    color: #dc2626;
}

.rejection-reason-text {
    font-size: 0.9375rem;
    color: #111827;
    line-height: 1.6;
    margin: 0 0 0.75rem 0;
    padding-left: 1.75rem;
    white-space: pre-wrap;
}

.rejection-reason-hint {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.5;
    margin: 0;
    padding-left: 1.75rem;
    font-style: italic;
}

/* Ratings Section */
.ratings-section {
    margin-top: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.section-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.ratings-summary {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.average-rating-display {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.star-icon-large {
    width: 32px;
    height: 32px;
    fill: #fbbf24;
}

.average-rating-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}

.total-ratings-text {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.ratings-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.rating-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.rating-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.rating-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.rating-client-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.client-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #34A853 0%, #2d8f47 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
    flex-shrink: 0;
}

.client-details {
    flex: 1;
    min-width: 0;
}

.client-name {
    font-weight: 600;
    font-size: 1.125rem;
    color: #111827;
    margin-bottom: 0.25rem;
}

.rating-date {
    font-size: 0.875rem;
    color: #6b7280;
}

.rating-stars {
    display: flex;
    gap: 0.25rem;
    flex-shrink: 0;
}

.rating-stars .star {
    width: 20px;
    height: 20px;
}

.rating-stars .star-filled {
    fill: #fbbf24;
    color: #fbbf24;
}

.rating-stars .star-empty {
    fill: #e5e7eb;
    color: #e5e7eb;
}

.rating-job-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding: 0.75rem 1rem;
    background: rgba(249, 250, 251, 0.8);
    border-radius: 12px;
    font-size: 0.9375rem;
    color: #475569;
    font-weight: 500;
}

.rating-job-info svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

.rating-comment {
    color: #374151;
    font-size: 1rem;
    line-height: 1.6;
    margin: 0;
    padding: 1rem;
    background: rgba(249, 250, 251, 0.6);
    border-radius: 12px;
    border-left: 3px solid #34A853;
}

.rating-comment.no-comment {
    color: #9ca3af;
    font-style: italic;
    border-left-color: #d1d5db;
}

.empty-ratings {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.empty-ratings svg {
    width: 64px;
    height: 64px;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-ratings p {
    color: #6b7280;
    font-size: 1.125rem;
    margin: 0;
}
</style>
