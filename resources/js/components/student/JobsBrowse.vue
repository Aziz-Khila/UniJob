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
                <div class="jobs-browse-page">
            <header class="page-header">
                <h1>Uncover your perfect job opportunity! 💼</h1>
            </header>

            <div class="search-filters">
                <div class="search-box">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input v-model="filters.search" @input="searchJobs" type="text" placeholder="Search perfect job..." />
                </div>
                <div class="filter-buttons">
                    <button class="filter-btn" @click="toggleView">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    </button>
                    <button class="filter-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                    </button>
                    <button class="btn-find-job">Find a Job</button>
                </div>
            </div>
            
            <div class="job-count" v-if="filteredJobs.length > 0">
                {{ filteredJobs.length }} Jobs match on your expertise
            </div>
            
            <select v-model="filters.category" @change="searchJobs" class="category-filter-mobile">
                <option value="">All Categories</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>

            <div v-if="loading" class="loading">Loading jobs...</div>
            <div v-else-if="filteredJobs.length === 0" class="empty-state">
                <p>No jobs found. Try adjusting your search or filters.</p>
            </div>
            <div v-else class="jobs-grid">
                <div v-for="job in filteredJobs" :key="job.id" class="job-card">
                    <div class="job-card-header">
                        <div class="company-logo">
                            <span>{{ getCompanyInitials(job.client?.name || 'Company') }}</span>
                        </div>
                        <div class="job-title-section">
                            <h3 class="job-title">{{ job.title }}</h3>
                            <p class="job-company-location">
                                {{ job.client?.name || 'Unknown Company' }} - {{ job.location || 'Location not specified' }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="job-tags">
                        <span class="tag tag-location">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            {{ capitalizeWorkType(job.work_type || 'remote') }}
                        </span>
                        <span v-if="job.workers_needed" class="tag tag-level">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            {{ job.workers_needed }} Worker{{ job.workers_needed > 1 ? 's' : '' }}
                        </span>
                    </div>
                    
                    <p class="job-description">{{ truncateDescription(job.description) }}</p>
                    
                    <div class="job-card-footer">
                        <div class="salary-container">
                            <span class="salary-label">Salary</span>
                            <span class="job-salary">{{ formatMoney(job.budget) }}<span class="salary-currency"> TND</span><span class="salary-period">/month</span></span>
                        </div>
                        <router-link :to="`/student/jobs/${job.id}`" class="btn-apply">Apply Now</router-link>
                    </div>
                </div>
            </div>

            <div v-if="jobs?.links" class="pagination">
                <button v-if="jobs.links.prev" @click="loadPage(getPageFromUrl(jobs.links.prev))" class="pagination-btn">« Previous</button>
                <span class="pagination-info">Page {{ jobs.current_page }} of {{ jobs.last_page }}</span>
                <button v-if="jobs.links.next" @click="loadPage(getPageFromUrl(jobs.links.next))" class="pagination-btn">Next »</button>
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
    name: 'StudentJobsBrowse',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            jobs: null,
            categories: [],
            filters: {
                search: '',
                category: '',
            },
            searchTimeout: null,
        };
    },
    computed: {
        filteredJobs() {
            if (!this.jobs?.data) return [];
            return this.jobs.data;
        },
    },
    async mounted() {
        await this.loadJobs();
    },
    methods: {
        async loadJobs(page = 1) {
            this.loading = true;
            try {
                const params = {
                    page,
                    ...this.filters,
                };
                Object.keys(params).forEach(key => {
                    if (!params[key]) delete params[key];
                });
                const response = await api.get('/student/jobs', { params });
                this.jobs = response.data.jobs;
                this.categories = response.data.categories || [];
            } catch (error) {
                console.error('Failed to load jobs:', error);
            } finally {
                this.loading = false;
            }
        },
        searchJobs() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.loadJobs(1);
            }, 500);
        },
        loadPage(page) {
            this.loadJobs(page);
        },
        getPageFromUrl(url) {
            const match = url.match(/page=(\d+)/);
            return match ? parseInt(match[1]) : 1;
        },
        formatMoney(amount) {
            const num = Number(amount) || 0;
            return num.toFixed(2);
        },
        capitalizeWorkType(type) {
            if (!type) return 'Remote';
            const types = {
                'on-site': 'On-Site',
                'remote': 'Remote',
                'hybrid': 'Hybrid',
            };
            return types[type] || type;
        },
        getCompanyInitials(name) {
            if (!name) return '?';
            return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        },
        truncateDescription(description) {
            if (!description) return '';
            if (description.length > 150) {
                return description.substring(0, 150) + '...';
            }
            return description;
        },
        toggleView() {
            // Toggle between grid and list view if needed
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

.jobs-browse-page {
    padding: 2rem;
    padding-bottom: 4rem;
}

.page-header {
    background: transparent;
    padding: 0;
    margin-bottom: 2rem;
}

.page-header h1 {
    color: #111827;
    margin: 0;
    font-size: 2.25rem;
    font-weight: 700;
    letter-spacing: -0.5px;
    line-height: 1.2;
}

.search-filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    align-items: center;
}

