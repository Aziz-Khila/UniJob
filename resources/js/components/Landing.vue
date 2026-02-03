<template>
    <div class="landing-container">
        <!-- Header -->
        <header :class="['landing-header', { 'scrolled': isScrolled }]">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-wrapper">
                        <div class="logo-icon">
                            <img src="/images/logo/logo.png" alt="UniJob" class="logo-img" />
                        </div>
                    <span class="logo-text">UniJob</span>
                    </div>
                </div>
                <nav class="header-nav">
                    <a href="#home" class="nav-link" @click.prevent="scrollToHome">Home</a>
                    <a href="#about" class="nav-link nav-link-secondary" @click.prevent="scrollToAbout">About</a>
                    <a href="#jobs" class="nav-link nav-link-primary" @click.prevent="scrollToJobs">Jobs</a>
                    <a href="#contact" class="nav-link nav-link-secondary" @click.prevent="scrollToContact">Contact Us</a>
                </nav>
                <div class="header-actions">
                    <router-link to="/login" class="login-link">Log In</router-link>
                    <router-link to="/register" class="join-btn">Join Now</router-link>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="home" class="hero-section">
            <!-- Floating Particles -->
            <div class="hero-particles">
                <div class="particle" v-for="n in 20" :key="n" :style="getParticleStyle(n)"></div>
            </div>
            
            <!-- Animated Waves -->
            <div class="hero-waves">
                <svg class="wave wave-1" viewBox="0 0 1440 320" preserveAspectRatio="none">
                    <path fill="rgba(255, 255, 255, 0.05)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
                <svg class="wave wave-2" viewBox="0 0 1440 320" preserveAspectRatio="none">
                    <path fill="rgba(255, 255, 255, 0.03)" d="M0,224L48,208C96,192,192,160,288,144C384,128,480,128,576,144C672,160,768,192,864,192C960,192,1056,160,1152,144C1248,128,1344,128,1392,128L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
            </div>
            
            <div class="hero-content">
                <!-- Centered Content -->
                <div class="hero-center">
                    <p class="hero-welcome">Welcome To UniJob</p>
                    <h1 class="hero-title">Connecting Talent, Building Careers</h1>
                    <p class="hero-description">
                        Your gateway to part-time opportunities. Connect with employers, showcase your skills, 
                        and build your career while studying.
                    </p>
                    <div class="hero-buttons">
                        <router-link to="/register" class="cta-button primary">Search Now</router-link>
                        <router-link to="/register" class="cta-button secondary">
                            Explore More
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                    </router-link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="stats-section">
            <div class="stats-content">
                <div class="stats-left">
                    <div class="stats-image-wrapper">
                        <div class="stats-overlay">10+ Years Experience</div>
                        <img src="/images/landing/stats-image.webp" alt="Statistics" class="stats-image" />
                </div>
                </div>
                <div class="stats-right">
                    <h2 class="stats-title">Nurturing Talent, Fueling Growth</h2>
                    <p class="stats-description">
                        We connect talented students with exceptional part-time opportunities, 
                        helping them build their careers while studying.
                    </p>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">25000+</div>
                            <div class="stat-label">Jobs Posted</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Active Employers</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">24000+</div>
                            <div class="stat-label">Students</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">160+</div>
                            <div class="stat-label">Companies</div>
                        </div>
                    </div>
                    <div class="stats-actions">
                        <router-link to="/register" class="stats-button">Browse Jobs</router-link>
                        <span class="stats-phone">+123-456-789</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Service Offerings Section -->
        <section id="about" class="services-section">
            <div class="services-content">
                <div class="section-header">
                    <h2 class="section-title">How We Help Students</h2>
                    <p class="section-subtitle">Everything you need to find the perfect part-time job</p>
                </div>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-tag">Easy Access</div>
                        <div class="service-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Browse Jobs</h3>
                        <p class="service-description">Explore thousands of part-time opportunities from verified employers in your area.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-tag">Quick Apply</div>
                        <div class="service-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11l3 3L22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Easy Application</h3>
                        <p class="service-description">Apply to multiple jobs with just a few clicks. Track your applications all in one place.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-tag">Direct Chat</div>
                        <div class="service-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Direct Communication</h3>
                        <p class="service-description">Chat directly with employers, ask questions, and build relationships before applying.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-tag">Build Profile</div>
                        <div class="service-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Showcase Skills</h3>
                        <p class="service-description">Create a professional profile highlighting your skills, experience, and availability to stand out.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Job Listings Section -->
        <section id="jobs" class="jobs-section">
            <div class="jobs-content">
                <div class="jobs-header">
                    <h2 class="section-title white">Featured Job Opportunities</h2>
                    <p class="jobs-description">Discover exciting part-time opportunities that match your skills and interests.</p>
            </div>
                <div class="jobs-grid">
                    <div class="job-card" v-for="(job, index) in jobs" :key="index">
                        <div class="job-logo-wrapper">
                            <img :src="`/images/landing/companies/${job.name.toLowerCase()}.${job.name === 'Intern' ? 'png' : 'jpg'}`" 
                                 :alt="job.name" 
                                 class="job-logo-img" />
                        </div>
                        <h3 class="job-title">Part-Time {{ job.name }}</h3>
                        <div class="job-location">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            <span>{{ job.locations[index] || 'Remote / On-site' }}</span>
                            </div>
                        <div class="job-type">{{ job.types[index] || 'Part Time' }}</div>
                        <router-link to="/register" class="job-button">View Job</router-link>
                    </div>
            </div>
            </div>
        </section>

        <!-- Application Process Section -->
        <section class="process-section">
            <div class="process-content">
                <div class="section-header">
                    <h2 class="section-title">How It Works</h2>
                    <p class="section-subtitle">Simple steps to find your perfect part-time job</p>
                </div>
                <div class="process-wrapper">
                    <div class="process-steps">
                        <div class="process-step">
                            <div class="step-number">01</div>
                            <div class="step-content">
                                <h3 class="step-title">Create Your Profile</h3>
                                <p class="step-description">Sign up and build your profile with your skills, experience, and availability.</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-number">02</div>
                            <div class="step-content">
                                <h3 class="step-title">Browse & Apply</h3>
                                <p class="step-description">Explore part-time opportunities and apply to jobs that match your interests.</p>
                        </div>
                    </div>
                        <div class="process-step">
                            <div class="step-number">03</div>
                            <div class="step-content">
                                <h3 class="step-title">Get Hired</h3>
                                <p class="step-description">Connect with employers and start your part-time career journey.</p>
                            </div>
                        </div>
                        </div>
                    <div class="process-image-wrapper">
                        <div class="process-badge">Trusted Platform</div>
                        <img src="/images/landing/process-image.webp" alt="Application Process" class="process-image" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Student Testimonials Section -->
        <section class="testimonials-section">
            <div class="testimonials-content">
                <div class="section-header">
                    <h2 class="section-title">What Students Say</h2>
                    <p class="section-subtitle">Real experiences from students who found success</p>
                </div>
                <div class="testimonials-grid">
                    <div class="testimonial-card" v-for="(testimonial, index) in testimonials" :key="index">
                        <div class="testimonial-avatar">{{ testimonial.initials }}</div>
                        <p class="testimonial-text">"{{ testimonial.text }}"</p>
                        <div class="testimonial-author">{{ testimonial.author }}</div>
                        </div>
                        </div>
                    </div>
        </section>

        <!-- Contact & FAQ Section -->
        <section id="contact" class="contact-faq-section">
            <div class="contact-faq-content">
                <div class="contact-form-wrapper">
                    <h2 class="form-title">Have Questions?</h2>
                    <p class="form-subtitle">Get in touch with our team</p>
                    <form class="contact-form" @submit.prevent="handleContactSubmit">
                        <input type="text" v-model="contactForm.name" placeholder="Your Name" class="form-input" required />
                        <input type="email" v-model="contactForm.email" placeholder="Your Email" class="form-input" required />
                        <input type="tel" v-model="contactForm.phone" placeholder="Your Phone" class="form-input" />
                        <input type="text" v-model="contactForm.subject" placeholder="Subject" class="form-input" />
                        <textarea v-model="contactForm.message" placeholder="Your Message" class="form-textarea" rows="4" required></textarea>
                        <button type="submit" class="form-submit">Send Message</button>
                    </form>
                </div>
                <div class="faq-wrapper">
                    <h2 class="faq-title">Frequently Asked Questions</h2>
                    <p class="faq-intro">Find answers to common questions about our platform.</p>
                    <div class="faq-list">
                        <div class="faq-item" v-for="(faq, index) in faqs" :key="index">
                            <div class="faq-question" @click="toggleFaq(index)">
                                <span>{{ faq.question }}</span>
                                <svg :class="['faq-icon', { active: activeFaq === index }]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </div>
                            <transition name="faq-slide">
                                <div v-if="activeFaq === index" class="faq-answer">
                                    <p>{{ faq.answer }}</p>
                        </div>
                            </transition>
                    </div>
                        </div>
                        </div>
                    </div>
        </section>

        <!-- Footer -->
        <footer class="landing-footer">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo-wrapper">
                        <img src="/images/logo/logo.png" alt="UniJob" class="footer-logo-img" />
                        <span class="footer-logo-text">UniJob</span>
                </div>
                    <p class="footer-about">Connecting students with part-time opportunities. Building careers, one placement at a time.</p>
                    <div class="footer-social">
                        <a href="#" class="social-icon">f</a>
                        <a href="#" class="social-icon">t</a>
                        <a href="#" class="social-icon">in</a>
            </div>
                </div>
                <div class="footer-column">
                    <h3 class="footer-heading">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#jobs">Browse Jobs</a></li>
                        <li><a href="#contact">FAQs</a></li>
                        <li><a href="#contact">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-heading">For Students</h3>
                    <ul class="footer-links">
                        <li><router-link to="/register">Create Account</router-link></li>
                        <li><a href="#jobs">Find Jobs</a></li>
                        <li><a href="#about">Career Resources</a></li>
                        <li><a href="#contact">Get Help</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-heading">Stay Updated</h3>
                    <div class="footer-subscribe">
                        <input type="email" v-model="subscribeEmail" placeholder="Enter your email" class="subscribe-input" />
                        <button class="subscribe-button" @click.prevent="handleSubscribe">Subscribe</button>
                    </div>
                    <div class="footer-contact">
                        <p>Email: contact@unijob.com</p>
                        <p>Phone: +123-456-789</p>
                        <p>Address: 123 University Ave, City</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>Copyright ©2024 UniJob. All Rights Reserved.</p>
            </div>
        </footer>
    </div>
