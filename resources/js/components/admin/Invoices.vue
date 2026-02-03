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
                <div v-if="alertMessage" class="alert" :class="alertType" @click="alertMessage = null">
                    <span>{{ alertMessage }}</span>
                    <button type="button" class="alert-close" aria-label="Close">×</button>
                </div>
                <div class="dashboard-header">
                    <div class="header-content">
                        <div class="header-text">
                            <h1 class="header-title">Invoices</h1>
                            <p class="header-subtitle">Client pays platform → platform pays student</p>
                        </div>
                    </div>
                </div>
                <div class="section-card">
                    <div class="section-header">
                        <select v-model="statusFilter" @change="loadInvoices" class="filter-select">
                            <option value="">All statuses</option>
                            <option value="sent">Sent</option>
                            <option value="paid">Paid</option>
                            <option value="draft">Draft</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button @click="loadInvoices" class="refresh-btn">Refresh</button>
                    </div>

                    <div v-if="loading" class="loading-state">Loading invoices...</div>
                    <div v-else-if="invoices?.data?.length > 0" class="invoices-table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Job</th>
                                    <th>Client</th>
                                    <th>Student</th>
                                    <th>Client pays (TND)</th>
                                    <th>Commission (TND)</th>
                                    <th>Pay student (TND)</th>
                                    <th>Status</th>
                                    <th>Issued</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="inv in invoices.data" :key="inv.id">
                                    <td><strong>{{ inv.invoice_number }}</strong></td>
                                    <td>{{ inv.job?.title || '—' }}</td>
                                    <td>{{ inv.client?.name || '—' }}</td>
                                    <td>{{ inv.student?.name || '—' }}</td>
                                    <td>{{ formatMoney(inv.total_amount) }}</td>
                                    <td>{{ formatMoney(inv.commission_amount) }}</td>
                                    <td>{{ formatMoney(inv.student_amount) }}</td>
                                    <td>
                                        <span class="status-badge" :class="getInvoiceStatusClass(inv.status)">{{ capitalize(inv.status) }}</span>
                                    </td>
                                    <td>{{ formatDate(inv.issued_at) }}</td>
                                    <td>
                                        <button v-if="inv.status === 'sent'" @click="openMarkPaidModal(inv)" :disabled="markingPaidId === inv.id" class="btn-mark-paid">
                                            {{ markingPaidId === inv.id ? '...' : 'Mark paid' }}
                                        </button>
                                        <span v-else-if="inv.paid_at" class="paid-at">Paid {{ formatDate(inv.paid_at) }}</span>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="empty-state">No invoices found.</div>

                    <div v-if="invoices?.links" class="pagination">
                        <button v-if="invoices.links.prev" @click="loadInvoices(getPageFromUrl(invoices.links.prev))" class="pagination-btn">« Previous</button>
                        <span class="pagination-info">Page {{ invoices.current_page }} of {{ invoices.last_page }}</span>
                        <button v-if="invoices.links.next" @click="loadInvoices(getPageFromUrl(invoices.links.next))" class="pagination-btn">Next »</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mark paid modal -->
        <Teleport to="body">
            <div v-if="showMarkPaidModal" class="modal-overlay" @click.self="showMarkPaidModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3>Mark Invoice as Paid</h3>
                        <button type="button" @click="showMarkPaidModal = false" class="modal-close" aria-label="Close">×</button>
                    </div>
                    <div class="modal-body">
                        <p v-if="selectedInvoice">Are you sure you want to mark invoice <strong>{{ selectedInvoice.invoice_number }}</strong> as paid? (Client has paid the platform.)</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showMarkPaidModal = false" class="btn-cancel">Cancel</button>
                        <button type="button" @click="confirmMarkPaid" :disabled="markingPaidId" class="btn-confirm btn-mark-paid-confirm">Mark Paid</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </Layout>
</template>

<script>
import Layout from '../shared/Layout.vue';
import api from '../../api.js';

