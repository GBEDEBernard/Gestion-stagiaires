@props(['declarationUrl' => null])

<div class="mb-5 rounded-xl border border-red-200 dark:border-red-700/40 bg-red-50 dark:bg-red-900/15 p-4">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-500 text-white flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-sans text-sm font-bold text-red-800 dark:text-red-200">Rapport de travail du jour manquant</p>
            <p class="font-sans text-sm leading-relaxed text-red-700/90 dark:text-red-300/90">
                Vous devez déposer votre rapport de travail du jour avant de pouvoir pointer votre départ.
                Sans ce rapport, le pointage de départ reste bloqué.
            </p>
        </div>
        <a href="{{ $declarationUrl ?? route('tasks.index') }}"
           class="flex-shrink-0 w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold whitespace-nowrap transition">
            + Déposer mon rapport
        </a>
    </div>
</div>