</template>

<script>
export default {
    name: 'Landing',
    data() {
        return {
            isScrolled: false,
            activeFaq: null,
            subscribeEmail: '',
            contactForm: {
                name: '',
                email: '',
                phone: '',
                subject: '',
                message: ''
            },
            jobs: [
                { name: 'Developer', locations: ['Remote'], types: ['Part Time'] },
                { name: 'Designer', locations: ['Hybrid'], types: ['Part Time'] },
                { name: 'Writer', locations: ['Remote'], types: ['Part Time'] },
                { name: 'Assistant', locations: ['On-site'], types: ['Part Time'] },
                { name: 'Tutor', locations: ['Remote'], types: ['Part Time'] },
                { name: 'Intern', locations: ['Hybrid'], types: ['Internship'] }
            ],
            testimonials: [
                {
                    initials: 'ST',
                    text: 'UniJob made it so easy to find the perfect part-time job that fits my university schedule. Highly recommended!',
                    author: 'Sarah Thompson, Student'
                },
                {
                    initials: 'MJ',
                    text: 'The platform is amazing! I found my dream job within a week. The application process was smooth and professional.',
                    author: 'Michael Johnson, Student'
                },
                {
                    initials: 'EW',
                    text: 'UniJob connected me with great employers. The support team is always helpful and responsive.',
                    author: 'Emily Williams, Student'
                },
                {
                    initials: 'DR',
                    text: 'As a student, finding flexible work was challenging. UniJob solved that problem perfectly!',
                    author: 'David Rodriguez, Student'
                }
            ],
            faqs: [
                {
                    question: "How do I apply for a job?",
                    answer: "Simply create an account, complete your profile, browse available jobs, and click 'Apply' on any position that interests you. You can track your applications in your dashboard."
                },
                {
                    question: "What kind of jobs are available?",
                    answer: "We focus on part-time opportunities suitable for students, including internships, freelance work, remote positions, and flexible schedule jobs across various industries."
                },
                {
                    question: "Is UniJob free for students?",
                    answer: "Yes! Creating an account and applying to jobs is completely free for students. We're here to help you find opportunities without any cost."
                },
                {
                    question: "How long does it take to find a job?",
                    answer: "The process varies, but most students find suitable positions within 2-4 weeks of completing their profile and starting to apply. We work efficiently to match you with the right opportunities."
                },
                {
                    question: "Can I work around my class schedule?",
                    answer: "Absolutely! Most jobs on our platform offer flexible scheduling. You can filter jobs by availability and discuss your schedule directly with employers during the application process."
                }
            ]
        };
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        handleScroll() {
            this.isScrolled = window.scrollY > 20;
        },
        toggleFaq(index) {
            this.activeFaq = this.activeFaq === index ? null : index;
        },
        handleContactSubmit() {
            alert('Thank you for your message! We will get back to you soon.');
            this.contactForm = {
                name: '',
                email: '',
                phone: '',
                subject: '',
                message: ''
            };
        },
        handleSubscribe() {
            if (this.subscribeEmail) {
                alert('Thank you for subscribing!');
                this.subscribeEmail = '';
        }
    },
        scrollToHome(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        scrollToJobs(e) {
            e.preventDefault();
            const section = document.getElementById('jobs');
            if (section) {
                const headerHeight = 88;
                const elementPosition = section.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerHeight;
                window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
            }
        },
        scrollToAbout(e) {
            e.preventDefault();
            const section = document.getElementById('about');
            if (section) {
                const headerHeight = 88;
                const elementPosition = section.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerHeight;
                window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
            }
        },
        scrollToContact(e) {
            e.preventDefault();
            const section = document.getElementById('contact');
            if (section) {
                const headerHeight = 88;
                const elementPosition = section.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerHeight;
                window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                }
        },
        getParticleStyle(index) {
            // Generate random but consistent positions for particles
            const seed = index * 137.508; // Golden angle for distribution
            const angle = (seed % 360) * Math.PI / 180;
            const distance = 30 + (seed % 40);
            const left = 50 + Math.cos(angle) * distance;
            const top = 50 + Math.sin(angle) * distance;
            const size = 3 + (index % 4);
            const delay = (index * 0.2) % 5;
            const duration = 8 + (index % 4);
            
            return {
                left: `${left}%`,
                top: `${top}%`,
                width: `${size}px`,
                height: `${size}px`,
                animationDelay: `${delay}s`,
                animationDuration: `${duration}s`
            };
                    }
    }
};
</script>

<style scoped>
.landing-container {
    min-height: 100vh;
    background: #ffffff;
    overflow-x: hidden;
    font-family: 'Poppins', sans-serif;
}

/* Header */
.landing-header {
    background: linear-gradient(135deg, rgba(31, 106, 225, 0.9) 0%, rgba(37, 99, 235, 0.9) 50%, rgba(59, 130, 246, 0.9) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 0.875rem 4rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }
                
.landing-header.scrolled {
    background: linear-gradient(135deg, rgba(31, 106, 225, 0.95) 0%, rgba(37, 99, 235, 0.95) 50%, rgba(59, 130, 246, 0.95) 100%);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border-bottom: 1px solid rgba(255, 255, 255, 0.25);
                }

.header-content {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 4rem;
    max-width: 1200px;
    margin: 0 auto;
    height: 88px;
            }
            
.logo {
    display: flex;
    align-items: center;
}

.logo-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: transform 0.2s ease;
}

.logo-wrapper:hover {
    transform: translateY(-1px);
}

.logo-icon {
    width: 80px;
    height: 80px;
    background: #ffffff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    border: none;
    transition: all 0.3s ease;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.logo-wrapper:hover .logo-icon {
    background: #ffffff;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.logo-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}

.logo-text {
    font-size: 1.25rem;
    font-weight: 700;
    color: #FFFFFF;
    font-family: 'Poppins', sans-serif;
    letter-spacing: -0.5px;
}

.header-nav {
    display: flex;
    justify-content: center;
    gap: 2.5rem;
    align-items: center;
}

.nav-link {
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9375rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    padding: 0.5rem 0;
    font-family: 'Poppins', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.nav-link-primary {
    color: rgba(255, 255, 255, 0.95);
    font-weight: 600;
}

.nav-link-secondary {
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #FFFFFF, rgba(255, 255, 255, 0.8));
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 1px;
}

.nav-link:hover {
    color: #FFFFFF;
    transform: translateY(-1px);
                        }

.nav-link:hover::after {
    width: 100%;
}

.nav-link-primary::after {
    background: #FFFFFF;
    height: 2.5px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    justify-content: flex-end;
}

.login-link {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9375rem;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.05);
}

.login-link:hover {
    color: #FFFFFF;
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

.join-btn {
    background: linear-gradient(135deg, #FFFFFF 0%, #F0F4FF 100%);
    color: #1F6AE1;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9375rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: 'Poppins', sans-serif;
    border: none;
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.2), 0 1px 3px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.join-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s ease;
}

.join-btn:hover::before {
    left: 100%;
}

.join-btn:hover {
    background: linear-gradient(135deg, #FFFFFF 0%, #EAF2FF 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3), 0 2px 6px rgba(0, 0, 0, 0.15);
}

.join-btn:active {
    transform: translateY(0);
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    padding: 6rem 4rem;
    color: white;
    position: relative;
    overflow: hidden;
    min-height: 75vh;
    display: flex;
    align-items: center;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.12) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

/* Floating Particles */
.hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    pointer-events: none;
    overflow: hidden;
}

.particle {
    position: absolute;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 50%;
    animation: particleFloat 10s ease-in-out infinite;
    box-shadow: 0 0 6px rgba(255, 255, 255, 0.5);
}

@keyframes particleFloat {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0.6;
    }
    25% {
        transform: translate(20px, -30px) scale(1.2);
        opacity: 0.8;
    }
    50% {
        transform: translate(-15px, -50px) scale(0.8);
        opacity: 0.4;
    }
    75% {
        transform: translate(30px, -20px) scale(1.1);
        opacity: 0.7;
    }
}

/* Animated Waves */
.hero-waves {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 200px;
    z-index: 0;
    pointer-events: none;
    overflow: hidden;
}

.hero-waves .wave {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0.6;
}

.wave-1 {
    animation: waveAnimation1 15s ease-in-out infinite;
}

.wave-2 {
    animation: waveAnimation2 20s ease-in-out infinite;
    opacity: 0.4;
}

@keyframes waveAnimation1 {
    0%, 100% {
        transform: translateX(0) translateY(0);
    }
    50% {
        transform: translateX(-25%) translateY(-10px);
    }
}

@keyframes waveAnimation2 {
    0%, 100% {
        transform: translateX(0) translateY(0);
    }
    50% {
        transform: translateX(25%) translateY(10px);
    }
}

.hero-content {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 10;
    width: 100%;
    min-height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-center {
    position: relative;
    z-index: 11;
    text-align: center;
    max-width: 900px;
    width: 100%;
    animation: fadeInUp 0.8s ease-out;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-welcome {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    opacity: 0.95;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: 'Poppins', sans-serif;
    color: rgba(255, 255, 255, 0.9);
    display: block;
}

.hero-title {
    font-size: 56px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 2rem;
    color: white;
    letter-spacing: -1.5px;
    font-family: 'Poppins', sans-serif;
    display: block;
    text-shadow: 0 2px 20px rgba(255, 255, 255, 0.2);
}

.hero-description {
    font-size: 18px;
    line-height: 1.7;
    margin-bottom: 3rem;
    opacity: 0.95;
    font-weight: 400;
    font-family: 'Poppins', sans-serif;
    color: rgba(255, 255, 255, 0.95);
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    display: block;
}

.hero-buttons {
    display: flex;
    gap: 1.25rem;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    width: 100%;
}

.cta-button {
    padding: 1rem 2.75rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.0625rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    letter-spacing: 0.3px;
    font-family: 'Poppins', sans-serif;
    min-width: 160px;
    white-space: nowrap;
}

.cta-button.primary {
    background: transparent;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.cta-button.primary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.cta-button.secondary {
    background: white;
    color: #1F6AE1;
    border: 2px solid white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.cta-button.secondary:hover {
    background: #EAF2FF;
    border-color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.cta-button.secondary svg {
    width: 18px;
    height: 18px;
}

.hero-graphics {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 1;
    pointer-events: none;
}

/* Enhanced Icon Elements */
.graphic-icon {
    position: absolute;
    width: 56px;
    height: 56px;
    color: rgba(255, 255, 255, 0.4);
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(15px);
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.875rem;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12),
                inset 0 0 12px rgba(255, 255, 255, 0.08);
    transition: all 0.3s ease;
}

.graphic-icon:hover {
    transform: scale(1.1) translateY(-4px);
    background: rgba(255, 255, 255, 0.18);
    box-shadow: 0 10px 32px rgba(0, 0, 0, 0.15),
                inset 0 0 16px rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.6);
}

.graphic-icon svg {
    width: 100%;
    height: 100%;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

/* Icons scattered randomly throughout the entire hero background */
.graphic-icon-1 {
    top: 5%;
    left: 3%;
    animation: floatRandom1 12s ease-in-out infinite;
    animation-delay: 0s;
}

.graphic-icon-2 {
    top: 15%;
    left: 18%;
    animation: floatRandom2 14s ease-in-out infinite;
    animation-delay: 1.5s;
}

.graphic-icon-3 {
    top: 8%;
    left: 35%;
    animation: floatRandom3 11s ease-in-out infinite;
    animation-delay: 0.8s;
}

.graphic-icon-4 {
    top: 12%;
    right: 32%;
    animation: floatRandom4 13s ease-in-out infinite;
    animation-delay: 2.2s;
}

.graphic-icon-5 {
    top: 10%;
    right: 15%;
    animation: floatRandom5 15s ease-in-out infinite;
    animation-delay: 0.5s;
}

.graphic-icon-6 {
    top: 6%;
    right: 4%;
    animation: floatRandom6 12s ease-in-out infinite;
    animation-delay: 1.8s;
}

.graphic-icon-7 {
    top: 28%;
    left: 5%;
    animation: floatRandom7 14s ease-in-out infinite;
    animation-delay: 0.3s;
}

.graphic-icon-8 {
    top: 32%;
    left: 22%;
    animation: floatRandom8 13s ease-in-out infinite;
    animation-delay: 2.5s;
}

.graphic-icon-9 {
    top: 30%;
    right: 25%;
    animation: floatRandom1 13s ease-in-out infinite;
    animation-delay: 1.2s;
}

.graphic-icon-10 {
    top: 35%;
    right: 8%;
    animation: floatRandom2 15s ease-in-out infinite;
    animation-delay: 0.7s;
}

.graphic-icon-11 {
    bottom: 32%;
    left: 8%;
    animation: floatRandom3 12s ease-in-out infinite;
    animation-delay: 2s;
}

.graphic-icon-12 {
    bottom: 28%;
    left: 25%;
    animation: floatRandom4 11s ease-in-out infinite;
    animation-delay: 1.5s;
}

.graphic-icon-13 {
    bottom: 35%;
    right: 28%;
    animation: floatRandom5 14s ease-in-out infinite;
    animation-delay: 0.9s;
}

.graphic-icon-14 {
    bottom: 30%;
    right: 10%;
    animation: floatRandom6 13s ease-in-out infinite;
    animation-delay: 1.7s;
}

.graphic-icon-15 {
    bottom: 15%;
    left: 12%;
    animation: floatRandom7 12s ease-in-out infinite;
    animation-delay: 2.3s;
}

.graphic-icon-16 {
    bottom: 12%;
    right: 20%;
    animation: floatRandom8 15s ease-in-out infinite;
    animation-delay: 0.6s;
}

/* Random Floating Animations */
@keyframes floatRandom1 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
        opacity: 0.7;
    }
    20% {
        transform: translate(25px, -30px) rotate(5deg);
        opacity: 0.85;
}
    40% {
        transform: translate(-15px, -20px) rotate(-3deg);
        opacity: 0.75;
    }
    60% {
        transform: translate(30px, -10px) rotate(4deg);
        opacity: 0.9;
}
    80% {
        transform: translate(-20px, -35px) rotate(-2deg);
        opacity: 0.8;
}
}

@keyframes floatRandom2 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
        opacity: 0.7;
    }
    25% {
        transform: translate(-30px, -25px) rotate(-4deg);
        opacity: 0.9;
    }
    50% {
        transform: translate(20px, -15px) rotate(3deg);
        opacity: 0.75;
    }
    75% {
        transform: translate(-25px, -30px) rotate(-5deg);
        opacity: 0.85;
}
}

@keyframes floatRandom3 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
        opacity: 0.7;
    }
    30% {
        transform: translate(35px, -20px) rotate(6deg);
        opacity: 0.9;
    }
    60% {
        transform: translate(-25px, -35px) rotate(-4deg);
        opacity: 0.8;
}
    90% {
        transform: translate(15px, -10px) rotate(2deg);
        opacity: 0.85;
    }
}

@keyframes floatRandom4 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
        opacity: 0.7;
    }
    15% {
        transform: translate(-20px, -25px) rotate(-3deg);
        opacity: 0.85;
    }
    35% {
        transform: translate(30px, -15px) rotate(5deg);
        opacity: 0.75;
    }
    55% {
        transform: translate(-15px, -30px) rotate(-4deg);
        opacity: 0.9;
    }
    75% {
        transform: translate(25px, -20px) rotate(3deg);
        opacity: 0.8;
    }
}

