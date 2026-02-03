<script setup>
import { ref } from "vue";
import Carousel from 'primevue/carousel';
import Button from 'primevue/button';
import Tag from 'primevue/tag';

const highlights = ref([
    {
        id: 1,
        title: 'Iron Maiden anuncia nova turnê mundial',
        category: 'Heavy Metal',
        excerpt: 'A lendária banda britânica passará por 15 países em 2026 com a turnê "Eternity".',
        image: 'https://igormiranda.com.br/wp-content/cache/seraphinite-accelerator/s/m/d/img/667c973d9f5a63ef63bdec5d423139ef.11934.jpeg'
    },
    {
        id: 2,
        title: 'O Retorno do Gótico nos festivais europeus',
        category: 'Gótico',
        excerpt: 'Bandas clássicas dos anos 80 voltam aos palcos principais do verão europeu.',
        image: 'https://cvltnation.com/wp-content/uploads/2021/03/3524_120170744833606_1279563416_n-1.jpeg'
    },
    {
        id: 3,
        title: 'Sepultura: Documentário inédito será lançado',
        category: 'Thrash Metal',
        excerpt: 'Imagens de arquivo nunca vistas mostram os bastidores do álbum Roots.',
        image: 'https://s2.glbimg.com/fsrSZ2i6y-DheMdmHs7s0sDDVTk=/620x465/s.glbimg.com/jo/g1/f/original/2016/04/20/mxxx0427.jpg'
    }
]);

const responsiveOptions = ref([
    { breakpoint: '1024px', numVisible: 1, numScroll: 1 },
    { breakpoint: '768px', numVisible: 1, numScroll: 1 },
    { breakpoint: '560px', numVisible: 1, numScroll: 1 }
]);
</script>

<template>
    <div class="carousel-wrapper">
        <Carousel :value="highlights" :numVisible="1" :numScroll="1" :responsiveOptions="responsiveOptions" circular
            autoplayInterval="5000" :showIndicators="true">
            <template #item="slotProps">
                <div class="hero-slide h-[300px] md:h-[500px]">
                    <img :src="slotProps.data.image" :alt="slotProps.data.title" class="hero-image" />
                    <div class="overlay"></div>
                    <div class="hero-content p-6 md:p-12">
                        <Tag :value="slotProps.data.category" severity="secondary" class="mb-2 md:mb-3 category-tag" />
                        <h1 class="text-2xl md:text-4xl font-bold mb-2 leading-tight">{{ slotProps.data.title }}</h1>
                        <p class="mb-4 text-sm md:text-xl text-gray-300 hidden sm:block">{{ slotProps.data.excerpt }}
                        </p>
                        <Button label="Ler Matéria" icon="pi pi-bolt" size="small"
                            class="p-button-rounded p-button-help mt-2" />
                    </div>
                </div>
            </template>
        </Carousel>
    </div>
</template>

<style scoped>
.carousel-wrapper {
    margin-bottom: 2rem;
    border-bottom: 2px solid #333;
}

.hero-slide {
    position: relative;
    display: flex;
    align-items: flex-end;
    border-radius: 8px;
    /* Borda mais suave */
    overflow: hidden;
}

.hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 10%, rgba(0, 0, 0, 0.4) 60%, transparent 100%);
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    width: 100%;
    max-width: 800px;
    color: white;
}

.category-tag {
    background-color: #a855f7 !important;
    color: white !important;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.7rem;
}
</style>