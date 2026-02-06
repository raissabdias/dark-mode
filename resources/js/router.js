import { createRouter, createWebHistory } from 'vue-router';
import HomeView from './views/HomeView.vue';
import NewsDetail from './components/NewsDetail.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView
    },
    {
        path: '/noticia/:id',
        name: 'news.detail',
        component: NewsDetail
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