@keyframes floatRandom5 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
        opacity: 0.7;
    }
    20% {
        transform: translate(20px, -35px) rotate(4deg);
        opacity: 0.9;
}
    40% {
        transform: translate(-30px, -20px) rotate(-5deg);
        opacity: 0.75;
    }
    60% {
        transform: translate(15px, -25px) rotate(2deg);
        opacity: 0.85;
    }
    80% {
        transform: translate(-25px, -15px) rotate(-3deg);
        opacity: 0.8;
}
}

@keyframes floatRandom6 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
        opacity: 0.7;
}
    25% {
        transform: translate(-35px, -15px) rotate(-6deg);
        opacity: 0.85;
    }
    50% {
        transform: translate(25px, -30px) rotate(4deg);
        opacity: 0.75;
    }
    75% {
        transform: translate(-20px, -25px) rotate(-2deg);
        opacity: 0.9;
}
}

@keyframes floatRandom7 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
        opacity: 0.7;
    }
    30% {
        transform: translate(30px, -25px) rotate(5deg);
        opacity: 0.8;
    }
    60% {
        transform: translate(-25px, -20px) rotate(-4deg);
        opacity: 0.9;
    }
    90% {
        transform: translate(20px, -30px) rotate(3deg);
        opacity: 0.75;
    }
}

