<script setup>
import { ref, onMounted, watch } from 'vue'; // Adicionei 'watch'
import { useRouter } from 'vue-router';
import NewsCard from '../components/NewsCard.vue';
import Sidebar from '../components/Sidebar.vue';

// Recebe a categoria via Rota (definido no router.js)
const props = defineProps({
    category: {
        type: String,
        default: null
    }
});

const router = useRouter();
const newsData = ref({ data: [] });
const loading = ref(true);

const fetchNews = async (page = 1) => {
    loading.value = true;
    try {
        let url = `/api/news-paginated?page=${page}`;
        if (props.category) {
            url += `&category=${props.category}`;
        }

        const response = await fetch(url);
        newsData.value = await response.json();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        loading.value = false;
    }
};

const openNews = (slug) => {
    router.push(`/noticia/${slug}`);
};

onMounted(() => {
    fetchNews();
});

watch(() => props.category, () => {
    fetchNews(1);
});
</script>

<template>
    <div class="flex flex-col lg:flex-row gap-8 py-8 animate-fade-in">
        <div class="w-full lg:w-3/4">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-8 border-l-4 border-purple-500 pl-4 font-michroma">
                {{ category ? category : 'Todas as Notícias' }}
            </h1>
            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
            </div>
            <div v-else>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <NewsCard v-for="post in newsData.data" :key="post.id" :post="post" @open-news="openNews" />
                </div>

                <div v-if="newsData.last_page > 1" class="flex justify-center gap-2">
                    <button v-for="page in newsData.last_page" :key="page" @click="fetchNews(page)"
                        class="w-10 h-10 rounded-lg font-bold transition-colors"
                        :class="page === newsData.current_page ? 'bg-purple-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'">
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
        <aside class="w-full lg:w-1/4">
            <Sidebar />
        </aside>
    </div>
</template>

<style scoped>
.font-michroma {
    font-family: 'Michroma', sans-serif;
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}
</style>