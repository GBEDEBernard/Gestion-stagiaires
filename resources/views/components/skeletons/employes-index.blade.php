{{-- Squelette réel de la liste des employés (/admin/employes) --}}
<div class="w-full space-y-3">

    {{-- 1. Bannière d'en-tête --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg ks-sk-block ks-sk-shimmer shrink-0"></div>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-44 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                </div>
                <div class="h-3 w-64 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-8 w-24 ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-8 w-32 ks-sk-block ks-sk-shimmer rounded-lg"></div>
        </div>
    </div>

    {{-- 2. 4 KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3 sm:p-4 flex items-center justify-between">
            <div class="space-y-1.5">
                <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-6 w-12 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 3. Barre de filtres --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3 space-y-2.5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
        </div>
        <div class="flex justify-between items-center pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630]">
            <div class="h-3 w-28 ks-sk-block ks-sk-shimmer"></div>
            <div class="flex gap-2">
                <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-lg"></div>
                <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-lg"></div>
            </div>
        </div>
    </div>

    {{-- 4. Grand Tableau --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="h-9 bg-[#f6f5f0] dark:bg-[#131519] border-b border-[#dcdad2] dark:border-[#222630] px-4 flex items-center justify-between">
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
            <div class="p-3.5 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full ks-sk-block ks-sk-shimmer shrink-0"></div>
                    <div class="space-y-1">
                        <div class="h-3.5 w-32 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-2.5 w-24 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                </div>
                <div class="h-3 w-28 ks-sk-block ks-sk-shimmer hidden md:block"></div>
                <div class="h-3 w-20 ks-sk-block ks-sk-shimmer hidden md:block"></div>
                <div class="h-4 w-14 ks-sk-block ks-sk-shimmer rounded-full hidden md:block"></div>
                <div class="flex gap-1.5">
                    <div class="w-6 h-6 rounded-md ks-sk-block ks-sk-shimmer"></div>
                    <div class="w-6 h-6 rounded-md ks-sk-block ks-sk-shimmer"></div>
                    <div class="w-6 h-6 rounded-md ks-sk-block ks-sk-shimmer"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

</div>