@keyframes floatRandom8 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
        opacity: 0.7;
}
    20% {
        transform: translate(-15px, -30px) rotate(-3deg);
        opacity: 0.9;
    }
    40% {
        transform: translate(35px, -15px) rotate(6deg);
        opacity: 0.75;
    }
    60% {
        transform: translate(-30px, -25px) rotate(-5deg);
        opacity: 0.85;
}
    80% {
        transform: translate(20px, -20px) rotate(2deg);
        opacity: 0.8;
}
}


@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Statistics Section */
.stats-section {
    padding: 8rem 4rem;
    background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
}

.stats-content {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5rem;
    align-items: center;
}

.stats-image-wrapper {
    width: 100%;
    height: 450px;
    border-radius: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
    transition: transform 0.3s ease;
}

.stats-image-wrapper:hover {
    transform: translateY(-5px);
}

.stats-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 20px;
}

.stats-overlay {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    background: #1F6AE1;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    z-index: 10;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.stats-title {
    font-size: 36px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
    font-family: 'Poppins', sans-serif;
    line-height: 1.4;
}

.stats-description {
    font-size: 18px;
    color: #6b7280;
    line-height: 1.7;
    margin-bottom: 2.5rem;
    font-family: 'Poppins', sans-serif;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2.5rem;
    margin-bottom: 2.5rem;
}

