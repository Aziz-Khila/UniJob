<template>
    <Layout>
        <div v-if="loading && !user.id" class="loading">Loading...</div>
        <div v-else class="dashboard-wrapper">
            <!-- Animated Background Orbs -->
            <div class="dashboard-orbs">
                <div class="dashboard-orb orb-1"></div>
                <div class="dashboard-orb orb-2"></div>
                <div class="dashboard-orb orb-3"></div>
                <div class="dashboard-orb orb-4"></div>
            </div>
            
            <div class="dashboard-main">
                <div class="profile-edit-page">
            <div v-if="alertMessage" class="alert" :class="alertType" @click="alertMessage = null">
                <span>{{ alertMessage }}</span>
                <button class="alert-close">×</button>
            </div>

            <div class="page-header-section">
                <button @click="$router.push('/student/profile')" class="back-button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back to Profile
                </button>
                <h1 class="page-title">Edit Profile</h1>
            </div>

            <form @submit.prevent="handleSubmit" class="profile-edit-form" enctype="multipart/form-data">
                <!-- Basic Information Card -->
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h2 class="form-card-title">Basic Information</h2>
                    </div>
                    <div class="form-card-content">
                        <div class="form-row">
                            <div class="form-group">
                                <label>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    Full Name *
                                </label>
                                <input v-model="form.name" type="text" required placeholder="Enter your full name" />
                                <span v-if="errors.name" class="error-text">{{ errors.name[0] }}</span>
                            </div>

                            <div class="form-group">
                                <label>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                    Email *
                                </label>
                                <input v-model="form.email" type="email" required placeholder="your.email@example.com" />
                                <span v-if="errors.email" class="error-text">{{ errors.email[0] }}</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    Address
                                </label>
                                <textarea v-model="form.address" placeholder="Enter your address" rows="3"></textarea>
                                <span v-if="errors.address" class="error-text">{{ errors.address[0] }}</span>
                            </div>

                            <div class="form-group">
                                <label>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                        <path d="M6 12v5c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2v-5"/>
                                    </svg>
                                    University
                                </label>
                                <input v-model="form.university" type="text" placeholder="Enter your university" />
                                <span v-if="errors.university" class="error-text">{{ errors.university[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Picture Card -->
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon photo-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </div>
                        <h2 class="form-card-title">Profile Picture</h2>
                    </div>
                    <div class="form-card-content">
                        <div class="file-upload-group">
                            <label class="file-upload-label">
                                <input type="file" @change="handleFileChange($event, 'profile_picture')" accept="image/*" class="file-input" />
                                <div class="file-upload-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                    <span>Choose an image</span>
                                    <small>JPEG, PNG, JPG, GIF. Max size: 2MB</small>
                                </div>
                            </label>
                            <div v-if="user.profile_picture || files.profile_picture" class="file-preview">
                                <div v-if="files.profile_picture" class="preview-image">
                                    <img :src="getFilePreview(files.profile_picture)" alt="Preview" />
                                    <span class="preview-label">New</span>
                                </div>
                                <div v-else-if="user.profile_picture" class="preview-image">
                                    <img :src="getImageUrl(user.profile_picture)" alt="Current" />
                                    <span class="preview-label">Current</span>
                                </div>
                            </div>
                            <span v-if="errors.profile_picture" class="error-text">{{ errors.profile_picture[0] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Documents Card -->
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon documents-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                        </div>
                        <h2 class="form-card-title">Documents</h2>
                    </div>
                    <div class="form-card-content">
                        <div class="documents-grid">
                            <!-- CV File -->
                            <div class="file-upload-item">
                                <div class="file-upload-header">
                                    <div class="file-upload-icon cv-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="file-upload-name">CV / Resume</div>
                                        <div class="file-upload-hint">PDF, DOC, DOCX. Max size: 5MB</div>
                                    </div>
                                </div>
                                <label class="file-upload-label-small">
                                    <input type="file" @change="handleFileChange($event, 'cv_file')" accept=".pdf,.doc,.docx" class="file-input" />
                                    <span class="file-upload-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                            <line x1="12" y1="3" x2="12" y2="15"/>
                                        </svg>
                                        {{ files.cv_file ? 'Change File' : 'Upload File' }}
                                    </span>
                                </label>
                                <div v-if="user.cv_file || files.cv_file" class="current-file-info">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    <span v-if="files.cv_file">{{ files.cv_file.name }}</span>
                                    <a v-else :href="getImageUrl(user.cv_file)" target="_blank">View current file</a>
                                </div>
                                <span v-if="errors.cv_file" class="error-text">{{ errors.cv_file[0] }}</span>
                            </div>

                            <!-- Identity Card -->
                            <div class="file-upload-item">
                                <div class="file-upload-header">
                                    <div class="file-upload-icon identity-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                            <line x1="7" y1="8" x2="17" y2="8"/>
                                            <line x1="7" y1="12" x2="17" y2="12"/>
                                            <line x1="7" y1="16" x2="12" y2="16"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="file-upload-name">Identity Card</div>
                                        <div class="file-upload-hint">PDF, DOC, DOCX, JPEG, PNG, JPG. Max size: 5MB</div>
                                    </div>
                                </div>
                                <label class="file-upload-label-small">
                                    <input type="file" @change="handleFileChange($event, 'identity_card')" accept=".pdf,.doc,.docx,.jpeg,.png,.jpg" class="file-input" />
                                    <span class="file-upload-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                            <line x1="12" y1="3" x2="12" y2="15"/>
                                        </svg>
                                        {{ files.identity_card ? 'Change File' : 'Upload File' }}
                                    </span>
                                </label>
                                <div v-if="user.identity_card || files.identity_card" class="current-file-info">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    <span v-if="files.identity_card">{{ files.identity_card.name }}</span>
                                    <a v-else :href="getImageUrl(user.identity_card)" target="_blank">View current file</a>
                                </div>
                                <span v-if="errors.identity_card" class="error-text">{{ errors.identity_card[0] }}</span>
                            </div>

                            <!-- Student Card -->
                            <div class="file-upload-item">
                                <div class="file-upload-header">
                                    <div class="file-upload-icon student-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                            <path d="M6 12v5c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2v-5"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="file-upload-name">Student Card</div>
                                        <div class="file-upload-hint">PDF, DOC, DOCX, JPEG, PNG, JPG. Max size: 5MB</div>
                                    </div>
                                </div>
                                <label class="file-upload-label-small">
                                    <input type="file" @change="handleFileChange($event, 'student_card')" accept=".pdf,.doc,.docx,.jpeg,.png,.jpg" class="file-input" />
                                    <span class="file-upload-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                            <line x1="12" y1="3" x2="12" y2="15"/>
                                        </svg>
                                        {{ files.student_card ? 'Change File' : 'Upload File' }}
                                    </span>
                                </label>
                                <div v-if="user.student_card || files.student_card" class="current-file-info">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    <span v-if="files.student_card">{{ files.student_card.name }}</span>
                                    <a v-else :href="getImageUrl(user.student_card)" target="_blank">View current file</a>
                                </div>
                                <span v-if="errors.student_card" class="error-text">{{ errors.student_card[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verification Status Card -->
                <div class="form-card" v-if="user.identity_card && user.student_card">
                    <div class="form-card-header">
                        <div class="form-card-icon verification-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <h2 class="form-card-title">Verification Status</h2>
                    </div>
                    <div class="form-card-content">
                        <div class="verification-status-box" :class="getVerificationStatusClass()">
                            <div class="verification-status-icon">
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
                            </div>
                            <div class="verification-status-info">
                                <div class="verification-status-title">{{ getVerificationStatusText() }}</div>
                                <div class="verification-status-description">{{ getVerificationStatusDescription() }}</div>
                            </div>
                        </div>
                        <div v-if="user.verification_status === 'pending'" class="verification-note">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="16" x2="12" y2="12"/>
                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                            </svg>
                            <span>Your documents are under review. You'll be notified once the verification is complete.</span>
                        </div>
                        <div v-if="user.verification_status === 'rejected' && user.rejection_reason" class="rejection-reason-box">
                            <div class="rejection-reason-header">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                                <span class="rejection-reason-label">Rejection Reason</span>
                            </div>
                            <p class="rejection-reason-text">{{ user.rejection_reason }}</p>
                            <p class="rejection-reason-hint">Please correct the issues mentioned above and upload new documents to resubmit for verification.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" :disabled="submitting" class="submit-btn">
                        <svg v-if="!submitting" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        <span v-if="submitting" class="spinner"></span>
                        {{ submitting ? 'Saving...' : 'Save Changes' }}
                    </button>
                    <router-link to="/student/profile" class="cancel-btn">Cancel</router-link>
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
    name: 'StudentProfileEdit',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            submitting: false,
            user: {},
            form: {
                name: '',
                email: '',
                address: '',
                university: '',
            },
            files: {
                profile_picture: null,
                cv_file: null,
                identity_card: null,
                student_card: null,
            },
            errors: {},
            alertMessage: null,
            alertType: 'success',
        };
    },
    async mounted() {
        await this.loadProfile();
    },
    methods: {
        async loadProfile() {
            try {
                const response = await api.get('/student/profile');
                this.user = response.data;
                this.form = {
                    name: response.data.name || '',
                    email: response.data.email || '',
                    address: response.data.address || '',
                    university: response.data.university || '',
                };
            } catch (error) {
                this.alertMessage = 'Failed to load profile';
                this.alertType = 'error';
            } finally {
                this.loading = false;
            }
        },
        handleFileChange(event, field) {
            this.files[field] = event.target.files[0];
        },
        async handleSubmit() {
            this.submitting = true;
            this.errors = {};
            this.alertMessage = null;

            // Validate required fields client-side first
            if (!this.form.name || !this.form.name.trim()) {
                this.errors.name = ['The name field is required.'];
                this.alertMessage = 'The name field is required.';
                this.alertType = 'error';
                this.submitting = false;
                return;
            }

            if (!this.form.email || !this.form.email.trim()) {
                this.errors.email = ['The email field is required.'];
                this.alertMessage = 'The email field is required.';
                this.alertType = 'error';
                this.submitting = false;
                return;
            }

            const formData = new FormData();
            formData.append('name', this.form.name.trim());
            formData.append('email', this.form.email.trim());
            // Only append address and university if they have values, otherwise skip them (Laravel will treat as null)
            if (this.form.address && this.form.address.trim()) {
                formData.append('address', this.form.address.trim());
            }
            if (this.form.university && this.form.university.trim()) {
                formData.append('university', this.form.university.trim());
            }

            Object.keys(this.files).forEach(key => {
                if (this.files[key]) {
                    formData.append(key, this.files[key]);
                }
            });

            // Debug: Log form data
            console.log('Form data:', {
                name: this.form.name,
                email: this.form.email,
                address: this.form.address,
                university: this.form.university,
            });
            
            // Debug: Log FormData contents
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
            }

            try {
                // Use POST instead of PUT because FormData works better with POST
                const response = await api.post('/student/profile', formData);
                localStorage.setItem('user', JSON.stringify(response.data.user));
                this.user = response.data.user;
                this.alertMessage = 'Profile updated successfully!';
                this.alertType = 'success';
                setTimeout(() => {
                    this.$router.push('/student/profile');
                }, 1500);
            } catch (error) {
                console.error('Profile update error:', error.response?.data || error.message);
                console.error('Full error response:', JSON.stringify(error.response?.data, null, 2));
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                    // Show a general error message if there are validation errors
                    const firstError = Object.values(error.response.data.errors || {})[0];
                    if (firstError) {
                        this.alertMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                        this.alertType = 'error';
                    } else {
                        this.alertMessage = error.response?.data?.message || 'Validation error. Please check the form fields.';
                        this.alertType = 'error';
                    }
                } else {
                    this.alertMessage = error.response?.data?.message || 'Failed to update profile';
                    this.alertType = 'error';
                }
            } finally {
                this.submitting = false;
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
        getFilePreview(file) {
            if (!file) return null;
            return URL.createObjectURL(file);
        },
        getVerificationStatusClass() {
            if (this.user.verification_status === 'approved') {
                return 'status-approved';
            } else if (this.user.verification_status === 'rejected') {
                return 'status-rejected';
            }
            return 'status-pending';
        },
        getVerificationStatusText() {
            if (this.user.verification_status === 'approved') {
                return 'Verified';
            } else if (this.user.verification_status === 'rejected') {
                return 'Verification Rejected';
            }
            return 'Pending Verification';
        },
        getVerificationStatusDescription() {
            if (this.user.verification_status === 'approved') {
                return 'Your account has been verified by an administrator.';
            } else if (this.user.verification_status === 'rejected') {
                return this.user.rejection_reason 
                    ? 'Your verification request was rejected. Please review the reason below and correct the issues.'
                    : 'Your verification request was rejected. Please upload new documents and try again.';
            }
            return 'Waiting for administrator review.';
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

.profile-edit-page {
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

.page-header-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
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

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.profile-edit-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* Form Card */
.form-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.form-card:hover {
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.form-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.form-card-icon {
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

.form-card-icon.photo-icon {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
}

.form-card-icon.documents-icon {
    background: linear-gradient(135deg, #34A853 0%, #2d8f47 100%);
}

.form-card-icon.verification-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.form-card-icon svg {
    width: 24px;
    height: 24px;
}

.form-card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.form-card-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #374151;
    font-size: 0.9375rem;
}

.form-group label svg {
    width: 18px;
    height: 18px;
    color: #6b7280;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 1.5px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    background: white;
    font-family: inherit;
    color: #111827;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1F6AE1;
    box-shadow: 0 0 0 3px rgba(31, 106, 225, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.error-text {
    display: block;
    color: #EA4335;
    font-size: 0.875rem;
    font-weight: 500;
}

/* File Upload */
.file-upload-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.file-upload-label {
    cursor: pointer;
}

.file-input {
    display: none;
}

.file-upload-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem;
    border: 2px dashed #d1d5db;
    border-radius: 16px;
    background: #f9fafb;
    transition: all 0.2s ease;
    cursor: pointer;
    gap: 1rem;
}

.file-upload-box:hover {
    border-color: #1F6AE1;
    background: #eff6ff;
}

.file-upload-box svg {
    width: 48px;
    height: 48px;
    color: #6b7280;
}

.file-upload-box span {
    font-weight: 600;
    color: #374151;
}

.file-upload-box small {
    font-size: 0.875rem;
    color: #6b7280;
}

.file-preview {
    display: flex;
    gap: 1rem;
}

.preview-image {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #e5e7eb;
}

.preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 0.25rem;
    text-align: center;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Documents Grid */
.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.file-upload-item {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.file-upload-item:hover {
    border-color: #d1d5db;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.file-upload-header {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.file-upload-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.file-upload-icon.cv-icon {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
}

.file-upload-icon.identity-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.file-upload-icon.student-icon {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
}

.file-upload-icon svg {
    width: 24px;
    height: 24px;
}

.file-upload-name {
    font-weight: 600;
    color: #111827;
    font-size: 0.9375rem;
    margin-bottom: 0.25rem;
}

.file-upload-hint {
    font-size: 0.8125rem;
    color: #6b7280;
}

.file-upload-label-small {
    cursor: pointer;
}

.file-upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: #f3f4f6;
    color: #374151;
    border-radius: 10px;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.file-upload-btn:hover {
    background: #1F6AE1;
    color: white;
}

.file-upload-btn svg {
    width: 16px;
    height: 16px;
}

.current-file-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
    font-size: 0.875rem;
    color: #6b7280;
}

.current-file-info svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

.current-file-info a {
    color: #1F6AE1;
    text-decoration: none;
    font-weight: 500;
}

.current-file-info a:hover {
    text-decoration: underline;
}

/* Verification Status */
.verification-status-box {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 16px;
    border: 2px solid;
    transition: all 0.3s ease;
}

.verification-status-box.status-approved {
    background: rgba(52, 168, 83, 0.1);
    border-color: #34A853;
}

.verification-status-box.status-rejected {
    background: rgba(239, 68, 68, 0.1);
    border-color: #EA4335;
}

.verification-status-box.status-pending {
    background: rgba(245, 158, 11, 0.1);
    border-color: #f59e0b;
}

.verification-status-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.verification-status-box.status-approved .verification-status-icon {
    background: #34A853;
    color: white;
}

.verification-status-box.status-rejected .verification-status-icon {
    background: #EA4335;
    color: white;
}

.verification-status-box.status-pending .verification-status-icon {
    background: #f59e0b;
    color: white;
}

.verification-status-icon svg {
    width: 24px;
    height: 24px;
}

.verification-status-info {
    flex: 1;
}

.verification-status-title {
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.verification-status-box.status-approved .verification-status-title {
    color: #059669;
}

.verification-status-box.status-rejected .verification-status-title {
    color: #dc2626;
}

.verification-status-box.status-pending .verification-status-title {
    color: #d97706;
}

.verification-status-description {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.5;
}

.verification-note {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: rgba(31, 106, 225, 0.1);
    border-radius: 12px;
    border-left: 4px solid #1F6AE1;
    margin-top: 1rem;
}

.verification-note svg {
    width: 20px;
    height: 20px;
    color: #1F6AE1;
    flex-shrink: 0;
    margin-top: 2px;
}

.verification-note span {
    font-size: 0.875rem;
    color: #374151;
    line-height: 1.6;
}

.rejection-reason-box {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: rgba(239, 68, 68, 0.1);
    border-radius: 12px;
    border-left: 4px solid #EA4335;
}

.rejection-reason-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.rejection-reason-header svg {
    width: 20px;
    height: 20px;
    color: #dc2626;
    flex-shrink: 0;
}

.rejection-reason-label {
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

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 1rem;
}

.submit-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 2rem;
    background: #1F6AE1;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.submit-btn:hover:not(:disabled) {
    background: #0B3C8A;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.submit-btn svg {
    width: 18px;
    height: 18px;
}

.spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

.cancel-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.875rem 2rem;
    background: white;
    color: #374151;
    border: 1.5px solid #e5e7eb;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    text-decoration: none;
    transition: all 0.2s ease;
}

.cancel-btn:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #111827;
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
    .profile-edit-page {
        padding: 1.5rem;
        padding-bottom: 4rem;
    }

    .page-header-section {
        flex-direction: column;
        align-items: flex-start;
    }

    .back-button {
        width: 100%;
        justify-content: center;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .documents-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .submit-btn,
    .cancel-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
