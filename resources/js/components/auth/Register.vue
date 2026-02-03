<template>
    <div class="register-container">
        <div class="register-panel">
            <div class="register-header">
                <router-link to="/" class="logo">
                    <div class="logo-wrapper">
                        <img src="/images/logo/logo.png" alt="UniJob" class="logo-img" />
                        <span class="logo-text">UniJob</span>
                    </div>
                </router-link>
                <h2 class="welcome-title">Create Your Account</h2>
                <p class="welcome-subtitle">Join UniJob and start your journey today</p>
            </div>

            <div class="register-form-wrapper">
                <form @submit.prevent="handleRegister" class="register-form">
                <div v-if="error" class="alert alert-error">
                    {{ error }}
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        placeholder="Enter your full name"
                        :disabled="loading"
                    />
                    <span v-if="errors.name" class="error-text">{{ errors.name[0] }}</span>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        placeholder="Enter your email"
                        :disabled="loading"
                    />
                    <span v-if="errors.email" class="error-text">{{ errors.email[0] }}</span>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select
                        id="role"
                        v-model="form.role"
                        required
                        :disabled="loading"
                    >
                        <option value="">Select your role</option>
                        <option value="student">Student</option>
                        <option value="client">Client</option>
                    </select>
                    <span v-if="errors.role" class="error-text">{{ errors.role[0] }}</span>
                </div>

                <div class="form-group">
                    <label for="identity_card_number">Identity Card Number</label>
                    <input
                        id="identity_card_number"
                        type="text"
                        v-model="form.identity_card_number"
                        maxlength="8"
                        pattern="[0-9]{8}"
                        required
                        placeholder="8 digits"
                        :disabled="loading"
                    />
                    <span v-if="errors.identity_card_number" class="error-text">{{ errors.identity_card_number[0] }}</span>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea
                        id="address"
                        v-model="form.address"
                        required
                        placeholder="Enter your address"
                        :disabled="loading"
                        rows="3"
                    ></textarea>
                    <span v-if="errors.address" class="error-text">{{ errors.address[0] }}</span>
                </div>

                <div v-show="form.role === 'student'" class="form-group">
                    <label for="university">University</label>
                    <input
                        id="university"
                        type="text"
                        v-model="form.university"
                        :required="form.role === 'student'"
                        placeholder="Enter your university"
                        :disabled="loading"
                    />
                    <span v-if="errors.university" class="error-text">{{ errors.university[0] }}</span>
                </div>

                <div class="form-group">
                    <label for="last_3_numbers">Last 3 Numbers (D17 or Bank Account)</label>
                    <input
                        id="last_3_numbers"
                        type="text"
                        v-model="form.last_3_numbers"
                        maxlength="3"
                        pattern="[0-9]{3}"
                        required
                        placeholder="Exactly 3 digits"
                        :disabled="loading"
                    />
                    <span v-if="errors.last_3_numbers" class="error-text">{{ errors.last_3_numbers[0] }}</span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-wrapper">
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            required
                            placeholder="Enter your password"
                            :disabled="loading"
                        />
                        <button
                            type="button"
                            class="password-toggle"
                            @click="showPassword = !showPassword"
                            tabindex="-1"
                        >
                            <svg v-if="showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                            <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <span v-if="errors.password" class="error-text">{{ errors.password[0] }}</span>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="password-input-wrapper">
                        <input
                            id="password_confirmation"
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            v-model="form.password_confirmation"
                            required
                            placeholder="Confirm your password"
                            :disabled="loading"
                        />
                        <button
                            type="button"
                            class="password-toggle"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                            tabindex="-1"
                        >
                            <svg v-if="showPasswordConfirmation" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                            <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                </form>
            </div>

            <div class="register-footer">
                <button type="button" @click="handleRegister" :disabled="loading" class="submit-btn">
                    <span v-if="loading" class="loading-spinner"></span>
                    {{ loading ? 'Creating Account...' : 'Create Account' }}
                </button>
                <p>
                    Already have an account?
                    <router-link to="/login" class="login-link">Sign In</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../../api.js';

export default {
    name: 'Register',
    data() {
        return {
            form: {
                name: '',
                email: '',
                role: '',
                identity_card_number: '',
                address: '',
                university: '',
                last_3_numbers: '',
                password: '',
                password_confirmation: '',
            },
            errors: {},
            loading: false,
            error: null,
            showPassword: false,
            showPasswordConfirmation: false,
        };
    },
    mounted() {
    },
    beforeUnmount() {
    },
    methods: {
        async handleRegister() {
            this.loading = true;
            this.error = null;
            this.errors = {};

            try {
                const response = await api.post('/register', this.form);
                localStorage.setItem('token', response.data.token);
                localStorage.setItem('user', JSON.stringify(response.data.user));

                if (response.data.user.role === 'student') {
                    this.$router.push('/student/dashboard');
                } else if (response.data.user.role === 'client') {
                    this.$router.push('/client/dashboard');
                }
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    this.error = error.response?.data?.message || 'Registration failed';
                }
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.register-container {
    height: 100vh;
    max-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    padding: 2rem;
    overflow: hidden;
}

.register-panel {
    width: 100%;
    max-width: 600px;
    background: #ffffff;
    border-radius: 24px;
    padding: 3rem 4rem;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 4rem);
    overflow: hidden;
}

.register-header {
    flex-shrink: 0;
}

.register-form-wrapper {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    margin: 0 -1rem;
    padding: 0 1rem;
    min-height: 0;
}

.register-form-wrapper::-webkit-scrollbar {
    width: 6px;
}

.register-form-wrapper::-webkit-scrollbar-track {
    background: transparent;
}

.register-form-wrapper::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
}

.register-form-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.15);
}


