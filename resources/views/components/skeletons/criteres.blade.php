{{-- Squelette réel des critères d'évaluation (/admin/evaluations/criteres) --}}
<div class="w-full space-y-3.5">

    {{-- 1. Bannière d'en-tête --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg ks-sk-block ks-sk-shimmer shrink-0"></div>
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-40 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-4 w-20 ks-sk-block ks-sk-shimmer rounded-full"></div>
                </div>
                <div class="h-3 w-56 sm:w-80 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-8 w-32 ks-sk-block ks-sk-shimmer rounded-lg"></div>
        </div>
    </div>

    {{-- 2. Cartes de grilles par type --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 space-y-2 shadow-xs">
            <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-6 w-12 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-2.5 w-32 ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 3. Liste des critères --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
        <div class="p-4 border-b border-[#dcdad2] dark:border-[#222630] flex justify-between items-center bg-[#f6f5f0] dark:bg-[#131519]">
            <div class="h-4 w-32 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-40 ks-sk-block ks-sk-shimmer hidden sm:block"></div>
        </div>
        <div class="p-4 space-y-2.5">
            @for($j = 0; $j < 4; $j++)
            <div class="p-3 rounded-lg border border-[#dcdad2]/60 dark:border-[#222630] flex items-center justify-between">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="h-3.5 w-36 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-3 w-12 ks-sk-block ks-sk-shimmer rounded-full"></div>
                    </div>
                    <div class="h-2.5 w-48 ks-sk-block ks-sk-shimmer"></div>
                </div>
                <div class="flex gap-1.5">
                    <div class="h-6 w-14 ks-sk-block ks-sk-shimmer rounded-md"></div>
                    <div class="h-6 w-14 ks-sk-block ks-sk-shimmer rounded-md"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

</div>
