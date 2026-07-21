<x-layouts::auth.split :title="$title ?? null">
<style>
    /* ==================== KONTENER UTAMA ==================== */
    .auth-dark-mode {
        /* Background gelap */
        background: #1a1a2e !important;
        min-height: 100vh;
    }

    /* ==================== SEMUA TEXT MENJADI PUTIH ==================== */
    .auth-dark-mode,
    .auth-dark-mode *,
    .auth-dark-mode .text-gray-500,
    .auth-dark-mode .text-gray-600,
    .auth-dark-mode .text-gray-700,
    .auth-dark-mode .text-gray-800,
    .auth-dark-mode .text-gray-900,
    .auth-dark-mode .text-zinc-500,
    .auth-dark-mode .text-zinc-600,
    .auth-dark-mode .text-zinc-700,
    .auth-dark-mode .text-zinc-800,
    .auth-dark-mode .text-zinc-900,
    .auth-dark-mode .text-black,
    .auth-dark-mode .text-neutral-600,
    .auth-dark-mode .text-neutral-700,
    .auth-dark-mode .text-neutral-800,
    .auth-dark-mode .text-neutral-900,
    .auth-dark-mode label,
    .auth-dark-mode p,
    .auth-dark-mode span,
    .auth-dark-mode h1,
    .auth-dark-mode h2,
    .auth-dark-mode h3,
    .auth-dark-mode h4,
    .auth-dark-mode h5,
    .auth-dark-mode h6,
    .auth-dark-mode .font-bold,
    .auth-dark-mode .font-medium,
    .auth-dark-mode .font-normal,
    .auth-dark-mode .text-sm,
    .auth-dark-mode .text-base,
    .auth-dark-mode .text-lg,
    .auth-dark-mode .text-xl,
    .auth-dark-mode .text-2xl {
        color: #ffffff !important;
    }

    /* ==================== INPUT STYLING ==================== */
    .auth-dark-mode input[type="text"],
    .auth-dark-mode input[type="email"],
    .auth-dark-mode input[type="password"],
    .auth-dark-mode input[type="number"],
    .auth-dark-mode input[type="tel"],
    .auth-dark-mode input[type="search"],
    .auth-dark-mode input[type="url"],
    .auth-dark-mode input:not([type="checkbox"]):not([type="radio"]),
    .auth-dark-mode select,
    .auth-dark-mode textarea,
    .auth-dark-mode .flux-input,
    .auth-dark-mode .form-input {
        background: #374151 !important;
        /* Gray 700 */
        color: #ffffff !important;
        border: 1px solid #4b5563 !important;
        border-radius: 8px !important;
        padding: 10px 14px !important;
        transition: all 0.3s ease !important;
    }

    /* Input focus */
    .auth-dark-mode input[type="text"]:focus,
    .auth-dark-mode input[type="email"]:focus,
    .auth-dark-mode input[type="password"]:focus,
    .auth-dark-mode input[type="number"]:focus,
    .auth-dark-mode input[type="tel"]:focus,
    .auth-dark-mode input[type="search"]:focus,
    .auth-dark-mode input:not([type="checkbox"]):not([type="radio"]):focus,
    .auth-dark-mode select:focus,
    .auth-dark-mode textarea:focus,
    .auth-dark-mode .flux-input:focus {
        background: #4b5563 !important;
        border-color: #6366f1 !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3) !important;
    }

    /* Input placeholder */
    .auth-dark-mode input::placeholder,
    .auth-dark-mode textarea::placeholder {
        color: #9ca3af !important;
        /* Gray 400 */
        opacity: 1 !important;
    }

    /* Input disabled */
    .auth-dark-mode input:disabled,
    .auth-dark-mode select:disabled,
    .auth-dark-mode textarea:disabled {
        background: #1f2937 !important;
        color: #6b7280 !important;
        cursor: not-allowed !important;
    }

    /* ==================== LABEL ==================== */
    .auth-dark-mode label,
    .auth-dark-mode .flux-label,
    .auth-dark-mode [data-flux-label] {
        color: #ffffff !important;
        font-weight: 500 !important;
        margin-bottom: 6px !important;
        display: block !important;
    }

    /* ==================== CHECKBOX ==================== */
    .auth-dark-mode .checkbox-wrapper {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    /* Checkbox dengan text hitam */
    .auth-dark-mode input[type="checkbox"],
    .auth-dark-mode .flux-checkbox {
        accent-color: #000000 !important;
        width: 18px !important;
        height: 18px !important;
        cursor: pointer !important;
        background: #ffffff !important;
        border: 2px solid #000000 !important;
        border-radius: 4px !important;
        flex-shrink: 0 !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        position: relative !important;
    }

    /* Checkbox checked */
    .auth-dark-mode input[type="checkbox"]:checked,
    .auth-dark-mode .flux-checkbox:checked {
        background: #000000 !important;
        border-color: #000000 !important;
    }

    /* Tanda centang hitam */
    .auth-dark-mode input[type="checkbox"]:checked::after,
    .auth-dark-mode .flux-checkbox:checked::after {
        content: '✓' !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        color: #ffffff !important;
        font-size: 14px !important;
        font-weight: bold !important;
    }

    /* Label checkbox - TEXT HITAM */
    .auth-dark-mode .checkbox-label,
    .auth-dark-mode label[for*="remember"],
    .auth-dark-mode label[for*="terms"],
    .auth-dark-mode .checkbox-text {
        color: #000000 !important;
        /* TEXT HITAM */
        font-weight: 500 !important;
        cursor: pointer !important;
        background: #ffffff !important;
        padding: 4px 12px !important;
        border-radius: 4px !important;
        display: inline-block !important;
    }

    /* Hover checkbox */
    .auth-dark-mode input[type="checkbox"]:hover,
    .auth-dark-mode .flux-checkbox:hover {
        transform: scale(1.05) !important;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.2) !important;
    }

    /* Checkbox focus */
    .auth-dark-mode input[type="checkbox"]:focus,
    .auth-dark-mode .flux-checkbox:focus {
        outline: 2px solid #000000 !important;
        outline-offset: 2px !important;
    }

    /* ==================== FLUX CHECKBOX OVERRIDE ==================== */
    .auth-dark-mode .flux-checkbox-wrapper {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .auth-dark-mode .flux-checkbox-wrapper label {
        color: #000000 !important;
        /* TEXT HITAM */
        background: #ffffff !important;
        padding: 4px 12px !important;
        border-radius: 4px !important;
    }

    /* ==================== LINK ==================== */
    .auth-dark-mode a:not(.flux-button):not(.no-style) {
        color: #93c5fd !important;
        /* Light blue */
        text-decoration: none !important;
        transition: color 0.3s ease !important;
    }

    .auth-dark-mode a:not(.flux-button):not(.no-style):hover {
        color: #bfdbfe !important;
        text-decoration: underline !important;
    }

    /* ==================== BUTTON ==================== */
    .auth-dark-mode .flux-button,
    .auth-dark-mode button[type="submit"] {
        background: #6366f1 !important;
        color: #ffffff !important;
        border: none !important;
        padding: 10px 20px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
    }

    .auth-dark-mode .flux-button:hover,
    .auth-dark-mode button[type="submit"]:hover {
        background: #4f46e5 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4) !important;
    }

    /* ==================== CARD / CONTAINER ==================== */
    .auth-dark-mode .card,
    .auth-dark-mode .flux-card,
    .auth-dark-mode .bg-white,
    .auth-dark-mode .bg-gray-100,
    .auth-dark-mode .bg-gray-50 {
        background: #1f2937 !important;
        /* Dark background */
        border-color: #374151 !important;
    }

    .auth-dark-mode .shadow-md,
    .auth-dark-mode .shadow-lg,
    .auth-dark-mode .shadow-xl {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5) !important;
    }

    /* ==================== BORDER ==================== */
    .auth-dark-mode .border,
    .auth-dark-mode .border-t,
    .auth-dark-mode .border-b,
    .auth-dark-mode .border-l,
    .auth-dark-mode .border-r {
        border-color: #374151 !important;
    }

    /* ==================== DIVIDER ==================== */
    .auth-dark-mode .divide-y > * + * {
        border-color: #374151 !important;
    }

    /* ==================== ERROR MESSAGE ==================== */
    .auth-dark-mode .text-red-500,
    .auth-dark-mode .text-red-600,
    .auth-dark-mode .text-red-700 {
        color: #f87171 !important;
    }

    /* ==================== SUCCESS MESSAGE ==================== */
    .auth-dark-mode .text-green-500,
    .auth-dark-mode .text-green-600,
    .auth-dark-mode .text-green-700 {
        color: #34d399 !important;
    }

    /* ==================== SELECT / DROPDOWN ==================== */
    .auth-dark-mode select {
        background: #374151 !important;
        color: #ffffff !important;
        border: 1px solid #4b5563 !important;
    }

    .auth-dark-mode select option {
        background: #1f2937 !important;
        color: #ffffff !important;
    }

    /* ==================== SCROLLBAR ==================== */
    .auth-dark-mode::-webkit-scrollbar {
        width: 8px !important;
    }

    .auth-dark-mode::-webkit-scrollbar-track {
        background: #1f2937 !important;
    }

    .auth-dark-mode::-webkit-scrollbar-thumb {
        background: #4b5563 !important;
        border-radius: 4px !important;
    }

    .auth-dark-mode::-webkit-scrollbar-thumb:hover {
        background: #6b7280 !important;
    }

    /* ==================== RESPONSIVE FIX ==================== */
    @media (max-width: 640px) {
        .auth-dark-mode input[type="text"],
        .auth-dark-mode input[type="email"],
        .auth-dark-mode input[type="password"],
        .auth-dark-mode input:not([type="checkbox"]):not([type="radio"]),
        .auth-dark-mode select,
        .auth-dark-mode textarea {
            font-size: 16px !important;
            /* Prevent zoom on mobile */
        }
    }
</style>
    {{ $slot }}
</x-layouts::auth.split>
