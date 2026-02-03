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
                <div class="job-edit-page">
                    <router-link :to="job ? `/client/jobs/${job.id}` : '/client/jobs'" class="back-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        Back to Job
                    </router-link>
                    <!-- Dashboard Header -->
                    <div class="dashboard-header">
                        <div class="header-content">
                            <div class="header-text">
                                <h1 class="header-title">Edit Job</h1>
                                <p class="header-subtitle">Update your job posting details</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="alertMessage" class="alert" :class="alertType" @click="alertMessage = null">
                        {{ alertMessage }}
                    </div>

                    <form @submit.prevent="handleSubmit" class="job-form">
                        <div class="form-group">
                            <label>Job Title *</label>
                            <input v-model="form.title" type="text" required />
                            <span v-if="errors.title" class="error-text">{{ errors.title[0] }}</span>
                        </div>

                        <div class="form-group">
                            <label>Job Description *</label>
                            <textarea v-model="form.description" required rows="8"></textarea>
                            <span v-if="errors.description" class="error-text">{{ errors.description[0] }}</span>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <select v-model="form.category">
                                <option value="">Select Category</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Design & Multimedia">Design & Multimedia</option>
                                <option value="Marketing & Communication">Marketing & Communication</option>
                                <option value="Commerce, Management & Finance">Commerce, Management & Finance</option>
                                <option value="Sales & Customer Relations">Sales & Customer Relations</option>
                                <option value="Education & Educational Services">Education & Educational Services</option>
                                <option value="Languages & Translation">Languages & Translation</option>
                                <option value="Event Management">Event Management</option>
                                <option value="Tourism & Hospitality">Tourism & Hospitality</option>
                                <option value="Technical & Practical Services">Technical & Practical Services</option>
                                <option value="Health & Social Services">Health & Social Services</option>
                                <option value="Personal Services">Personal Services</option>
                                <option value="Logistics & Mobility">Logistics & Mobility</option>
                            </select>
                            <span v-if="errors.category" class="error-text">{{ errors.category[0] }}</span>
                        </div>

                        <div class="form-group">
                            <label>Budget</label>
                            <input v-model.number="form.budget" type="number" step="0.01" min="0" />
                            <span v-if="errors.budget" class="error-text">{{ errors.budget[0] }}</span>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Number of Workers Needed *</label>
                                <input v-model.number="form.workers_needed" type="number" min="1" required />
                                <small>How many students do you want to hire for this job?</small>
                                <span v-if="errors.workers_needed" class="error-text">{{ errors.workers_needed[0] }}</span>
                            </div>
                            <div class="form-group">
                                <label>Work Type *</label>
                                <select v-model="form.work_type" required>
                                    <option value="remote">Remote</option>
                                    <option value="on-site">On-Site</option>
                                    <option value="hybrid">Hybrid</option>
                                </select>
                                <span v-if="errors.work_type" class="error-text">{{ errors.work_type[0] }}</span>
                            </div>
                        </div>

                        <div class="date-row">
                            <div class="form-group">
                                <label>Start Day (Optional)</label>
                                <input v-model="form.start_day" type="date" />
                                <span v-if="errors.start_day" class="error-text">{{ errors.start_day[0] }}</span>
                            </div>
                            <div class="form-group">
                                <label>End Day (Optional)</label>
                                <input v-model="form.end_day" type="date" :min="form.start_day || ''" />
                                <span v-if="errors.end_day" class="error-text">{{ errors.end_day[0] }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status *</label>
                            <select v-model="form.status" required>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <span v-if="errors.status" class="error-text">{{ errors.status[0] }}</span>
                        </div>

                        <div class="form-actions">
                            <button type="submit" :disabled="submitting" class="btn btn-primary">
                                {{ submitting ? 'Updating...' : 'Update Job' }}
                            </button>
                            <router-link :to="`/client/jobs/${job.id}`" class="btn btn-secondary">Cancel</router-link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from '../shared/Layout.vue';
import api from '../../api.js';

export default {
    name: 'ClientJobEdit',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            submitting: false,
            job: null,
            form: {
                title: '',
                description: '',
                category: '',
                budget: null,
                start_day: '',
                end_day: '',
                status: 'open',
                workers_needed: 1,
                work_type: 'remote',
            },
            errors: {},
            alertMessage: null,
            alertType: 'error',
        };
    },
    async mounted() {
        await this.loadJob();
    },
    methods: {
        async loadJob() {
            try {
                const response = await api.get(`/client/jobs/${this.$route.params.id}`);
                this.job = response.data.job;
                this.form = {
                    title: this.job.title || '',
                    description: this.job.description || '',
                    category: this.job.category || '',
                    budget: this.job.budget || null,
                    start_day: this.job.start_day ? this.formatDateForInput(this.job.start_day) : '',
                    end_day: this.job.end_day ? this.formatDateForInput(this.job.end_day) : '',
                    status: this.job.status || 'open',
                    workers_needed: this.job.workers_needed || 1,
                    work_type: this.job.work_type || 'remote',
                };
            } catch (error) {
                this.alertMessage = 'Failed to load job';
            } finally {
                this.loading = false;
            }
        },
        async handleSubmit() {
            this.submitting = true;
            this.errors = {};
            this.alertMessage = null;

            try {
                const response = await api.put(`/client/jobs/${this.job.id}`, this.form);
                this.alertMessage = 'Job updated successfully!';
                this.alertType = 'success';
                setTimeout(() => {
                    this.$router.push(`/client/jobs/${this.job.id}`);
                }, 1500);
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    this.alertMessage = error.response?.data?.message || 'Failed to update job';
                }
            } finally {
                this.submitting = false;
            }
        },
        formatDateForInput(date) {
            const d = new Date(date);
            return d.toISOString().split('T')[0];
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

.job-edit-page {
    padding: 2rem;
    padding-bottom: 4rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.job-edit-page .back-button {
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

.job-form {
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

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #0f172a;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #fafbfc;
    font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.form-group textarea {
    resize: vertical;
    font-family: inherit;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group small {
    display: block;
    margin-top: 0.25rem;
    color: #64748b;
    font-size: 0.875rem;
}

.date-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.error-text {
    display: block;
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-primary:hover:not(:disabled)::before {
    width: 400px;
    height: 400px;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary {
    background: #f1f5f9;
    color: #0f172a;
    border: 1px solid #e2e8f0;
}

.btn-secondary:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    cursor: pointer;
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
</style>
