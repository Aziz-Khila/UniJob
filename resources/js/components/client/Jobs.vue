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
                <!-- Dashboard Header -->
                <div class="dashboard-header">
                    <div class="header-content">
                        <div class="header-text">
                            <h1 class="header-title">My Jobs</h1>
                            <p class="header-subtitle">Manage your job postings and track applications</p>
                        </div>
                        <div class="header-actions">
                            <router-link to="/client/jobs/create" class="post-job-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Post New Job
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- Jobs Section -->
                <div class="jobs-section">
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
                            Post Your First Job
                        </router-link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="jobs?.links" class="pagination">
                        <button v-if="jobs.links.prev" @click="loadPage(getPageFromUrl(jobs.links.prev))" class="pagination-btn">« Previous</button>
                        <span class="pagination-info">Page {{ jobs.current_page }} of {{ jobs.last_page }}</span>
                        <button v-if="jobs.links.next" @click="loadPage(getPageFromUrl(jobs.links.next))" class="pagination-btn">Next »</button>
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
    name: 'ClientJobs',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            jobs: null,
            showDeleteModal: false,
            selectedJobForDeletion: null,
            deleting: false,
        };
    },
    async mounted() {
        await this.loadJobs();
    },
    methods: {
        async loadJobs(page = 1) {
            this.loading = true;
            try {
                // Try dedicated jobs endpoint first, fallback to dashboard endpoint
                try {
                    const response = await api.get('/client/jobs', { params: { page } });
                    this.jobs = response.data.jobs || response.data;
                } catch (jobsError) {
                    // Fallback to dashboard endpoint if jobs endpoint doesn't exist
                    const response = await api.get('/client/dashboard', { params: { page } });
                    this.jobs = response.data.jobs || response.data;
                }
            } catch (error) {
                console.error('Failed to load jobs:', error);
            } finally {
                this.loading = false;
            }
        },
        loadPage(page) {
            this.loadJobs(page);
        },
        getPageFromUrl(url) {
            const match = url.match(/page=(\d+)/);
            return match ? parseInt(match[1]) : 1;
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
                await this.loadJobs();
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

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.post-job-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.post-job-btn:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
}

.post-job-btn svg {
    width: 18px;
    height: 18px;
}

/* Jobs Section */
.jobs-section {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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
}

.job-title-link {
    text-decoration: none;
    color: inherit;
}

.job-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.75rem 0;
    transition: color 0.2s ease;
}

.job-title:hover {
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

.job-meta-item {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
}

.job-meta-item svg {
    width: 16px;
    height: 16px;
}

.job-actions {
    display: flex;
    align-items: center;
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

.job-description {
    color: #64748b;
    font-size: 0.9375rem;
    line-height: 1.6;
    margin: 0;
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
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.browse-jobs-btn:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
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
}

.pagination-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.pagination-info {
    color: #64748b;
    font-size: 0.875rem;
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