.search-box {
    flex: 1;
    position: relative;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    color: #9ca3af;
    pointer-events: none;
}

.search-box input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 1rem;
    background: white;
    transition: all 0.2s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #1F6AE1;
    box-shadow: 0 0 0 3px rgba(31, 106, 225, 0.1);
}

.filter-buttons {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.filter-btn {
    width: 40px;
    height: 40px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    background: white;
    color: #6b7280;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.filter-btn svg {
    width: 18px;
    height: 18px;
}

.filter-btn:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.btn-find-job {
    padding: 0.875rem 1.5rem;
    background: #1F6AE1;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-find-job:hover {
    background: #2563EB;
    box-shadow: 0 4px 12px rgba(31, 106, 225, 0.3);
}

.job-count {
    color: #6b7280;
    font-size: 0.9375rem;
    margin-bottom: 1.5rem;
}

.category-filter-mobile {
    display: none;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.9375rem;
    width: 100%;
    margin-bottom: 2rem;
    background: white;
    cursor: pointer;
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

@media (max-width: 1400px) {
    .jobs-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 968px) {
    .jobs-grid {
        grid-template-columns: 1fr;
    }
    
    .category-filter-mobile {
        display: block;
    }
    
    .filter-buttons {
        display: none;
    }
}

.job-card {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    position: relative;
    overflow: hidden;
}

.job-card:hover {
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
    border-color: #d1d5db;
}

.job-card-header {
    display: flex;
    gap: 1.25rem;
    align-items: flex-start;
    margin-bottom: 0.25rem;
}

.company-logo {
    width: 64px;
    height: 64px;
    border-radius: 14px;
    background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
    flex-shrink: 0;
    box-shadow: 0 4px 16px rgba(255, 107, 53, 0.3);
}

.job-title-section {
    flex: 1;
    min-width: 0;
}

.job-title {
    font-size: 1.375rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.5rem 0;
    line-height: 1.4;
    letter-spacing: -0.02em;
}

.job-company-location {
    font-size: 0.9375rem;
    color: #4b5563;
    margin: 0;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0;
}

.job-company-location::before {
    content: '•';
    color: #d1d5db;
    font-weight: 400;
}

.job-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.625rem;
    margin-top: 0.25rem;
}

.tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    background: #f3f4f6;
    color: #4b5563;
    border: 1px solid #e5e7eb;
}

.tag svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.tag-location {
    background: #EAF2FF;
    color: #1F6AE1;
    border-color: rgba(31, 106, 225, 0.2);
}

.tag-level {
    background: #FEF3C7;
    color: #92400e;
    border-color: rgba(146, 64, 14, 0.2);
}

.tag-type {
    background: #f0fdf4;
    color: #166534;
}

.job-description {
    color: #4b5563;
    font-size: 1rem;
    line-height: 1.7;
    margin: 0.5rem 0 0 0;
    flex: 1;
    font-weight: 400;
    min-height: 3.4em;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.job-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.25rem;
    margin-top: 0.5rem;
    border-top: 2px solid #f3f4f6;
    gap: 1.5rem;
}

.salary-container {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.salary-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.job-salary {
    font-size: 1.75rem;
    font-weight: 800;
    color: #FF6B35;
    letter-spacing: -0.03em;
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 0.125rem;
}

.salary-currency {
    font-size: 1.125rem;
    font-weight: 700;
    color: #FF6B35;
    margin-left: 0.125rem;
}

.salary-period {
    font-size: 0.875rem;
    font-weight: 600;
    color: #9ca3af;
    margin-left: 0.125rem;
}

.btn-apply {
    padding: 0.75rem 2rem;
    background: linear-gradient(135deg, #1F6AE1 0%, #2563EB 50%, #3B82F6 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(31, 106, 225, 0.3);
    white-space: nowrap;
    min-width: 140px;
}

.btn-apply:hover {
    background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
    box-shadow: 0 8px 24px rgba(31, 106, 225, 0.4);
    transform: translateY(-2px);
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
}

.pagination-btn:hover {
    background: #f8fafc;
}

.pagination-info {
    color: #64748b;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #64748b;
}

.empty-state::before {
    content: '🔍';
    font-size: 4rem;
    display: block;
    margin-bottom: 1rem;
    opacity: 0.5;
    animation: pulse 2s ease-in-out infinite;
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
        opacity: 0.5;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}
</style>
