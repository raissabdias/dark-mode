<script setup>
import { ref, onMounted } from 'vue';
import NewsCard from '../components/NewsCard.vue';
import Sidebar from '../components/Sidebar.vue';
import NewsDetail from '../components/NewsDetail.vue';
import Carousel from '../components/Carousel.vue';

const selectedPostId = ref(null);
const newsList = ref([]);
const isLoading = ref(true);

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

// Funções de navegação
const openNews = (id) => {
    selectedPostId.value = id;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const closeNews = () => {
    selectedPostId.value = null;
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
            <div v-else class="cards-grid">
                <NewsCard v-for="post in newsList" :key="post.id" :post="post" @click="openNews(post.id)" />
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
    margin-top: 20px;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.section-title {
    font-family: 'Michroma', sans-serif;
    font-size: 1.8rem;
    color: white;
    margin-bottom: 20px;
    border-left: 4px solid #a855f7;
    padding-left: 12px;
}

@media (max-width: 1024px) {
    .main-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }

    .section-title {
        font-size: 1.5rem;
    }
}
</style>