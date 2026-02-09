<script setup>
import { ref, onMounted, computed } from 'vue';
import Calendar from 'primevue/calendar';
import { usePrimeVue } from 'primevue/config';

const primevue = usePrimeVue();

const configurarPortugues = () => {
    primevue.config.locale.dayNames = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
    primevue.config.locale.dayNamesShort = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    primevue.config.locale.dayNamesMin = ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'];
    primevue.config.locale.monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    primevue.config.locale.monthNamesShort = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    primevue.config.locale.firstDayOfWeek = 1;
};

const allMonthEvents = ref([]);
const loading = ref(true);
const viewDate = ref(new Date());
const selectedDate = ref(null);

const onMonthChange = (event) => {
    viewDate.value = new Date(event.year, event.month - 1, 1);

    selectedDate.value = null;
    fetchMonthEvents();
};

const onYearChange = (event) => {
    viewDate.value = new Date(event.year, event.month - 1, 1);

    selectedDate.value = null;
    fetchMonthEvents();
};

// --- BUSCA API ---
const fetchMonthEvents = async () => {
    loading.value = true;
    try {
        const year = viewDate.value.getFullYear();
        const month = viewDate.value.getMonth() + 1;

        let url = `/api/events?year=${year}&month=${month}&per_page=100`;

        const response = await fetch(url);
        const json = await response.json();

        allMonthEvents.value = json.data || json;
    } catch (error) {
        console.error('Erro ao carregar mês:', error);
    } finally {
        loading.value = false;
    }
};

const displayedEvents = computed(() => {
    if (!selectedDate.value) {
        return allMonthEvents.value;
    }

    return allMonthEvents.value.filter(event => {
        const rawDate = event.date || event.event_date || event.start_date;
        if (!rawDate) return false;
        const eDate = new Date(rawDate);

        return eDate.getDate() === selectedDate.value.getDate() &&
            eDate.getMonth() === selectedDate.value.getMonth() &&
            eDate.getFullYear() === selectedDate.value.getFullYear();
    });
});

const hasEvent = (calendarDate) => {
    const year = calendarDate.year;
    const month = calendarDate.month;
    const day = calendarDate.day;

    return allMonthEvents.value.some(event => {
        const rawDate = event.date || event.event_date || event.start_date;
        if (!rawDate) return false;

        const eDate = new Date(rawDate);
        return eDate.getDate() === day &&
            eDate.getMonth() === month &&
            eDate.getFullYear() === year;
    });
};

const isSelected = (date) => {
    return selectedDate.value &&
        date.day === selectedDate.value.getDate() &&
        date.month === selectedDate.value.getMonth() &&
        date.year === selectedDate.value.getFullYear();
};

const clearSelection = () => {
    selectedDate.value = null;
};

const changeMonth = (direction) => {
    const current = viewDate.value;
    const newDate = new Date(current.getFullYear(), current.getMonth() + direction, 1);
    viewDate.value = newDate;
    selectedDate.value = null;
    fetchMonthEvents();
};

onMounted(() => {
    configurarPortugues();
    fetchMonthEvents();
});
</script>

