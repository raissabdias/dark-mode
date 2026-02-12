<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

import NewsCard from '../components/NewsCard.vue';
import Sidebar from '../components/Sidebar.vue';
import Carousel from '../components/Carousel.vue';

const newsList = ref([]);
const isLoading = ref(true);
const router = useRouter();

const fetchNews = async () => {
    try {
        const response = await fetch('/api/news');
        const data = await response.json();
        newsList.value = data;
    } catch (error) {
        console.error('Erro ao carregar notícias:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchNews();
});

const openNews = (slug) => {
    if (slug) router.push(`/noticia/${slug}`);
};

const goToAllNews = () => {
    router.push({ path: '/noticias', query: { page: 2 } });
};
</script>

<template>
    <Carousel @open-news="openNews" />
    <div class="main-grid">
        <div class="news-section">
            <h2 class="section-title">Últimas Notícias</h2>
            <div v-if="isLoading" class="text-white text-center py-10">
                Carregando...
            </div>
            <div v-else>
                <div class="cards-grid mb-8">
                    <NewsCard v-for="post in newsList.slice(0, 9)" :key="post.id" :post="post" @open-news="openNews" />
                </div>
                <div class="flex justify-center w-full">
                    <button @click="goToAllNews"
                        class="group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-200 bg-transparent border-2 border-purple-600 rounded-full hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600 text-sm md:text-base cursor-pointer">
                        <span>Carregar mais notícias</span>
                        <i class="pi pi-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </div>
        </div>
        <aside class="sidebar-section">
            <Sidebar />
        </aside>
    </div>
</template>

<style scoped>
.main-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 40px;
    margin-top: 40px;
    padding-bottom: 40px;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

.section-title {
    font-family: 'Michroma', sans-serif;
    font-size: 1.8rem;
    color: white;
    margin-bottom: 24px;
    border-left: 4px solid #a855f7;
    padding-left: 16px;
}

/* Tablet e Laptops menores (até 1024px) */
@media (max-width: 1024px) {
    .main-grid {
        grid-template-columns: 1fr;
        gap: 30px;
        margin-top: 30px;
    }

    .cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Mobile (até 640px) */
@media (max-width: 640px) {
    .main-grid {
        gap: 24px;
        margin-top: 20px;
    }

    .cards-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .section-title {
        font-size: 1.5rem;
        padding-left: 12px;
        border-left-width: 3px;
    }
}
</style>