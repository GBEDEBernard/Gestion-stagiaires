{{-- Squelette réel de la page Suivi des Pointages (/attendance-tracking) compact style Mon Stage --}}
<div class="w-full space-y-3 sm:space-y-3.5">
    {{-- 1. Bannière d'en-tête compacte --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3 mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
            <div class="space-y-1.5">
                <div class="h-5 w-48 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-72 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="h-7 w-32 ks-sk-block ks-sk-shimmer rounded-lg"></div>
        </div>
    </div>

    {{-- 2. Barre de filtres et périodes --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-1.5">
                <div class="h-8 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-8 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-8 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-8 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-8 w-48 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-8 w-32 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
        </div>
    </div>

    {{-- 3. 2 Cartes Résumé (Stagiaires & Employés) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @for($k = 0; $k < 2; $k++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3.5 space-y-3">
            <div class="flex items-center justify-between">
                <div class="h-4 w-28 ks-sk-block ks-sk-shimmer"></div>
                <div class="w-7 h-7 rounded-md ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <div class="p-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#161a22] space-y-1.5">
                    <div class="h-3 w-12 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-6 w-10 ks-sk-block ks-sk-shimmer"></div>
                </div>
                <div class="p-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#161a22] space-y-1.5">
                    <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-6 w-10 ks-sk-block ks-sk-shimmer"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 4. Tableau Pointages --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="px-3.5 py-2.5 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between">
            <div class="h-4 w-48 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-5 w-20 ks-sk-block ks-sk-shimmer rounded-full"></div>
        </div>
        <div class="p-3 space-y-2">
            @for($r = 0; $r < 5; $r++)
            <div class="flex items-center justify-between py-2 border-b border-[#ecebe3] dark:border-[#222630] last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full ks-sk-block ks-sk-shimmer"></div>
                    <div class="space-y-1">
                        <div class="h-3.5 w-32 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-2.5 w-20 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                    <div class="h-4 w-12 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-6 w-16 ks-sk-block ks-sk-shimmer rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