.stat-item {
    text-align: left;
}

.stat-number {
    font-size: 3.5rem;
    font-weight: 700;
    color: #1F6AE1;
    margin-bottom: 0.5rem;
    font-family: 'Poppins', sans-serif;
    line-height: 1;
}

.stat-label {
    font-size: 18px;
    color: #6b7280;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
}

.stats-actions {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-top: 2rem;
}

.stats-button {
    background: #1F6AE1;
    color: white;
    padding: 1rem 2.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.stats-button:hover {
    background: #0B3C8A;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(31, 106, 225, 0.4);
}

.stats-phone {
    color: #1F6AE1;
    font-weight: 600;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
}

/* Services Section */
.services-section {
    padding: 8rem 4rem;
    background: white;
}

.services-content {
    max-width: 1400px;
    margin: 0 auto;
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-title {
    font-size: 36px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
    letter-spacing: -0.5px;
    line-height: 1.4;
    font-family: 'Poppins', sans-serif;
}

.section-subtitle {
    font-size: 18px;
    color: #6b7280;
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
}

.section-title.white {
    color: white;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
}

.service-card {
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: 24px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 24px rgba(31, 106, 225, 0.2);
    position: relative;
    overflow: hidden;
}

.service-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.service-card:hover::before {
    opacity: 1;
}

.service-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 40px rgba(31, 106, 225, 0.35);
}

.service-tag {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    color: white;
    padding: 0.375rem 1rem;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.service-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    transition: transform 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.service-card:hover .service-icon {
    transform: scale(1.1) rotate(5deg);
}

.service-icon svg {
    width: 40px;
    height: 40px;
    stroke: white;
    stroke-width: 2.5;
}

.service-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 1rem;
    font-family: 'Poppins', sans-serif;
}

.service-description {
    font-size: 16px;
    line-height: 1.7;
    opacity: 0.95;
    font-family: 'Poppins', sans-serif;
}

/* Jobs Section */
.jobs-section {
    padding: 8rem 4rem;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    position: relative;
    overflow: hidden;
}

.jobs-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
    pointer-events: none;
}

