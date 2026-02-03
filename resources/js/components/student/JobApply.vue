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
                <div class="job-apply-page">
            <header class="page-header">
                <h1>Apply to {{ job.title }}</h1>
            </header>

            <div v-if="alertMessage" class="alert" :class="alertType" @click="alertMessage = null">
                {{ alertMessage }}
            </div>

            <div v-if="!hasCV" class="alert alert-warning">
                <strong>⚠️ CV Required:</strong> Please upload your CV in your profile before applying.
                <router-link to="/student/profile/edit">Upload CV →</router-link>
            </div>

            <div v-else class="alert alert-info">
                <strong>✅ Your CV is uploaded:</strong> Your CV from your profile will be automatically attached to this application.
                <router-link to="/student/profile/edit">Update CV →</router-link>
            </div>

            <form @submit.prevent="handleSubmit" class="apply-form">
                <div class="job-preview">
                    <h3>{{ job.title }}</h3>
                    <div class="job-preview-meta">
                        <span v-if="job.budget">Budget: {{ formatMoney(job.budget) }} TND</span>
                        <span v-if="job.category">{{ job.category }}</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Cover Letter (Optional)</label>
                    <textarea v-model="form.cover_letter" rows="8" maxlength="2000" placeholder="Write a cover letter explaining why you're interested in this job..."></textarea>
                    <small>{{ form.cover_letter?.length || 0 }}/2000 characters</small>
                    <span v-if="errors.cover_letter" class="error-text">{{ errors.cover_letter[0] }}</span>
                </div>

                <div class="form-actions">
                    <button type="submit" :disabled="submitting || !hasCV" class="btn btn-primary">
                        {{ submitting ? 'Submitting...' : 'Submit Application' }}
                    </button>
                    <router-link :to="`/student/jobs/${job.id}`" class="btn btn-secondary">Cancel</router-link>
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
    name: 'StudentJobApply',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            submitting: false,
            job: null,
            hasCV: false,
            form: {
                cover_letter: '',
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
                const response = await api.get(`/student/jobs/${this.$route.params.id}`);
                this.job = response.data.job;
                const user = JSON.parse(localStorage.getItem('user') || '{}');
                this.hasCV = !!user.cv_file;
            } catch (error) {
                this.alertMessage = 'Failed to load job details';
            } finally {
                this.loading = false;
            }
        },
        async handleSubmit() {
            this.submitting = true;
            this.errors = {};
            this.alertMessage = null;

            try {
                await api.post(`/student/jobs/${this.job.id}/apply`, this.form);
                this.alertMessage = 'Application submitted successfully!';
                this.alertType = 'success';
                setTimeout(() => {
                    this.$router.push('/student/applications');
                }, 1500);
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    this.alertMessage = error.response?.data?.message || 'Failed to submit application';
                }
            } finally {
                this.submitting = false;
            }
        },
        formatMoney(amount) {
            const num = Number(amount) || 0;
            return num.toFixed(2);
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

.job-apply-page {
    padding: 2rem;
    padding-bottom: 4rem;
}

.page-header {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    padding: 2.5rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(31, 106, 225, 0.3);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

.page-header h1 {
    color: white;
    margin: 0;
    position: relative;
    z-index: 1;
}

.apply-form {
    background: white;
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
}

.job-preview {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    padding: 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.job-preview h3 {
    margin: 0 0 0.5rem 0;
}

.job-preview-meta {
    display: flex;
    gap: 1rem;
    color: #64748b;
    font-size: 0.875rem;
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

.form-group textarea {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    font-family: inherit;
    resize: vertical;
    background: #fafbfc;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-group textarea:focus {
    outline: none;
    border-color: #1F6AE1;
    background: white;
    box-shadow: 0 0 0 4px rgba(31, 106, 225, 0.1);
    transform: translateY(-1px);
}

.form-group small {
    display: block;
    margin-top: 0.25rem;
    color: #64748b;
    font-size: 0.875rem;
}

.error-text {
    display: block;
    color: #EA4335;
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
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
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
    box-shadow: 0 8px 20px rgba(31, 106, 225, 0.4);
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

.alert-warning {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

.alert-info {
    background: #EAF2FF;
    color: #0B3C8A;
    border: 1px solid rgba(31, 106, 225, 0.3);
}

.alert-success {
    background: rgba(52, 168, 83, 0.1);
    color: #2d8f47;
    border: 1px solid rgba(52, 168, 83, 0.3);
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert a {
    color: inherit;
    font-weight: 600;
    text-decoration: underline;
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
    border-top-color: #1F6AE1;
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
