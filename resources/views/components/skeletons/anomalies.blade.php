{{-- Squelette réel de la page Anomalies de Présence (/admin/presence/anomalies) compact style Mon Stage --}}
<div class="w-full space-y-3 sm:space-y-3.5">
    {{-- 1. Bannière d'en-tête compacte --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3 mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
            <div class="space-y-1.5">
                <div class="h-5 w-52 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-80 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="h-7 w-32 ks-sk-block ks-sk-shimmer rounded-md"></div>
        </div>
    </div>

    {{-- 2. Barre de filtres & synthèse --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-1.5">
                <div class="h-7 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-7 w-24 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-6 w-36 ks-sk-block ks-sk-shimmer rounded-full"></div>
            </div>
        </div>
    </div>

    {{-- 3. Tableau des anomalies --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="px-3.5 py-2.5 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between">
            <div class="h-4 w-52 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
        </div>
        <div class="p-3 space-y-2.5">
            @for($r = 0; $r < 4; $r++)
            <div class="flex items-center justify-between py-2.5 border-b border-[#ecebe3] dark:border-[#222630] last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
                    <div class="space-y-1.5">
                        <div class="h-3.5 w-32 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-2.5 w-20 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <div class="h-5 w-36 ks-sk-block ks-sk-shimmer rounded-full"></div>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    <div class="h-5 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-5 w-8 ks-sk-block ks-sk-shimmer rounded-full"></div>
                    <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