.jobs-content {
    max-width: 1400px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.jobs-header {
    text-align: center;
    margin-bottom: 4rem;
}

.jobs-description {
    font-size: 18px;
    color: rgba(255, 255, 255, 0.95);
    margin-top: 1rem;
    font-family: 'Poppins', sans-serif;
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

.job-card {
    background: white;
    padding: 3rem 2.5rem;
    border-radius: 24px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
}

.job-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #1F6AE1 0%, #2563EB 100%);
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.job-card:hover::before {
    transform: scaleX(1);
}

.job-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-color: rgba(31, 106, 225, 0.3);
}

.job-logo-wrapper {
    width: 70px;
    height: 70px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.job-logo-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 0.5rem;
}

.job-title {
    font-size: 24px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
    font-family: 'Poppins', sans-serif;
}

.job-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 16px;
    margin-bottom: 1rem;
    font-family: 'Poppins', sans-serif;
}

.job-location svg {
    width: 16px;
    height: 16px;
}

.job-type {
    display: inline-block;
    background: #EAF2FF;
    color: #1F6AE1;
    padding: 0.375rem 1rem;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-family: 'Poppins', sans-serif;
}

.job-button {
    display: inline-block;
    background: #1F6AE1;
    color: white;
    padding: 0.875rem 2rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.job-button:hover {
    background: #0B3C8A;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(31, 106, 225, 0.4);
}

/* Process Section */
.process-section {
    padding: 8rem 4rem;
    background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
}

.process-content {
    max-width: 1400px;
    margin: 0 auto;
}

.process-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5rem;
    align-items: center;
}

