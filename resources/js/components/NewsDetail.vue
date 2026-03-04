<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import Sidebar from './Sidebar.vue';
import CommentsSection from './CommentsSection.vue';

const props = defineProps({
    newsSlug: {
        type: String,
        default: ''
    }
});

const route = useRoute();
const router = useRouter();

const news = ref(null);
const loading = ref(true);
const copied = ref(false);
const newsSlug = computed(() => route.params.slug || props.newsSlug || '');
const currentUrl = computed(() => window.location.href);

const fetchNews = async () => {
    if (!newsSlug.value) {
        news.value = null;
        loading.value = false;
        return;
    }

    loading.value = true;
    try {
        const response = await fetch(`/api/news/${newsSlug.value}`);
        if (!response.ok) throw new Error('Falha ao buscar notícia');
        news.value = await response.json();
        document.title = `${news.value.title} | Dark Mode`;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchNews();
});

watch(() => route.params.slug, (newSlug, oldSlug) => {
    if (newSlug && newSlug !== oldSlug) {
        fetchNews();
        scrollToTop();
    }
});

const goBack = () => {
    if (window.history.length > 1) {
        router.back();
        return;
    }
    router.push('/');
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit', month: 'long', year: 'numeric'
    });
};

const cleanedContent = computed(() => {
    if (!news.value) return '';

    const rawContent = news.value.content || news.value.body || '';
    if (typeof rawContent !== 'string') return '';

    const hasHtmlTags = /<\/?[a-z][\s\S]*>/i.test(rawContent);
    if (hasHtmlTags) {
        const container = document.createElement('div');
        container.innerHTML = rawContent;

        // Convert soft breaks to explicit spacer elements for predictable front-end spacing.
        container.querySelectorAll('p br').forEach((lineBreak) => {
            const spacer = document.createElement('span');
            spacer.className = 'dm-soft-break';
            spacer.setAttribute('aria-hidden', 'true');
            lineBreak.replaceWith(spacer);
        });

        container.querySelectorAll('p').forEach((paragraph) => {
            const paragraphText = paragraph.textContent?.trim() || '';
            const paragraphHtml = paragraph.innerHTML.replace(/\u200B/g, '').trim();

            // Keep visible blank lines from editor output like <p><br></p> or empty paragraphs.
            const isVisuallyEmpty = !paragraphText
                && (/^\s*(&nbsp;|<span[^>]*class=["']dm-soft-break["'][^>]*><\/span>|\s)*\s*$/i.test(paragraphHtml)
                    || paragraphHtml === '');

            if (isVisuallyEmpty) {
                paragraph.classList.add('dm-empty-line');
                paragraph.innerHTML = '&nbsp;';
            }

            if (/^<iframe[\s\S]*<\/iframe>$/i.test(paragraphText)) {
                paragraph.innerHTML = paragraphText;
            }
        });

        return container.innerHTML;
    }

    const looksEscapedHtml = /&lt;\/?[a-z][\s\S]*&gt;/i.test(rawContent);
    if (looksEscapedHtml) {
        const txt = document.createElement('textarea');
        txt.innerHTML = rawContent;
        return txt.value;
    }

    return rawContent.replace(/\n/g, '<br>');
});

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const copyToClipboard = () => {
    navigator.clipboard.writeText(currentUrl.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
};
</script>

<template>
    <div class="flex flex-col lg:flex-row gap-8 py-4 md:py-8 animate-fade-in">
        <div class="w-full lg:w-3/4">
            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
            </div>
            <article v-else-if="news" class="bg-black rounded-xl shadow-lg overflow-hidden border border-gray-800">
                <img v-if="news.image_url" :src="news.image_url" :alt="news.title"
                    class="w-full h-56 md:h-96 object-cover">
                <div class="p-5 md:p-8">
                    <div
                        class="flex flex-wrap items-center gap-3 md:gap-4 text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-6">
                        <span class="px-2 py-1 rounded-md text-[10px] md:text-xs font-semibold uppercase tracking-wide"
                            :style="{
                                backgroundColor: news.category.bg_color,
                                color: news.category.text_color
                            }">
                            {{ news.category.name || 'Notícia' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="pi pi-calendar text-[10px] md:text-xs"></i>
                            {{ formatDate(news.published_at) }}
                        </span>
                        <router-link :to="{ name: 'columnist', params: { slug: news.columnist.slug } }"
                            class="author-wrapper flex items-center gap-2 hover:opacity-80 transition-opacity">
                            <img v-if="news.columnist?.avatar_url" :src="news.columnist.avatar_url"
                                class="w-6 h-6 md:w-8 md:h-8 rounded-full border border-black object-cover shrink-0" />
                            <span class="text-purple-400 font-semibold leading-none">
                                {{ news.columnist?.name || news.author || 'Redação' }}
                            </span>
                        </router-link>
                    </div>
                    <h1 class="text-2xl md:text-4xl font-bold text-white mb-6 leading-tight font-michroma">
                        {{ news.title }}
                    </h1>
                    <div class="prose prose-sm md:prose-lg max-w-none dark:prose-invert text-gray-300 leading-relaxed mb-10"
                        v-html="cleanedContent"></div>
                    <div v-if="news.columnist && news.columnist.bio"
                        class="author-bio-box bg-neutral-900/50 border border-gray-800 rounded-xl p-4 mb-8 flex flex-row items-center gap-4 md:gap-6">
                        <router-link :to="{ name: 'columnist', params: { slug: news.columnist.slug } }"
                            class="shrink-0 hover:opacity-80 transition-opacity">
                            <img :src="news.columnist.avatar_url || 'https://via.placeholder.com/150'"
                                class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover shadow-xl" />
                        </router-link>
                        <div class="flex-1 text-left">
                            <router-link :to="{ name: 'columnist', params: { slug: news.columnist.slug } }"
                                class="hover:opacity-80 transition-opacity">
                                <h4 class="text-purple-400 text-sm md:text-lg font-michroma mb-1">{{ news.columnist.name
                                    }}</h4>
                            </router-link>
                            <p
                                class="text-gray-400 text-xs md:text-sm leading-relaxed mb-0 line-clamp-3 md:line-clamp-none">
                                {{ news.columnist.bio }}
                            </p>
                        </div>
                    </div>
                    <div class="share-box border-t border-gray-800 pt-8 mb-8">
                        <p
                            class="text-[10px] md:text-xs uppercase tracking-widest text-gray-500 mb-5 font-bold text-center md:text-left">
                            Compartilhe esta notícia
                        </p>
                        <div class="grid grid-cols-2 md:flex md:flex-wrap gap-2 md:gap-3">
                            <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent(news.title + ' - ' + currentUrl)"
                                target="_blank" class="share-btn wa">
                                <i class="pi pi-whatsapp"></i> <span class="truncate">WhatsApp</span>
                            </a>
                            <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(currentUrl)"
                                target="_blank" class="share-btn fb">
                                <i class="pi pi-facebook"></i> <span class="truncate">Facebook</span>
                            </a>
                            <a :href="'https://twitter.com/intent/tweet?url=' + encodeURIComponent(currentUrl) + '&text=' + encodeURIComponent(news.title)"
                                target="_blank" class="share-btn tw">
                                <i class="pi pi-twitter"></i> <span class="truncate">Twitter</span>
                            </a>
                            <button @click="copyToClipboard" class="share-btn copy">
                                <i class="pi pi-copy"></i> <span class="truncate">{{ copied ? 'Copiado!' : 'Link'
                                    }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-center border-t border-gray-800 pt-8">
                        <button @click="goBack"
                            class="group flex items-center gap-2 px-8 py-3 bg-gray-900 hover:bg-purple-600 border border-gray-700 hover:border-purple-500 text-gray-300 hover:text-white rounded-full transition-all duration-300 font-bold shadow-lg w-full md:w-auto justify-center cursor-pointer">
                            <i
                                class="pi pi-arrow-left group-hover:-translate-y-1 transition-transform duration-300"></i>
                            Voltar para página anterior
                        </button>
                    </div>
                </div>
            </article>
            <div v-else class="text-center py-20">
                <p class="text-gray-500 text-lg">Notícia não encontrada.</p>
                <button @click="goBack"
                    class="inline-flex items-center justify-center px-6 py-1 my-4 font-bold text-white transition-all duration-200 bg-transparent border-2 border-purple-600 rounded-full hover:bg-purple-600 cursor-pointer text-sm md:text-base">
                    Voltar para página anterior
                </button>
            </div>
            <CommentsSection v-if="news" :news-slug="newsSlug" />
        </div>
        <aside class="w-full lg:w-1/4">
            <Sidebar />
        </aside>
    </div>
</template>

<style scoped>
@reference "../../css/app.css";

.font-michroma {
    font-family: 'Michroma', sans-serif;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
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

.share-btn {
    @apply flex items-center justify-center gap-2 px-3 py-3 rounded-lg text-[11px] md:text-sm font-bold transition-all duration-300 cursor-pointer;
    background: #0d0d0d;
    border: 1px solid #1f1f1f;
    color: #999;
}

.share-btn:hover {
    @apply bg-neutral-900 border-purple-600 text-white shadow-xl;
}

.wa:hover {
    color: #25d366;
    border-color: #25d366;
}

.fb:hover {
    color: #1877f2;
    border-color: #1877f2;
}

.tw:hover {
    color: #ffffff;
    border-color: #ffffff;
}

.copy:hover {
    color: #facc15;
    border-color: #facc15;
}

.author-bio-box {
    box-shadow: inset 0 0 20px rgba(168, 85, 247, 0.05);
}

.author-wrapper {
    display: inline-flex;
    align-items: center;
}

:deep(.prose blockquote) {
    @apply border-l-4 border-purple-600 pl-6 my-2 text-gray-400 bg-gray-700/30 pr-4 py-2 rounded-r-lg;
}

:deep(.prose iframe) {
    width: 100% !important;
    height: auto;
    aspect-ratio: 16 / 9; 
    border-radius: 0.5rem;
    margin-top: 2rem;
    margin-bottom: 2rem;
}

:deep(.prose .text-center img) {
    display: block !important;
    margin-left: auto !important;
    margin-right: auto !important;
}

:deep(.prose [style*="text-align: center"] img),
:deep(.prose [style*="text-align:center"] img) {
    display: block !important;
    margin-left: auto !important;
    margin-right: auto !important;
}

:deep(.prose img) {
    max-width: 100%;
    height: auto;
}

:deep(.prose hr) {
    margin: 10px 0!important;
}

:deep(.prose p) {
    margin-block-start: 1rem;
}

:deep(.prose p.dm-empty-line) {
    min-height: 1rem;
    margin: 0.95rem 0 !important;
}

:deep(.prose .dm-soft-break) {
    display: block;
    height: 0.75rem;
    line-height: 0;
}
</style>