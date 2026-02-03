<template>
    <div>
        <Layout>
            <div v-if="loading && !stats" class="loading">Loading...</div>
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
                                <h1 class="header-title">Admin Dashboard</h1>
                                <p class="header-subtitle">Manage verifications, completions, and users</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="alertMessage" class="alert" :class="alertType" @click="alertMessage = null">
                        <span>{{ alertMessage }}</span>
                        <button class="alert-close">×</button>
                    </div>

                    <!-- Stats Section -->
                    <div class="stats-section">
                        <div class="stat-card">
                            <div class="stat-icon-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Total Students</div>
                                <div class="stat-value">{{ stats?.total_students || 0 }}</div>
                            </div>
                        </div>

                        <div class="stat-card pending">
                            <div class="stat-icon-wrapper pending-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Pending Verifications</div>
                                <div class="stat-value">{{ stats?.pending || 0 }}</div>
                            </div>
                        </div>

                        <div class="stat-card approved">
                            <div class="stat-icon-wrapper approved-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Approved</div>
                                <div class="stat-value">{{ stats?.approved || 0 }}</div>
                            </div>
                        </div>

                        <div class="stat-card rejected">
                            <div class="stat-icon-wrapper rejected-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Rejected</div>
                                <div class="stat-value">{{ stats?.rejected || 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Management Section -->
                    <div class="users-section">
                        <div class="section-header">
                            <h2 class="section-title">Users Management</h2>
                            <div class="header-actions">
                                <div class="tabs-and-filter">
                                    <div class="user-type-tabs">
                                        <button @click="switchUserType('student')" :class="['tab-btn', { active: userType === 'student' }]">
                                            Students
                                        </button>
                                        <button @click="switchUserType('client')" :class="['tab-btn', { active: userType === 'client' }]">
                                            Clients
                                        </button>
                                    </div>
                                    <select v-show="userType === 'student'" v-model="userFilter" @change="loadUsers" class="filter-select">
                                        <option value="">All Students</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <button @click="loadUsers" class="refresh-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="23 4 23 10 17 10"/>
                                        <polyline points="1 20 1 14 7 14"/>
                                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                                    </svg>
                                    Refresh
                                </button>
                            </div>
                        </div>

                        <div v-if="loadingUsers" class="loading-state">
                            <div class="spinner"></div>
                            <span>Loading {{ userType === 'student' ? 'students' : 'clients' }}...</span>
                        </div>

                        <div v-else-if="users?.data?.length > 0" class="users-list">
                            <div v-for="user in users.data" :key="user.id" class="user-card">
                                <div class="user-card-header">
                                    <div class="user-info">
                                        <h3 class="user-name">{{ user.name }}</h3>
                                        <p class="user-email">{{ user.email }}</p>
                                        <p v-if="userType === 'student' && user.university" class="user-university">{{ user.university }}</p>
                                        <p v-if="userType === 'student' && user.identity_card_number" class="user-id-card">ID: {{ user.identity_card_number }}</p>
                                        <p v-if="userType === 'client'" class="user-role">Client</p>
                                    </div>
                                    <div class="user-status">
                                        <span v-if="userType === 'student'" class="status-badge" :class="getStatusClass(user.verification_status)">
                                            {{ capitalize(user.verification_status || 'pending') }}
                                        </span>
                                        <span v-else class="status-badge badge-success">Active</span>
                                        <p v-if="userType === 'student' && user.verified_at" class="verified-date">Verified: {{ formatDate(user.verified_at) }}</p>
                                        <p v-if="userType === 'student' && user.verified_by" class="verified-by">By: {{ user.verified_by }}</p>
                                        <p v-if="userType === 'client' && user.created_at" class="user-joined">Joined: {{ formatDate(user.created_at) }}</p>
                                    </div>
                                </div>
                                <div v-if="userType === 'student' && user.rejection_reason" class="rejection-reason">
                                    <strong>Rejection Reason:</strong> {{ user.rejection_reason }}
                                </div>
                            </div>
                        </div>

                        <div v-else class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <h3>No {{ userType === 'student' ? 'Students' : 'Clients' }} Found</h3>
                            <p>No {{ userType === 'student' ? 'students' : 'clients' }} match the selected filter.</p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="users?.links" class="pagination">
                            <button v-if="users.links.prev" @click="loadUsers(getPageFromUrl(users.links.prev))" class="pagination-btn">« Previous</button>
                            <span class="pagination-info">Page {{ users.current_page }} of {{ users.last_page }}</span>
                            <button v-if="users.links.next" @click="loadUsers(getPageFromUrl(users.links.next))" class="pagination-btn">Next »</button>
                        </div>
                    </div>

                    <!-- Pending Job Completions Section -->
                    <div class="completions-section">
                        <div class="section-header">
                            <h2 class="section-title">Pending Job Completions</h2>
                            <button @click="loadPendingCompletions" class="refresh-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="23 4 23 10 17 10"/>
                                    <polyline points="1 20 1 14 7 14"/>
                                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                                </svg>
                                Refresh
                            </button>
                        </div>

                        <div v-if="loadingCompletions" class="loading-state">
                            <div class="spinner"></div>
                            <span>Loading completions...</span>
                        </div>

                        <div v-else-if="pendingCompletions.length === 0" class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <h3>No Pending Completions</h3>
                            <p>All job completions have been processed.</p>
                        </div>

                        <div v-else class="completions-list">
                            <div v-for="completion in pendingCompletions" :key="completion.id" class="completion-card">
                                <div class="completion-card-header">
                                    <div class="completion-info">
                                        <h3 class="completion-job-title">{{ completion.job.title }}</h3>
                                        <p class="completion-student-name">
                                            <strong>Student:</strong> {{ completion.student.name }} ({{ completion.student.email }})
                                        </p>
                                        <p class="completion-client-name">
                                            <strong>Client:</strong> {{ completion.job.client_name }}
                                        </p>
                                        <p class="completion-date">
                                            <strong>Submitted:</strong> {{ formatDate(completion.created_at) }}
                                        </p>
                                    </div>
                                    <div class="completion-actions">
                                        <button @click="approveCompletion(completion)" :disabled="processing" class="btn-approve">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                <polyline points="22 4 12 14.01 9 11.01"/>
                                            </svg>
                                            Approve
                                        </button>
                                        <button @click="rejectCompletion(completion)" :disabled="processing" class="btn-reject">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="15" y1="9" x2="9" y2="15"/>
                                                <line x1="9" y1="9" x2="15" y2="15"/>
                                            </svg>
                                            Reject
                                        </button>
                                    </div>
                                </div>
                                <div class="completion-card-content">
                                    <div class="completion-document">
                                        <label>Completion Proof</label>
                                        <a :href="completion.completion_file_url" target="_blank" class="document-link">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                            </svg>
                                            View Completion File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Verifications Section -->
                    <div class="verifications-section">
                        <div class="section-header">
                            <h2 class="section-title">Pending Verifications</h2>
                            <button @click="loadPendingVerifications" class="refresh-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="23 4 23 10 17 10"/>
                                    <polyline points="1 20 1 14 7 14"/>
                                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                                </svg>
                                Refresh
                            </button>
                        </div>

                        <div v-if="loadingVerifications" class="loading-state">
                            <div class="spinner"></div>
                            <span>Loading verifications...</span>
                        </div>

                        <div v-else-if="pendingStudents.length === 0" class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <h3>No Pending Verifications</h3>
                            <p>All student verifications have been processed.</p>
                        </div>

                        <div v-else class="verifications-list">
                            <div v-for="student in pendingStudents" :key="student.id" class="verification-card">
                                <div class="verification-card-header">
                                    <div class="student-info">
                                        <h3 class="student-name">{{ student.name }}</h3>
                                        <p class="student-email">{{ student.email }}</p>
                                        <p v-if="student.university" class="student-university">{{ student.university }}</p>
                                    </div>
                                    <div class="verification-actions">
                                        <button @click="approveStudent(student)" :disabled="processing" class="btn-approve">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                <polyline points="22 4 12 14.01 9 11.01"/>
                                            </svg>
                                            Approve
                                        </button>
                                        <button @click="rejectStudent(student)" :disabled="processing" class="btn-reject">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="15" y1="9" x2="9" y2="15"/>
                                                <line x1="9" y1="9" x2="15" y2="15"/>
                                            </svg>
                                            Reject
                                        </button>
                                    </div>
                                </div>
                                <div class="verification-card-content">
                                    <div class="documents-preview">
                                        <div class="document-item">
                                            <label>Identity Card</label>
                                            <a :href="student.identity_card_url" target="_blank" class="document-link">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                </svg>
                                                View Identity Card
                                            </a>
                                        </div>
                                        <div class="document-item">
                                            <label>Student Card</label>
                                            <a :href="student.student_card_url" target="_blank" class="document-link">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                </svg>
                                                View Student Card
                                            </a>
                                        </div>
                                        <div class="document-item" v-if="student.cv_file_url">
                                            <label>CV / Resume</label>
                                            <a :href="student.cv_file_url" target="_blank" class="document-link">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                </svg>
                                                View CV
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>

        <!-- Teleport modal to body to ensure it's on top layer -->
        <Teleport to="body">
        <div v-if="showRejectionModal" class="modal-overlay" @click.self="showRejectionModal = false">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Reject Verification</h3>
                    <button @click="showRejectionModal = false" class="modal-close">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="modal-description">
                        Please provide a reason for rejecting {{ selectedStudent?.name }}'s verification. 
                        This reason will be visible to the student so they can correct the issues.
                    </p>
                    <div class="form-group">
                        <label for="rejection-reason">Rejection Reason *</label>
                        <textarea
                            id="rejection-reason"
                            v-model="rejectionReason"
                            rows="5"
                            placeholder="Enter the reason for rejection (e.g., Identity card is not clear, Student card is expired, etc.)"
                            class="form-textarea"
                            required
                        ></textarea>
                        <small class="form-help">Maximum 1000 characters</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="showRejectionModal = false" class="btn-cancel">Cancel</button>
                    <button @click="confirmReject" :disabled="!rejectionReason.trim() || processing" class="btn-confirm-reject">
                        Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
        </Teleport>

        <!-- Completion Rejection Modal -->
        <Teleport to="body">
        <div v-if="showCompletionRejectionModal" class="modal-overlay" @click.self="showCompletionRejectionModal = false">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Reject Job Completion</h3>
                    <button @click="showCompletionRejectionModal = false" class="modal-close">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="modal-description">
                        Please provide a reason for rejecting this job completion. 
                        This reason will be visible to the student so they can resubmit if needed.
                    </p>
                    <div class="form-group">
                        <label for="completion-rejection-reason">Rejection Reason *</label>
                        <textarea
                            id="completion-rejection-reason"
                            v-model="completionRejectionReason"
                            rows="5"
                            placeholder="Enter the reason for rejection..."
                            class="form-textarea"
                            required
                        ></textarea>
                        <small class="form-help">Maximum 1000 characters</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="showCompletionRejectionModal = false" class="btn-cancel">Cancel</button>
                    <button @click="confirmCompletionReject" :disabled="!completionRejectionReason.trim() || processing" class="btn-confirm-reject">
                        Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
        </Teleport>
    </div>
</template>

<script>
import Layout from '../shared/Layout.vue';
import api from '../../api.js';
import { Teleport } from 'vue';

export default {
    name: 'AdminDashboard',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            loadingVerifications: false,
            loadingCompletions: false,
            loadingUsers: false,
            processing: false,
            stats: null,
            pendingStudents: [],
            pendingCompletions: [],
            users: null,
            userType: 'student',
            userFilter: '',
            alertMessage: null,
            alertType: 'success',
            showRejectionModal: false,
            selectedStudent: null,
            rejectionReason: '',
            showCompletionRejectionModal: false,
            selectedCompletion: null,
            completionRejectionReason: '',
        };
    },
    async mounted() {
        await Promise.all([
            this.loadStats(),
            this.loadPendingVerifications(),
            this.loadPendingCompletions(),
            this.loadUsers(),
        ]);
        this.loading = false;
    },
    methods: {
        async loadStats() {
            try {
                const response = await api.get('/admin/verifications/stats');
                this.stats = response.data;
            } catch (error) {
                console.error('Failed to load stats:', error);
                this.alertMessage = 'Failed to load statistics';
                this.alertType = 'error';
            }
        },
        switchUserType(type) {
            this.userType = type;
            this.userFilter = ''; // Reset filter when switching types
            this.loadUsers();
        },
        async loadUsers(page = 1) {
            this.loadingUsers = true;
            try {
                const params = { page };
                const endpoint = this.userType === 'student' ? '/admin/students' : '/admin/clients';
                
                if (this.userType === 'student' && this.userFilter) {
                    params.status = this.userFilter;
                }
                
                const response = await api.get(endpoint, { params });
                this.users = response.data;
            } catch (error) {
                console.error('Failed to load users:', error);
                this.alertMessage = `Failed to load ${this.userType === 'student' ? 'students' : 'clients'}`;
                this.alertType = 'error';
            } finally {
                this.loadingUsers = false;
            }
        },
        getPageFromUrl(url) {
            const match = url.match(/page=(\d+)/);
            return match ? parseInt(match[1]) : 1;
        },
        async loadPendingVerifications() {
            this.loadingVerifications = true;
            try {
                const response = await api.get('/admin/verifications/pending');
                this.pendingStudents = response.data.students || [];
            } catch (error) {
                console.error('Failed to load pending verifications:', error);
                this.alertMessage = 'Failed to load pending verifications';
                this.alertType = 'error';
            } finally {
                this.loadingVerifications = false;
            }
        },
        async approveStudent(student) {
            if (!confirm(`Approve verification for ${student.name}?`)) {
                return;
            }

            this.processing = true;
            try {
                await api.post(`/admin/students/${student.id}/approve`);
                this.alertMessage = `Successfully approved ${student.name}`;
                this.alertType = 'success';
                await Promise.all([
                    this.loadStats(),
                    this.loadPendingVerifications(),
                    this.loadUsers(),
                ]);
            } catch (error) {
                console.error('Failed to approve student:', error);
                this.alertMessage = error.response?.data?.message || 'Failed to approve student';
                this.alertType = 'error';
            } finally {
                this.processing = false;
            }
        },
        rejectStudent(student) {
            this.selectedStudent = student;
            this.rejectionReason = '';
            this.showRejectionModal = true;
        },
        async confirmReject() {
            if (!this.rejectionReason.trim()) {
                return;
            }

            this.processing = true;
            try {
                await api.post(`/admin/students/${this.selectedStudent.id}/reject`, {
                    rejection_reason: this.rejectionReason.trim(),
                });
                this.alertMessage = `Successfully rejected ${this.selectedStudent.name}`;
                this.alertType = 'success';
                this.showRejectionModal = false;
                this.selectedStudent = null;
                this.rejectionReason = '';
                await Promise.all([
                    this.loadStats(),
                    this.loadPendingVerifications(),
                    this.loadUsers(),
                ]);
            } catch (error) {
                console.error('Failed to reject student:', error);
                this.alertMessage = error.response?.data?.message || 'Failed to reject student';
                this.alertType = 'error';
            } finally {
                this.processing = false;
            }
        },
        async loadPendingCompletions() {
            this.loadingCompletions = true;
            try {
                const response = await api.get('/admin/completions/pending');
                this.pendingCompletions = response.data.completions || [];
            } catch (error) {
                console.error('Failed to load pending completions:', error);
                this.alertMessage = 'Failed to load pending completions';
                this.alertType = 'error';
            } finally {
                this.loadingCompletions = false;
            }
        },
        async approveCompletion(completion) {
            if (!confirm(`Approve completion for job: ${completion.job.title}?`)) {
                return;
            }

            this.processing = true;
            try {
                await api.post(`/admin/completions/${completion.id}/approve`);
                this.alertMessage = `Successfully approved job completion`;
                this.alertType = 'success';
                await this.loadPendingCompletions();
            } catch (error) {
                console.error('Failed to approve completion:', error);
                this.alertMessage = error.response?.data?.message || 'Failed to approve completion';
                this.alertType = 'error';
            } finally {
                this.processing = false;
            }
        },
        rejectCompletion(completion) {
            this.selectedCompletion = completion;
            this.completionRejectionReason = '';
            this.showCompletionRejectionModal = true;
        },
        async confirmCompletionReject() {
            if (!this.completionRejectionReason.trim()) {
                return;
            }

            this.processing = true;
            try {
                await api.post(`/admin/completions/${this.selectedCompletion.id}/reject`, {
                    rejection_reason: this.completionRejectionReason.trim(),
                });
                this.alertMessage = `Successfully rejected job completion`;
                this.alertType = 'success';
                this.showCompletionRejectionModal = false;
                this.selectedCompletion = null;
                this.completionRejectionReason = '';
                await this.loadPendingCompletions();
            } catch (error) {
                console.error('Failed to reject completion:', error);
                this.alertMessage = error.response?.data?.message || 'Failed to reject completion';
                this.alertType = 'error';
            } finally {
                this.processing = false;
            }
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        },
        capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ');
        },
        getStatusClass(status) {
            const classes = {
                pending: 'badge-warning',
                approved: 'badge-success',
                rejected: 'badge-danger',
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

.alert {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.alert:hover {
    transform: translateY(-2px);
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border-color: #6ee7b7;
}

.alert-error {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border-color: #fecaca;
}

.alert-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: inherit;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
    padding: 0;
    margin-left: 1rem;
}

.alert-close:hover {
    opacity: 1;
}

/* Stats Section */
.stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.stat-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.stat-icon-wrapper.pending-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-icon-wrapper.approved-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-icon-wrapper.rejected-icon {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.stat-icon-wrapper svg {
    width: 28px;
    height: 28px;
}

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
}

/* Sections */
.users-section,
.completions-section,
.verifications-section {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    flex-wrap: wrap;
    gap: 1rem;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.tabs-and-filter {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-type-tabs {
    display: flex;
    gap: 0.5rem;
    background: #f3f4f6;
    padding: 0.25rem;
    border-radius: 10px;
}

.tab-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #6b7280;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.tab-btn:hover {
    color: #111827;
    background: rgba(255, 255, 255, 0.5);
}

.tab-btn.active {
    background: white;
    color: #6366f1;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: white;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 150px;
}

.filter-select:hover {
    border-color: #d1d5db;
}

.filter-select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.refresh-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    color: #64748b;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.refresh-btn:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #111827;
}

.refresh-btn svg {
    width: 16px;
    height: 16px;
}

.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    gap: 1rem;
    color: #6b7280;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(99, 102, 241, 0.2);
    border-top-color: #6366f1;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
    color: #6b7280;
}

.empty-state svg {
    width: 64px;
    height: 64px;
    margin-bottom: 1rem;
    color: #10b981;
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.5rem 0;
}

.empty-state p {
    margin: 0;
    font-size: 0.9375rem;
}

/* Users List */
.users-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.user-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.user-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #d1d5db;
}

.user-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1.5rem;
}

