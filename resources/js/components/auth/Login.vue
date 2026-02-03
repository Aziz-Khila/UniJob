<template>
    <div class="login-container">
        <div class="login-panel">
            <div class="login-header">
                <router-link to="/" class="logo">
                    <div class="logo-wrapper">
                        <img src="/images/logo/logo.png" alt="UniJob" class="logo-img" />
                        <span class="logo-text">UniJob</span>
                    </div>
                </router-link>
                <h2 class="welcome-title">Welcome Back</h2>
                <p class="welcome-subtitle">Enter your email and password to access your account</p>
            </div>

            <form @submit.prevent="handleLogin" class="login-form">
                <div v-if="errorMessage" class="alert alert-error">
                    {{ errorMessage }}
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
                            <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" v-model="rememberMe" />
                        <span>Remember me</span>
                    </label>
                    <a href="#" @click.prevent class="forgot-password">Forgot Password</a>
                </div>

                <button type="submit" class="submit-btn" :disabled="loading">
                    <span v-if="!loading">Sign In</span>
                    <span v-else class="loading-text">
                        <span class="spinner"></span>
                        Signing in...
                    </span>
                </button>
            </form>

            <div class="login-footer">
                <p>
                    Don't have an account?
                    <router-link to="/register" class="signup-link">Sign Up</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../../api.js';

export default {
    name: 'Login',
    data() {
        return {
            form: {
                email: '',
                password: '',
            },
            loading: false,
            errorMessage: null,
            showPassword: false,
            rememberMe: false,
        };
    },
    mounted() {
    },
    beforeUnmount() {
    },
    methods: {
        generatePath(numPoints, maxX, maxY) {
            const path = [];
            for (let i = 0; i < numPoints; i++) {
                path.push({
                    x: (Math.random() - 0.5) * maxX * 2,
                    y: (Math.random() - 0.5) * maxY * 2
                });
            }
            // Close the loop by adding the first point at the end
            path.push(path[0]);
            return path;
        },
        getPositionOnPath(path, progress) {
            // progress is 0-1, loops continuously
            const normalizedProgress = progress % 1;
            const totalSegments = path.length - 1;
            const segmentProgress = normalizedProgress * totalSegments;
            const segmentIndex = Math.floor(segmentProgress);
            const segmentT = segmentProgress - segmentIndex;
            
            const start = path[segmentIndex];
            const end = path[segmentIndex + 1];
            
            // Linear interpolation between waypoints
            return {
                x: start.x + (end.x - start.x) * segmentT,
                y: start.y + (end.y - start.y) * segmentT
            };
        },
        initPaths() {
            const panelElement = this.$el?.querySelector('.login-left-panel');
            if (!panelElement) return;
            
            const rect = panelElement.getBoundingClientRect();
            const maxX = rect.width * 0.7;
            const maxY = rect.height * 0.7;
            
            // Generate random paths with 6-8 waypoints each
            this.orb1Path = this.generatePath(6 + Math.floor(Math.random() * 3), maxX, maxY);
            this.orb2Path = this.generatePath(6 + Math.floor(Math.random() * 3), maxX, maxY);
            this.orb3Path = this.generatePath(6 + Math.floor(Math.random() * 3), maxX, maxY);
            
            // Start each orb at a random point in its path
            this.orb1Progress = Math.random();
            this.orb2Progress = Math.random();
            this.orb3Progress = Math.random();
        },
        startOrbAnimation() {
            const animate = () => {
                if (!this.panelEntered) {
                    this.animationFrameId = requestAnimationFrame(animate);
                    return;
                }
                
                // Constant speed: increment progress at a steady rate
                const speed1 = 0.00015; // Slow constant speed
                const speed2 = 0.00012;
                const speed3 = 0.0001;
                
                // Update progress (loops automatically via modulo)
                this.orb1Progress = (this.orb1Progress + speed1) % 1;
                this.orb2Progress = (this.orb2Progress + speed2) % 1;
                this.orb3Progress = (this.orb3Progress + speed3) % 1;
                
                // Get current position on path
                const pos1 = this.getPositionOnPath(this.orb1Path, this.orb1Progress);
                const pos2 = this.getPositionOnPath(this.orb2Path, this.orb2Progress);
                const pos3 = this.getPositionOnPath(this.orb3Path, this.orb3Progress);
                
                // Update positions (smooth interpolation for rendering)
                this.orb1Position.x += (pos1.x - this.orb1Position.x) * 0.1;
                this.orb1Position.y += (pos1.y - this.orb1Position.y) * 0.1;
                
                this.orb2Position.x += (pos2.x - this.orb2Position.x) * 0.1;
                this.orb2Position.y += (pos2.y - this.orb2Position.y) * 0.1;
                
                this.orb3Position.x += (pos3.x - this.orb3Position.x) * 0.1;
                this.orb3Position.y += (pos3.y - this.orb3Position.y) * 0.1;
                
                this.animationFrameId = requestAnimationFrame(animate);
            };
            animate();
        },
        async handleLogin() {
            this.errorMessage = null;
            this.loading = true;

            try {
                const response = await api.post('/login', this.form);
                const { token, user } = response.data;

                localStorage.setItem('token', token);
                localStorage.setItem('user', JSON.stringify(user));

                if (this.rememberMe) {
                    localStorage.setItem('rememberMe', 'true');
                }

                // Redirect based on user role
                if (user.role === 'student') {
                    this.$router.push('/student/dashboard');
                } else if (user.role === 'client') {
                    this.$router.push('/client/dashboard');
                } else if (user.role === 'admin') {
                    this.$router.push('/admin/dashboard');
                } else {
                    this.$router.push('/');
                }
            } catch (error) {
                // Clear any existing token/user data on failed login
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                
                // Show error message and stay on login page
                this.errorMessage = error.response?.data?.message || 'Login failed. Please check your credentials.';
                
                // Prevent any navigation - stay on login page
                return false;
            } finally {
                this.loading = false;
            }
        },
        handleGoogleLogin() {
            // Implement Google OAuth if needed
            console.log('Google login clicked');
        },
    },
};
</script>

