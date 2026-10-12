@php
    // Partiel "état vide" pour la table des jours fériés - Style Mon Stage
@endphp
<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="w-12 h-12 rounded-xl bg-[#f1efe9] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] flex items-center justify-center text-[#64748b] dark:text-[#94a3b8] mb-3 shadow-sm">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
    <h3 class="text-sm font-semibold text-[#111827] dark:text-[#f8fafc]">Aucun jour férié enregistré</h3>
    <p class="text-xs text-[#64748b] dark:text-[#94a3b8] mt-1 max-w-sm">
        Ajoutez un jour férié pour suspendre automatiquement les pointages et planifier le calendrier.
    </p>
    @can('holidays.create')
    <a href="{{ route('admin.holidays.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#1c3e96] hover:bg-[#162f73] text-white shadow-sm transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Ajouter un jour férié
    </a>
    @endcan
</div>
