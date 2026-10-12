{{-- Squelette réel des Bilans périodiques / Résumés IA (/summaries) --}}
<div class="w-full space-y-3.5">
    {{-- 1. En-tête de la page --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 sm:p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
            <div class="space-y-1">
                <div class="h-4 w-36 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-52 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-8 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
        </div>
    </div>

    {{-- 2. KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @for($i = 0; $i < 3; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex items-center justify-between">
            <div class="space-y-1">
                <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-6 w-12 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 3. Grille des bilans --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        @for($j = 0; $j < 4; $j++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div class="h-4 w-28 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
            </div>
            <div class="space-y-1.5">
                <div class="h-3 w-full ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-4/5 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="space-y-1 pt-1">
                <div class="flex justify-between">
                    <div class="h-2.5 w-16 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-2.5 w-8 ks-sk-block ks-sk-shimmer"></div>
                </div>
                <div class="h-1.5 w-full rounded-full ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="pt-2 border-t border-[#dcdad2] dark:border-[#222630] flex justify-end">
                <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
        </div>
        @endfor
    </div>
</div>
