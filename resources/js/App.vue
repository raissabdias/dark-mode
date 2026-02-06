<script setup>
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';
</script>

<template>
    <div class="layout-wrapper">
        <Header />
        <main class="main-container">
            <router-view></router-view>
        </main>
        <Footer />
    </div>
</template>

<style scoped>
.layout-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.main-container {
    flex: 1;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 16px;
}

@media (min-width: 768px) {
    .main-container {
        padding: 40px 24px;
    }
}
</style>