<script setup>
import { ref, onMounted } from 'vue';
import Button from 'primevue/button';

const nextGigs = ref([]);
const ads = ref([
    {
        id: 1,
        title: 'Guitar Shop',
        image: 'https://placehold.co/300x150/111/333?text=LOJA+DE+GUITARRAS',
        link: 'https://google.com'
    },
    {
        id: 2,
        title: 'Hellfest 2026',
        image: 'https://agendametal.com.br/wp-content/uploads/2025/08/hellfest-2026-1.jpg',
        link: 'https://google.com'
    }
]);

const formatDateStyle = (dateString) => {
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = date.toLocaleString('pt-BR', { month: 'short' }).toUpperCase().replace('.', '');
    return `${day} ${month}`;
};

const fetchEvents = async () => {
    try {
        const response = await fetch('/api/events');
        const data = await response.json();
        nextGigs.value = data.map(event => ({
            id: event.id,
            band: event.title, 
            loc: event.location,
            img: event.image_url,
            date: formatDateStyle(event.event_date)
        }));
    } catch (error) {
        console.error('Erro ao buscar agenda:', error);
    }
};

onMounted(() => {
    fetchEvents();
});
</script>

<template>
    <div class="sidebar-container">

        <div class="sidebar-block">
            <h3 class="sidebar-title">Agenda</h3>
            <div class="agenda-list">
                <div v-for="gig in nextGigs" :key="gig.id" class="agenda-item group">
                    <img :src="gig.img" class="agenda-img" />
                    <div>
                        <div class="date">{{ gig.date }}</div>
                        <div class="band group-hover:text-white">{{ gig.band }}</div>
                        <div class="loc">{{ gig.loc }}</div>
                    </div>
                </div>
            </div>
            <Button label="Ver tudo" link class="w-full mt-2 text-xs text-gray-500" />
        </div>

        <div class="sidebar-block">
            <span class="ad-label">Patrocinado</span>

            <div class="flex flex-col gap-4">
                <a v-for="ad in ads" :key="ad.id" :href="ad.link" target="_blank" :title="`Acessar o site ${ad.title}`"
                    class="ad-item block relative group rounded-lg overflow-hidden h-[120px] transition-all cursor-pointer">
                    <img :src="ad.image" :alt="ad.title"
                        class="w-full h-full object-cover transition-all duration-700 group-hover:scale-110 opacity-70 group-hover:opacity-100" />
                </a>
            </div>
        </div>

        <div class="newsletter-block">
            <div class="newsletter-box">
                <h4 class="text-white font-bold mb-2 flex items-center gap-3">
                    <i class="pi pi-envelope text-purple-500 text-lg mr-2"></i>
                    <span>Newsletter</span>
                </h4>

                <p class="text-xs text-gray-400 mb-3">Receba as novidades.</p>
                <div class="flex gap-2">
                    <input type="email" placeholder="Email..." class="newsletter-input" />
                    <button class="newsletter-btn"><i class="pi pi-send"></i></button>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.sidebar-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    gap: 3rem;
}

.sidebar-title {
    font-family: 'Michroma', cursive;
    font-size: 1.5rem;
    color: white;
    border-bottom: 1px solid #333;
    margin-bottom: 1rem;
}

.agenda-item {
    display: flex;
    gap: 0.75rem;
    padding: 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}

.agenda-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

.agenda-img {
    width: 3rem;
    height: 3rem;
    border-radius: 4px;
    object-fit: cover;
    opacity: 0.7;
}

.date {
    color: #a855f7;
    font-size: 0.75rem;
    font-weight: bold;
}

.band {
    color: #ccc;
    font-size: 0.9rem;
    font-weight: bold;
}

.loc {
    color: #666;
    font-size: 0.7rem;
}

.ad-label {
    display: block;
    text-align: center;
    font-size: 0.6rem;
    color: #666;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.newsletter-box {
    background: linear-gradient(to bottom, #111, #000);
    border: 1px solid #222;
    border-radius: 8px;
    padding: 1.25rem;
}

.newsletter-input {
    width: 100%;
    background: #000;
    border: 1px solid #333;
    padding: 0.5rem;
    color: white;
    border-radius: 4px;
}

.newsletter-btn {
    background: #a855f7;
    color: white;
    padding: 0.5rem 0.8rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}

.ad-item {
    border-radius: 4px;
    margin: 5px 0;
}
</style>