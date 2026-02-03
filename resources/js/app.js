import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';

// Import components
import Landing from './components/Landing.vue';
import Login from './components/auth/Login.vue';
import Register from './components/auth/Register.vue';
import StudentDashboard from './components/student/Dashboard.vue';
import StudentProfile from './components/student/Profile.vue';
import StudentProfileEdit from './components/student/ProfileEdit.vue';
import StudentJobsBrowse from './components/student/JobsBrowse.vue';
import StudentJobShow from './components/student/JobShow.vue';
import StudentJobApply from './components/student/JobApply.vue';
import StudentApplications from './components/student/Applications.vue';
import StudentInvoices from './components/student/Invoices.vue';
import StudentInvoiceShow from './components/student/InvoiceShow.vue';
import ClientDashboard from './components/client/Dashboard.vue';
import ClientJobs from './components/client/Jobs.vue';
import ClientJobCreate from './components/client/JobCreate.vue';
import ClientJobEdit from './components/client/JobEdit.vue';
import ClientJobShow from './components/client/JobShow.vue';
import ClientInvoices from './components/client/Invoices.vue';
import ClientInvoiceShow from './components/client/InvoiceShow.vue';
import AdminDashboard from './components/admin/Dashboard.vue';
import AdminUsers from './components/admin/Users.vue';
import AdminJobsHistory from './components/admin/JobsHistory.vue';
import AdminInvoices from './components/admin/Invoices.vue';

// Router configuration
const routes = [
    { path: '/', name: 'landing', component: Landing, meta: { guest: true } },
    { path: '/login', name: 'login', component: Login, meta: { guest: true } },
    { path: '/register', name: 'register', component: Register, meta: { guest: true } },
    { path: '/student/dashboard', name: 'student.dashboard', component: StudentDashboard, meta: { requiresAuth: true, role: 'student' } },
    { path: '/student/profile', name: 'student.profile', component: StudentProfile, meta: { requiresAuth: true, role: 'student' } },
    { path: '/student/profile/edit', name: 'student.profile.edit', component: StudentProfileEdit, meta: { requiresAuth: true, role: 'student' } },
    { path: '/student/jobs', name: 'student.jobs.browse', component: StudentJobsBrowse, meta: { requiresAuth: true, role: 'student' } },
    { path: '/student/jobs/:id', name: 'student.jobs.show', component: StudentJobShow, meta: { requiresAuth: true, role: 'student' } },
    { path: '/student/jobs/:id/apply', name: 'student.jobs.apply', component: StudentJobApply, meta: { requiresAuth: true, role: 'student' } },
    { path: '/student/applications', name: 'student.applications', component: StudentApplications, meta: { requiresAuth: true, role: 'student' } },
    { path: '/student/invoices', name: 'student.invoices', component: StudentInvoices, meta: { requiresAuth: true, role: 'student' } },
    { path: '/student/invoices/:id', name: 'student.invoices.show', component: StudentInvoiceShow, meta: { requiresAuth: true, role: 'student' } },
    { path: '/client/dashboard', name: 'client.dashboard', component: ClientDashboard, meta: { requiresAuth: true, role: 'client' } },
    { path: '/client/jobs', name: 'client.jobs', component: ClientJobs, meta: { requiresAuth: true, role: 'client' } },
    { path: '/client/jobs/create', name: 'client.jobs.create', component: ClientJobCreate, meta: { requiresAuth: true, role: 'client' } },
    { path: '/client/jobs/:id/edit', name: 'client.jobs.edit', component: ClientJobEdit, meta: { requiresAuth: true, role: 'client' } },
    { path: '/client/jobs/:id', name: 'client.jobs.show', component: ClientJobShow, meta: { requiresAuth: true, role: 'client' } },
    { path: '/client/invoices', name: 'client.invoices', component: ClientInvoices, meta: { requiresAuth: true, role: 'client' } },
    { path: '/client/invoices/:id', name: 'client.invoices.show', component: ClientInvoiceShow, meta: { requiresAuth: true, role: 'client' } },
    { path: '/admin/dashboard', name: 'admin.dashboard', component: AdminDashboard, meta: { requiresAuth: true, role: 'admin' } },
    { path: '/admin/users', name: 'admin.users', component: AdminUsers, meta: { requiresAuth: true, role: 'admin' } },
    { path: '/admin/jobs', name: 'admin.jobs', component: AdminJobsHistory, meta: { requiresAuth: true, role: 'admin' } },
    { path: '/admin/invoices', name: 'admin.invoices', component: AdminInvoices, meta: { requiresAuth: true, role: 'admin' } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
    // Prevent scrollbar flickering during transition
    document.body.classList.add('transitioning');
    
    const token = localStorage.getItem('token');
    const userStr = localStorage.getItem('user');
    const user = userStr ? JSON.parse(userStr) : null;
    
    if (to.meta.requiresAuth && !token) {
        next({ name: 'login' });
    } else if (to.meta.guest && token) {
        // Redirect authenticated users away from login/register
        if (user && user.role === 'student') {
            next({ name: 'student.dashboard' });
        } else if (user && user.role === 'client') {
            next({ name: 'client.dashboard' });
        } else if (user && user.role === 'admin') {
            next({ name: 'admin.dashboard' });
        } else {
            next();
        }
    } else if (to.meta.role && user && user.role !== to.meta.role) {
        // Redirect if user doesn't have the required role
        if (user.role === 'student') {
            next({ name: 'student.dashboard' });
        } else if (user.role === 'client') {
            next({ name: 'client.dashboard' });
        } else if (user.role === 'admin') {
            next({ name: 'admin.dashboard' });
        } else {
            next({ name: 'login' });
        }
    } else {
        next();
    }
});

router.afterEach(() => {
    // Remove transitioning class after route change
    setTimeout(() => {
        document.body.classList.remove('transitioning');
    }, 500); // Wait for transition to complete
});

// Create Vue app
const app = createApp(App);
app.use(router);
app.mount('#app');
