<x-layouts::auth.split :title="$title ?? null">
<style>
    /* Force white text di semua mode */
    .auth-white-text,
    .auth-white-text *,
    .auth-white-text label,
    .auth-white-text p,
    .auth-white-text span,
    .auth-white-text a,
    .auth-white-text h1,
    .auth-white-text h2,
    .auth-white-text h3,
    .auth-white-text .text-gray-500,
    .auth-white-text .text-gray-600,
    .auth-white-text .text-gray-700,
    .auth-white-text .text-gray-800,
    .auth-white-text .text-gray-900,
    .auth-white-text .text-zinc-500,
    .auth-white-text .text-zinc-600,
    .auth-white-text .text-zinc-700,
    .auth-white-text .text-zinc-800,
    .auth-white-text .text-zinc-900,
    .dark .auth-white-text,
    .dark .auth-white-text *,
    .dark .auth-white-text label,
    .dark .auth-white-text p,
    .dark .auth-white-text span,
    .dark .auth-white-text a,
    .dark .auth-white-text h1,
    .dark .auth-white-text h2,
    .dark .auth-white-text h3 {
        color: #ffffff !important;
    }
    
    /* Label input tetap putih */
    .auth-white-text label[data-flux-label] {
        color: #ffffff00 !important;
    }
    
    /* Link tetap putih dengan hover */
    .auth-white-text a:not(.flux-button) {
        color: #ffffff !important;
    }
    
    .auth-white-text a:not(.flux-button):hover {
        color: #bfdbfe !important;
    }
</style>
    {{ $slot }}
</x-layouts::auth.split>