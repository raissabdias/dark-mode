<script setup>
import { ref } from "vue";
import Menubar from 'primevue/menubar';

const items = ref([
    { label: 'Home', icon: 'pi pi-home', route: '/' },
    { label: 'Notícias', icon: 'pi pi-megaphone', route: '/noticias' },
    { label: 'Reviews', icon: 'pi pi-star', route: '/reviews' },
    { label: 'Agenda', icon: 'pi pi-calendar', route: '/agenda' },
    { key: 'social-mobile', class: 'lg:hidden' }
]);
</script>

<template>
    <div class="sticky top-0 z-[1000] w-full">
        <Menubar :model="items"
            class="w-full bg-black/95 backdrop-blur-md border-b border-white/10 rounded-none px-4 py-3 lg:px-8 flex items-center justify-between"
            :pt="{
                root: { class: '!border-b-2 !border-black !rounded-none' },
                button: { class: 'ml-auto order-3 text-gray-200 hover:bg-white/10 focus:ring-0 w-10 h-10 flex items-center justify-center rounded-lg transition-all' },
                rootList: { class: '!bg-black/95 !border-t-2 !border-black lg:!bg-transparent lg:!border-none w-full lg:w-auto top-full left-0 absolute lg:static shadow-xl lg:shadow-none' },
                itemContent: { class: 'text-gray-200 hover:text-white' },
                itemLink: { class: 'py-3 px-4 lg:py-2 lg:px-3' }
            }">
            <template #start>
                <router-link to="/" class="flex items-center gap-2 mr-4 lg:mr-8 shrink-0 order-1">
                    <img src="/images/logo.png" alt="DarkMode Logo"
                        class="h-8 md:h-9 w-auto object-contain hover:opacity-80 transition-opacity" />
                </router-link>
            </template>
            <template #item="{ item, props }">
                <div v-if="item.key === 'social-mobile'"
                    class="flex items-center justify-center gap-6 py-6 border-t border-white/10 mt-2 bg-black/50">
                    <a href="#"
                        class="text-gray-400 text-2xl hover:text-purple-500 hover:scale-110 transition-transform"><i
                            class="pi pi-instagram"></i></a>
                    <a href="#"
                        class="text-gray-400 text-2xl hover:text-purple-500 hover:scale-110 transition-transform"><i
                            class="pi pi-twitter"></i></a>
                    <a href="#"
                        class="text-gray-400 text-2xl hover:text-purple-500 hover:scale-110 transition-transform"><i
                            class="pi pi-youtube"></i></a>
                    <a href="#"
                        class="text-gray-400 text-2xl hover:text-purple-500 hover:scale-110 transition-transform"><i
                            class="pi pi-facebook"></i></a>
                </div>
                <router-link v-else :to="item.route" custom v-slot="{ href, navigate, isActive }">
                    <a :href="href" @click="navigate" class="flex items-center group w-full" v-bind="props.action"
                        :class="{ 'text-purple-500 font-bold': isActive }">
                        <span
                            :class="[item.icon, isActive ? 'text-purple-500' : 'text-gray-400', 'group-hover:text-purple-400 transition-colors mr-3']" />
                        <span
                            :class="[isActive ? 'text-white' : '', 'tracking-wide group-hover:text-white transition-colors']">{{ item.label }}</span>
                    </a>
                </router-link>
            </template>
            <template #end>
                <div class="hidden lg:flex items-center gap-5 order-2">
                    <a href="#"
                        class="text-gray-400 text-lg hover:text-purple-500 hover:-translate-y-1 transition-all"><i
                            class="pi pi-instagram"></i></a>
                    <a href="#"
                        class="text-gray-400 text-lg hover:text-purple-500 hover:-translate-y-1 transition-all"><i
                            class="pi pi-twitter"></i></a>
                    <a href="#"
                        class="text-gray-400 text-lg hover:text-purple-500 hover:-translate-y-1 transition-all"><i
                            class="pi pi-youtube"></i></a>
                    <a href="#"
                        class="text-gray-400 text-lg hover:text-purple-500 hover:-translate-y-1 transition-all"><i
                            class="pi pi-facebook"></i></a>
                </div>
            </template>
        </Menubar>
    </div>
</template>

<style scoped>
.icon-link:hover {
    text-shadow: 0 0 12px rgba(168, 85, 247, 0.5);
}
</style>