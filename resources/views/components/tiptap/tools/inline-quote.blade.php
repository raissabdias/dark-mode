<x-filament-tiptap-editor::button
    label="Citação Inline"
    active="inlineQuote"
    action="editor().chain().focus().toggleInlineQuote().run()"
>
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10 11H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2" />
        <path d="M20 11h-4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2" />
        <path d="M8 11v3a4 4 0 0 1-4 4" />
        <path d="M18 11v3a4 4 0 0 1-4 4" />
    </svg>

    <span class="sr-only">Citação Inline</span>
</x-filament-tiptap-editor::button>
