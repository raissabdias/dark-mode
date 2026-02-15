<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { useRouter } from 'vue-router';
import Menubar from 'primevue/menubar';
import AutoComplete from 'primevue/autocomplete';
import SocialIcons from './SocialIcons.vue';

const router = useRouter();

const items = ref([
    { label: 'Notícias', icon: 'pi pi-megaphone', route: '/noticias' },
    { label: 'Reviews', icon: 'pi pi-star', route: '/reviews' },
    { label: 'Agenda', icon: 'pi pi-calendar', route: '/agenda' },
    { label: 'Guia', icon: 'pi pi-book', route: '/guia' },
    { key: 'search-mobile', class: 'min-[1180px]:hidden' },
    { key: 'social-mobile', class: 'min-[1180px]:hidden' }
]);

const viewportWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1400);

const updateViewportWidth = () => {
    viewportWidth.value = window.innerWidth;
};

onMounted(() => {
    window.addEventListener('resize', updateViewportWidth);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', updateViewportWidth);
});

const displayItems = computed(() => {
    const hideGuia = viewportWidth.value >= 1180 && viewportWidth.value < 1400;

    if (!hideGuia) {
        return items.value;
    }

    return items.value.filter((item) => item.route !== '/agenda');
});

const searchQuery = ref('');
const searchResults = ref([]);
const searchLoading = ref(false);
let searchTimeout = null;

