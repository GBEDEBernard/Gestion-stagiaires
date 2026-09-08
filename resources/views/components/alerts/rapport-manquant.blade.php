<div class="mb-5 flex items-start gap-3 px-4 py-3.5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50">
    <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
        <path stroke-linecap="round" d="M9 12h6m-6 4h6M8 7V5m8 2V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <div class="min-w-0">
        <p class="text-sm font-semibold text-red-800 dark:text-red-300">Rapport de travail du jour manquant</p>
        <p class="mt-0.5 text-sm text-red-700/85 dark:text-red-400/85">Vous devez déposer votre rapport de travail du jour avant de pouvoir pointer votre départ. <a href="{{ route('tasks.index') }}" class="underline font-medium">Déposer mon rapport</a>.</p>
    </div>
</div>
