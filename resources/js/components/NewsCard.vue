<script setup>
defineProps({ post: Object });
</script>

<template>
    <div class="news-card">
        <div class="image-wrapper">
            <img :src="post.image_url" :alt="post.title" />
            <span class="category-badge">{{ post.category }}</span>
        </div>
        <div class="content">
            <div class="meta">
                <span>{{ post.date_formatted }}</span>
                <span>•</span>
                <span class="author">{{ post.author }}</span>
            </div>
            <h3 class="title">{{ post.title }}</h3>
            <p class="excerpt">{{ post.excerpt }}</p>
            <a href="#" class="read-more">Ler Matéria <i class="pi pi-arrow-right ml-1"></i></a>
        </div>
    </div>
</template>

<style scoped>
.news-card {
    background-color: #0a0a0a;
    border: 1px solid #333;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%; /* Garante que o card ocupe toda a altura da linha */
    transition: transform 0.2s, border-color 0.2s;
}

.news-card:hover {
    transform: translateY(-5px);
    border-color: #a855f7;
}

.image-wrapper {
    position: relative;
    height: 200px; /* Altura fixa para a imagem */
    width: 100%;
}

.image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-badge {
    position: absolute;
    top: 10px; right: 10px;
    background: #a855f7; color: white;
    font-size: 0.7rem; font-weight: bold;
    padding: 2px 8px; border-radius: 4px;
    text-transform: uppercase;
}

.content {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1; /* O conteúdo cresce para preencher o card */
}

.meta {
    font-size: 0.75rem; color: #888; margin-bottom: 0.5rem;
    display: flex; gap: 0.5rem;
}
.author { color: #c084fc; }

.title {
    font-size: 1.1rem; font-weight: bold; color: white;
    margin-bottom: 0.5rem; line-height: 1.3;
}

.excerpt {
    font-size: 0.85rem; color: #aaa; margin-bottom: 1rem;
    line-height: 1.5;
    /* Limita o texto a 3 linhas para ficar uniforme */
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}

.read-more {
    margin-top: auto; /* Empurra o botão para baixo */
    color: #a855f7; text-decoration: none;
    font-size: 0.8rem; font-weight: bold; text-transform: uppercase;
}
</style>