export default {
    name: 'AdminInvoices',
    components: { Layout },
    data() {
        return {
            loading: false,
            invoices: null,
            statusFilter: '',
            markingPaidId: null,
            showMarkPaidModal: false,
            selectedInvoice: null,
            alertMessage: null,
            alertType: 'success',
        };
    },
    mounted() {
        this.loadInvoices();
    },
    methods: {
        async loadInvoices(page = 1) {
            this.loading = true;
            try {
                const params = { page };
                if (this.statusFilter) params.status = this.statusFilter;
                const response = await api.get('/admin/invoices', { params });
                this.invoices = response.data;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        openMarkPaidModal(inv) {
            this.selectedInvoice = inv;
            this.showMarkPaidModal = true;
        },
        async confirmMarkPaid() {
            if (!this.selectedInvoice) return;
            this.markingPaidId = this.selectedInvoice.id;
            try {
                await api.post(`/admin/invoices/${this.selectedInvoice.id}/mark-paid`);
                this.showMarkPaidModal = false;
                this.selectedInvoice = null;
                this.alertMessage = 'Invoice marked as paid.';
                this.alertType = 'success';
                await this.loadInvoices(this.invoices.current_page);
            } catch (e) {
                this.alertMessage = e.response?.data?.message || 'Failed to mark as paid.';
                this.alertType = 'error';
            } finally {
                this.markingPaidId = null;
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
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
        },
        getInvoiceStatusClass(s) {
            return { sent: 'badge-warning', paid: 'badge-success', draft: 'badge-secondary', cancelled: 'badge-danger' }[s] || 'badge-secondary';
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
.alert { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 0; cursor: pointer; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
.alert-success { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border-color: #6ee7b7; }
.alert-error { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border-color: #fecaca; }
.alert-close { background: none; border: none; font-size: 1.5rem; color: inherit; cursor: pointer; opacity: 0.7; padding: 0; margin-left: 1rem; }
.section-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-radius: 20px; padding: 2rem; border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04); }
.section-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
.filter-select { padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid #e5e7eb; }
.refresh-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; margin-left: auto; }
.invoices-table-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; min-width: 900px; }
.data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
.data-table th { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; }
.data-table td { font-size: 0.875rem; color: #111827; }
.status-badge { padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.badge-secondary { background: #f1f5f9; color: #475569; }
.btn-mark-paid { padding: 0.35rem 0.75rem; border-radius: 6px; border: none; background: #0B3C8A; color: #fff; font-size: 0.8125rem; font-weight: 600; cursor: pointer; }
.btn-mark-paid:disabled { opacity: 0.7; cursor: not-allowed; }
.paid-at { font-size: 0.8125rem; color: #065f46; }
.text-muted { color: #9ca3af; }
/* Modal – match reference: white card, bold black title, prominent X, Cancel (gray) + action */
.modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem; }
.modal-content { background: #fff; border-radius: 16px; min-width: 380px; max-width: 480px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05); overflow: hidden; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; }
.modal-header h3 { margin: 0; font-size: 1.25rem; font-weight: 700; color: #111827; }
.modal-close { background: none; border: none; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6b7280; font-size: 1.25rem; line-height: 1; border-radius: 8px; transition: background 0.15s, color 0.15s; }
.modal-close:hover { background: #f3f4f6; color: #111827; }
.modal-body { padding: 1.5rem 1.5rem; }
.modal-body p { margin: 0; color: #111827; font-size: 0.9375rem; line-height: 1.5; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background: #fafafa; }
.btn-cancel { padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #d1d5db; background: #f3f4f6; color: #374151; font-weight: 600; font-size: 0.9375rem; cursor: pointer; }
.btn-cancel:hover { background: #e5e7eb; }
.btn-confirm { padding: 0.5rem 1.25rem; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 0.9375rem; }
.btn-mark-paid-confirm { background: #0B3C8A; color: #fff; }
.btn-mark-paid-confirm:hover:not(:disabled) { background: #093078; }
.btn-mark-paid-confirm:disabled { opacity: 0.7; cursor: not-allowed; }
.empty-state { text-align: center; padding: 2rem; color: #6b7280; }
.pagination { display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 1.5rem; }
.pagination-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; }
.pagination-info { font-size: 0.875rem; color: #6b7280; }
.loading-state { text-align: center; padding: 2rem; color: #6b7280; }
</style>
