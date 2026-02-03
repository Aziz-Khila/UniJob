<template>
    <Layout>
        <div class="dashboard-wrapper">
            <div class="dashboard-orbs">
                <div class="dashboard-orb orb-1"></div>
                <div class="dashboard-orb orb-2"></div>
                <div class="dashboard-orb orb-3"></div>
                <div class="dashboard-orb orb-4"></div>
            </div>
            <div class="dashboard-main">
                <div class="dashboard-header">
                    <div class="header-content">
                        <div class="header-text">
                            <h1 class="header-title">Jobs History</h1>
                            <p class="header-subtitle">All jobs – client pays platform, platform pays student</p>
                        </div>
                    </div>
                </div>
                <div class="section-card">
                    <div class="section-header">
                        <select v-model="statusFilter" @change="loadJobs" class="filter-select">
                            <option value="">All statuses</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        <button @click="loadJobs" class="refresh-btn">Refresh</button>
                    </div>

                    <div v-if="loading" class="loading-state">Loading jobs...</div>
                    <div v-else-if="jobs?.data?.length > 0" class="jobs-table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Job</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Budget</th>
                                    <th>Affected students</th>
                                    <th>Invoices</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="job in jobs.data" :key="job.id">
                                    <td>
                                        <strong>{{ job.title }}</strong>
                                        <span v-if="job.category" class="meta-tag">{{ job.category }}</span>
                                    </td>
                                    <td>
                                        <span v-if="job.client">{{ job.client.name }}</span>
                                        <span v-else>—</span>
                                    </td>
                                    <td>
                                        <span class="status-badge" :class="getJobStatusClass(job.status)">{{ capitalize(job.status) }}</span>
                                    </td>
                                    <td>{{ job.budget != null ? formatMoney(job.budget) + ' TND' : '—' }}</td>
                                    <td>
                                        <span v-if="job.affected_students?.length" class="students-list">
                                            <template v-for="(s, i) in job.affected_students" :key="s.id">
                                                {{ s.name }}<template v-if="i < job.affected_students.length - 1">, </template>
                                            </template>
                                        </span>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                    <td>
                                        <span v-if="job.invoices_count > 0">{{ job.invoices_count }} invoice(s)</span>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                    <td>{{ formatDate(job.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="empty-state">No jobs found.</div>

                    <div v-if="jobs?.links" class="pagination">
                        <button v-if="jobs.links.prev" @click="loadJobs(getPageFromUrl(jobs.links.prev))" class="pagination-btn">« Previous</button>
                        <span class="pagination-info">Page {{ jobs.current_page }} of {{ jobs.last_page }}</span>
                        <button v-if="jobs.links.next" @click="loadJobs(getPageFromUrl(jobs.links.next))" class="pagination-btn">Next »</button>
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
    name: 'AdminJobsHistory',
    components: { Layout },
    data() {
        return {
            loading: false,
            jobs: null,
            statusFilter: '',
        };
    },
    mounted() {
        this.loadJobs();
    },
    methods: {
        async loadJobs(page = 1) {
            this.loading = true;
            try {
                const params = { page };
                if (this.statusFilter) params.status = this.statusFilter;
                const response = await api.get('/admin/jobs', { params });
                this.jobs = response.data;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        getPageFromUrl(url) {
            const m = url && url.match(/page=(\d+)/);
            return m ? parseInt(m[1]) : 1;
        },
        formatDate(d) {
            return d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
        },
        formatMoney(n) {
            const x = Number(n);
            return isNaN(x) ? '0.00' : x.toFixed(2);
        },
        capitalize(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ') : '';
        },
        getJobStatusClass(s) {
            return { open: 'badge-info', in_progress: 'badge-warning', completed: 'badge-success' }[s] || 'badge-secondary';
        },
    },
};
</script>

<style scoped>
.dashboard-wrapper { display: grid; grid-template-columns: 1fr; gap: 1.5rem; min-height: calc(100vh - 70px); padding: 1.5rem; max-width: 1600px; margin: 0 auto; background: transparent; position: relative; overflow: hidden; }
.dashboard-orbs { position: fixed; top: 70px; left: 0; right: 0; bottom: 0; width: 100vw; height: calc(100vh - 70px); pointer-events: none; z-index: 0; overflow: visible; }
.dashboard-orb { position: absolute; border-radius: 50%; filter: blur(80px); will-change: transform; }
.dashboard-orb.orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(99, 102, 241, 0.4) 0%, rgba(99, 102, 241, 0.2) 50%, rgba(99, 102, 241, 0) 80%); top: 10%; left: -10%; animation: float-orb-1 40s ease-in-out infinite; }
.dashboard-orb.orb-2 { width: 450px; height: 450px; background: radial-gradient(circle, rgba(16, 185, 129, 0.4) 0%, rgba(16, 185, 129, 0.2) 50%, rgba(16, 185, 129, 0) 80%); top: 30%; right: -10%; animation: float-orb-2 45s ease-in-out infinite; }
.dashboard-orb.orb-3 { width: 480px; height: 480px; background: radial-gradient(circle, rgba(139, 92, 246, 0.4) 0%, rgba(139, 92, 246, 0.2) 50%, rgba(139, 92, 246, 0) 80%); bottom: 10%; left: -5%; animation: float-orb-3 50s ease-in-out infinite; }
.dashboard-orb.orb-4 { width: 450px; height: 450px; background: radial-gradient(circle, rgba(16, 185, 129, 0.35) 0%, rgba(16, 185, 129, 0.18) 50%, rgba(16, 185, 129, 0) 80%); top: 60%; left: 50%; transform: translateX(-50%); animation: float-orb-4 42s ease-in-out infinite; }
@keyframes float-orb-1 { 0% { transform: translate(0, 0) scale(1); } 25% { transform: translate(calc(50vw + 100px), 30vh) scale(1.15); } 50% { transform: translate(calc(90vw + 200px), -20vh) scale(0.95); } 75% { transform: translate(calc(60vw + 150px), 50vh) scale(1.1); } 100% { transform: translate(0, 0) scale(1); } }
@keyframes float-orb-2 { 0% { transform: translate(0, 0) scale(1); } 33% { transform: translate(calc(-40vw - 100px), 40vh) scale(1.2); } 66% { transform: translate(calc(-80vw - 200px), -30vh) scale(0.9); } 100% { transform: translate(0, 0) scale(1); } }
@keyframes float-orb-3 { 0% { transform: translate(0, 0) scale(1); } 20% { transform: translate(calc(40vw + 150px), -25vh) scale(1.18); } 40% { transform: translate(calc(85vw + 250px), 35vh) scale(0.92); } 60% { transform: translate(calc(25vw + 100px), 70vh) scale(1.12); } 80% { transform: translate(calc(-15vw - 50px), 25vh) scale(1.05); } 100% { transform: translate(0, 0) scale(1); } }
@keyframes float-orb-4 { 0% { transform: translate(-50%, 0) scale(1); } 25% { transform: translate(calc(-50% + 35vw + 100px), -40vh) scale(1.22); } 50% { transform: translate(calc(-50% - 25vw - 100px), 30vh) scale(0.88); } 75% { transform: translate(calc(-50% + 45vw + 150px), 20vh) scale(1.15); } 100% { transform: translate(-50%, 0) scale(1); } }
.dashboard-main { position: relative; z-index: 1; display: flex; flex-direction: column; gap: 1.5rem; }
.dashboard-header { background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-radius: 20px; padding: 2rem; border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.9); position: relative; overflow: hidden; }
.dashboard-header::before { content: ''; position: absolute; top: 0; right: 0; width: 200px; height: 200px; background: radial-gradient(circle, rgba(99, 102, 241, 0.06) 0%, transparent 70%); border-radius: 50%; pointer-events: none; }
.header-content { display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; position: relative; z-index: 1; flex-wrap: wrap; }
.header-text { flex: 1; }
.header-title { font-size: 2.25rem; font-weight: 800; background: linear-gradient(135deg, #111827 0%, #374151 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0 0 0.5rem 0; letter-spacing: -0.03em; line-height: 1.2; }
.header-subtitle { font-size: 1.0625rem; color: #6b7280; margin: 0; font-weight: 500; letter-spacing: -0.01em; }
.section-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-radius: 20px; padding: 2rem; border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04); }
.section-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
.filter-select { padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid #e5e7eb; }
.refresh-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; margin-left: auto; }
.jobs-table-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
.data-table th { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; }
.data-table td { font-size: 0.9375rem; color: #111827; }
.students-list { font-size: 0.875rem; }
.meta-tag { display: inline-block; margin-left: 0.5rem; font-size: 0.75rem; color: #6b7280; background: #f3f4f6; padding: 0.15rem 0.5rem; border-radius: 4px; }
.status-badge { padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
.badge-info { background: #dbeafe; color: #1e40af; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-secondary { background: #f1f5f9; color: #475569; }
.text-muted { color: #9ca3af; }
.empty-state { text-align: center; padding: 2rem; color: #6b7280; }
.pagination { display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 1.5rem; }
.pagination-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; }
.pagination-info { font-size: 0.875rem; color: #6b7280; }
.loading-state { text-align: center; padding: 2rem; color: #6b7280; }
</style>
