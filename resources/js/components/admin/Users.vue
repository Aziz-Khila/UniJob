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
                            <h1 class="header-title">Manage Users</h1>
                            <p class="header-subtitle">View and manage students and clients</p>
                        </div>
                    </div>
                </div>
                <div class="section-card">
                    <div class="section-header">
                        <div class="user-type-tabs">
                            <button @click="switchUserType('student')" :class="['tab-btn', { active: userType === 'student' }]">Students</button>
                            <button @click="switchUserType('client')" :class="['tab-btn', { active: userType === 'client' }]">Clients</button>
                        </div>
                        <select v-show="userType === 'student'" v-model="userFilter" @change="loadUsers" class="filter-select">
                            <option value="">All Students</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <button @click="loadUsers" class="refresh-btn">Refresh</button>
                    </div>

                    <div v-if="loadingUsers" class="loading-state">Loading...</div>
                    <div v-else-if="users?.data?.length > 0" class="users-table-wrap">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th v-if="userType === 'student'">University</th>
                                    <th v-if="userType === 'student'">Status</th>
                                    <th v-if="userType === 'client'">Joined</th>
                                    <th v-if="userType === 'student'">Verified</th>
                                    <th>Banned</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id">
                                    <td><strong>{{ user.name }}</strong></td>
                                    <td>{{ user.email }}</td>
                                    <td v-if="userType === 'student'">{{ user.university || '—' }}</td>
                                    <td v-if="userType === 'student'">
                                        <span class="status-badge" :class="getStatusClass(user.verification_status)">{{ capitalize(user.verification_status || 'pending') }}</span>
                                    </td>
                                    <td v-if="userType === 'client'">{{ formatDate(user.created_at) }}</td>
                                    <td v-if="userType === 'student'">{{ user.verified_at ? formatDate(user.verified_at) : '—' }}</td>
                                    <td>
                                        <span v-if="user.banned_at" class="status-badge badge-banned" :title="user.ban_reason || 'Banned'">Banned</span>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <template v-if="user.id !== currentUserId">
                                                <button v-if="user.banned_at" @click="openUnbanModal(user)" :disabled="actionLoading" class="btn-action btn-unban">Unban</button>
                                                <button v-else @click="openBanModal(user)" :disabled="actionLoading" class="btn-action btn-ban">Ban</button>
                                                <button @click="openDeleteModal(user)" :disabled="actionLoading" class="btn-action btn-delete">Delete</button>
                                            </template>
                                            <span v-else class="text-muted">—</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="empty-state">No {{ userType === 'student' ? 'students' : 'clients' }} found.</div>

                    <div v-if="users?.links" class="pagination">
                        <button v-if="users.links.prev" @click="loadUsers(getPageFromUrl(users.links.prev))" class="pagination-btn">« Previous</button>
                        <span class="pagination-info">Page {{ users.current_page }} of {{ users.last_page }}</span>
                        <button v-if="users.links.next" @click="loadUsers(getPageFromUrl(users.links.next))" class="pagination-btn">Next »</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ban modal -->
        <Teleport to="body">
            <div v-if="showBanModal" class="modal-overlay" @click.self="showBanModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3>Ban User</h3>
                        <button type="button" @click="showBanModal = false" class="modal-close" aria-label="Close">×</button>
                    </div>
                    <div class="modal-body">
                        <p v-if="selectedUser">Are you sure you want to ban <strong>{{ selectedUser.name }}</strong> ({{ selectedUser.email }})? They will not be able to sign in.</p>
                        <div class="form-group">
                            <label for="ban-reason">Reason (optional)</label>
                            <textarea id="ban-reason" v-model="banReason" rows="3" placeholder="Optional reason for the ban" class="form-textarea"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showBanModal = false" class="btn-cancel">Cancel</button>
                        <button type="button" @click="confirmBan" :disabled="actionLoading" class="btn-confirm btn-ban-confirm">Ban User</button>
                    </div>
                </div>
            </div>
        </Teleport>
        <!-- Unban modal -->
        <Teleport to="body">
            <div v-if="showUnbanModal" class="modal-overlay" @click.self="showUnbanModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3>Unban User</h3>
                        <button type="button" @click="showUnbanModal = false" class="modal-close" aria-label="Close">×</button>
                    </div>
                    <div class="modal-body">
                        <p v-if="selectedUserForUnban">Are you sure you want to unban <strong>{{ selectedUserForUnban.name }}</strong> ({{ selectedUserForUnban.email }})? They will be able to sign in again.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showUnbanModal = false" class="btn-cancel">Cancel</button>
                        <button type="button" @click="confirmUnban" :disabled="actionLoading" class="btn-confirm btn-unban-confirm">Unban</button>
                    </div>
                </div>
            </div>
        </Teleport>
        <!-- Delete modal -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3>Delete User</h3>
                        <button type="button" @click="showDeleteModal = false" class="modal-close" aria-label="Close">×</button>
                    </div>
                    <div class="modal-body">
                        <p v-if="selectedUserForDelete">Are you sure you want to delete <strong>{{ selectedUserForDelete.name }}</strong> ({{ selectedUserForDelete.email }})? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showDeleteModal = false" class="btn-cancel">Cancel</button>
                        <button type="button" @click="confirmDelete" :disabled="actionLoading" class="btn-confirm btn-delete-confirm">Delete User</button>
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
    name: 'AdminUsers',
    components: { Layout },
    data() {
        return {
            loadingUsers: false,
            users: null,
            userType: 'student',
            userFilter: '',
            actionLoading: false,
            showBanModal: false,
            selectedUser: null,
            banReason: '',
            showUnbanModal: false,
            selectedUserForUnban: null,
            showDeleteModal: false,
            selectedUserForDelete: null,
            alertMessage: null,
            alertType: 'success',
            currentUserId: null,
        };
    },
    mounted() {
        try {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            this.currentUserId = user?.id ?? null;
        } catch (_) {}
        this.loadUsers();
    },
    methods: {
        switchUserType(type) {
            this.userType = type;
            this.userFilter = '';
            this.loadUsers();
        },
        async loadUsers(page = 1) {
            this.loadingUsers = true;
            try {
                const params = { page };
                const endpoint = this.userType === 'student' ? '/admin/students' : '/admin/clients';
                if (this.userType === 'student' && this.userFilter) params.status = this.userFilter;
                const response = await api.get(endpoint, { params });
                this.users = response.data;
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingUsers = false;
            }
        },
        openBanModal(user) {
            this.selectedUser = user;
            this.banReason = '';
            this.showBanModal = true;
        },
        async confirmBan() {
            if (!this.selectedUser) return;
            this.actionLoading = true;
            try {
                await api.post(`/admin/users/${this.selectedUser.id}/ban`, { ban_reason: this.banReason.trim() || undefined });
                this.showBanModal = false;
                this.selectedUser = null;
                this.banReason = '';
                this.alertMessage = 'User has been banned.';
                this.alertType = 'success';
                await this.loadUsers(this.users?.current_page || 1);
            } catch (e) {
                this.alertMessage = e.response?.data?.message || 'Failed to ban user.';
                this.alertType = 'error';
            } finally {
                this.actionLoading = false;
            }
        },
        openUnbanModal(user) {
            this.selectedUserForUnban = user;
            this.showUnbanModal = true;
        },
        async confirmUnban() {
            if (!this.selectedUserForUnban) return;
            this.actionLoading = true;
            try {
                await api.post(`/admin/users/${this.selectedUserForUnban.id}/unban`);
                this.showUnbanModal = false;
                this.selectedUserForUnban = null;
                this.alertMessage = 'User has been unbanned.';
                this.alertType = 'success';
                await this.loadUsers(this.users?.current_page || 1);
            } catch (e) {
                this.alertMessage = e.response?.data?.message || 'Failed to unban user.';
                this.alertType = 'error';
            } finally {
                this.actionLoading = false;
            }
        },
        openDeleteModal(user) {
            this.selectedUserForDelete = user;
            this.showDeleteModal = true;
        },
        async confirmDelete() {
            if (!this.selectedUserForDelete) return;
            this.actionLoading = true;
            try {
                await api.delete(`/admin/users/${this.selectedUserForDelete.id}`);
                this.showDeleteModal = false;
                this.selectedUserForDelete = null;
                this.alertMessage = 'User has been deleted.';
                this.alertType = 'success';
                await this.loadUsers(this.users?.current_page || 1);
            } catch (e) {
                this.alertMessage = e.response?.data?.message || 'Failed to delete user.';
                this.alertType = 'error';
            } finally {
                this.actionLoading = false;
            }
        },
        getPageFromUrl(url) {
            const m = url && url.match(/page=(\d+)/);
            return m ? parseInt(m[1]) : 1;
        },
        formatDate(d) {
            return d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
        },
        capitalize(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
        },
        getStatusClass(s) {
            return { pending: 'badge-warning', approved: 'badge-success', rejected: 'badge-danger' }[s] || 'badge-secondary';
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
.section-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.user-type-tabs { display: flex; gap: 0.25rem; }
.tab-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; font-weight: 500; cursor: pointer; }
.tab-btn.active { background: #0B3C8A; color: #fff; border-color: #0B3C8A; }
.filter-select { padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid #e5e7eb; }
.refresh-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; margin-left: auto; }
.users-table-wrap { overflow-x: auto; }
.users-table { width: 100%; border-collapse: collapse; }
.users-table th, .users-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
.users-table th { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; }
.users-table td { font-size: 0.9375rem; color: #111827; }
.status-badge { padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.badge-secondary { background: #f1f5f9; color: #475569; }
.badge-banned { background: #fef2f2; color: #991b1b; }
.text-muted { color: #9ca3af; font-size: 0.875rem; }
.action-buttons { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.btn-action { padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600; cursor: pointer; border: none; }
.btn-action:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-ban { background: #fef2f2; color: #991b1b; }
.btn-unban { background: #d1fae5; color: #065f46; }
.btn-delete { background: #f3f4f6; color: #374151; }
/* Modal – match reference: white card, bold black title, prominent X, Cancel (gray) + action (red for destructive) */
.modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem; }
.modal-content { background: #fff; border-radius: 16px; min-width: 380px; max-width: 480px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05); overflow: hidden; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; }
.modal-header h3 { margin: 0; font-size: 1.25rem; font-weight: 700; color: #111827; }
.modal-close { background: none; border: none; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6b7280; font-size: 1.25rem; line-height: 1; border-radius: 8px; transition: background 0.15s, color 0.15s; }
.modal-close:hover { background: #f3f4f6; color: #111827; }
.modal-body { padding: 1.5rem 1.5rem; }
.modal-body p { margin: 0; color: #111827; font-size: 0.9375rem; line-height: 1.5; }
.form-group { margin-top: 1rem; margin-bottom: 0; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.35rem; }
.form-textarea { width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.9375rem; resize: vertical; background: #fff; }
.form-textarea.destructive-hint { background: #fef2f2; border-color: #fecaca; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background: #fafafa; }
.btn-cancel { padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #d1d5db; background: #f3f4f6; color: #374151; font-weight: 600; font-size: 0.9375rem; cursor: pointer; }
.btn-cancel:hover { background: #e5e7eb; }
.btn-confirm { padding: 0.5rem 1.25rem; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 0.9375rem; }
.btn-ban-confirm { background: #dc2626; color: #fff; }
.btn-ban-confirm:hover:not(:disabled) { background: #b91c1c; }
.btn-ban-confirm:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-unban-confirm { background: #059669; color: #fff; }
.btn-unban-confirm:hover:not(:disabled) { background: #047857; }
.btn-unban-confirm:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-delete-confirm { background: #dc2626; color: #fff; }
.btn-delete-confirm:hover:not(:disabled) { background: #b91c1c; }
.btn-delete-confirm:disabled { opacity: 0.7; cursor: not-allowed; }
.empty-state { text-align: center; padding: 2rem; color: #6b7280; }
.pagination { display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 1.5rem; }
.pagination-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; }
.pagination-info { font-size: 0.875rem; color: #6b7280; }
.loading-state { text-align: center; padding: 2rem; color: #6b7280; }
</style>
