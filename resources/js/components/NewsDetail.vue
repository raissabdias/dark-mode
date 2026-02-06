<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
// 1. Importar o Sidebar
import Sidebar from './Sidebar.vue';

const props = defineProps({
    newsId: {
        type: Number,
        required: true
    }
});

const route = useRoute();
const router = useRouter();

const news = ref(null);
const loading = ref(true);
const newsSlug = route.params.slug || props.newsSlug;

onMounted(async () => {
    try {
        const response = await fetch(`/api/news/${newsSlug}`);
        if (!response.ok) throw new Error('Falha ao buscar notícia');
        news.value = await response.json();
        document.title = `${news.value.title} | Dark Mode`;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
});

const goBack = () => {
    router.push('/');
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit', month: 'long', year: 'numeric'
    });
};

const getAuthorName = (authorData) => {
    if (!authorData) return 'Redação';
    return authorData.name || authorData;
};

const cleanedContent = computed(() => {
    if (!news.value) return '';
    const rawContent = news.value.content || news.value.body || '';
    const txt = document.createElement("textarea");
    txt.innerHTML = rawContent;
    return txt.value;
});
</script>

<template>
    <div class="flex flex-col lg:flex-row gap-8 py-8 animate-fade-in">
        <div class="w-full lg:w-3/4">
            <button @click="goBack" class="mb-6 flex items-center gap-2 transition-colors cursor-pointer back-home">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Voltar para Home
            </button>
            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
            </div>
            <article v-else-if="news" class="bg-black rounded-xl shadow-lg overflow-hidden">
                <img v-if="news.image_url" :src="news.image_url" :alt="news.title"
                    class="w-full h-64 md:h-96 object-cover">
                <div class="p-6 md:p-8">
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-4">
                        <span class="px-2 py-1 rounded-md text-xs font-semibold uppercase tracking-wide" :style="{
                            backgroundColor: news.category.bg_color,
                            color: news.category.text_color
                        }">
                            {{ news.category.name || 'Notícia' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ formatDate(news.created_at) }}
                        </span>
                        <span class="hidden md:inline text-gray-600">•</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            por <span class="text-purple-400 font-semibold">{{ getAuthorName(news.author || news.user)
                                }}</span>
                        </span>
                    </div>
                    <h1
                        class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6 leading-tight font-michroma">
                        {{ news.title }}
                    </h1>
                    <div class="prose prose-lg max-w-none dark:prose-invert text-gray-700 dark:text-gray-300 leading-relaxed video-container"
                        v-html="cleanedContent"></div>
                </div>
            </article>
            <div v-else class="text-center py-10 text-red-500">
                Notícia não encontrada.
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
    animation: fadeIn 0.3s ease-in-out;
}

.back-home {
    color: #a855f7;
    font-weight: 600;
}

.back-home:hover {
    color: #d8b4fe;
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

:deep(.prose iframe),
:deep(.body-text iframe) {
    width: 100% !important;
    height: auto !important;
    aspect-ratio: 16 / 9;
    border-radius: 0.5rem;
    margin: 2rem 0;
}

.body-text,
.prose {
    max-width: 100%;
    overflow-x: hidden;
}
</style>