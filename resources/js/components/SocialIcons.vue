<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'compact'
    },
    links: {
        type: Array,
        default: () => []
    }
});

const variantDefaults = {
    menu: [
        {
            id: 'instagram',
            href: 'https://www.instagram.com/dark_mode_magazine',
            icon: 'pi pi-instagram',
            handle: '@dark_mode_magazine',
            class: 'social-hover-instagram group inline-flex items-center gap-2 h-11 px-4 rounded-full border border-white/10 bg-black/60 text-pink-500 hover:text-white hover:scale-[1.02] hover:border-transparent transition-all duration-200'
        },
        {
            id: 'youtube',
            href: 'https://www.youtube.com/@darkmode-revistadigital',
            icon: 'pi pi-youtube',
            handle: '@darkmode-revistadigital',
            class: 'social-hover-youtube group inline-flex items-center gap-2 h-11 px-4 rounded-full border border-white/10 bg-black/60 text-red-500 hover:text-white hover:scale-[1.02] hover:border-transparent transition-all duration-200'
        }
    ],
    compact: [
        {
            id: 'instagram',
            href: 'https://www.instagram.com/dark_mode_magazine',
            icon: 'pi pi-instagram',
            linkClass: 'social-hover-instagram hover:border-transparent',
            iconClass: 'text-pink-500 group-hover:text-white'
        },
        {
            id: 'youtube',
            href: 'https://www.youtube.com/@darkmode-revistadigital',
            icon: 'pi pi-youtube',
            linkClass: 'social-hover-youtube hover:border-transparent',
            iconClass: 'text-red-500 group-hover:text-white'
        }
    ]
};

const resolvedLinks = computed(() => {
    if (props.links.length) {
        return props.links;
    }

    return variantDefaults[props.variant] ?? variantDefaults.compact;
});

const wrapperClass = computed(() => {
    return props.variant === 'menu'
    ? 'flex flex-col gap-3 w-full max-w-xs mx-auto'
        : 'flex items-center gap-2';
});

const linkClass = (item) => {
    if (props.variant === 'menu') {
        return `${item.class} social-content-shadow w-full justify-center`;
    }

    return ['group social-content-shadow flex items-center justify-center w-8 h-8 rounded-full border border-gray-800 bg-[#0a0a0a] transition-colors duration-200', item.linkClass];
};

const iconClass = (item) => {
    if (props.variant === 'menu') {
        return '';
    }

    return ['transition-colors duration-200', item.iconClass];
};
</script>

<template>
    <div :class="wrapperClass">
        <a v-for="item in resolvedLinks" :key="item.id" :href="item.href" target="_blank" rel="noopener noreferrer" :class="linkClass(item)">
            <i :class="[item.icon, iconClass(item)]"></i>
            <span v-if="props.variant === 'menu' && item.handle" class="text-xs md:text-sm font-semibold tracking-wide whitespace-nowrap">
                {{ item.handle }}
            </span>
        </a>
    </div>
</template>

<style scoped>
.social-content-shadow {
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.6);
}

.social-hover-instagram:hover {
    background-image: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 8%, #fd5949 42%, #d6249f 62%, #285aeb 90%);
}

.social-hover-youtube:hover {
    background-image: linear-gradient(135deg, #7a0f14 0%, #b91c1c 45%, #dc2626 100%);
}
</style>
