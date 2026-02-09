<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import NewsCard from '../components/NewsCard.vue';
import Sidebar from '../components/Sidebar.vue';

const props = defineProps({
    categoryIds: {
        type: String,
        default: null
    },
    pageTitle: {
        type: String,
        default: 'Todas as Notícias'
    }
});

const router = useRouter();
const route = useRoute();

const newsData = ref({ data: [] });
const loading = ref(true);

const fetchNews = async (page = 1) => {
    loading.value = true;
    try {
        let url = `/api/news-paginated?page=${page}`;
        if (props.categoryIds) {
            url += `&categories=${props.categoryIds}`;
        }

        const response = await fetch(url);
        newsData.value = await response.json();

        window.scrollTo({ top: 0, behavior: 'smooth' });
        router.replace({ query: { ...route.query, page } });
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
    const initialPage = route.query.page ? parseInt(route.query.page) : 1;
    fetchNews(initialPage);
});

watch(() => props.categoryIds, () => {
    fetchNews(1);
});
</script>

<template>
    <div class="flex flex-col lg:flex-row gap-8 py-8 animate-fade-in">
        <div class="w-full lg:w-3/4">
            <h1 class="text-2xl md:text-4xl font-bold text-white mb-8 border-l-4 border-purple-500 pl-4 font-michroma">
                {{ pageTitle }}
            </h1>
            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
            </div>
            <div v-else>
                <div v-if="newsData.data.length === 0" class="text-gray-400 text-lg py-10">
                    Nenhuma publicação encontrada.
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <NewsCard v-for="post in newsData.data" :key="post.id" :post="post" @open-news="openNews" />
                </div>
                <div v-if="newsData.last_page > 1" class="flex flex-wrap justify-center gap-2">
                    <button @click="fetchNews(newsData.current_page - 1)" :disabled="newsData.current_page === 1"
                        class="px-3 py-2 rounded-lg bg-gray-800 text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-purple-600 transition-colors">
                        &laquo;
                    </button>
                    <button v-for="page in newsData.last_page" :key="page" @click="fetchNews(page)"
                        class="w-10 h-10 rounded-lg font-bold transition-colors"
                        :class="page === newsData.current_page ? 'bg-purple-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'">
                        {{ page }}
                    </button>
                    <button @click="fetchNews(newsData.current_page + 1)"
                        :disabled="newsData.current_page === newsData.last_page"
                        class="px-3 py-2 rounded-lg bg-gray-800 text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-purple-600 transition-colors">
                        &raquo;
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