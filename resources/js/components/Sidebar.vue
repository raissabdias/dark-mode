<script setup>
import SocialIcons from './SocialIcons.vue';

import { ref, onMounted } from 'vue';
import Button from 'primevue/button';

const nextGigs = ref([]);
const ads = ref([]);

const formatDateStyle = (dateString) => {
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = date.toLocaleString('pt-BR', { month: 'short' }).toUpperCase().replace('.', '');
    return `${day} ${month}`;
};

const fetchEvents = async () => {
    try {
        const response = await fetch('/api/events/comming');
        const data = await response.json();
        if (data) {
            nextGigs.value = data.slice(0, 8).map(event => ({
                id: event.id,
                band: event.title,
                loc: event.location,
                img: event.image_url,
                date: formatDateStyle(event.date || event.event_date)
            }));
        }
    } catch (error) { 
        console.error('Erro ao buscar agenda:', error);
    }
};

const fetchAds = async () => {
    try {
        const response = await fetch('/api/ads');
        const data = await response.json();
        ads.value = data.map(ad => ({
            id: ad.id,
            title: ad.title,
            image: ad.image_url,
            link: ad.link
        }));
    } catch (error) {
        console.error('Erro ao buscar anúncios:', error);
    }
};

onMounted(() => {
    fetchEvents();
    fetchAds();
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:flex lg:flex-col gap-8 h-full">

        <div class="sidebar-block">
            <h3 class="sidebar-title">Agenda</h3>
            <div class="agenda-list flex flex-col gap-2">
                <div v-for="(gig, index) in nextGigs" :key="gig.id" class="agenda-item group"
                    :class="index >= 5 ? 'hidden lg:flex' : 'flex'">
                    <img :src="gig.img" class="agenda-img" />
                    <div>
                        <div class="date">{{ gig.date }}</div>
                        <div class="band group-hover:text-white line-clamp-1">{{ gig.band }}</div>
                        <div class="loc line-clamp-1">{{ gig.loc }}</div>
                    </div>
                </div>
            </div>
            <router-link to="/agenda">
                <Button label="Ver tudo" link class="w-full mt-2 text-xs text-gray-500 hover:text-purple-400" />
            </router-link>
        </div>
        <div class="flex flex-col gap-8">
            <div class="sidebar-block">
                <span class="ad-label">Patrocinado</span>
                <div class="flex flex-col gap-4">
                    <a v-for="ad in ads" :key="ad.id" :href="ad.link" target="_blank"
                        class="ad-item block relative group rounded-lg overflow-hidden h-[120px] transition-all cursor-pointer border border-gray-800 hover:border-purple-500">
                        <img :src="ad.image" :alt="ad.title"
                            class="w-full h-full object-cover transition-all duration-700 group-hover:scale-110 opacity-70 group-hover:opacity-100" />
                    </a>
                </div>
            </div>
            <div class="newsletter-block">
                <div class="newsletter-box group">
                    <h4 class="text-white font-bold mb-2 flex items-center gap-3">
                        <i class="pi pi-whatsapp text-green-500 text-xl"></i>
                        <span>Comunidade VIP</span>
                    </h4>
                    <p class="text-xs text-gray-400 mb-4 leading-relaxed">
                        Fique por dentro dos shows e novidades do underground em primeira mão.
                    </p>
                    <a href="https://chat.whatsapp.com/F0Pw6ClCkl3ATvaWnfdRlB?mode=gi_t" 
                       target="_blank"
                       class="flex items-center justify-center gap-2 w-full 
                              bg-green-600 hover:bg-green-500 text-white font-bold 
                              py-3 rounded-full transition-all transform group-hover:scale-105 shadow-lg shadow-green-900/20">
                        <i class="pi pi-whatsapp text-lg"></i>
                        Entrar no Grupo
                    </a>
                </div>
            </div>
            <div class="logo-block hidden md:block">
                <SocialIcons variant="menu" />
            </div>
            <div class="logo-block hidden md:block">
                <router-link to="/" class="logo-link">
                    <img src="/images/logo-red.png" alt="Logo do site" class="logo-image w-40 mx-auto opacity-80" />
                </router-link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.sidebar-title {
    font-family: 'Michroma', cursive;
    font-size: 1.25rem;
    color: white;
    border-bottom: 1px solid #333;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
}

.agenda-item {
    gap: 0.75rem;
    padding: 0.5rem;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
    background: rgba(255, 255, 255, 0.02);
}

.agenda-item:hover {
    background: rgba(168, 85, 247, 0.1);
}

.agenda-img {
    width: 3rem;
    height: 3rem;
    border-radius: 4px;
    object-fit: cover;
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
    letter-spacing: 1px;
}

.newsletter-box {
    background: linear-gradient(180deg, #1a1a1a 0%, #000000 100%);
    border: 1px solid #333;
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
    font-size: 0.9rem;
}

.newsletter-input:focus {
    border-color: #a855f7;
    outline: none;
}

.newsletter-btn {
    background: #a855f7;
    color: white;
    padding: 0.5rem 0.8rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}

.logo-link {
    display: block;
}

.logo-image {
    transition: transform 0.35s ease, opacity 0.35s ease, filter 0.35s ease;
    transform: translateY(0) scale(1);
}

.logo-link:hover .logo-image {
    transform: translateY(-4px) scale(1.06);
    opacity: 1;
    filter: drop-shadow(0 8px 16px rgba(168, 85, 247, 0.25));
}
</style>