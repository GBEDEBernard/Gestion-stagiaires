{{-- Squelette réel de la liste des employés (/admin/employes) --}}
<div class="w-full max-w-6xl mx-auto px-3 sm:px-6 pt-3 sm:pt-4 pb-8 space-y-4">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-[#dcdad2] dark:border-[#222630]">
        <div class="space-y-1">
            <div class="h-3 w-44 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-6 w-56 ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="flex gap-2">
            <div class="h-8 w-24 ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-8 w-32 ks-sk-block ks-sk-shimmer rounded-lg"></div>
        </div>
    </div>

    {{-- 4 KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex flex-col justify-between h-[96px]">
            <div class="flex justify-between items-center">
                <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="w-6 h-6 rounded ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="h-6 w-16 ks-sk-block ks-sk-shimmer my-1"></div>
            <div class="flex justify-between items-center pt-1 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                <div class="h-2.5 w-16 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-10 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        @endfor
    </div>

    {{-- Barre de filtres --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 space-y-2.5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
        </div>
        <div class="flex justify-between items-center pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630]">
            <div class="h-3 w-24 ks-sk-block ks-sk-shimmer"></div>
            <div class="flex gap-1.5">
                <div class="h-6 w-16 ks-sk-block ks-sk-shimmer rounded"></div>
                <div class="h-6 w-16 ks-sk-block ks-sk-shimmer rounded"></div>
            </div>
        </div>
    </div>

    {{-- Grand Tableau --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="h-9 bg-[#faf9f6] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630] px-3.5 flex items-center justify-between">
            <div class="h-3 w-8 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-28 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-24 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-24 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-14 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
            @for($j = 0; $j < 5; $j++)
            <div class="p-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full ks-sk-block ks-sk-shimmer shrink-0"></div>
                    <div class="space-y-1">
                        <div class="h-3 w-32 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-2 w-20 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                </div>
                <div class="h-3 w-28 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-14 ks-sk-block ks-sk-shimmer rounded-full"></div>
                <div class="flex gap-1.5">
                    <div class="w-5 h-5 rounded ks-sk-block ks-sk-shimmer"></div>
                    <div class="w-5 h-5 rounded ks-sk-block ks-sk-shimmer"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

</div>
