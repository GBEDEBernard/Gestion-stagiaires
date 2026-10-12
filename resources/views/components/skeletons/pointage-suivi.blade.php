{{-- Squelette réel de la page Suivi des Pointages (/admin/presence/pointage-suivi) compact style Mon Stage --}}
<div class="w-full space-y-3 sm:space-y-3.5">
    {{-- 1. Bannière d'en-tête compacte --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
            <div class="space-y-1.5">
                <div class="h-5 w-56 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-80 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-7 w-24 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
        </div>
    </div>

    {{-- 2. Barre de filtres compacte --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
            <div class="space-y-1">
                <div class="h-2.5 w-14 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
            <div class="space-y-1">
                <div class="h-2.5 w-12 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
            <div class="space-y-1">
                <div class="h-2.5 w-16 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
            <div class="space-y-1">
                <div class="h-2.5 w-10 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
            <div class="space-y-1">
                <div class="h-2.5 w-12 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
            <div class="flex items-end gap-1.5 pt-1 sm:pt-0">
                <div class="h-8 flex-1 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-8 flex-1 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
        </div>
    </div>

    {{-- 3. Cartes Stats KPIs (3 colonnes) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
        @for($i = 0; $i < 3; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex items-center justify-between">
            <div class="space-y-1.5">
                <div class="h-2.5 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-6 w-14 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-2 w-32 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 4. Rapport détaillé par collaborateur --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="px-3.5 py-2.5 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between">
            <div class="h-4 w-48 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-4 w-24 ks-sk-block ks-sk-shimmer rounded-full"></div>
        </div>
        <div class="p-3 space-y-3">
            @for($b = 0; $b < 3; $b++)
            <div class="rounded-[8px] border border-[#ecebe3] dark:border-[#222630] overflow-hidden">
                {{-- En-tête collaborateur --}}
                <div class="p-2.5 bg-[#faf9f6] dark:bg-[#181c24] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-[#ecebe3] dark:border-[#222630]">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-4 w-32 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                        <div class="h-4 w-20 ks-sk-block ks-sk-shimmer rounded-full"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                        <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                        <div class="h-4 w-20 ks-sk-block ks-sk-shimmer rounded-full"></div>
                    </div>
                </div>
                {{-- Lignes journées --}}
                <div class="p-2.5 space-y-2">
                    @for($d = 0; $d < 2; $d++)
                    <div class="flex items-center justify-between py-1 border-b border-[#f1f0ed] dark:border-[#1d212a] last:border-0">
                        <div class="h-3 w-28 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                    </div>
                    @endfor
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
