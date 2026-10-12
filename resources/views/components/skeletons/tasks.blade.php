{{-- Squelette réel de l'Espace de travail / Tâches (/tasks) --}}
<div class="w-full space-y-3.5">
    {{-- 1. Barre d'outils et filtres par statut --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 space-y-2.5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="h-8 w-64 ks-sk-block ks-sk-shimmer rounded-md w-full sm:w-64"></div>
            <div class="flex items-center gap-2">
                <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded"></div>
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 pt-1 border-t border-[#dcdad2] dark:border-[#222630]">
            @for($k = 0; $k < 5; $k++)
            <div class="h-6 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
            @endfor
        </div>
    </div>

    {{-- 2. Disposition 2 colonnes (Liste des tâches & Détail de la tâche) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-start">
        {{-- Colonne gauche : Liste des tâches (5 cols) --}}
        <div class="lg:col-span-5 space-y-2.5">
            @for($m = 0; $m < 4; $m++)
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3.5 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                    <div class="h-3 w-12 ks-sk-block ks-sk-shimmer"></div>
                </div>
                <div class="h-4 w-4/5 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-full ks-sk-block ks-sk-shimmer"></div>
                <div class="flex items-center justify-between pt-1">
                    <div class="h-1.5 w-24 ks-sk-block ks-sk-shimmer rounded-full"></div>
                    <div class="h-3 w-8 ks-sk-block ks-sk-shimmer"></div>
                </div>
            </div>
            @endfor
        </div>

        {{-- Colonne droite : Détail de la tâche sélectionnée (7 cols) --}}
        <div class="lg:col-span-7 rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#dcdad2] dark:border-[#222630]">
                <div class="space-y-1.5 flex-1">
                    <div class="h-5 w-3/4 ks-sk-block ks-sk-shimmer"></div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded"></div>
                        <div class="h-4 w-20 ks-sk-block ks-sk-shimmer rounded"></div>
                    </div>
                </div>
                <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>

            <div class="space-y-2">
                <div class="h-3 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3.5 w-full ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3.5 w-5/6 ks-sk-block ks-sk-shimmer"></div>
            </div>

            <div class="space-y-2.5 pt-2 border-t border-[#dcdad2] dark:border-[#222630]">
                <div class="h-3.5 w-32 ks-sk-block ks-sk-shimmer"></div>
                @for($n = 0; $n < 3; $n++)
                <div class="p-2.5 rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-3.5 w-40 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                    <div class="h-4 w-12 ks-sk-block ks-sk-shimmer rounded"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>
