import { createRouter, createWebHistory } from 'vue-router';
import HomeView from './views/HomeView.vue';
import NewsDetail from './components/NewsDetail.vue';
import NewsIndex from './views/NewsIndex.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView
    },
    {
        path: '/noticia/:slug',
        name: 'news.detail',
        component: NewsDetail
    },
    {
        path: '/noticias',
        name: 'news.index',
        component: NewsIndex
    },
    {
        path: '/reviews',
        name: 'reviews.index',
        component: NewsIndex,
        props: { category: 'Review' } 
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return { top: 0 };
    }
});

export default router;