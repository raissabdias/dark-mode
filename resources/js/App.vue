<script setup>
import { ref, onMounted } from 'vue';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';
import Carousel from './components/Carousel.vue';
import NewsCard from './components/NewsCard.vue';
import Sidebar from './components/Sidebar.vue';

const latestNews = ref([
    { id: 1, title: 'Metallica: 40 anos de Master of Puppets', excerpt: 'Box especial conta com vinis coloridos e fitas demo raras.', image: 'https://www.radiofrance.fr/s3/cruiser-production/2020/03/95910584-c96a-44ec-b087-db4344f9a619/1200x680_gettyimages-830914322.webp', category: 'Thrash', date: '02 Fev', author: 'Eddie' },
    { id: 2, title: 'The Cure confirma turnê no Brasil', excerpt: 'Robert Smith promete sets de 3 horas em SP e Rio.', image: 'https://rollingstone.com.br/wp-content/uploads/gettyimages-57464291.jpg', category: 'Pós-Punk', date: '01 Fev', author: 'Siouxsie' },
    { id: 3, title: 'Gojira e o ativismo ambiental', excerpt: 'Banda francesa mantém peso técnico e letras conscientes.', image: 'https://www.tenhomaisdiscosqueamigos.com/wp-content/uploads/2016/07/gojira.jpg', category: 'Death', date: '30 Jan', author: 'Joe' },
    { id: 4, title: 'Black Sabbath: Reunião à vista?', excerpt: 'Tony Iommi não descarta último show histórico.', image: 'https://i.guim.co.uk/img/static/sys-images/Music/Pix/pictures/2012/6/11/1339424251444/Black-Sabbath-in-2012-006.jpg?width=620&dpr=2&s=none&crop=none', category: 'Heavy', date: '28 Jan', author: 'Ozzy' },
    { id: 5, title: 'Nightwish anuncia nova vocalista', excerpt: 'Banda finlandesa revela quem assume a nova era.', image: 'https://kissfm.com.br/wp-content/uploads/2024/05/KISSFMSP.svg', category: 'Symphonic', date: '27 Jan', author: 'Tuomas' },
    { id: 6, title: 'Slipknot: De volta às raízes', excerpt: 'Novo single traz a agressividade dos primeiros álbuns.', image: 'https://rollingstone.com.br/wp-content/uploads/2022/12/slipknot-foto-anthony-scanga.jpg', category: 'Nu Metal', date: '26 Jan', author: 'Maggot' },
    { id: 7, title: 'Sleep Token esgota Wembley', excerpt: 'Fenômeno mascarado anuncia turnê mundial massiva.', image: 'https://www.tenhomaisdiscosqueamigos.com/wp-content/uploads/2023/02/sleep-token-1.jpg', category: 'Alt', date: '25 Jan', author: 'Vessel' },
    { id: 8, title: 'Megadeth revela capa polêmica', excerpt: 'Arte criada por IA gera debate nas redes sociais.', image: 'https://static.wixstatic.com/media/fe6907_cfbf2801fd96414db3babbbfd8795fcd~mv2.jpg/v1/fill/w_925,h_520,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/fe6907_cfbf2801fd96414db3babbbfd8795fcd~mv2.jpg', category: 'Thrash', date: '24 Jan', author: 'Vic' },
    { id: 9, title: 'Angra grava DVD em caverna', excerpt: 'Show exclusivo para 500 fãs na Gruta de Maquiné.', image: 'https://s2-g1.glbimg.com/Zv7ePWezUzCQh3alI8P4DgF2lEY=/0x0:1920x1281/1008x0/smart/filters:strip_icc()/i.s3.glbimg.com/v1/AUTH_59edd422c0c84a879bd37670ae4f538a/internal_photos/bs/2025/I/r/GyVlKSTlWLlLUASYfrPw/angra-2023-henrique-grandi-35.jpg', category: 'Power', date: '23 Jan', author: 'Rafael' }
]);

const newsList = ref([]);
const isLoading = ref(true);

const fetchNews = async () => {
    try {
        const response = await fetch('/api/news');
        const data = await response.json();
        newsList.value = data;
        console.log('Notícias carregadas:', data);
    } catch (error) {
        console.error('Erro ao carregar notícias:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchNews();
});
</script>

<template>
    <div class="layout-wrapper">
        <Header />
        <main class="main-container">
            <Carousel />
            <div class="main-grid">
                <div class="news-section">
                    <h2 class="section-title">Últimas Notícias</h2>
                    <div class="cards-grid">
                        <NewsCard v-for="post in newsList" :key="post.id" :post="post" />
                    </div>
                </div>
                <aside class="sidebar-section">
                    <Sidebar />
                </aside>
            </div>
        </main>
        <Footer />
    </div>
</template>

<style scoped>
.layout-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.main-container {
    flex: 1;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 16px;
}

@media (min-width: 768px) {
    .main-container {
        padding: 40px 24px;
    }
}

.main-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 40px;
    margin-top: 20px;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.section-title {
    font-family: 'Michroma', sans-serif;
    font-size: 1.8rem;
    color: white;
    margin-bottom: 20px;
    border-left: 4px solid #a855f7;
    padding-left: 12px;
}

@media (max-width: 1024px) {
    .main-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }

    .section-title {
        font-size: 1.5rem;
    }
}
</style>