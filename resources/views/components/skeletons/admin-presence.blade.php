{{-- Squelette réel de la page Supervision Présence (/admin/presence) compact style Mon Stage --}}
<div class="w-full space-y-3 sm:space-y-3.5">
    {{-- 1. Bannière d'en-tête compacte --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
            <div class="space-y-1.5">
                <div class="h-5 w-60 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-80 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
        </div>
    </div>

    {{-- 2. Onglets Période & Filtre Date --}}
    <div class="space-y-2.5">
        <div class="flex items-center gap-1 p-0.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] w-fit">
            <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-7 w-20 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-7 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-7 w-16 ks-sk-block ks-sk-shimmer rounded-md"></div>
        </div>

        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 items-end">
                <div class="space-y-1">
                    <div class="h-2.5 w-10 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-md"></div>
                </div>
                <div class="space-y-1">
                    <div class="h-2.5 w-10 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-md"></div>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="h-8 flex-1 ks-sk-block ks-sk-shimmer rounded-md"></div>
                    <div class="h-8 flex-1 ks-sk-block ks-sk-shimmer rounded-md"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. 4 Cartes KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex items-center justify-between">
            <div class="space-y-1.5">
                <div class="h-2.5 w-20 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-6 w-14 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-2 w-24 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 4. 3 Actions Rapides --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
        @for($i = 0; $i < 3; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
            <div class="space-y-1 flex-1">
                <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-2.5 w-16 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 5. Grand bloc Graphique --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3.5 space-y-3">
        <div class="flex items-center justify-between">
            <div class="h-4 w-52 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-40 ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="h-56 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
    </div>

    {{-- 6. Groupes Stagiaires / Employés --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
        @for($i = 0; $i < 2; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 space-y-2">
            <div class="flex items-center justify-between">
                <div class="h-4 w-28 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-16 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="h-2 w-full ks-sk-block ks-sk-shimmer rounded-full"></div>
        </div>
        @endfor
    </div>
</div>
