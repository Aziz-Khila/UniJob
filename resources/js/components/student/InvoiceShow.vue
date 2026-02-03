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
                <div class="invoice-show-page">
                    <router-link to="/student/invoices" class="back-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        Back to Invoices
                    </router-link>
                    <div class="dashboard-header">
                        <div class="header-content">
                            <div class="header-text">
                                <h1 class="header-title">Invoice {{ invoice ? invoice.invoice_number : '' }}</h1>
                                <p class="header-subtitle">
                                    <span v-if="invoice">
                                        Issued: {{ formatDate(invoice.issued_at) }}
                                        <span v-if="invoice.paid_at"> • Paid: {{ formatDate(invoice.paid_at) }}</span>
                                    </span>
                                    <span v-else>Loading invoice details...</span>
                                </p>
                            </div>
                            <div v-if="invoice" class="header-actions">
                                <button @click="downloadInvoice" class="download-btn" :disabled="downloading">
                                    <svg v-if="!downloading" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                    <span v-else>Downloading...</span>
                                    <span v-if="!downloading">Download PDF</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="loading" class="loading">Loading invoice...</div>
                    <div v-else-if="invoice" class="invoice-detail">
                        <div class="invoice-info-grid">
                            <div class="info-card">
                                <h3>Job Information</h3>
                                <div class="info-item">
                                    <span class="info-label">Job Title:</span>
                                    <span class="info-value">{{ invoice.job?.title }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Job Description:</span>
                                    <span class="info-value">{{ invoice.job?.description }}</span>
                                </div>
                            </div>
                            <div class="info-card">
                                <h3>Client Information</h3>
                                <div class="info-item">
                                    <span class="info-label">Name:</span>
                                    <span class="info-value">{{ invoice.client?.name }}</span>
                                </div>
                                <div class="info-item" v-if="invoice.client?.email">
                                    <span class="info-label">Email:</span>
                                    <span class="info-value">{{ invoice.client.email }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Full summary with all details highlighted (student view) -->
                        <div class="invoice-summary">
                            <h2>Invoice Summary</h2>
                            <div class="summary-table student-summary">
                                <div class="summary-row">
                                    <span>Job Budget (Total Amount):</span>
                                    <span class="amount">{{ formatMoney(invoice.total_amount) }} TND</span>
                                </div>
                                <div class="summary-row commission">
                                    <span>Platform Commission ({{ invoice.commission_rate }}%):</span>
                                    <span class="amount negative">-{{ formatMoney(invoice.commission_amount) }} TND</span>
                                </div>
                                <div class="summary-row total">
                                    <span>Your Payment:</span>
                                    <span class="amount positive">{{ formatMoney(invoice.student_amount) }} TND</span>
                                </div>
                            </div>
                        </div>
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
    name: 'StudentInvoiceShow',
    components: {
        Layout,
    },
    data() {
        return {
            loading: true,
            invoice: null,
            downloading: false,
        };
    },
    async mounted() {
        await this.loadInvoice();
    },
    methods: {
        async loadInvoice() {
            this.loading = true;
            try {
                const response = await api.get(`/student/invoices/${this.$route.params.id}`);
                this.invoice = response.data;
            } catch (error) {
                console.error('Failed to load invoice:', error);
            } finally {
                this.loading = false;
            }
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
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
        async downloadInvoice() {
            this.downloading = true;
            try {
                const token = localStorage.getItem('token');
                const baseURL = window.location.origin;
                const response = await fetch(`${baseURL}/api/student/invoices/${this.invoice.id}/download`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/pdf',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to download invoice');
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `invoice-${this.invoice.invoice_number}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            } catch (error) {
                console.error('Error downloading invoice:', error);
                alert('Failed to download invoice. Please try again.');
            } finally {
                this.downloading = false;
            }
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

.invoice-show-page {
    padding: 2rem;
    padding-bottom: 4rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
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

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
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
}

.header-subtitle {
    font-size: 1.0625rem;
    color: #6b7280;
    margin: 0;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.download-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9375rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}

.download-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #4f46e5 0%, #1d4ed8 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}

.download-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.download-btn svg {
    width: 18px;
    height: 18px;
}

.invoice-detail {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.8);
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.invoice-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.info-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
}

.info-card h3 {
    margin: 0 0 1rem 0;
    color: #0f172a;
    font-size: 1.125rem;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 0.75rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    margin-bottom: 1rem;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.info-value {
    font-size: 0.9375rem;
    color: #0f172a;
}

.invoice-summary {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    padding: 2rem;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
}

.invoice-summary h2 {
    margin: 0 0 1.5rem 0;
    color: #0f172a;
    font-size: 1.5rem;
}

.summary-table {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    font-size: 1rem;
}

.summary-row.commission {
    background: #fef2f2;
    border-left: 4px solid #ef4444;
}

.summary-row.total {
    background: #f0fdf4;
    border-left: 4px solid #10b981;
    font-weight: 600;
    font-size: 1.125rem;
}

.amount {
    font-weight: 600;
    font-size: 1.125rem;
}

.amount.negative {
    color: #ef4444;
}

.amount.positive {
    color: #10b981;
}

.loading {
    text-align: center;
    padding: 4rem;
    color: #64748b;
}
</style>
