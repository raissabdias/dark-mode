<script setup>
import { ref, onMounted } from 'vue';

const eventsData = ref({ data: [] });
const loading = ref(true);

const fetchEvents = async (page = 1) => {
    loading.value = true;
    try {
        const response = await fetch(`/api/events?page=${page}`);
        eventsData.value = await response.json();

        console.log('Eventos carregados:', eventsData.value);
    } catch (error) {
        console.error('Erro ao carregar agenda:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchEvents();
});
</script>

<template>
    <div class="py-8 animate-fade-in w-full">
        <div class="w-full">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-8 border-l-4 border-purple-500 pl-4 font-michroma">
                Agenda de Shows
            </h1>
            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
            </div>
            <div v-else>
                <div v-if="eventsData.data.length === 0" class="text-gray-400 text-lg">
                    Nenhum evento encontrado por enquanto.
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
                    <div v-for="event in eventsData.data" :key="event.id"
                        class="group bg-gray-900 rounded-xl overflow-hidden border border-gray-800 hover:border-purple-500 hover:-translate-y-1 transition-all duration-300 flex flex-col shadow-lg hover:shadow-purple-900/20">
                        <div class="relative w-full aspect-[2/3] overflow-hidden">
                            <img :src="event.image_url || '/images/default-event.jpg'" :alt="event.title"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-60">
                            </div>
                            <div
                                class="absolute top-3 right-3 bg-black/80 backdrop-blur-sm border border-purple-500/50 text-white p-2 text-center rounded-lg min-w-[60px] shadow-lg">
                                <div class="text-xl font-black leading-none text-purple-400">{{ event.day }}</div>
                                <div class="text-[10px] font-bold uppercase tracking-wider">{{ event.month }}</div>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow relative">
                            <div
                                class="text-xs font-bold text-purple-400 mb-2 uppercase tracking-wide flex items-center gap-1">
                                <i class="pi pi-calendar"></i>
                                {{ event.date_formatted }}
                            </div>
                            <h3
                                class="text-xl font-bold text-white mb-2 leading-tight group-hover:text-purple-300 transition-colors line-clamp-2">
                                {{ event.title }}
                            </h3>
                            <div class="text-gray-400 text-sm mb-4 flex items-center gap-2">
                                <i class="pi pi-map-marker text-purple-600"></i>
                                <span class="line-clamp-1">{{ event.location || 'Local a confirmar' }}</span>
                            </div>
                            <div v-if="event.ticket_url" class="mt-auto pt-4 border-t border-gray-800">
                                <a  :href="event.ticket_url || '#'" target="_blank"
                                    class="flex items-center justify-center gap-2 w-full bg-white/5 hover:bg-purple-600 text-gray-300 hover:text-white py-2 px-4 rounded-lg transition-all font-bold text-sm">
                                    <i class="pi pi-ticket"></i>Mais informações
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <div v-if="eventsData.last_page > 1" class="flex justify-center gap-2">
                    <button v-for="page in eventsData.last_page" :key="page" @click="fetchEvents(page)"
                        class="w-10 h-10 rounded-lg font-bold transition-colors"
                        :class="page === eventsData.current_page ? 'bg-purple-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'">
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
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