<style scoped>
.login-container {
    height: 100vh;
    max-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    padding: 2rem;
    overflow: hidden;
}

.login-panel {
    width: 100%;
    max-width: 600px;
    background: #ffffff;
    border-radius: 24px;
    padding: 3rem 4rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    max-height: calc(100vh - 4rem);
    overflow-y: auto;
}

.login-header {
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

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
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

.form-group input {
    width: 100%;
    padding: 1.125rem 1.25rem;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: #EAF2FF;
    color: #000000;
}

.form-group input:focus {
    outline: none;
    border-color: #1F6AE1;
    background: #FFFFFF;
    box-shadow: 0 0 0 3px rgba(31, 106, 225, 0.1);
}

.password-input-wrapper {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle svg {
    width: 20px;
    height: 20px;
}

.password-toggle:hover {
    color: #1F6AE1;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9375rem;
    margin-top: 0.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    color: #000000;
}

.checkbox-label input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #1F6AE1;
}

.forgot-password {
    color: #1F6AE1;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.forgot-password:hover {
    color: #2563EB;
    text-decoration: underline;
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
    margin-top: 0.75rem;
}

.submit-btn:hover:not(:disabled) {
    background: #2563EB;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.loading-text {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
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

.login-footer {
    margin-top: 2rem;
    padding-top: 1.5rem;
    text-align: center;
    font-size: 0.9375rem;
    color: #000000;
}

.signup-link {
    color: #1F6AE1;
    text-decoration: underline;
    font-weight: 600;
    margin-left: 0.25rem;
    transition: color 0.2s ease;
}

.signup-link:hover {
    color: #2563EB;
}

/* Responsive */
@media (max-width: 1280px) {
    .login-panel {
        max-width: 550px;
        padding: 3.5rem 4rem;
    }
}

@media (max-width: 1024px) {
    .login-panel {
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
    .login-container {
        padding: 1.5rem;
    }
    
    .login-panel {
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
    
    .login-header {
        margin-bottom: 3rem;
    }
    
    .login-form {
        gap: 1.75rem;
    }
}

@media (max-width: 640px) {
    .login-panel {
        padding: 2rem;
    }

    .welcome-title {
        font-size: 2rem;
    }
    
    .form-group input {
        padding: 1rem 1.125rem;
        font-size: 0.9375rem;
    }
    
    .submit-btn {
        padding: 1rem 1.25rem;
        font-size: 1rem;
    }
    
}

@media (max-width: 480px) {
    .login-panel {
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
    
    .login-header {
        margin-bottom: 2.5rem;
    }
}
</style>
