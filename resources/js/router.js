import { createRouter, createWebHistory } from 'vue-router';
import HomeView from './views/HomeView.vue';
import NewsDetail from './components/NewsDetail.vue';
import NewsIndex from './views/NewsIndex.vue';
import AgendaIndex from './views/AgendaIndex.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView,
        meta: { title: 'Home' }
    },
    {
        path: '/noticia/:slug',
        name: 'news.detail',
        component: NewsDetail
    },
    {
        path: '/noticias',
        name: 'news.index',
        component: NewsIndex,
        meta: { title: 'Notícias' }
    },
    {
        path: '/reviews',
        name: 'reviews.index',
        component: NewsIndex,
        props: { 
            categoryIds: '6,7,8,9', 
            pageTitle: 'Reviews' 
        },
        meta: { title: 'Reviews' }
    },
    {
        path: '/agenda',
        name: 'agenda.index',
        component: AgendaIndex,
        meta: { title: 'Agenda' }
    },
    {
        path: '/guia',
        name: 'guia.index',
        component: NewsIndex,
        props: { 
            categoryIds: '23,24', 
            pageTitle: 'Guia' 
        },
        meta: { title: 'Guia' }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return { top: 0 };
    }
});

router.beforeEach((to, from, next) => {
    const siteName = 'Dark Mode';
    if (to.meta.title) {
        document.title = `${to.meta.title} | ${siteName}`;
    } else {
        document.title = siteName;
    }
    
    next();
});

export default router;