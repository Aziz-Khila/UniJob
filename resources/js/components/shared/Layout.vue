<template>
    <div class="layout">
        <div class="container">
            <div class="animated-background">
                <div class="floating-orb orb-1"></div>
                <div class="floating-orb orb-2"></div>
                <div class="floating-orb orb-3"></div>
                <div class="floating-orb orb-4"></div>
                <div class="floating-orb orb-5"></div>
            </div>
            <slot />
        </div>
    </div>
</template>

<script>
export default {
    name: 'Layout',
};
</script>

<style scoped>
.layout {
    min-height: 100vh;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    position: relative;
}

.container {
    margin-top: 70px;
    overflow: visible;
    min-height: calc(100vh - 70px);
    padding-bottom: 3rem;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    position: relative;
    width: 100%;
}

.container::before {
    display: none;
}

.container::after {
    display: none;
}


.animated-background {
    display: none;
}

.floating-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    will-change: transform;
}

.orb-1 {
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.4) 0%, rgba(99, 102, 241, 0.1) 50%, transparent 100%);
    top: 10%;
    left: 5%;
    animation: float-orb-1 20s ease-in-out infinite;
}

.orb-2 {
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.4) 0%, rgba(16, 185, 129, 0.1) 50%, transparent 100%);
    top: 60%;
    right: 10%;
    animation: float-orb-2 25s ease-in-out infinite;
}

.orb-3 {
    width: 350px;
    height: 350px;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.35) 0%, rgba(139, 92, 246, 0.1) 50%, transparent 100%);
    bottom: 15%;
    left: calc(50% - 175px);
    animation: float-orb-3 30s ease-in-out infinite;
}

.orb-4 {
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(236, 72, 153, 0.3) 0%, rgba(236, 72, 153, 0.1) 50%, transparent 100%);
    top: 40%;
    right: 20%;
    animation: float-orb-1 22s ease-in-out infinite reverse;
}

.orb-5 {
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(251, 146, 60, 0.3) 0%, rgba(251, 146, 60, 0.1) 50%, transparent 100%);
    top: 70%;
    left: 15%;
    animation: float-orb-2 28s ease-in-out infinite reverse;
}

.container > *:not(.animated-background) {
    position: relative;
    z-index: 1;
}

/* Animated floating orbs */
@keyframes float-orb-1 {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0.4;
    }
    33% {
        transform: translate(30px, -40px) scale(1.1);
        opacity: 0.6;
    }
    66% {
        transform: translate(-20px, 30px) scale(0.9);
        opacity: 0.5;
    }
}

@keyframes float-orb-2 {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50px, 50px) scale(1.2);
        opacity: 0.5;
    }
}

@keyframes float-orb-3 {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0.35;
    }
    25% {
        transform: translate(50px, -40px) scale(1.15);
        opacity: 0.55;
    }
    50% {
        transform: translate(-30px, 30px) scale(0.9);
        opacity: 0.4;
    }
    75% {
        transform: translate(-50px, -50px) scale(1.1);
        opacity: 0.5;
    }
}

@keyframes gradient-pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
        filter: brightness(1) hue-rotate(0deg);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
        filter: brightness(1.1) hue-rotate(5deg);
    }
}
</style>

