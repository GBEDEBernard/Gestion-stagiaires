{{-- Squelette réel de la page Rapports d'activité (/reports) --}}
<div class="w-full space-y-3">
    {{-- 1. Barre d'en-tête & filtres d'intervalle --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 sm:p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
            <div class="space-y-1">
                <div class="h-4 w-36 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-48 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-7 w-48 ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
        </div>
    </div>

    {{-- 2. Bannière du rapport du jour --}}
    <div class="rounded-[10px] bg-[#ecfdf5]/40 dark:bg-emerald-950/20 border border-[#dcdad2] dark:border-[#222630] border-l-4 border-l-[#15803d] p-3 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-6 h-6 rounded-md ks-sk-block ks-sk-shimmer"></div>
            <div class="space-y-1">
                <div class="h-3.5 w-44 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-72 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        <div class="flex gap-1.5">
            <div class="h-6 w-16 ks-sk-block ks-sk-shimmer rounded"></div>
            <div class="h-6 w-16 ks-sk-block ks-sk-shimmer rounded"></div>
        </div>
    </div>

    {{-- 3. Liste des cartes de rapports d'activité --}}
    <div class="space-y-2.5">
        @for($i = 0; $i < 3; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 space-y-2.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-32 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                    <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                </div>
                <div class="flex gap-1.5">
                    <div class="h-6 w-14 ks-sk-block ks-sk-shimmer rounded"></div>
                    <div class="h-6 w-14 ks-sk-block ks-sk-shimmer rounded"></div>
                </div>
            </div>

            <div class="space-y-1">
                <div class="h-3.5 w-full ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3.5 w-4/5 ks-sk-block ks-sk-shimmer"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 border-t border-[#dcdad2] dark:border-[#222630]">
                <div class="h-10 w-full rounded-md ks-sk-block ks-sk-shimmer"></div>
                <div class="h-10 w-full rounded-md ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        @endfor
    </div>
</div>