.user-info {
    flex: 1;
}

.user-name {
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.5rem 0;
}

.user-email {
    font-size: 0.9375rem;
    color: #6b7280;
    margin: 0 0 0.25rem 0;
}

.user-university,
.user-id-card,
.user-role,
.user-joined {
    font-size: 0.875rem;
    color: #9ca3af;
    margin: 0.25rem 0;
}

.user-role {
    color: #6366f1;
    font-weight: 600;
}

.user-status {
    text-align: right;
    flex-shrink: 0;
}

.status-badge {
    display: inline-block;
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

.badge-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.badge-secondary {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #475569;
}

.verified-date,
.verified-by {
    font-size: 0.75rem;
    color: #9ca3af;
    margin: 0.5rem 0 0 0;
}

.rejection-reason {
    margin-top: 1rem;
    padding: 1rem;
    background: #fef2f2;
    border-radius: 8px;
    color: #991b1b;
    font-size: 0.875rem;
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

/* Completions List */
.completions-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.completion-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.completion-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #d1d5db;
}

.completion-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    gap: 1.5rem;
}

.completion-info {
    flex: 1;
}

.completion-job-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.75rem 0;
}

.completion-student-name,
.completion-client-name,
.completion-date {
    font-size: 0.9375rem;
    color: #6b7280;
    margin: 0.5rem 0;
}

