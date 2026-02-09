<script setup>
defineProps({ post: Object });
</script>

<template>
    <div class="news-card group">
        <div class="image-wrapper">
            <img :src="post.image_url" :alt="post.title" />
            <span v-if="post.category" class="category-badge"
                :style="{ backgroundColor: post.category.bg_color || '#a855f7', color: post.category.text_color || '#fff' }">
                {{ post.category.name || post.category }}
            </span>
        </div>
        <div class="content">
            <div class="meta">
                <span>{{ post.date_formatted }}</span>
                <span>•</span>
                <span class="author">{{ post.author?.name || post.author || 'Redação' }}</span>
            </div>
            <h3 class="title group-hover:text-purple-400 transition-colors">{{ post.title }}</h3>
            <p class="excerpt">{{ post.excerpt }}</p>
            <div @click="$emit('open-news', post.slug)"
                class="flex items-center font-semibold text-sm mt-auto cursor-pointer read-more">
                Ler Matéria <i class="pi pi-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>
    </div>
</template>

<style scoped>
.news-card {
    background-color: #0a0a0a;
    border: 1px solid #262626;
    /* Cinza mais escuro */
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: all 0.3s ease;
}

.news-card:hover {
    transform: translateY(-4px);
    border-color: #a855f7;
    box-shadow: 0 10px 30px -10px rgba(168, 85, 247, 0.2);
}

.image-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    overflow: hidden;
}

.image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.news-card:hover .image-wrapper img {
    transform: scale(1.05);
}

.category-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    z-index: 10;
}

.content {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.meta {
    font-size: 0.75rem;
    color: #737373;
    margin-bottom: 0.75rem;
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.author {
    color: #d8b4fe;
    font-weight: 500;
}

.title {
    font-size: 1.1rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.75rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@media (max-width: 640px) {
    .title {
        font-size: 1rem;
        font-weight: 500;
    }

    .excerpt {
        font-size: 0.75rem;
    }
}

.excerpt {
    font-size: 0.875rem;
    color: #a3a3a3;
    margin-bottom: 1.25rem;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.read-more {
    margin-top: auto;
    color: #a855f7;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>