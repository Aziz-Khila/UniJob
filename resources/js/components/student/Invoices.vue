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
                <div class="invoices-page">
                    <div class="dashboard-header">
                        <div class="header-content">
                            <div class="header-text">
                                <h1 class="header-title">My Invoices</h1>
                                <p class="header-subtitle">View earnings and payment details</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="loading" class="loading">Loading invoices...</div>
                    <div v-else-if="invoices?.data?.length > 0" class="invoices-list">
                        <div v-for="invoice in invoices.data" :key="invoice.id" class="invoice-card">
                            <div class="invoice-header">
                                <div class="invoice-info">
                                    <router-link :to="`/student/invoices/${invoice.id}`" class="invoice-number">
                                        {{ invoice.invoice_number }}
                                    </router-link>
                                    <div class="invoice-meta">
                                        <span>📋 Job: {{ invoice.job?.title }}</span>
                                        <span>•</span>
                                        <span>👤 Client: {{ invoice.client?.name }}</span>
                                        <span>•</span>
                                        <span>📅 Issued: {{ formatDate(invoice.issued_at) }}</span>
                                    </div>
                                </div>
                                <div class="invoice-amount">
                                    <div class="amount-label">Your Payment</div>
                                    <div class="amount-value highlight-positive">{{ formatMoney(invoice.student_amount) }} TND</div>
                                    <span class="badge" :class="getStatusClass(invoice.status)">
                                        {{ capitalize(invoice.status) }}
                                    </span>
                                </div>
                            </div>
                            <!-- Full breakdown with all details highlighted (student view) -->
                            <div class="invoice-breakdown student-breakdown">
                                <div class="breakdown-item">
                                    <span>Job Budget (Total Amount):</span>
                                    <span>{{ formatMoney(invoice.total_amount) }} TND</span>
                                </div>
                                <div class="breakdown-item commission">
                                    <span>Platform Commission ({{ invoice.commission_rate }}%):</span>
                                    <span class="negative">-{{ formatMoney(invoice.commission_amount) }} TND</span>
                                </div>
                                <div class="breakdown-item student-payment">
                                    <span>Your Payment:</span>
                                    <span class="positive">{{ formatMoney(invoice.student_amount) }} TND</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state">
                        <p>No invoices yet. Invoices will appear here once your completed jobs are paid.</p>
                    </div>

                    <div v-if="invoices?.links" class="pagination">
                        <button v-if="invoices.links.prev" @click="loadPage(getPageFromUrl(invoices.links.prev))" class="pagination-btn">« Previous</button>
                        <span class="pagination-info">Page {{ invoices.current_page }} of {{ invoices.last_page }}</span>
                        <button v-if="invoices.links.next" @click="loadPage(getPageFromUrl(invoices.links.next))" class="pagination-btn">Next »</button>
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
    name: 'StudentInvoices',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            invoices: null,
        };
    },
    async mounted() {
        await this.loadInvoices();
    },
    methods: {
        async loadInvoices(page = 1) {
            this.loading = true;
            try {
                const response = await api.get('/student/invoices', { params: { page } });
                this.invoices = response.data;
            } catch (error) {
                console.error('Failed to load invoices:', error);
            } finally {
                this.loading = false;
            }
        },
        loadPage(page) {
            this.loadInvoices(page);
        },
        getPageFromUrl(url) {
            const match = url && url.match(/page=(\d+)/);
            return match ? parseInt(match[1]) : 1;
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        },
        formatMoney(amount) {
            const num = Number(amount) || 0;
            return num.toFixed(2);
        },
        capitalize(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
        },
        getStatusClass(status) {
            const classes = {
                sent: 'badge-warning',
                paid: 'badge-success',
                cancelled: 'badge-danger',
                draft: 'badge-secondary',
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
    0% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(calc(50vw + 100px), 30vh) scale(1.15); }
    50% { transform: translate(calc(90vw + 200px), -20vh) scale(0.95); }
    75% { transform: translate(calc(60vw + 150px), 50vh) scale(1.1); }
    100% { transform: translate(0, 0) scale(1); }
}

@keyframes float-dashboard-orb-2 {
    0% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(calc(-40vw - 100px), 40vh) scale(1.2); }
    66% { transform: translate(calc(-80vw - 200px), -30vh) scale(0.9); }
    100% { transform: translate(0, 0) scale(1); }
}

@keyframes float-dashboard-orb-3 {
    0% { transform: translate(0, 0) scale(1); }
    20% { transform: translate(calc(40vw + 150px), -25vh) scale(1.18); }
    40% { transform: translate(calc(85vw + 250px), 35vh) scale(0.92); }
    60% { transform: translate(calc(25vw + 100px), 70vh) scale(1.12); }
    80% { transform: translate(calc(-15vw - 50px), 25vh) scale(1.05); }
    100% { transform: translate(0, 0) scale(1); }
}

@keyframes float-dashboard-orb-4 {
    0% { transform: translate(-50%, 0) scale(1); }
    25% { transform: translate(calc(-50% + 35vw + 100px), -40vh) scale(1.22); }
    50% { transform: translate(calc(-50% - 25vw - 100px), 30vh) scale(0.88); }
    75% { transform: translate(calc(-50% + 45vw + 150px), 20vh) scale(1.15); }
    100% { transform: translate(-50%, 0) scale(1); }
}

.dashboard-main {
    position: relative;
    z-index: 1;
}

.invoices-page {
    padding: 2rem;
    padding-bottom: 4rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.dashboard-header {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
    margin-bottom: 1.5rem;
}

.header-title {
    font-size: 2.25rem;
    font-weight: 800;
    background: linear-gradient(135deg, #111827 0%, #374151 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 0.5rem 0;
}

.header-subtitle {
    font-size: 1.0625rem;
    color: #6b7280;
    margin: 0;
}

.invoices-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.invoice-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.8);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.invoice-card:hover {
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
    border-color: rgba(99, 102, 241, 0.2);
}

.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.invoice-number {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
    text-decoration: none;
    display: block;
    margin-bottom: 0.5rem;
}

.invoice-number:hover {
    color: #0B3C8A;
}

.invoice-meta {
    display: flex;
    gap: 0.5rem;
    color: #64748b;
    font-size: 0.875rem;
    flex-wrap: wrap;
}

.invoice-amount {
    text-align: right;
}

.amount-value.highlight-positive {
    color: #10b981;
    font-weight: 700;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.badge-warning { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; }
.badge-success { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; }
.badge-danger { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; }
.badge-secondary { background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); color: #475569; }

.invoice-breakdown.student-breakdown {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9375rem;
}

.breakdown-item span:first-child {
    color: #64748b;
}

.breakdown-item span:last-child,
.breakdown-item .positive,
.breakdown-item .negative {
    font-weight: 600;
}

.breakdown-item.commission {
    background: #fef2f2;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border-left: 4px solid #ef4444;
}

.breakdown-item.commission .negative {
    color: #ef4444;
}

.breakdown-item.student-payment {
    background: #f0fdf4;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border-left: 4px solid #10b981;
}

.breakdown-item.student-payment .positive {
    color: #10b981;
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
    background: rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.8);
}

.loading {
    text-align: center;
    padding: 4rem;
    color: #64748b;
}
</style>
