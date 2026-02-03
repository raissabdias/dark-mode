<script setup>
const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="w-full bg-black border-t border-white/10 mt-auto">
        <div class="max-w-[1400px] mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-center md:text-left">
                    <p class="text-gray-500 text-sm font-['Lato']">
                        &copy; {{ currentYear }} <span class="text-gray-300">Dark Mode Digital Magazine</span>. Todos os direitos reservados.
                    </p>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-2 md:gap-6">
                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <span>Developed by</span>
                        <a href="https://www.linkedin.com/in/raissabdias/" target="_blank"
                            class="group flex items-center gap-2 text-purple-500 font-bold hover:text-purple-400 transition-colors bg-white/5 px-3 py-1 rounded-full border border-purple-500/20 hover:border-purple-500/50">
                            Raissa
                            <i class="pi pi-linkedin text-lg group-hover:scale-110 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>