.completion-student-name strong,
.completion-client-name strong,
.completion-date strong {
    color: #374151;
    font-weight: 600;
}

.completion-actions {
    display: flex;
    gap: 0.75rem;
    flex-shrink: 0;
}

.completion-card-content {
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.completion-document {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.completion-document label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

/* Verifications List */
.verifications-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.verification-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.verification-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #d1d5db;
}

.verification-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    gap: 1.5rem;
}

.student-info {
    flex: 1;
}

.student-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.5rem 0;
}

.student-email {
    font-size: 0.9375rem;
    color: #6b7280;
    margin: 0 0 0.25rem 0;
}

.student-university {
    font-size: 0.875rem;
    color: #9ca3af;
    margin: 0;
}

.verification-actions {
    display: flex;
    gap: 0.75rem;
    flex-shrink: 0;
}

.btn-approve,
.btn-reject {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-approve {
    background: #10b981;
    color: white;
}

.btn-approve:hover:not(:disabled) {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-reject {
    background: #ef4444;
    color: white;
}

.btn-reject:hover:not(:disabled) {
    background: #dc2626;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-approve:disabled,
.btn-reject:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-approve svg,
.btn-reject svg {
    width: 18px;
    height: 18px;
}

.verification-card-content {
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.documents-preview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.document-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.document-item label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.document-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #f3f4f6;
    border-radius: 8px;
    color: #6366f1;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.document-link:hover {
    background: #e5e7eb;
    color: #4f46e5;
}

.document-link svg {
    width: 18px;
    height: 18px;
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

@media (max-width: 768px) {
    .dashboard-wrapper {
        padding: 1rem;
    }

    .stats-section {
        grid-template-columns: 1fr;
    }

    .verification-card-header,
    .completion-card-header,
    .user-card-header {
        flex-direction: column;
    }

    .verification-actions,
    .completion-actions {
        width: 100%;
    }

    .btn-approve,
    .btn-reject {
        flex: 1;
        justify-content: center;
    }

    .documents-preview {
        grid-template-columns: 1fr;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-actions {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

<style>
/* Modal Styles - Global to work with Teleport */
.modal-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(0, 0, 0, 0.6) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 99999 !important;
    padding: 1rem;
    animation: fadeIn 0.2s ease-out;
    margin: 0 !important;
    border: none !important;
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
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.3);
    animation: slideUp 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #111827;
}

.modal-close svg {
    width: 20px;
    height: 20px;
}

.modal-body {
    padding: 1.5rem;
    flex: 1;
    overflow-y: auto;
}

.modal-description {
    color: #6b7280;
    font-size: 0.9375rem;
    line-height: 1.6;
    margin: 0 0 1.5rem 0;
}

.modal-footer {
    display: flex;
    gap: 0.75rem;
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    justify-content: flex-end;
}

.modal-body .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.modal-body .form-group label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
}

.form-textarea {
    width: 100%;
    padding: 0.875rem;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.9375rem;
    font-family: inherit;
    resize: vertical;
    min-height: 120px;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.form-textarea:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-help {
    font-size: 0.75rem;
    color: #6b7280;
}

.btn-cancel {
    padding: 0.625rem 1.25rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    color: #374151;
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-cancel:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-confirm-reject {
    padding: 0.625rem 1.25rem;
    background: #ef4444;
    border: none;
    border-radius: 10px;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-confirm-reject:hover:not(:disabled) {
    background: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-confirm-reject:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
