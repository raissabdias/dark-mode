<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    newsSlug: { type: String, required: true }
});

const comments = ref([]);
const form = ref({ name: '', email: '', content: '' });
const isSubmitting = ref(false);
const message = ref(null);

const fetchComments = async () => {
    try {
        const res = await fetch(`/api/news/${props.newsSlug}/comments`);
        comments.value = await res.json();
    } catch (e) {
        console.error(e);
    }
};

const submitComment = async () => {
    if (!form.value.name || !form.value.email || !form.value.content) return;
    
    isSubmitting.value = true;
    message.value = null;

    try {
        const res = await fetch(`/api/news/${props.newsSlug}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(form.value)
        });

        if (res.ok) {
            const newComment = await res.json();
            comments.value.unshift(newComment); 
            form.value = { name: '', email: '', content: '' };
            message.value = { type: 'success', text: 'Comentário enviado!' };
        } else {
            message.value = { type: 'error', text: 'Erro ao enviar. Tente novamente.' };
        }
    } catch (e) {
        message.value = { type: 'error', text: 'Erro de conexão.' };
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    if (props.newsSlug) fetchComments();
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <div class="mt-12 border-t border-gray-800 pt-8">
        <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
            <i class="pi pi-comments text-purple-500"></i> Comentários ({{ comments.length }})
        </h3>
        <div class="bg-gray-900 rounded-xl p-6 mb-8 border border-gray-800">
            <h4 class="text-lg font-semibold text-gray-300 mb-4">Deixe sua opinião</h4>
            <form @submit.prevent="submitComment" class="flex flex-col gap-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input v-model="form.name" type="text" placeholder="Seu nome" required
                        class="bg-black border border-gray-700 text-white rounded-lg p-3 focus:border-purple-500 focus:outline-none transition-colors" />
                    
                    <input v-model="form.email" type="email" placeholder="Seu e-mail (não será publicado)" required
                        class="bg-black border border-gray-700 text-white rounded-lg p-3 focus:border-purple-500 focus:outline-none transition-colors" />
                </div>
                <textarea v-model="form.content" rows="3" placeholder="O que você achou?" required
                    class="bg-black border border-gray-700 text-white rounded-lg p-3 focus:border-purple-500 focus:outline-none transition-colors resize-none"></textarea>
                <div v-if="message" :class="message.type === 'success' ? 'text-green-400' : 'text-red-400'" class="text-sm font-bold">
                    {{ message.text }}
                </div>
                <button type="submit" :disabled="isSubmitting"
                    class="self-end px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg transition-colors disabled:opacity-50">
                    {{ isSubmitting ? 'Enviando...' : 'Publicar Comentário' }}
                </button>
            </form>
        </div>
        <div class="flex flex-col gap-4">
            <div v-for="comment in comments" :key="comment.id" class="bg-black/50 border border-gray-800 p-4 rounded-lg">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-purple-900/50 flex items-center justify-center text-purple-300 font-bold text-xs">
                            {{ comment.name.substring(0,2).toUpperCase() }}
                        </div>
                        <span class="font-bold text-white">{{ comment.name }}</span>
                    </div>
                    <span class="text-xs text-gray-500">{{ formatDate(comment.created_at) }}</span>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed pl-10">{{ comment.content }}</p>
            </div>
            <div v-if="comments.length === 0" class="text-gray-500 text-center py-4">
                Seja o primeiro a comentar!
            </div>
        </div>
    </div>
</template>