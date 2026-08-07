@php
    // Partiel "état vide" pour la table des jours fériés (utilisé par index.blade.php)
@endphp
<div class="flex flex-col items-center justify-center py-12">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 mb-4">
        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
    <p class="text-gray-700 dark:text-gray-200 font-semibold">Aucun jour férié enregistré</p>
    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Ajoutez un jour férié pour commencer.</p>
</div>

