<template>
    <Layout>
        <div class="dashboard-wrapper">
            <!-- Animated Background Orbs -->
            <div class="dashboard-orbs">
                <div class="dashboard-orb orb-1"></div>
                <div class="dashboard-orb orb-2"></div>
                <div class="dashboard-orb orb-3"></div>
                <div class="dashboard-orb orb-4"></div>
            </div>
            
            <div class="dashboard-main">
                <!-- Dashboard Header -->
                <div class="dashboard-header">
                    <div class="header-content">
                        <div class="header-text">
                            <h1 class="header-title">My Applications</h1>
                            <p class="header-subtitle">Track and manage your job applications</p>
                        </div>
                    </div>
                </div>

                <!-- Applications Section -->
                <div class="applications-section">
                    <div v-if="loading" class="loading">Loading...</div>
                    <div v-else-if="applications?.data?.length > 0" class="applications-list">
                        <div v-for="application in applications.data" :key="application.id" class="application-card">
                            <div class="application-card-header">
                                <div class="application-title-section">
                                    <router-link :to="`/student/jobs/${application.job.id}`" class="application-title-link">
                                        <h3 class="application-title">{{ application.job.title }}</h3>
                                    </router-link>
                                    <div class="application-meta-row">
                                        <span class="status-badge" :class="getStatusClass(application.status)">
                                            {{ capitalize(application.status) }}
                                        </span>
                                        <span class="application-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            {{ formatDate(application.created_at) }}
                                        </span>
                                        <span v-if="application.job.category" class="application-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                            </svg>
                                            {{ application.job.category }}
                                        </span>
                                        <span v-if="application.job.budget" class="application-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="12" y1="1" x2="12" y2="23"/>
                                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                            </svg>
                                            {{ formatMoney(application.job.budget) }} TND
                                        </span>
                                    </div>
                                </div>
                                <div class="application-actions">
                                    <button
                                        v-if="application.status === 'pending'"
                                        @click="withdrawApplication(application.id)"
                                        class="action-btn withdraw-btn"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                        Withdraw
                                    </button>
                                </div>
                            </div>
                            <p v-if="application.job.description" class="application-description">{{ truncate(application.job.description, 150) }}</p>
                            
                            <!-- Cover Letter Section -->
                            <div v-if="application.cover_letter" class="cover-letter-section">
                                <strong>Cover Letter:</strong>
                                <p>{{ application.cover_letter }}</p>
                            </div>
                            
                            <!-- Job Completion Section -->
                            <div v-if="application.status === 'accepted'" class="completion-section">
                                <div v-if="!application.completion" class="completion-pending">
                                    <h4>Job Completion</h4>
                                    <p>Submit proof of job completion for admin verification.</p>
                                    <form @submit.prevent="submitCompletion(application.id)" class="completion-form">
                                        <div class="file-upload-container">
                                            <label :for="`completion-file-${application.id}`" class="file-label">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                    <polyline points="17 8 12 3 7 8"/>
                                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                                </svg>
                                                <span>{{ completionFiles[application.id]?.name || 'Choose file...' }}</span>
                                                <input
                                                    type="file"
                                                    :id="`completion-file-${application.id}`"
                                                    @change="handleFileSelect($event, application.id)"
                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar"
                                                    required
                                                    class="file-input"
                                                />
                                            </label>
                                            <button type="submit" :disabled="!completionFiles[application.id] || submittingCompletion === application.id" class="btn-submit">
                                                {{ submittingCompletion === application.id ? 'Submitting...' : 'Submit Completion' }}
                                            </button>
                                        </div>
                                        <small class="file-hint">Accepted formats: PDF, DOC, DOCX, JPG, PNG, ZIP, RAR (Max: 10MB)</small>
                                    </form>
                                </div>
                                <div v-else class="completion-status">
                                    <h4>Completion Status</h4>
                                    <div class="completion-info">
                                        <span class="badge" :class="getCompletionStatusClass(application.completion.admin_verification_status)">
                                            {{ capitalize(application.completion.admin_verification_status) }}
                                        </span>
                                        <a v-if="application.completion.completion_file_url" :href="application.completion.completion_file_url" target="_blank" class="view-file-link">
                                            View Submission
                                        </a>
                                    </div>
                                    <p v-if="application.completion.rejection_reason" class="rejection-reason">
                                        <strong>Rejection Reason:</strong> {{ application.completion.rejection_reason }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state">
                        <div class="empty-state-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/>
                            </svg>
                        </div>
                        <p>No applications yet.</p>
                        <router-link to="/student/jobs" class="browse-jobs-btn">
                            Browse Jobs
                        </router-link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="applications?.links" class="pagination">
                        <button v-if="applications.links.prev" @click="loadPage(getPageFromUrl(applications.links.prev))" class="pagination-btn">« Previous</button>
                        <span class="pagination-info">Page {{ applications.current_page }} of {{ applications.last_page }}</span>
                        <button v-if="applications.links.next" @click="loadPage(getPageFromUrl(applications.links.next))" class="pagination-btn">Next »</button>
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
    name: 'StudentApplications',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            applications: null,
            completionFiles: {},
            submittingCompletion: null,
        };
    },
    async mounted() {
        await this.loadApplications();
    },
    methods: {
        async loadApplications(page = 1) {
            this.loading = true;
            try {
                const response = await api.get('/student/applications', { params: { page } });
                this.applications = response.data;
            } catch (error) {
                console.error('Failed to load applications:', error);
            } finally {
                this.loading = false;
            }
        },
        async withdrawApplication(id) {
            if (!confirm('Are you sure you want to withdraw this application?')) return;
            try {
                await api.post(`/student/applications/${id}/withdraw`);
                await this.loadApplications(this.applications.current_page);
            } catch (error) {
                alert(error.response?.data?.message || 'Failed to withdraw application');
            }
        },
        loadPage(page) {
            this.loadApplications(page);
        },
        handleFileSelect(event, applicationId) {
            const file = event.target.files[0];
            if (file) {
                // Vue 3: Direct assignment to reactive object property
                this.completionFiles[applicationId] = file;
            }
        },
        async submitCompletion(applicationId) {
            const file = this.completionFiles[applicationId];
            if (!file) {
                alert('Please select a file');
                return;
            }

            this.submittingCompletion = applicationId;
            try {
                const formData = new FormData();
                formData.append('completion_file', file);

                await api.post(`/student/applications/${applicationId}/complete`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                alert('Completion submitted successfully! Waiting for admin verification.');
                await this.loadApplications(this.applications.current_page);
            } catch (error) {
                alert(error.response?.data?.message || 'Failed to submit completion');
            } finally {
                this.submittingCompletion = null;
            }
        },
        getCompletionStatusClass(status) {
            const classes = {
                pending: 'status-pending',
                approved: 'status-approved',
                rejected: 'status-rejected',
            };
            return classes[status] || '';
        },
        getPageFromUrl(url) {
            const match = url.match(/page=(\d+)/);
            return match ? parseInt(match[1]) : 1;
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        },
        truncate(text, length) {
            if (!text) return '';
            return text.length > length ? text.substring(0, length) + '...' : text;
        },
        formatMoney(amount) {
            const num = Number(amount) || 0;
            return num.toFixed(2);
        },
        capitalize(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
        },
        getStatusClass(status) {
            const classes = {
                pending: 'badge-warning',
                accepted: 'badge-success',
                rejected: 'badge-danger',
                withdrawn: 'badge-secondary',
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
    background: radial-gradient(circle, rgba(31, 106, 225, 0.06) 0%, transparent 70%);
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

/* Applications Section */
.applications-section {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.applications-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.application-card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(229, 231, 235, 0.5);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.application-card:hover {
    border-color: rgba(31, 106, 225, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.application-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.application-title-section {
    flex: 1;
}

.application-title-link {
    text-decoration: none;
    color: inherit;
}

.application-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.75rem 0;
    transition: color 0.2s ease;
}

.application-title:hover {
    color: #1F6AE1;
}

.application-meta-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.application-meta-item {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
}

.application-meta-item svg {
    width: 16px;
    height: 16px;
}

.application-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.status-badge {
    padding: 0.375rem 0.875rem;
    border-radius: 12px;
    font-size: 0.8125rem;
    font-weight: 600;
    white-space: nowrap;
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
    background: linear-gradient(135deg, #dbeafe 0%, #93c5fd 100%);
    color: #1e40af;
}

.badge-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.badge-secondary {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #475569;
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

.action-btn.withdraw-btn {
    background: rgba(239, 68, 68, 0.1);
    color: #EA4335;
}

.action-btn.withdraw-btn:hover {
    background: rgba(239, 68, 68, 0.15);
    transform: translateY(-1px);
}

.application-description {
    color: #64748b;
    font-size: 0.9375rem;
    line-height: 1.6;
    margin: 0;
}

.cover-letter-section {
    margin-top: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.cover-letter-section p {
    margin: 0.5rem 0 0 0;
    color: #64748b;
    line-height: 1.6;
}

.completion-section {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: rgba(240, 249, 255, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    border: 1px solid rgba(186, 230, 253, 0.5);
}

.completion-section h4 {
    margin: 0 0 0.75rem 0;
    color: #0c4a6e;
    font-size: 1.125rem;
    font-weight: 600;
}

.completion-pending p {
    margin: 0 0 1rem 0;
    color: #075985;
    font-size: 0.875rem;
}

.completion-form {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.file-upload-container {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.file-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: white;
    border: 2px dashed #38bdf8;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    color: #0284c7;
    font-weight: 500;
    flex: 1;
    min-width: 200px;
}

.file-label:hover {
    background: #f0f9ff;
    border-color: #0ea5e9;
}

.file-label svg {
    width: 20px;
    height: 20px;
}

.file-input {
    display: none;
}

.file-hint {
    color: #64748b;
    font-size: 0.75rem;
}

.btn-submit {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3);
}

.btn-submit:hover:not(:disabled) {
    background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.completion-status {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.completion-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.status-pending {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
}

.status-approved {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.status-rejected {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.view-file-link {
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #38bdf8;
    border-radius: 8px;
    color: #0284c7;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.view-file-link:hover {
    background: #f0f9ff;
    border-color: #0ea5e9;
}

.rejection-reason {
    margin-top: 0.75rem;
    padding: 1rem;
    background: #fef2f2;
    border-left: 4px solid #EA4335;
    border-radius: 8px;
    color: #991b1b;
    font-size: 0.875rem;
    line-height: 1.6;
}

.rejection-reason strong {
    display: block;
    margin-bottom: 0.5rem;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
}

.pagination-btn {
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s ease;
    color: #64748b;
}

.pagination-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #111827;
}

.pagination-info {
    color: #64748b;
    font-size: 0.875rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #64748b;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    color: #cbd5e1;
}

.empty-state-icon svg {
    width: 100%;
    height: 100%;
}

.empty-state p {
    font-size: 1.125rem;
    margin: 0 0 1.5rem 0;
    color: #64748b;
}

.browse-jobs-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 2rem;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.browse-jobs-btn:hover {
    background: linear-gradient(135deg, #0B3C8A 0%, #4338ca 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(31, 106, 225, 0.4);
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
</style>