const searchNews = async (event) => {
    const query = event.query;

    if (!query || query.length < 2) {
        searchResults.value = [];
        return;
    }

    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(async () => {
        searchLoading.value = true;
        try {
            const response = await fetch(`/api/news/search/query?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            searchResults.value = data;
        } catch (error) {
            console.error('Erro ao buscar notícias:', error);
            searchResults.value = [];
        } finally {
            searchLoading.value = false;
        }
    }, 300);
};

const selectNews = (event) => {
    if (event.value && event.value.slug) {
        router.push(`/noticia/${event.value.slug}`);
        searchQuery.value = '';
    }
};
</script>

<template>
    <div class="sticky top-0 z-[1000] w-full">
        <Menubar :model="displayItems" breakpoint="1179px"
            class="w-full bg-black/95 backdrop-blur-md border-b border-white/10 rounded-none px-4 py-3 lg:px-8 flex items-center justify-between"
            :pt="{
                root: { class: '!border-b-2 !border-black !rounded-none' },
                button: { class: 'ml-auto order-3 text-gray-200 hover:bg-white/10 focus:ring-0 w-10 h-10 flex items-center justify-center rounded-lg transition-all' },
                rootList: { class: '!bg-black/95 !border-t-2 !border-black min-[1180px]:!bg-transparent min-[1180px]:!border-none w-full min-[1180px]:w-auto top-full left-0 absolute min-[1180px]:static shadow-xl min-[1180px]:shadow-none' },
                itemContent: { class: 'text-gray-200 hover:text-white' },
                itemLink: { class: 'py-3 px-4 min-[1180px]:py-2 min-[1180px]:px-3' },
                end: { class: 'w-full max-w-xl' }
            }">
            <template #start>
                <router-link to="/" class="flex items-center gap-2 mr-4 min-[1180px]:mr-8 shrink-0 order-1">
                    <img src="/images/logo.png" alt="DarkMode Logo"
                        class="h-8 md:h-9 w-auto object-contain hover:opacity-80 transition-opacity" />
                </router-link>
            </template>
            <template #item="{ item, props }">
                <!-- Search Bar Mobile -->
                <div v-if="item.key === 'search-mobile'" class="mobile-search px-4 py-3 relative" @click.stop
                    @keydown.stop>
                    <i class="pi pi-search absolute left-7 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                        style="z-index: 999;"></i>
                    <AutoComplete v-model="searchQuery" :suggestions="searchResults" @complete="searchNews"
                        @item-select="selectNews" optionLabel="title" placeholder="Buscar notícias..."
                        :loading="searchLoading" class="w-full" :pt="{
                            root: { class: 'w-full !relative', style: 'position: relative; z-index: 1' },
                            pcInputText: {
                                class: 'w-full text-white placeholder:text-gray-500 rounded-full pl-10 pr-4 py-2 !border-0 focus:!border-0 focus:!outline-none focus:ring-2 focus:ring-purple-500/50',
                                style: 'background-color: rgba(107, 114, 128, 0.35) !important; position: relative; z-index: 1'
                            },
                            overlay: {
                                class: 'bg-gray-900/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl mt-2 overflow-hidden'
                            },
                            list: { class: 'p-0' },
                            option: { class: 'hover:bg-purple-600/20 transition-colors' }
                        }">
                        <template #option="slotProps">
                            <div class="flex items-center gap-3 p-3 cursor-pointer w-full overflow-hidden">
                                <img v-if="slotProps.option.image_url" :src="slotProps.option.image_url"
                                    :alt="slotProps.option.title" class="w-16 h-16 object-cover rounded-lg shrink-0" />
                                <div v-else
                                    class="w-16 h-16 bg-gray-800 rounded-lg shrink-0 flex items-center justify-center">
                                    <i class="pi pi-image text-gray-600 text-2xl"></i>
                                </div>
                                <div class="flex-1 min-w-0 overflow-hidden">
                                    <div class="text-white font-semibold text-sm mb-1 break-words whitespace-normal">{{
                                        slotProps.option.title }}</div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span v-if="slotProps.option.category"
                                            class="px-2 py-1 rounded text-xs font-bold" :style="{
                                                backgroundColor: slotProps.option.category.bg_color,
                                                color: slotProps.option.category.text_color
                                            }">
                                            {{ slotProps.option.category.name }}
                                        </span>
                                        <span class="text-gray-400">{{ slotProps.option.date_formatted }}</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template #empty>
                            <div class="p-4 text-center text-gray-400">
                                {{ searchLoading ? 'Carregando notícias...' : 'Nenhuma notícia encontrada' }}
                            </div>
                        </template>
                    </AutoComplete>
                </div>
                <div v-if="item.key === 'social-mobile'"
                    class="flex items-center justify-center gap-6 py-6 border-t border-white/10 mt-2 bg-black/50">
                    <SocialIcons variant="menu" />
                </div>
                <router-link v-else :to="item.route" custom v-slot="{ href, navigate, isActive }">
                    <a :href="href" @click="navigate" class="flex items-center group w-full" v-bind="props.action"
                        :class="[item.class, { 'text-purple-500 font-bold': isActive }]">
                        <span
                            :class="[item.icon, isActive ? 'text-purple-500' : 'text-gray-400', 'group-hover:text-purple-400 transition-colors mr-1']" />
                        <span
                            :class="[isActive ? 'text-white' : '', 'tracking-wide group-hover:text-white transition-colors', item.label ? 'menu-label-michroma' : '']">{{
                                item.label }}</span>
                    </a>
                </router-link>
            </template>
            <template #end>
                <div class="hidden min-[1180px]:flex items-center gap-4 2xl:gap-6 flex-1">
                    <!-- Search Bar Desktop -->
                    <div class="flex-1 relative min-[1180px]:max-w-sm xl:max-w-md 2xl:max-w-xl">
                        <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                            style="z-index: 999;"></i>
                        <AutoComplete v-model="searchQuery" :suggestions="searchResults" @complete="searchNews"
                            @item-select="selectNews" optionLabel="title" placeholder="Buscar notícias..."
                            :loading="searchLoading" class="w-full" :pt="{
                                root: { class: 'w-full !relative', style: 'position: relative; z-index: 1' },
                                pcInputText: {
                                    class: 'w-full text-white placeholder:text-gray-500 rounded-full pl-12 pr-5 py-2.5 text-sm 2xl:text-base !border-0 focus:!border-0 focus:!outline-none focus:ring-2 focus:ring-purple-500/50',
                                    style: 'background-color: #000 !important; position: relative; z-index: 1'
                                },
                                overlay: {
                                    class: 'bg-gray-900/95 backdrop-blur-md border rounded-lg shadow-2xl mt-2 overflow-hidden',
                                    style: 'max-width: 36rem !important;'
                                },
                                list: { class: 'p-0' },
                                option: { class: 'hover:bg-purple-600/20 transition-colors' }
                            }">
                            <template #option="slotProps">
                                <div class="flex items-center gap-3 p-3 cursor-pointer w-full overflow-hidden">
                                    <img v-if="slotProps.option.image_url" :src="slotProps.option.image_url"
                                        :alt="slotProps.option.title"
                                        class="w-16 h-16 object-cover rounded-lg shrink-0" />
                                    <div v-else
                                        class="w-16 h-16 bg-gray-800 rounded-lg shrink-0 flex items-center justify-center">
                                        <i class="pi pi-image text-gray-600 text-2xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0 overflow-hidden">
                                        <div
                                            class="text-white font-semibold text-sm mb-1 break-words whitespace-normal">
                                            {{
                                                slotProps.option.title }}</div>
                                        <div class="flex items-center gap-2 text-xs">
                                            <span v-if="slotProps.option.category"
                                                class="px-2 py-1 rounded text-xs font-bold" :style="{
                                                    backgroundColor: slotProps.option.category.bg_color,
                                                    color: slotProps.option.category.text_color
                                                }">
                                                {{ slotProps.option.category.name }}
                                            </span>
                                            <span class="text-gray-400">{{ slotProps.option.date_formatted }}</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template #empty>
                                <div class="p-4 text-center text-gray-400">
                                    {{ searchLoading ? 'Carregando notícias...' : 'Nenhuma notícia encontrada' }}
                                </div>
                            </template>
                        </AutoComplete>
                    </div>

                    <!-- Social Icons -->
                    <SocialIcons variant="compact" />
                </div>
            </template>
        </Menubar>
    </div>
</template>

<style scoped>
@reference "../../css/app.css";

.icon-link:hover {
    text-shadow: 0 0 12px rgba(168, 85, 247, 0.5);
}

.menu-label-michroma {
    font-family: 'Michroma', sans-serif;
    font-family: 'Michroma', sans-serif;
    font-size: 14px;
    font-weight: 500;
}

/* Force autocomplete to take full width */
:deep(.p-autocomplete),
:deep(.p-autocomplete-input-multiple) {
    width: 100% !important;
    display: block !important;
}

:deep(.p-autocomplete input),
:deep(.p-autocomplete .p-autocomplete-input),
:deep(.p-inputtext) {
    width: 100% !important;
    min-width: 100% !important;
    border-radius: 9999px !important;
    background-color: #000000 !important;
    border: 0px solid transparent !important;
    box-shadow: none !important;
    outline: none !important;
    padding-left: 3rem !important;
}

:deep(.p-autocomplete input:focus),
:deep(.p-autocomplete .p-autocomplete-input:focus),
:deep(.p-inputtext:focus) {
    border: 0px solid transparent !important;
    box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.5) !important;
    outline: none !important;
    background-color: #000000 !important;
}

:deep(.p-autocomplete input:hover),
:deep(.p-inputtext:hover) {
    border: 0px solid transparent !important;
    background-color: #000000 !important;
}

/* Remove hover effect on mobile */
@media (max-width: 1179px) {
    :deep(.p-menubar-item:not(.p-disabled) > .p-menubar-item-content:hover) {
        color: inherit !important;
        background: transparent !important;
    }

    .mobile-search :deep(.p-autocomplete input),
    .mobile-search :deep(.p-autocomplete .p-autocomplete-input),
    .mobile-search :deep(.p-inputtext),
    .mobile-search :deep(.p-autocomplete input:focus),
    .mobile-search :deep(.p-autocomplete .p-autocomplete-input:focus),
    .mobile-search :deep(.p-inputtext:focus),
    .mobile-search :deep(.p-autocomplete input:hover),
    .mobile-search :deep(.p-inputtext:hover) {
        background-color: rgba(107, 114, 128, 0.199) !important;
    }
}

.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>