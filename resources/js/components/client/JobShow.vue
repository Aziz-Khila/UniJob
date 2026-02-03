<template>
    <Layout>
        <div v-if="loading" class="loading">Loading...</div>
        <div v-else-if="job" class="dashboard-wrapper">
            <!-- Animated Background Orbs -->
            <div class="dashboard-orbs">
                <div class="dashboard-orb orb-1"></div>
                <div class="dashboard-orb orb-2"></div>
                <div class="dashboard-orb orb-3"></div>
                <div class="dashboard-orb orb-4"></div>
            </div>
            
            <div class="dashboard-main">
                <div class="job-show-page">
                    <router-link to="/client/jobs" class="back-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        Back to My Jobs
                    </router-link>
                    <!-- Dashboard Header -->
                    <div class="dashboard-header">
                        <div class="header-content">
                            <div class="header-text">
                                <h1 class="header-title">{{ job.title }}</h1>
                                <p class="header-subtitle">View job details and manage applications</p>
                            </div>
                            <div class="header-actions">
                                <router-link :to="`/client/jobs/${job.id}/edit`" class="btn btn-sm btn-secondary">Edit</router-link>
                                <button v-if="job.status === 'open'" @click="showDeleteModal = true" class="btn btn-sm btn-danger">Delete</button>
                            </div>
                        </div>
                    </div>

            <div class="job-details">
                <div class="job-info">
                    <div class="job-meta-section">
                        <div v-if="job.budget" class="meta-item">
                            <span class="meta-label">💰 Budget</span>
                            <span class="meta-value">{{ formatMoney(job.budget) }} TND</span>
                        </div>
                        <div v-if="job.category" class="meta-item">
                            <span class="meta-label">📁 Category</span>
                            <span class="meta-value">{{ job.category }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Status</span>
                            <span class="badge" :class="getStatusClass(job.status)">{{ capitalize(job.status) }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">👥 Workers</span>
                            <span class="meta-value">{{ acceptedCount || 0 }} / {{ job.workers_needed || 1 }} accepted</span>
                        </div>
                        <div v-if="job.work_type" class="meta-item">
                            <span class="meta-label">📍 Work Type</span>
                            <span class="meta-value">{{ capitalizeWorkType(job.work_type) }}</span>
                        </div>
                        <div v-if="job.start_day && job.end_day" class="meta-item">
                            <span class="meta-label">📅 Duration</span>
                            <span class="meta-value">{{ formatDate(job.start_day) }} - {{ formatDate(job.end_day) }}</span>
                        </div>
                    </div>

                    <div class="job-description">
                        <h3>Description</h3>
                        <p>{{ job.description }}</p>
                    </div>

                    <div class="applications-section">
                        <div class="section-header">
                            <h3>Applications ({{ applications?.length || 0 }})</h3>
                            <div v-if="job.workers_needed" class="worker-status">
                                <span class="worker-count">{{ acceptedCount || 0 }} / {{ job.workers_needed }} workers accepted</span>
                                <span v-if="hasReachedLimit" class="limit-reached">⚠️ Worker limit reached</span>
                            </div>
                        </div>
                        <div v-if="applications?.length > 0" class="applications-list">
                            <div v-for="app in applications" :key="app.id" class="application-card">
                                <div class="application-header">
                                    <div class="application-info">
                                        <div class="student-name-row">
                                            <h4>{{ app.student?.name }}</h4>
                                            <div v-if="app.student?.verification_status === 'approved'" class="verification-badge verified">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
                                                <span>Verified</span>
                                            </div>
                                        </div>
                                        <div class="student-details">
                                            <p v-if="app.student?.identity_card_number">
                                                <strong>Identity Card:</strong> {{ app.student.identity_card_number }}
                                            </p>
                                            <p v-if="app.student?.university">
                                                <strong>University:</strong> {{ app.student.university }}
                                            </p>
                                        </div>
                                        <p v-if="app.cover_letter" class="cover-letter">{{ truncate(app.cover_letter, 200) }}</p>
                                    </div>
                                    <div class="application-actions">
                                        <div class="application-status-row">
                                            <span class="badge" :class="getStatusClass(app.status)">{{ capitalize(app.status) }}</span>
                                            <div v-if="app.student?.average_rating" class="student-rating">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="star-icon">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                                <span class="rating-value">{{ app.student.average_rating }}</span>
                                                <span class="rating-count">({{ app.student.total_ratings }})</span>
                                            </div>
                                        </div>
                                        <div class="action-buttons">
                                            <button
                                                v-if="app.status === 'pending'"
                                                @click="acceptApplication(app.id)"
                                                :disabled="hasReachedLimit"
                                                class="btn btn-sm btn-success"
                                                :title="hasReachedLimit ? `Worker limit reached (${job.workers_needed} accepted)` : ''"
                                            >
                                                Accept
                                            </button>
                                            <button
                                                v-if="app.status === 'pending'"
                                                @click="rejectApplication(app.id)"
                                                class="btn btn-sm btn-danger"
                                            >
                                                Reject
                                            </button>
                                            <button
                                                v-if="app.status === 'accepted' && canRate(app)"
                                                @click="openRatingModal(app)"
                                                class="btn btn-sm btn-primary"
                                            >
                                                Rate Student
                                            </button>
                                            <a v-if="app.cv_file" :href="getFileUrl(app.cv_file)" target="_blank" class="btn btn-sm btn-secondary">
                                                View CV
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="empty-state">
                            <p>No applications yet.</p>
                        </div>
                    </div>
                </div>
            </div>
                </div>
            </div>

            <!-- Delete Job Modal -->
            <Teleport to="body">
            <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
                <div class="modal-content delete-modal">
                    <div class="modal-header">
                        <h2>Delete Job</h2>
                        <button @click="showDeleteModal = false" class="close-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this job? This action cannot be undone.</p>
                        <p v-if="job" class="job-title-warning"><strong>{{ job.title }}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button @click="showDeleteModal = false" class="btn btn-secondary">Cancel</button>
                        <button @click="confirmDelete" :disabled="deleting" class="btn btn-danger">
                            {{ deleting ? 'Deleting...' : 'Delete Job' }}
                        </button>
                    </div>
                </div>
            </div>
            </Teleport>

            <!-- Rating Modal -->
            <Teleport to="body">
            <div v-if="showRatingModal" class="modal-overlay" @click.self="closeRatingModal">
                <div class="modal-content rating-modal">
                    <div class="modal-header">
                        <h2>Rate Student</h2>
                        <button @click="closeRatingModal" class="close-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <div v-if="selectedApplication" class="modal-body">
                        <div class="student-info">
                            <h3>{{ selectedApplication.student?.name }}</h3>
                            <div v-if="selectedApplication.student?.identity_card_number" class="student-detail-item">
                                <strong>Identity Card Number:</strong> {{ selectedApplication.student.identity_card_number }}
                            </div>
                            <div v-if="selectedApplication.student?.university" class="student-detail-item">
                                <strong>University:</strong> {{ selectedApplication.student.university }}
                            </div>
                        </div>
                        <div class="rating-section">
                            <label>Rating (1-5 stars)</label>
                            <div class="star-rating">
                                <button
                                    v-for="i in 5"
                                    :key="i"
                                    @click="rating.rating = i"
                                    class="star-btn"
                                    :class="{ active: i <= rating.rating }"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="comment-section">
                            <label>Comment (Optional)</label>
                            <textarea
                                v-model="rating.comment"
                                rows="5"
                                placeholder="Share your experience working with this student..."
                                maxlength="1000"
                            ></textarea>
                            <small>{{ rating.comment?.length || 0 }}/1000 characters</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeRatingModal" class="btn btn-secondary">Cancel</button>
                        <button @click="submitRating" :disabled="submittingRating" class="btn btn-primary">
                            {{ submittingRating ? 'Submitting...' : 'Submit Rating' }}
                        </button>
                    </div>
                </div>
            </div>
            </Teleport>
        </div>
    </Layout>
</template>

<script>
import Layout from '../shared/Layout.vue';
import api from '../../api.js';
import { Teleport } from 'vue';

export default {
    name: 'ClientJobShow',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            job: null,
            applications: [],
            acceptedCount: 0,
            showRatingModal: false,
            showDeleteModal: false,
            deleting: false,
            selectedApplication: null,
            rating: {
                rating: 5,
                comment: '',
            },
            submittingRating: false,
            existingRating: null,
        };
    },
    computed: {
        hasReachedLimit() {
            if (!this.job || !this.job.workers_needed) return false;
            return this.acceptedCount >= this.job.workers_needed;
        },
    },
    async mounted() {
        await this.loadJob();
    },
    methods: {
        async loadJob() {
            try {
                const response = await api.get(`/client/jobs/${this.$route.params.id}`);
                this.job = response.data.job;
                this.applications = response.data.applications || [];
                // Count accepted applications
                this.acceptedCount = this.applications.filter(app => app.status === 'accepted').length;
            } catch (error) {
                console.error('Failed to load job:', error);
            } finally {
                this.loading = false;
            }
        },
        confirmDelete() {
            this.deleteJob();
        },
        async deleteJob() {
            this.deleting = true;
            try {
                await api.delete(`/client/jobs/${this.job.id}`);
                this.showDeleteModal = false;
                this.$router.push('/client/jobs');
            } catch (error) {
                alert(error.response?.data?.message || 'Failed to delete job');
            } finally {
                this.deleting = false;
            }
        },
        async acceptApplication(id) {
            try {
                await api.post(`/client/applications/${id}/accept`);
                await this.loadJob();
            } catch (error) {
                alert(error.response?.data?.message || 'Failed to accept application');
            }
        },
        async rejectApplication(id) {
            try {
                await api.post(`/client/applications/${id}/reject`);
                await this.loadJob();
            } catch (error) {
                alert(error.response?.data?.message || 'Failed to reject application');
            }
        },
        formatMoney(amount) {
            const num = Number(amount) || 0;
            return num.toFixed(2);
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
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
                pending: 'badge-warning',
                accepted: 'badge-success',
                rejected: 'badge-danger',
            };
            return classes[status] || 'badge-secondary';
        },
        getFileUrl(path) {
            return `/storage/${path}`;
        },
        capitalizeWorkType(type) {
            const types = {
                'on-site': 'On-Site',
                'remote': 'Remote',
                'hybrid': 'Hybrid',
            };
            return types[type] || type;
        },
        canRate(application) {
            // Can rate if application is accepted and completion is approved and not already rated
            return application.status === 'accepted' && 
                   application.completion && 
                   application.completion.admin_verification_status === 'approved' &&
                   !application.rating;
        },
        async openRatingModal(application) {
            this.selectedApplication = application;
            // Check if rating already exists
            try {
                const response = await api.get(`/client/applications/${application.id}/rating`);
                if (response.data.rating) {
                    this.existingRating = response.data.rating;
                    this.rating.rating = response.data.rating.rating;
                    this.rating.comment = response.data.rating.comment || '';
                } else {
                    this.existingRating = null;
                    this.rating.rating = 5;
                    this.rating.comment = '';
                }
            } catch (error) {
                console.error('Failed to load rating:', error);
                this.existingRating = null;
            }
            this.showRatingModal = true;
        },
        closeRatingModal() {
            this.showRatingModal = false;
            this.selectedApplication = null;
            this.rating.rating = 5;
            this.rating.comment = '';
            this.existingRating = null;
        },
        async submitRating() {
            if (!this.selectedApplication) return;

            this.submittingRating = true;
            try {
                await api.post(`/client/applications/${this.selectedApplication.id}/rate`, {
                    rating: this.rating.rating,
                    comment: this.rating.comment,
                });
                alert('Rating submitted successfully!');
                this.closeRatingModal();
                await this.loadJob(); // Reload to update the display
            } catch (error) {
                alert(error.response?.data?.message || 'Failed to submit rating');
            } finally {
                this.submittingRating = false;
            }
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
}

.job-show-page {
    padding: 2rem;
    padding-bottom: 4rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.job-show-page .back-button {
    align-self: flex-start;
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
    justify-content: space-between;
    gap: 1.5rem;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    color: #374151;
    font-weight: 500;
    font-size: 0.9375rem;
    text-decoration: none;
    transition: all 0.2s ease;
    margin-bottom: 1rem;
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

.header-text {
    flex: 1;
    min-width: 0;
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

.header-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.job-details {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.08),
        0 2px 8px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.8);
}

.job-meta-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.meta-label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.875rem;
}

.meta-value {
    font-size: 1.125rem;
    color: #0f172a;
    font-weight: 600;
}

.job-description {
    margin-bottom: 2rem;
}

.job-description h3 {
    margin-bottom: 1rem;
}

.job-description p {
    color: #64748b;
    line-height: 1.8;
    white-space: pre-wrap;
}

.applications-section .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.applications-section h3 {
    margin: 0;
}

.worker-status {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.worker-count {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.limit-reached {
    font-size: 0.875rem;
    color: #ef4444;
    font-weight: 600;
}

.applications-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.application-card {
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
}

.application-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.application-card:hover {
    box-shadow: 
        0 12px 48px rgba(0, 0, 0, 0.12),
        0 4px 12px rgba(0, 0, 0, 0.06);
    transform: translateY(-4px);
    border-color: rgba(99, 102, 241, 0.2);
}

.application-card:hover::before {
    transform: scaleY(1);
}

.application-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.application-info {
    flex: 1;
}

.student-name-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-bottom: 0.75rem;
}

.student-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.student-details p {
    margin: 0;
    color: #64748b;
    font-size: 0.9375rem;
}

.student-details strong {
    color: #374151;
    font-weight: 600;
    margin-right: 0.5rem;
}

.verification-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.75rem;
}

.verification-badge.verified {
    background: #d1fae5;
    color: #059669;
}

.verification-badge svg {
    width: 14px;
    height: 14px;
}

.application-info h4 {
    margin: 0;
}

.application-info p {
    color: #64748b;
    margin: 0.25rem 0;
}

.cover-letter {
    margin-top: 0.5rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    font-size: 0.875rem;
}

.application-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.badge {
    padding: 0.625rem 1.25rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    white-space: nowrap;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease;
}

.badge:hover {
    transform: scale(1.05);
}

.badge-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.badge-warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
}