.register-header {
    text-align: center;
    margin-bottom: 2.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.logo {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2.5rem;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.2s ease;
    width: 100%;
}

.logo:hover {
    opacity: 0.8;
}

.logo-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin: 0 auto;
}

.logo-img {
    width: 80px;
    height: 80px;
    object-fit: contain;
    display: block;
    flex-shrink: 0;
}

.logo-text {
    font-size: 2rem;
    font-weight: 700;
    color: #0B3C8A;
    letter-spacing: -0.5px;
    font-family: 'Poppins', sans-serif;
}

.welcome-title {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 3rem;
    font-weight: 700;
    color: #000000;
    margin: 0 auto 1rem auto;
    line-height: 1.2;
    text-align: center;
    width: 100%;
}

.welcome-subtitle {
    font-size: 1.0625rem;
    color: #6b7280;
    margin: 0 auto;
    text-align: center;
    width: 100%;
}

.register-form {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
    padding-bottom: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.form-group label {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #000000;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 1.125rem 1.25rem;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    font-size: 1rem;
    font-family: inherit;
    background: #EAF2FF;
    color: #000000;
    transition: all 0.2s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1F6AE1;
    background: #FFFFFF;
    box-shadow: 0 0 0 3px rgba(31, 106, 225, 0.1);
}

.form-group input:disabled,
.form-group select:disabled,
.form-group textarea:disabled {
    background: #f9fafb;
    cursor: not-allowed;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.password-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input-wrapper input {
    padding-right: 3rem;
}

.password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    transition: color 0.2s;
}

.password-toggle:hover {
    color: #1F6AE1;
}

.password-toggle svg {
    width: 20px;
    height: 20px;
}

.error-text {
    font-size: 0.75rem;
    color: #EA4335;
    margin-top: -0.25rem;
}

.alert {
    padding: 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 500;
}

.alert-error {
    background: rgba(234, 67, 53, 0.1);
    color: #EA4335;
    border: 1px solid rgba(234, 67, 53, 0.3);
}

.submit-btn {
    width: 100%;
    padding: 1.125rem 1.5rem;
    background: #1F6AE1;
    color: #FFFFFF;
    border: none;
    border-radius: 12px;
    font-size: 1.0625rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.submit-btn:hover:not(:disabled) {
    background: #2563EB;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.loading-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.register-footer {
    margin-top: 2rem;
    padding-top: 1.5rem;
    text-align: center;
    flex-shrink: 0;
}

.register-footer p {
    font-size: 0.9375rem;
    color: #000000;
    margin-top: 1rem;
}

.login-link {
    color: #1F6AE1;
    text-decoration: underline;
    font-weight: 600;
    margin-left: 0.25rem;
    transition: color 0.2s ease;
}

.login-link:hover {
    color: #2563EB;
}

/* Responsive */
@media (max-width: 1280px) {
    .register-panel {
        max-width: 550px;
        padding: 3.5rem 4rem;
    }
}

@media (max-width: 1024px) {
    .register-panel {
        max-width: 500px;
        padding: 3rem;
    }
    
    .welcome-title {
        font-size: 2.5rem;
    }
    
    .logo-text {
        font-size: 1.75rem;
    }
    
    .logo-img {
        width: 70px;
        height: 70px;
    }
}

@media (max-width: 768px) {
    .register-container {
        padding: 1.5rem;
    }
    
    .register-panel {
        max-width: 100%;
        padding: 2.5rem;
        border-radius: 20px;
    }
    
    .welcome-title {
        font-size: 2.25rem;
        margin-bottom: 0.875rem;
    }
    
    .welcome-subtitle {
        font-size: 1rem;
    }
    
    .logo {
        margin-bottom: 2rem;
    }
    
    .logo-img {
        width: 64px;
        height: 64px;
    }
    
    .logo-text {
        font-size: 1.625rem;
    }
    
    .register-header {
        margin-bottom: 3rem;
    }
    
    .register-form {
        gap: 1.75rem;
    }
}

@media (max-width: 640px) {
    .register-panel {
        padding: 2rem;
    }

    .welcome-title {
        font-size: 2rem;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 1rem 1.125rem;
        font-size: 0.9375rem;
    }
    
    .submit-btn {
        padding: 1rem 1.25rem;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .register-panel {
        padding: 1.75rem;
    }
    
    .welcome-title {
        font-size: 1.75rem;
    }
    
    .logo-img {
        width: 56px;
        height: 56px;
    }
    
    .logo-text {
        font-size: 1.5rem;
    }
    
    .register-header {
        margin-bottom: 2.5rem;
    }
}
</style>
