<template>
    <Sidebar v-if="showNavbar" />
    <router-view v-slot="{ Component, route }">
        <transition :name="getTransitionName(route)" mode="out-in">
            <component :is="Component" :key="route.path" />
        </transition>
    </router-view>
</template>

<script>
import Sidebar from './components/shared/Sidebar.vue';

export default {
    name: 'App',
    components: {
        Sidebar,
    },
    data() {
        return {
            currentRoute: null,
        };
    },
    computed: {
        showNavbar() {
            // Hide navbar on landing/login/register pages
            if (!this.currentRoute) return false;
            return this.currentRoute.name !== 'landing' && 
                   this.currentRoute.name !== 'login' && 
                   this.currentRoute.name !== 'register';
        },
    },
    watch: {
        '$route'(to) {
            this.currentRoute = to;
        },
    },
    mounted() {
        this.currentRoute = this.$route;
    },
    methods: {
        getTransitionName(route) {
            // Use slide transition for auth pages (login/register)
            if (route.name === 'login' || route.name === 'register') {
                return 'slide-fade';
            }
            // No transition for landing page
            if (route.name === 'landing') {
                return '';
            }
            // Use fade for all other pages
            return 'fade';
        }
    }
}
</script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    background: #f8fafc;
    overflow-x: hidden;
    height: auto;
}

body {
    font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    background: #f8fafc;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    overflow-y: auto;
    position: relative;
    min-height: 100vh;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
}

/* Prevent scrollbar during transitions */
body.transitioning {
    overflow: hidden !important;
}

/* Slide-fade transition for login/register pages */
.slide-fade-enter-active {
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(0.55, 0.06, 0.68, 0.19);
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.slide-fade-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}

/* Fade transition for other pages */
.fade-enter-active {
    transition: opacity 0.25s ease;
}

.fade-leave-active {
    transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Ensure navbar stays fixed during transitions */
.fade-enter-active .layout,
.fade-leave-active .layout {
    position: relative;
}

.fade-enter-active .layout .navbar,
.fade-leave-active .layout .navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
}

/* Respect reduced motion preference */
@media (prefers-reduced-motion: reduce) {
    .slide-fade-enter-active,
    .slide-fade-leave-active,
    .fade-enter-active,
    .fade-leave-active {
        transition: none;
    }
    
    .slide-fade-enter-from,
    .slide-fade-leave-to {
        opacity: 1;
        transform: translateX(0);
    }
    
    .fade-enter-from,
    .fade-leave-to {
        opacity: 1;
    }
}
</style>

