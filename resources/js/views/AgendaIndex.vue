<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import Calendar from 'primevue/calendar';
import { usePrimeVue } from 'primevue/config';
import { useRoute, useRouter } from 'vue-router';

const primevue = usePrimeVue();
const route = useRoute();
const router = useRouter();

const configurarPortugues = () => {
    primevue.config.locale.dayNames = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
    primevue.config.locale.dayNamesShort = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    primevue.config.locale.dayNamesMin = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    primevue.config.locale.monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    primevue.config.locale.monthNamesShort = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    primevue.config.locale.firstDayOfWeek = 1;
};

const allMonthEvents = ref([]);
const loading = ref(true);
const viewDate = ref(new Date());
const selectedDate = ref(null);
const calendarDate = ref(new Date());
const calendarKey = ref(0);

const fetchMonthEvents = async () => {
    loading.value = true;
    try {
        const year = viewDate.value.getFullYear();
        const month = viewDate.value.getMonth() + 1;
        const response = await fetch(`/api/events?year=${year}&month=${month}&per_page=100`);
        const json = await response.json();
        allMonthEvents.value = json.data || json;
    } catch (error) {
        console.error('Erro ao carregar mês:', error);
    } finally {
        loading.value = false;
    }
};

const checkDeepLink = async () => {
    const dateQuery = route.query.date;
    const viewMode = route.query.view;

    if (dateQuery) {
        const dateOnly = dateQuery.split('T')[0];
        const dateObj = new Date(dateOnly + 'T12:00:00');

        if (!isNaN(dateObj.getTime())) {
            viewDate.value = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
            if (viewMode === 'month') {
                calendarDate.value = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
                selectedDate.value = null;
            } else {
                calendarDate.value = dateObj;
                selectedDate.value = dateObj;
            }
            
            calendarKey.value++;
            
            await nextTick();
            await fetchMonthEvents();

            router.replace({ query: {} });
        }
    } else {
        await fetchMonthEvents();
    }
};

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

const onDateSelect = (date) => {
    selectedDate.value = date;
};

const displayedEvents = computed(() => {
    if (!selectedDate.value) return allMonthEvents.value;

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
    return allMonthEvents.value.some(event => {
        const rawDate = event.date || event.event_date || event.start_date;
        if (!rawDate) return false;
        const eDate = new Date(rawDate);
        return eDate.getDate() === calendarDate.day &&
               eDate.getMonth() === calendarDate.month &&
               eDate.getFullYear() === calendarDate.year;
    });
};

const isSelected = (date) => {
    return selectedDate.value &&
        date.day === selectedDate.value.getDate() &&
        date.month === selectedDate.value.getMonth() &&
        date.year === selectedDate.value.getFullYear();
};

const changeMonth = (direction) => {
    const newDate = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() + direction, 1);
    viewDate.value = newDate;
    calendarDate.value = newDate;
    selectedDate.value = null;
    fetchMonthEvents();
};

onMounted(() => {
    configurarPortugues();
    checkDeepLink();
});

watch(() => route.query.date, (newVal) => {
    if (newVal) checkDeepLink();
});
</script>

<template>
    <div class="py-8 animate-fade-in w-full min-h-screen">
        <div class="w-full">
            <h1 class="text-2xl md:text-4xl font-bold text-white mb-8 border-l-4 border-purple-500 pl-4 font-michroma capitalize">
                Agenda<span class="hidden md:inline"> - {{ viewDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' }) }}</span>
            </h1>
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <div class="lg:hidden w-full mb-4">
                    <div class="bg-gray-900/80 backdrop-blur-md rounded-xl p-4 border border-gray-800 shadow-xl flex items-center justify-between">
                        <button @click="changeMonth(-1)" class="p-2 text-purple-400"><i class="pi pi-chevron-left"></i></button>
                        <div class="text-center">
                            <div class="text-xl font-bold text-white capitalize">{{ viewDate.toLocaleString('pt-BR', { month: 'long' }) }}</div>
                            <div class="text-sm text-gray-400">{{ viewDate.getFullYear() }}</div>
                        </div>
                        <button @click="changeMonth(1)" class="p-2 text-purple-400"><i class="pi pi-chevron-right"></i></button>
                    </div>
                </div>
                <div class="hidden lg:block w-full lg:w-[350px] shrink-0 lg:sticky lg:top-4 z-20">
                    <div class="bg-gray-900/80 backdrop-blur-md rounded-xl p-4 border border-gray-800 shadow-xl">
                        <Calendar 
                            :key="calendarKey"
                            v-model="calendarDate"
                            inline 
                            class="custom-dark-calendar w-full"
                            @date-select="onDateSelect"
                            @month-change="onMonthChange" 
                            @year-change="onYearChange"
                        >
                            <template #date="slotProps">
                                <div class="w-8 h-8 flex items-center justify-center relative rounded-md transition-all duration-300 cursor-pointer font-bold text-sm"
                                    :class="{
                                        'bg-purple-600 text-white shadow-glow transform scale-110 z-10': isSelected(slotProps.date),
                                        'bg-purple-900/40 text-purple-200 border border-purple-500/40 hover:bg-purple-800': hasEvent(slotProps.date) && !isSelected(slotProps.date),
                                        'text-gray-500 hover:text-gray-300 hover:bg-gray-800': !hasEvent(slotProps.date) && !isSelected(slotProps.date)
                                    }">
                                    {{ slotProps.date.day }}
                                </div>
                            </template>
                        </Calendar>
                        <div v-if="selectedDate" class="mt-4 pt-4 border-t border-gray-800">
                            <button @click="selectedDate = null" class="w-full py-2 text-xs font-bold text-gray-400 hover:text-white flex items-center justify-center gap-2">
                                <i class="pi pi-undo"></i> Ver mês inteiro
                            </button>
                        </div>
                    </div>
                </div>
                <div class="w-full flex-grow">
                    <div v-if="loading" class="flex justify-center py-20">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-purple-500"></div>
                    </div>
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
                        <div v-for="event in displayedEvents" :key="event.id" class="group bg-gray-900 rounded-xl overflow-hidden border border-gray-800 hover:border-purple-500 transition-all flex flex-row sm:flex-col shadow-lg">
                            <div class="relative w-28 sm:w-full sm:aspect-[2/3] shrink-0 overflow-hidden">
                                <img :src="event.image_url || '/images/default-event.jpg'" class="w-full h-full object-cover">
                                <div class="absolute top-1 right-1 sm:top-3 sm:right-3 bg-black/80 border border-purple-500/50 text-white p-1 sm:p-2 text-center rounded-md sm:rounded-lg">
                                    <div class="text-sm sm:text-xl font-black text-purple-400">{{ event.day || new Date(event.date || event.event_date).getDate() }}</div>
                                    <div class="text-[8px] sm:text-[10px] font-bold uppercase">{{ event.month }}</div>
                                </div>
                            </div>
                            <div class="p-3 sm:p-5 flex flex-col flex-grow justify-between">
                                <div>
                                    <h3 class="text-base font-bold text-white mb-1 line-clamp-2">{{ event.title }}</h3>
                                    <div class="text-gray-400 text-xs sm:text-sm flex items-center gap-2">
                                        <i class="pi pi-map-marker text-purple-600"></i> {{ event.location }}
                                    </div>
                                </div>
                                <a v-if="event.ticket_url" :href="event.ticket_url" target="_blank" class="mt-2 sm:mt-4 py-1.5 bg-white/5 hover:bg-purple-600 text-center rounded-lg text-xs font-bold transition-colors">Ingressos</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>