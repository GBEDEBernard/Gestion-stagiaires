<div class="w-full space-y-3 pb-6">

    {{-- En-tête sans rupture (mobile/tablette/desktop) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-4">
        <div>
            <div class="h-6 w-44 ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="flex items-center flex-wrap sm:flex-nowrap gap-2">
            <div class="h-8 w-28 ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-8 w-36 ks-sk-block ks-sk-shimmer rounded-lg"></div>
        </div>
    </div>

    {{-- 4 KPIs Sparklines --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex flex-col justify-between h-[104px]">
            <div class="flex justify-between items-center">
                <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-12 ks-sk-block ks-sk-shimmer rounded"></div>
            </div>
            <div class="h-6 w-24 ks-sk-block ks-sk-shimmer my-1"></div>
            <div class="flex justify-between items-center pt-1 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                <div class="h-2.5 w-14 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-10 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 2 Colonnes : Tâches (Col-8) & Cadre (Col-4) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-start">
        <div class="lg:col-span-8 rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
            <div class="p-3 sm:px-4 flex justify-between items-center border-b border-[#dcdad2] dark:border-[#222630]">
                <div class="h-4 w-32 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="p-3 sm:p-4 space-y-3">
                @for($j = 0; $j < 3; $j++)
                <div class="p-4 rounded-md border border-[#dcdad2] dark:border-gray-700 bg-white dark:bg-gray-800 space-y-2.5">
                    <div class="flex justify-between gap-3">
                        <div class="space-y-1.5 flex-1">
                            <div class="h-4 w-56 ks-sk-block ks-sk-shimmer"></div>
                            <div class="flex gap-2">
                                <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                                <div class="h-4 w-14 ks-sk-block ks-sk-shimmer rounded-full"></div>
                            </div>
                            <div class="h-3 w-72 ks-sk-block ks-sk-shimmer"></div>
                        </div>
                        <div class="space-y-1 w-28">
                            <div class="h-3 w-full ks-sk-block ks-sk-shimmer"></div>
                            <div class="h-3 w-full ks-sk-block ks-sk-shimmer"></div>
                        </div>
                    </div>
                    <div class="h-1.5 w-40 ks-sk-block ks-sk-shimmer rounded-full"></div>
                </div>
                @endfor
            </div>
        </div>

        <div class="lg:col-span-4 space-y-3">
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 space-y-2.5">
                <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer pb-1 border-b border-[#dcdad2] dark:border-[#222630]"></div>
                <div class="h-3 w-full ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-full ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-full ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 space-y-2">
                <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-7 w-full ks-sk-block ks-sk-shimmer rounded"></div>
                <div class="h-7 w-full ks-sk-block ks-sk-shimmer rounded"></div>
            </div>
        </div>
    </div>

</div>