<template>
    <div class="py-8 animate-fade-in w-full min-h-screen">
        <div class="w-full">
            <h1
                class="text-2xl md:text-4xl font-bold text-white mb-8 border-l-4 border-purple-500 pl-4 font-michroma capitalize">
                Agenda<span class="hidden md:inline"> - {{ viewDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' }) }}</span>
            </h1>
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <!-- Filtro de mês para mobile -->
                <div class="lg:hidden w-full mb-4">
                    <div class="bg-gray-900/80 backdrop-blur-md rounded-xl p-4 border border-gray-800 shadow-xl">
                        <div class="flex items-center justify-between gap-4">
                            <button @click="changeMonth(-1)"
                                class="p-2 hover:bg-purple-600 rounded-lg transition-colors text-purple-400 hover:text-white">
                                <i class="pi pi-chevron-left text-lg"></i>
                            </button>

                            <div class="flex-grow text-center">
                                <div class="text-xl font-bold text-white capitalize">
                                    {{ viewDate.toLocaleString('pt-BR', { month: 'long' }) }}
                                </div>
                                <div class="text-sm text-gray-400 font-semibold">
                                    {{ viewDate.getFullYear() }}
                                </div>
                            </div>

                            <button @click="changeMonth(1)"
                                class="p-2 hover:bg-purple-600 rounded-lg transition-colors text-purple-400 hover:text-white">
                                <i class="pi pi-chevron-right text-lg"></i>
                            </button>
                        </div>

                        <div v-if="selectedDate" class="mt-3 pt-3 border-t border-gray-800">
                            <button @click="clearSelection"
                                class="w-full py-2 text-xs font-bold text-gray-400 hover:text-white hover:bg-white/5 rounded transition-colors flex items-center justify-center gap-2">
                                <i class="pi pi-undo"></i> Ver mês inteiro
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Calendário lateral para desktop -->
                <div class="hidden lg:block w-full lg:w-[350px] shrink-0 lg:sticky lg:top-4 z-20">
                    <div class="bg-gray-900/80 backdrop-blur-md rounded-xl p-4 border border-gray-800 shadow-xl">
                        <Calendar v-model="selectedDate" :viewDate="viewDate" inline class="custom-dark-calendar w-full"
                            @month-change="onMonthChange" @year-change="onYearChange">
                            <template #date="slotProps">
                                <div class="w-8 h-8 flex items-center justify-center relative rounded-md transition-all duration-300 cursor-pointer font-bold text-sm"
                                    :class="{
                                        'bg-purple-600 text-white shadow-glow transform scale-110 z-10':
                                            isSelected(slotProps.date),
                                        'bg-purple-900/40 text-purple-200 border border-purple-500/40 hover:bg-purple-800':
                                            hasEvent(slotProps.date) && !isSelected(slotProps.date),
                                        'text-gray-500 hover:text-gray-300 hover:bg-gray-800':
                                            !hasEvent(slotProps.date) && !isSelected(slotProps.date)
                                    }">
                                    {{ slotProps.date.day }}
                                </div>
                            </template>
                        </Calendar>
                        <div v-if="selectedDate" class="mt-4 pt-4 border-t border-gray-800">
                            <button @click="clearSelection"
                                class="w-full py-2 text-xs font-bold text-gray-400 hover:text-white hover:bg-white/5 rounded transition-colors flex items-center justify-center gap-2">
                                <i class="pi pi-undo"></i> Ver mês inteiro
                            </button>
                        </div>
                    </div>
                </div>
                <div class="w-full flex-grow">
                    <div v-if="loading" class="flex justify-center py-20">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
                    </div>
                    <div v-else>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
                            <div v-for="event in displayedEvents" :key="event.id"
                                class="group bg-gray-900 rounded-xl overflow-hidden border border-gray-800 hover:border-purple-500 hover:-translate-y-1 transition-all duration-300 flex flex-row sm:flex-col shadow-lg hover:shadow-purple-900/20 h-auto sm:h-auto">
                                <div class="relative w-28 xs:w-32 sm:w-full sm:aspect-[2/3] shrink-0 overflow-hidden">
                                    <img :src="event.image_url || '/images/default-event.jpg'" :alt="event.title"
                                        class="w-full h-full object-cover">
                                    <div
                                        class="absolute top-1 right-1 sm:top-3 sm:right-3 bg-black/80 backdrop-blur-sm border border-purple-500/50 text-white p-1 sm:p-2 text-center rounded-md sm:rounded-lg min-w-[40px] sm:min-w-[60px]">
                                        <div class="text-sm sm:text-xl font-black leading-none text-purple-400">{{
                                            event.day }}</div>
                                        <div class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider">{{
                                            event.month }}
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 sm:p-5 flex flex-col flex-grow relative w-full justify-between">
                                    <div>
                                        <h3 class="text-base font-bold text-white mb-1 line-clamp-2">{{
                                            event.title }}</h3>
                                        <div class="text-gray-400 text-xs sm:text-sm flex items-center gap-2">
                                            <i class="pi pi-map-marker text-purple-600"></i> <span
                                                class="line-clamp-1">{{
                                                    event.location }}</span>
                                        </div>
                                    </div>
                                    <div v-if="event.ticket_url" class="mt-2 sm:mt-auto pt-2 border-t border-gray-800">
                                        <a :href="event.ticket_url" target="_blank"
                                            class="flex items-center justify-center gap-2 w-full bg-white/5 hover:bg-purple-600 text-gray-300 hover:text-white py-1.5 rounded-lg text-xs font-bold">
                                            Ingressos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="displayedEvents.length === 0"
                            class="flex flex-col items-center justify-center py-16 text-center bg-gray-900/50 rounded-xl border border-gray-800 border-dashed">
                            <i class="pi pi-calendar-times text-5xl text-gray-600 mb-4"></i>
                            <p class="text-gray-400">Nenhum show encontrado.</p>
                        </div>
                    </div>
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

.shadow-glow {
    box-shadow: 0 0 15px rgba(168, 85, 247, 0.6);
}

:deep(.custom-dark-calendar .p-datepicker) {
    background: transparent;
    border: none;
    padding: 0;
}

:deep(.custom-dark-calendar .p-datepicker-header) {
    background: transparent;
    color: white;
    border-bottom: 1px solid #374151;
    padding-bottom: 0.5rem;
    margin-bottom: 0.5rem;
}

:deep(.custom-dark-calendar .p-datepicker-title) {
    font-weight: 800;
    font-size: 1.1rem;
    text-transform: capitalize;
}

:deep(.custom-dark-calendar .p-datepicker-prev-icon),
:deep(.custom-dark-calendar .p-datepicker-next-icon) {
    color: #a855f7;
    width: 1rem;
    height: 1rem;
}

/* Nomes dos dias (Seg, Ter...) */
:deep(.custom-dark-calendar .p-datepicker table th) {
    color: #9ca3af;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 0.5rem;
}

:deep(.custom-dark-calendar .p-datepicker table td) {
    padding: 2px;
}

:deep(.custom-dark-calendar .p-datepicker table td > span) {
    display: none;
}

:deep(.custom-dark-calendar .p-datepicker table td > span:has(div)) {
    display: block !important;
    width: 100%;
    height: 100%;
}
</style>