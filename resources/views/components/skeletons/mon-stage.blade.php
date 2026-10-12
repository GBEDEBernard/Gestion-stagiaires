{{-- Squelette réel de la page Mon Stage (/mon-stage) --}}
<div class="w-full space-y-4">
    {{-- 1. Boutons d'action supérieurs --}}
    <div class="flex justify-end gap-2">
        <div class="h-8 w-40 ks-sk-block ks-sk-shimmer rounded-md"></div>
        <div class="h-8 w-32 ks-sk-block ks-sk-shimmer rounded-md"></div>
    </div>

    {{-- 2. Strip de 4 cartes KPI Mon-Stage --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex flex-col justify-between h-[120px]">
            <div class="flex items-center justify-between">
                <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-8 w-8 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
            <div class="my-1 space-y-1">
                <div class="h-6 w-32 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-40 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="flex items-center justify-between pt-1">
                <div class="h-4 w-20 ks-sk-block ks-sk-shimmer rounded"></div>
                <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded"></div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 3. Grille centrale (Mes tâches + Cadre du stage) --}}
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-3 items-start">
        {{-- Section Tâches (col-span-3) --}}
        <div class="xl:col-span-3 rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
            <div class="p-3.5 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between">
                <div class="h-4 w-32 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="p-3 sm:p-4 space-y-3">
                @for($j = 0; $j < 4; $j++)
                <div class="p-3 rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] flex items-center justify-between gap-3">
                    <div class="space-y-1.5 flex-1 min-w-0">
                        <div class="h-4 w-48 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-3 w-3/4 ks-sk-block ks-sk-shimmer"></div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="h-5 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                        <div class="h-6 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        {{-- Section Cadre du stage (col-span-1) --}}
        <div class="xl:col-span-1 rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 space-y-3.5">
            <div class="h-4 w-28 ks-sk-block ks-sk-shimmer"></div>
            <div class="space-y-2 pt-1">
                <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-full ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="space-y-2 pt-1 border-t border-[#dcdad2] dark:border-[#222630]">
                <div class="h-3 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-3/4 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="space-y-2 pt-1 border-t border-[#dcdad2] dark:border-[#222630]">
                <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-1/2 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
    </div>
</div>
