{{-- Squelette réel de la gestion des badges (/admin/badges) --}}
<div class="w-full space-y-3.5">

    {{-- 1. Bannière d'en-tête --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg ks-sk-block ks-sk-shimmer shrink-0"></div>
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-28 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-4 w-24 ks-sk-block ks-sk-shimmer rounded-full"></div>
                </div>
                <div class="h-3 w-48 sm:w-64 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-8 w-32 ks-sk-block ks-sk-shimmer rounded-lg"></div>
        </div>
    </div>

    {{-- 2. Cartes KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-2.5">
        @for($i = 0; $i < 3; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
            <div class="space-y-1.5">
                <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-6 w-12 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="w-9 h-9 rounded-lg ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 3. Tableau Desktop / Cartes Mobile --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
        <div class="h-9 bg-[#f6f5f0] dark:bg-[#131519] border-b border-[#dcdad2] dark:border-[#222630] px-4 flex items-center justify-between">
            <div class="h-3 w-8 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-36 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-24 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]">
            @for($j = 0; $j < 5; $j++)
            <div class="p-3.5 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg ks-sk-block ks-sk-shimmer shrink-0"></div>
                    <div class="space-y-1">
                        <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-2.5 w-16 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                </div>
                <div class="h-3 w-28 ks-sk-block ks-sk-shimmer hidden sm:block"></div>
                <div class="flex gap-1.5">
                    <div class="w-7 h-7 rounded-lg ks-sk-block ks-sk-shimmer"></div>
                    <div class="w-7 h-7 rounded-lg ks-sk-block ks-sk-shimmer"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

</div>