.process-steps {
    display: flex;
    flex-direction: column;
    gap: 3.5rem;
}

.process-step {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}

.step-number {
    font-size: 4rem;
    font-weight: 700;
    color: #1F6AE1;
    line-height: 1;
    font-family: 'Poppins', sans-serif;
    flex-shrink: 0;
}

.step-content {
    flex: 1;
}

.step-title {
    font-size: 24px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.75rem;
    font-family: 'Poppins', sans-serif;
}

.step-description {
    color: #6b7280;
    line-height: 1.7;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
}

.process-image-wrapper {
    width: 100%;
    height: 550px;
    border-radius: 24px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
    transition: transform 0.3s ease;
}

.process-image-wrapper:hover {
    transform: translateY(-5px);
}

.process-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 24px;
}

.process-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: #1F6AE1;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    z-index: 10;
    font-family: 'Poppins', sans-serif;
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

/* Testimonials Section */
.testimonials-section {
    padding: 8rem 4rem;
    background: white;
}

.testimonials-content {
    max-width: 1400px;
    margin: 0 auto;
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
    align-items: stretch;
}

.testimonial-card {
    background: #f9fafb;
    padding: 2.5rem 2rem;
    border-radius: 20px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    height: 100%;
}

.testimonial-card::before {
    content: '"';
    position: absolute;
    top: 1rem;
    left: 1.5rem;
    font-size: 4rem;
    color: #EAF2FF;
    font-weight: 700;
    line-height: 1;
    opacity: 0.5;
}

.testimonial-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
    border-color: rgba(31, 106, 225, 0.2);
}

.testimonial-avatar {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 1.75rem;
    font-weight: 700;
    font-family: 'Poppins', sans-serif;
    box-shadow: 0 8px 20px rgba(31, 106, 225, 0.3);
    border: 4px solid white;
    transition: transform 0.3s ease;
    position: relative;
    z-index: 1;
}

.testimonial-card:hover .testimonial-avatar {
    transform: scale(1.1);
}

.testimonial-text {
    color: #4b5563;
    line-height: 1.7;
    margin-bottom: 1.5rem;
    font-style: italic;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    position: relative;
    z-index: 1;
    flex: 1;
}

.testimonial-author {
    font-weight: 600;
    color: #1f2937;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    position: relative;
    z-index: 1;
    margin-top: auto;
}

/* Contact & FAQ Section */
.contact-faq-section {
    padding: 8rem 4rem;
    background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
}

.contact-faq-content {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
}

