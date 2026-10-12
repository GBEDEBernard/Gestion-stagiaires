{{-- Squelette réel de la page Suivi des Tâches Admin (/admin/tasks-tracking) compact style Mon Stage --}}
<div class="w-full space-y-3 sm:space-y-3.5">
    {{-- 1. Bannière d'en-tête compacte --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer shrink-0"></div>
                <div class="space-y-1.5">
                    <div class="h-4 w-44 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-3 w-72 ks-sk-block ks-sk-shimmer"></div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-lg"></div>
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-lg"></div>
            </div>
        </div>
    </div>

    {{-- 2. 5 Cartes KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 sm:gap-3">
        @for($i = 0; $i < 5; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 space-y-1.5">
            <div class="h-2.5 w-16 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-5 w-12 ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 3. Barre de filtres --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-1 p-0.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] w-fit">
            <div class="h-7 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-7 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="h-7 w-32 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-7 w-36 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-7 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
        </div>
    </div>

    {{-- 4. Tableau principal --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="hidden sm:grid grid-cols-7 gap-4 px-4 py-3 bg-[#f8f7f4] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630]">
            <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-32 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-28 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-16 ks-sk-block ks-sk-shimmer justify-self-end"></div>
        </div>

        <div class="divide-y divide-[#dcdad2]/60 dark:divide-[#222630]/60 p-2 sm:p-0">
            @for($i = 0; $i < 6; $i++)
            <div class="hidden sm:grid grid-cols-7 gap-4 px-4 py-3 items-center">
                <div class="h-4 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-40 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-5 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                <div class="h-2 w-28 ks-sk-block ks-sk-shimmer rounded-full"></div>
                <div class="h-4 w-10 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="flex items-center justify-end gap-1.5 justify-self-end">
                    <div class="w-7 h-7 rounded-md ks-sk-block ks-sk-shimmer"></div>
                    <div class="w-7 h-7 rounded-md ks-sk-block ks-sk-shimmer"></div>
                </div>
            </div>

            {{-- Version mobile --}}
            <div class="sm:hidden p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="h-3.5 w-36 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                </div>
                <div class="h-3 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-2 w-full ks-sk-block ks-sk-shimmer rounded-full"></div>
                <div class="flex items-center justify-between pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630]/60">
                    <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-6 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