.badge-info {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
}

.badge-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.btn {
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-block;
    position: relative;
    overflow: hidden;
}

.btn-sm {
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
}

.btn-secondary {
    background: #f1f5f9;
    color: #0f172a;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.btn-secondary:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

.btn-success:hover:not(:disabled) {
    background: linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.btn-success:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #9ca3af;
    transform: none;
}

.btn-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #64748b;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.08),
        0 2px 8px rgba(0, 0, 0, 0.04);
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
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Rating Modal */
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

.modal-content {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideUp 0.3s ease-out;
}

.rating-modal,
.delete-modal {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
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
    border-bottom: 1px solid #e2e8f0;
}

.modal-header h2 {
    margin: 0;
    color: #0f172a;
    font-size: 1.5rem;
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

.job-title-warning {
    margin-top: 1rem;
    padding: 1rem;
    background: #fef2f2;
    border-radius: 8px;
    color: #991b1b;
}

.delete-modal .modal-footer .btn-danger {
    background: #dc2626;
    color: white;
    border: none;
}

.delete-modal .modal-footer .btn-danger:hover {
    background: #b91c1c;
}

.delete-modal .modal-footer .btn-danger:disabled {
    background: #fca5a5;
    cursor: not-allowed;
}

.student-info {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.student-info h3 {
    margin: 0 0 0.5rem 0;
    color: #0f172a;
}

.student-detail-item {
    margin: 0.5rem 0;
    color: #64748b;
    font-size: 0.9375rem;
}

.student-detail-item strong {
    color: #374151;
    font-weight: 600;
    margin-right: 0.5rem;
}

.rating-section {
    margin-bottom: 2rem;
}

.rating-section label {
    display: block;
    margin-bottom: 1rem;
    font-weight: 600;
    color: #0f172a;
}

.star-rating {
    display: flex;
    gap: 0.5rem;
}

.star-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    color: #e2e8f0;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.star-btn svg {
    width: 40px;
    height: 40px;
}

.star-btn:hover,
.star-btn.active {
    color: #fbbf24;
    transform: scale(1.1);
}

.comment-section {
    margin-bottom: 1rem;
}

.comment-section label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #0f172a;
}

.comment-section textarea {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    font-family: inherit;
    resize: vertical;
    transition: border-color 0.2s;
}

.comment-section textarea:focus {
    outline: none;
    border-color: #667eea;
}

.comment-section small {
    display: block;
    margin-top: 0.5rem;
    color: #64748b;
    font-size: 0.875rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1.5rem 2rem;
    border-top: 1px solid #e2e8f0;
}

/* Student Rating Display */
.application-status-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 0.5rem;
}

.student-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: #fbbf24;
}

.star-icon {
    width: 16px;
    height: 16px;
}

.rating-value {
    font-weight: 600;
    color: #0f172a;
}

.rating-count {
    font-size: 0.875rem;
    color: #64748b;
}
</style>
