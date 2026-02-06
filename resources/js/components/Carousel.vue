<script setup>
import { ref, onMounted, computed } from "vue";
import Carousel from 'primevue/carousel';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
const highlights = ref([]);

const emit = defineEmits(['open-news']);

const responsiveOptions = ref([
    { breakpoint: '1024px', numVisible: 1, numScroll: 1 },
    { breakpoint: '768px', numVisible: 1, numScroll: 1 },
    { breakpoint: '560px', numVisible: 1, numScroll: 1 }
]);

// Verifica se existe apenas 1 notícia para travar o carrossel
const isSingleItem = computed(() => highlights.value.length === 1);

const fetchFeatured = async () => {
    try {
        const response = await fetch('/api/news/featured');
        highlights.value = await response.json();
    } catch (error) {
        console.error('Erro ao carregar destaques:', error);
    }
};

const openNewsDetail = (slug) => {
    if (slug) {
        emit('open-news', slug);
    }
};

onMounted(() => {
    fetchFeatured();
});
</script>

<template>
    <div v-if="highlights.length > 0" class="carousel-wrapper">
        <Carousel :value="highlights" :numVisible="1" :numScroll="1" :responsiveOptions="responsiveOptions"
            :circular="!isSingleItem" :autoplayInterval="isSingleItem ? 0 : 5000" :showIndicators="!isSingleItem" :pt="{
                previousButton: { class: isSingleItem ? '!hidden' : '' },
                nextButton: { class: isSingleItem ? '!hidden' : '' }
            }">
            <template #item="slotProps">
                <div class="hero-slide h-[450px] md:h-[700px]">
                    <img :src="slotProps.data.image_url" :alt="slotProps.data.title" class="hero-image" />
                    <div class="overlay"></div>
                    <div class="hero-content p-8 md:p-16">
                        <Tag :value="slotProps.data.category" severity="secondary" class="mb-3 md:mb-4 category-tag" />
                        <h1 class="text-3xl md:text-4xl font-black mb-4 leading-tight title-shadow">
                            {{ slotProps.data.title }}
                        </h1>
                        <div
                            class="text-sm text-gray-300 mb-3 font-bold uppercase tracking-wider flex items-center gap-2">
                            <i class="pi pi-calendar text-purple-400"></i> {{ slotProps.data.date_formatted }}
                        </div>
                        <p
                            class="mb-6 text-base md:text-1xl text-gray-200 hidden sm:block max-w-3xl leading-relaxed title-shadow-sm">
                            {{ slotProps.data.excerpt }}
                        </p>
                        <Button @click="openNewsDetail(slotProps.data.slug)" label="Ler Matéria" icon="pi pi-arrow-right" iconPos="right"
                            class="p-button-rounded p-button-help font-bold px-6 py-3" />
                    </div>
                </div>
            </template>
        </Carousel>
    </div>
</template>

<style scoped>
.carousel-wrapper {
    margin-bottom: 3rem;
    border-bottom: 2px solid #333;
}

.hero-slide {
    position: relative;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    border-radius: 20px;
}

.hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1;
    transition: transform 1s ease-in-out;
}

.hero-slide:hover .hero-image {
    transform: scale(1.05);
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0, 0, 0, .85) 15%, rgba(0, 0, 0, 0.5) 30%, transparent 100%);
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    width: 100%;
    color: white;
}

.title-shadow {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 1);
}

.title-shadow-sm {
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
}

.category-tag {
    background-color: #a855f7 !important;
    color: white !important;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
}
</style>