.contact-form-wrapper {
    background: white;
    padding: 3rem;
    border-radius: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.form-title {
    font-size: 28px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
    font-family: 'Poppins', sans-serif;
}

.form-subtitle {
    font-size: 16px;
    color: #6b7280;
    margin-bottom: 2rem;
    font-family: 'Poppins', sans-serif;
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.form-input,
.form-textarea {
    padding: 1rem 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #1F6AE1;
    box-shadow: 0 0 0 4px rgba(31, 106, 225, 0.1);
    transform: translateY(-1px);
}

.form-submit {
    background: #1F6AE1;
    color: white;
    padding: 1rem 2.5rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.form-submit:hover {
    background: #0B3C8A;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(31, 106, 225, 0.4);
}

.faq-wrapper {
    background: white;
    padding: 3rem;
    border-radius: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.faq-title {
    font-size: 28px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
    font-family: 'Poppins', sans-serif;
}

.faq-intro {
    font-size: 16px;
    color: #6b7280;
    margin-bottom: 2rem;
    font-family: 'Poppins', sans-serif;
}

.faq-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.faq-item {
    background: #f9fafb;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.faq-item:hover {
    border-color: #1F6AE1;
    background: white;
}

.faq-question {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    color: #1f2937;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    transition: background 0.2s;
}

.faq-question:hover {
    background: rgba(31, 106, 225, 0.05);
}

.faq-icon {
    width: 20px;
    height: 20px;
    transition: transform 0.3s;
    flex-shrink: 0;
    color: #1F6AE1;
}

.faq-icon.active {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 1.5rem 1.5rem;
    color: #6b7280;
    line-height: 1.7;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
}

.faq-slide-enter-active {
    transition: all 0.3s ease-out;
    overflow: hidden;
}

.faq-slide-leave-active {
    transition: all 0.3s ease-in;
    overflow: hidden;
}

.faq-slide-enter-from {
    opacity: 0;
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
}

.faq-slide-enter-to {
    opacity: 1;
    max-height: 500px;
    padding-top: 0;
    padding-bottom: 1.5rem;
}

.faq-slide-leave-from {
    opacity: 1;
    max-height: 500px;
    padding-top: 0;
    padding-bottom: 1.5rem;
    }

.faq-slide-leave-to {
    opacity: 0;
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
    }

/* Footer */
.landing-footer {
    background: #0B3C8A;
    color: white;
    padding: 5rem 4rem 2rem;
    }

.footer-content {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 3rem;
    margin-bottom: 3rem;
}

.footer-logo-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    }

.footer-logo-img {
    height: 50px;
    width: auto;
    }

.footer-logo-text {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    font-family: 'Poppins', sans-serif;
    }

.footer-about {
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.7;
    margin-bottom: 2rem;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
}

.footer-social {
    display: flex;
    gap: 1rem;
    }

.social-icon {
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
    }

.social-icon:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
    }

.footer-heading {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-family: 'Poppins', sans-serif;
    }

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 1rem;
    }

.footer-links a {
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    transition: all 0.2s ease;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    }

.footer-links a:hover {
    color: white;
    padding-left: 4px;
    }

.footer-subscribe {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    }

.subscribe-input {
    flex: 1;
    padding: 0.875rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    }

.subscribe-input::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.subscribe-button {
    background: white;
    color: #1F6AE1;
    padding: 0.875rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    }

.subscribe-button:hover {
    background: #EAF2FF;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

.footer-contact {
    color: rgba(255, 255, 255, 0.85);
    font-size: 16px;
    line-height: 2;
    font-family: 'Poppins', sans-serif;
    }

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 2rem;
    text-align: center;
    color: rgba(255, 255, 255, 0.7);
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    }

/* Responsive */
@media (max-width: 1024px) {
    .header-content {
        grid-template-columns: auto 1fr;
        gap: 2rem;
        max-width: 100%;
        padding: 0 2rem;
    }

    .header-nav {
        display: none;
    }

    .logo-text {
        font-size: 1.125rem;
    }

    .hero-section {
        padding: 3rem 2rem;
        min-height: 55vh;
    }

    .hero-content {
        min-height: 350px;
    }

    .hero-center {
        padding: 0 1rem;
    }

    .stats-content,
    .process-wrapper {
        grid-template-columns: 1fr;
    }
    
    .services-grid,
    .testimonials-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .jobs-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .footer-content {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .landing-header {
        padding: 0.75rem 2rem;
    }
    
    .header-content {
        grid-template-columns: auto 1fr;
        gap: 1.5rem;
        height: 56px;
    }

    .header-nav {
        display: none;
    }
    
    .header-actions {
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .login-link {
        font-size: 0.875rem;
        padding: 0.5rem 0.875rem;
    }

    .join-btn {
        padding: 0.625rem 1.5rem;
        font-size: 0.875rem;
    }
    
    .logo-text {
        display: none;
    }

    .logo-icon {
        width: 40px;
        height: 40px;
        padding: 0.4rem;
    }

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .hero-section {
        padding: 3rem 2rem;
        min-height: 50vh;
    }
    
    .hero-content {
        min-height: 300px;
}

    .hero-center {
        padding: 0 1rem;
    }
    
    /* Reduce floating icons on mobile */
    .graphic-icon {
        width: 40px;
        height: 40px;
        padding: 0.625rem;
        opacity: 0.6;
    }
    
    .hero-title {
        font-size: 36px;
        line-height: 1.3;
        margin-bottom: 1.25rem;
    }
    
    .hero-description {
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 2rem;
        padding: 0 0.5rem;
}

    .hero-buttons {
    flex-direction: column;
        width: 100%;
        gap: 1rem;
}

    .cta-button {
    width: 100%;
        max-width: 280px;
        justify-content: center;
        padding: 0.875rem 2rem;
}

    .stats-section,
    .services-section,
    .jobs-section,
    .process-section,
    .testimonials-section,
    .contact-faq-section {
        padding: 4rem 2rem;
    }
    
    .section-title {
        font-size: 28px;
    }
    
    .services-grid,
    .jobs-grid,
    .testimonials-grid {
        grid-template-columns: 1fr;
}

    .contact-faq-content {
        grid-template-columns: 1fr;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
}

    .stats-grid {
        grid-template-columns: 1fr;
}

    .stats-actions {
    flex-direction: column;
        align-items: flex-start;
}

    .graphic-icon {
        display: none;
    }
}
</style>
