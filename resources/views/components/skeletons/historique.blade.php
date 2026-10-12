{{-- Squelette réel de l'Historique de présence (/presence/historique) --}}
<div class="w-full space-y-4">
    {{-- 1. Barre de sélection de période & filtres --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
            <div class="space-y-1">
                <div class="h-4 w-36 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-48 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-8 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
            <div class="h-8 w-24 ks-sk-block ks-sk-shimmer rounded-md"></div>
        </div>
    </div>

    {{-- 2. Strip de 4 cartes KPI avec Sparklines --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex flex-col justify-between h-[115px]">
            <div class="flex items-center justify-between">
                <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-7 w-7 ks-sk-block ks-sk-shimmer rounded"></div>
            </div>
            <div class="h-6 w-20 ks-sk-block ks-sk-shimmer my-1"></div>
            <div class="flex items-center justify-between pt-1">
                <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded"></div>
                <div class="h-5 w-20 ks-sk-block ks-sk-shimmer rounded"></div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 3. Grand tableau des pointages --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        {{-- En-tête du tableau --}}
        <div class="grid grid-cols-6 gap-3 p-3 bg-[#f1f0ed]/50 dark:bg-[#15181f]/60 border-b border-[#dcdad2] dark:border-[#222630]">
            <div class="h-3.5 w-20 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3.5 w-16 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3.5 w-16 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3.5 w-14 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3.5 w-16 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3.5 w-16 ks-sk-block ks-sk-shimmer justify-self-end"></div>
        </div>

        {{-- Lignes du tableau --}}
        <div class="divide-y divide-[#dcdad2] dark:divide-[#222630]">
            @for($j = 0; $j < 6; $j++)
            <div class="grid grid-cols-6 gap-3 p-3 items-center">
                <div class="h-4 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-12 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-12 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-10 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-5 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                <div class="h-6 w-14 ks-sk-block ks-sk-shimmer rounded justify-self-end"></div>
            </div>
            @endfor
        </div>
    </div>
</div>
