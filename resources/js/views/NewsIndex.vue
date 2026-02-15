<script setup>
import { ref, onMounted, watch, computed } from 'vue';
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

const newsData = ref({ data: [], current_page: 1, last_page: 1 });
const loading = ref(true);
const categories = ref([]);

const filteredCategories = computed(() => {
    if (!props.categoryIds || categories.value.length === 0) return [];

    const allowedIds = props.categoryIds.split(',').map(id => parseInt(id.trim()));

    return categories.value.filter(cat => allowedIds.includes(cat.id));
});

const showFilters = computed(() => props.categoryIds !== null);

/**
 * 2. BUSCA DE NOTÍCIAS
 */
const fetchNews = async (page = 1) => {
    loading.value = true;
    try {
        const activeCategory = route?.query?.categoryIds || props.categoryIds;
        let url = `/api/news-paginated?page=${page}`;
        if (activeCategory) {
            url += `&categories=${activeCategory}`;
        }

        const response = await fetch(url);
        newsData.value = await response.json();

        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        console.error('Erro ao buscar notícias:', error);
    } finally {
        loading.value = false;
    }
};

const fetchCategories = async () => {
    try {
        const response = await fetch('/api/categories');
        if (response.ok) {
            categories.value = await response.json();
        }
    } catch (error) {
        console.error('Erro ao buscar categorias:', error);
    }
};


const filterByCategory = (id) => {
    const query = { ...route?.query, page: 1 };
    if (id) {
        query.categoryIds = id;
    } else {
        delete query.categoryIds;
    }

    router.push({ query });
};

const openNews = (slug) => {
    router.push(`/noticia/${slug}`);
};

onMounted(() => {
    fetchCategories();
    const initialPage = route?.query?.page ? parseInt(route.query.page) : 1;
    fetchNews(initialPage);
});

watch(() => [route?.query?.categoryIds, route?.query?.page], () => {
    const page = route?.query?.page ? parseInt(route.query.page) : 1;
    fetchNews(page);
});
</script>

<template>
    <div class="flex flex-col lg:flex-row gap-8 py-8 animate-fade-in">
        <div class="w-full lg:w-3/4">
            <h1
                class="text-2xl md:text-4xl font-bold text-white mb-6 border-l-4 border-purple-500 pl-4 font-michroma uppercase tracking-tighter">
                {{ props.pageTitle }}
            </h1>
            <div v-if="showFilters"
                class="flex flex-wrap justify-center lg:justify-start gap-2 mb-4 lg:mb-8 items-center bg-transparent lg:bg-gray-900/40 p-3 rounded-lg lg:border lg:border-gray-800">
                <span class="text-gray-500 text-[10px] hidden lg:inline font-bold uppercase mr-2 tracking-widest">Filtrar por:</span>
                <button @click="filterByCategory(null)"
                    :class="[!route?.query?.categoryIds ? 'bg-purple-600 border-purple-600 text-white shadow-lg' : 'bg-gray-800 border-gray-700 text-gray-400']"
                    class="px-3 py-1 rounded-full text-[12px] font-bold uppercase border transition-all cursor-pointer hover:border-purple-500">
                    Todas
                </button>
                <button v-for="cat in filteredCategories" :key="cat.id" @click="filterByCategory(cat.id)"
                    :class="[route?.query?.categoryIds == cat.id ? 'text-white border-transparent shadow-lg' : 'bg-gray-800 border-gray-700 text-gray-500 hover:text-gray-300']"
                    :style="route?.query?.categoryIds == cat.id ? { backgroundColor: cat.bg_color || '#7c3aed' } : {}"
                    class="px-3 py-1 rounded-full text-[12px] font-bold uppercase border transition-all cursor-pointer">
                    {{ cat.name }}
                </button>
            </div>
            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
            </div>
            <div v-else>
                <div v-if="newsData.data.length === 0"
                    class="text-gray-400 text-lg py-16 bg-gray-900/30 rounded-xl text-center border border-dashed border-gray-800 font-michroma">
                    Nenhuma publicação encontrada nesta categoria.
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <NewsCard v-for="post in newsData.data" :key="post.id" :post="post" @open-news="openNews" />
                </div>
                <div v-if="newsData.last_page > 1"
                    class="flex flex-wrap justify-center gap-2 pt-6 border-t border-gray-800">
                    <button @click="fetchNews(newsData.current_page - 1)" :disabled="newsData.current_page === 1"
                        class="px-3 py-2 rounded-lg bg-gray-800 text-white disabled:opacity-30 disabled:cursor-not-allowed hover:bg-purple-600 transition-all cursor-pointer">
                        &laquo;
                    </button>
                    <button v-for="page in newsData.last_page" :key="page" @click="fetchNews(page)"
                        class="w-10 h-10 rounded-lg font-bold transition-all cursor-pointer shadow-sm"
                        :class="page === newsData.current_page ? 'bg-purple-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'">
                        {{ page }}
                    </button>
                    <button @click="fetchNews(newsData.current_page + 1)"
                        :disabled="newsData.current_page === newsData.last_page"
                        class="px-3 py-2 rounded-lg bg-gray-800 text-white disabled:opacity-30 disabled:cursor-not-allowed hover:bg-purple-600 transition-all cursor-pointer">
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
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>