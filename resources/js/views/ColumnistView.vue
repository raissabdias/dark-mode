<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import NewsCard from '../components/NewsCard.vue';
import Sidebar from '../components/Sidebar.vue';

const route = useRoute();
const router = useRouter();

const columnist = ref(null);
const newsData = ref({ data: [] });
const loading = ref(true);
const error = ref(false);

const fetchColumnistData = async (page = 1) => {
    loading.value = true;
    error.value = false;

    try {
        const response = await fetch(`/api/columnists/${route.params.slug}?page=${page}`);

        if (!response.ok) {
            throw new Error('Colunista não encontrado');
        }

        const data = await response.json();
        columnist.value = data.columnist;
        newsData.value = data.news;

        document.title = `${columnist.value.name} | Dark Mode`;
    } catch (err) {
        console.error("Erro ao buscar colunista:", err);
        error.value = true;
        columnist.value = null;
    } finally {
        loading.value = false;
    }
};

onMounted(() => fetchColumnistData());

watch(() => route.params.slug, () => fetchColumnistData());

const changePage = (url) => {
    if (!url) return;
    const page = new URL(url).searchParams.get('page');
    fetchColumnistData(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const goHome = () => {
    router.push('/');
};

const openNews = (slug) => {
    router.push({ name: 'news.detail', params: { slug: slug } });
};
</script>

<template>
    <div class="container mx-auto px-4 py-8 animate-fade-in text-white">
        <div v-if="loading" class="flex justify-center py-40">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
        </div>
        <div v-else-if="error || !columnist"
            class="max-w-2xl mx-auto text-center py-20 bg-neutral-900/50 border border-gray-800 rounded-2xl shadow-2xl">
            <div class="mb-6">
                <i class="pi pi-user-minus text-6xl text-gray-700"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-4 font-michroma">Colunista não encontrado</h1>
            <button @click="goHome"
                class="inline-flex items-center gap-2 px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-full font-bold transition-all duration-300 cursor-pointer shadow-lg shadow-purple-900/20">
                <i class="pi pi-home"></i>
                Voltar para o Início
            </button>
        </div>
        <div v-else class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-3/4">
                <header
                    class="bg-neutral-900 rounded-2xl p-5 md:p-8 mb-12 shadow-2xl relative overflow-hidden border border-gray-800">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-600/10 blur-3xl rounded-full -mr-16 -mt-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row items-center md:items-start gap-2 md:gap-8">
                            <div
                                class="flex flex-row md:flex-col items-center md:items-start gap-4 md:gap-0 w-full md:w-auto">
                                <img :src="columnist.avatar_url || '/img/default-avatar.png'" :alt="columnist.name"
                                    class="avatar-img shadow-2xl shrink-0">

                                <h3 class="text-lg font-bold text-purple-400 font-michroma leading-tight md:hidden">
                                    {{ columnist.name }}
                                </h3>
                            </div>
                            <div class="w-full">
                                <h3
                                    class="hidden md:block text-3xl font-bold text-purple-400 font-michroma leading-tight mb-4">
                                    {{ columnist.name }}
                                </h3>
                                <div class="border-t border-gray-800/50 pt-2 md:pt-0 md:border-0">
                                    <p
                                        class="text-gray-300 text-sm md:text-base leading-relaxed text-justify md:text-left">
                                        {{ columnist.bio || 'Colaborador Dark Mode.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="space-y-8">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-800 pb-4 gap-3">
                        <h2 class="text-lg md:text-xl font-bold text-white flex items-center gap-3 font-michroma">
                            <i class="pi pi-align-left text-purple-500"></i>
                            Matérias Publicadas
                        </h2>
                        <span
                            class="text-[10px] md:text-xs text-gray-500 uppercase tracking-widest font-semibold bg-gray-900 px-3 py-1 rounded border border-gray-800 self-center sm:self-auto">
                            {{ newsData.total }} resultado(s)
                        </span>
                    </div>
                    <div v-if="newsData.data.length > 0"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                        <NewsCard v-for="post in newsData.data" :key="post.id" :post="post" @open-news="openNews" />
                    </div>
                    <div v-else
                        class="text-center py-20 border border-dashed border-gray-800 rounded-xl bg-neutral-900/20">
                        <i class="pi pi-inbox text-4xl text-gray-700 mb-4 block"></i>
                        <p class="text-gray-500">Este colunista ainda não publicou nenhuma matéria.</p>
                    </div>
                    <nav v-if="newsData.last_page > 1"
                        class="flex flex-wrap justify-center gap-2 mt-12 py-8 border-t border-gray-800">
                        <button v-for="link in newsData.links" :key="link.label" @click="changePage(link.url)"
                            :disabled="!link.url"
                            class="px-4 py-2 rounded-lg border transition-all duration-300 cursor-pointer text-sm font-bold min-w-[40px] flex items-center justify-center"
                            :class="[
                                link.active
                                    ? 'bg-purple-600 border-purple-500 text-white shadow-lg shadow-purple-900/40'
                                    : 'bg-neutral-900 border-gray-800 text-gray-500 hover:border-purple-500 hover:text-white',
                                !link.url ? 'opacity-20 cursor-not-allowed' : ''
                            ]">
                            <i v-if="link.label.includes('Prev')" class="pi pi-angle-left"></i>
                            <i v-else-if="link.label.includes('Next')" class="pi pi-angle-right"></i>
                            <span v-else v-html="link.label"></span>
                        </button>
                    </nav>
                </div>
            </div>
            <aside class="w-full lg:w-1/4">
                <Sidebar />
            </aside>
        </div>
    </div>
</template>

<style scoped>
.font-michroma {
    font-family: 'Michroma', sans-serif;
}

.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}

.avatar-img {
    width: 72px;
    /* 18 * 4 */
    height: 72px;
    min-width: 72px;
    min-height: 72px;
    border-radius: 9999px;
    object-fit: cover;
    display: block;
}

@media (min-width: 768px) {
    .avatar-img {
        width: 128px;
        /* 32 * 4 */
        height: 128px;
        min-width: 128px;
        min-height: 128px;
    }
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

p {
    overflow: visible !important;
    display: block !important;
    -webkit-line-clamp: none !important;
}

:deep(span) {
    display: inline-block;
}
</style>