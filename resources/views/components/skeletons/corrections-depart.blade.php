{{-- Squelette réel de la page Corrections de départ (/admin/presence/corrections-depart) compact style Mon Stage --}}
<div class="w-full space-y-3 sm:space-y-3.5">
    {{-- 1. Bannière d'en-tête compacte --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
            <div class="space-y-1.5">
                <div class="h-5 w-60 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-80 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
        </div>
    </div>

    {{-- 2. Cartes Stats KPIs (3 colonnes) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
        @for($i = 0; $i < 3; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex items-center justify-between">
            <div class="space-y-1.5">
                <div class="h-2.5 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-6 w-12 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-2 w-32 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 3. Panneau principal des régularisations --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="px-3.5 py-2.5 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between">
            <div class="h-4 w-48 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-4 w-28 ks-sk-block ks-sk-shimmer rounded-full"></div>
        </div>
        <div class="p-3 space-y-2.5">
            @for($r = 0; $r < 4; $r++)
            <div class="rounded-[8px] border border-[#ecebe3] dark:border-[#222630] p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg ks-sk-block ks-sk-shimmer"></div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <div class="h-3.5 w-32 ks-sk-block ks-sk-shimmer"></div>
                            <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                        </div>
                        <div class="h-2.5 w-24 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="space-y-1">
                        <div class="h-2 w-16 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-4 w-12 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                    <div class="space-y-1">
                        <div class="h-2 w-14 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-4 w-12 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                    <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
