<script setup>
import { ref } from "vue";
import Menubar from 'primevue/menubar';

// Removi o import do InputText pois não vamos mais usar

const items = ref([
    { label: 'Home', icon: 'pi pi-home', route: '/' },
    { label: 'Notícias', icon: 'pi pi-megaphone', route: '/news' },
    { label: 'Reviews', icon: 'pi pi-star', route: '/reviews' },
    { label: 'Agenda', icon: 'pi pi-calendar', route: '/agenda' }
]);
</script>

<template>
    <div class="header-wrapper">
        <Menubar :model="items" class="custom-menubar">
            <template #start>
                <a href="/" class="flex items-center gap-2 mr-8">
                    <img src="/images/logo.png" alt="DarkMode Logo"
                        class="h-10 w-auto object-contain hover:opacity-80 transition-opacity" />
                </a>
            </template>
            
            <template #end>
                <div class="social-group">
                    <a href="https://instagram.com" target="_blank" class="icon-link">
                        <i class="pi pi-instagram"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" class="icon-link">
                        <i class="pi pi-twitter"></i>
                    </a>
                    <a href="https://youtube.com" target="_blank" class="icon-link">
                        <i class="pi pi-youtube"></i>
                    </a>
                    <a href="https://facebook.com" target="_blank" class="icon-link">
                        <i class="pi pi-facebook"></i>
                    </a>
                </div>
            </template>
        </Menubar>
    </div>
</template>

<style scoped>
.header-wrapper {
    margin-bottom: 2rem;
    position: sticky;
    top: 0;
    z-index: 1000;
}

:deep(.p-menubar) {
    background-color: rgba(10, 10, 10, 0.95) !important;
    border: none !important;
    border-bottom: 1px solid #333 !important;
    border-radius: 0 !important;
    padding: 1rem 2rem;
}

:deep(.p-menuitem-text),
:deep(.p-menuitem-icon) {
    color: #e2e8f0 !important;
}

:deep(.p-menuitem-link:hover .p-menuitem-text),
:deep(.p-menuitem-link:hover .p-menuitem-icon) {
    color: #a855f7 !important;
}

/* --- Estilo dos Ícones Sociais --- */
.icon-link {
    color: #cccccc;
    /* Cinza claro padrão */
    text-decoration: none;
    font-size: 1.3rem;
    /* Tamanho do ícone */
    transition: all 0.3s ease;
    /* Animação suave */
    display: flex;
    align-items: center;
}

.icon-link:hover {
    color: #a855f7;
    /* Roxo Neon ao passar o mouse */
    transform: translateY(-2px);
    /* Pulo sutil para cima */
    text-shadow: 0 0 8px rgba(168, 85, 247, 0.6);
    /* Brilho neon */
}

/* Logo */
.logo-text {
    font-family: 'Grenze Gotisch', cursive;
    letter-spacing: 2px;
}

.social-group {
    display: flex;
    align-items: center;
    gap: 25px;
    /* <--- AQUI É O CONTROLE DA DISTÂNCIA. Aumente se quiser mais. */
}

/* --- Estilo dos Ícones Sociais (Mantido e Refinado) --- */
.icon-link {
    color: #cccccc;
    text-decoration: none;
    font-size: 1.4rem;
    /* Aumentei um pouquinho o ícone também */
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-link:hover {
    color: #a855f7;
    transform: translateY(-3px);
    text-shadow: 0 0 10px rgba(168, 85, 247, 0.8);
}
</style>