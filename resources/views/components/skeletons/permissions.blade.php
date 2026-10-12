{{-- Squelette réel de la page Permissions (/permissions) --}}
<div class="w-full space-y-3.5">
    {{-- 1. Strip de 4 cartes KPI compactes --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex items-center justify-between">
            <div class="space-y-1">
                <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-6 w-12 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 2. Barre d'outils & filtres --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-1.5 w-full sm:w-auto">
            @for($j = 0; $j < 4; $j++)
            <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
            @endfor
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <div class="h-8 w-44 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-8 w-36 ks-sk-block ks-sk-shimmer rounded-md"></div>
        </div>
    </div>

    {{-- 3. Liste des demandes de permission --}}
    <div class="space-y-2.5">
        @for($k = 0; $k < 4; $k++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="space-y-1.5 flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-28 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-4 w-20 ks-sk-block ks-sk-shimmer rounded-full"></div>
                </div>
                <div class="h-3.5 w-3/4 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-32 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 self-end sm:self-center">
                <div class="h-7 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
        </div>
        @endfor
    </div>
</div>
