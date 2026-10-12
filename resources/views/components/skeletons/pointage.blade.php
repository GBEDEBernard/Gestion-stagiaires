{{-- Squelette réel de la page de Pointage (/presence/pointage) --}}
<div class="w-full max-w-4xl mx-auto px-3 sm:px-6 pt-3 sm:pt-4 pb-6 space-y-4">
    {{-- 1. Mini-bannière supérieure d'état --}}
    <div class="h-9 w-full rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-2 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3.5 w-48 ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="h-4 w-12 ks-sk-block ks-sk-shimmer rounded"></div>
    </div>

    {{-- 2. Grande carte de pointage centrale --}}
    <div class="max-w-xl mx-auto rounded-[12px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-sm p-5 sm:p-7 space-y-6">
        {{-- En-tête stagiaire & site --}}
        <div class="flex items-center justify-between pb-4 border-b border-[#dcdad2] dark:border-[#222630]">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full ks-sk-block ks-sk-shimmer flex-shrink-0"></div>
                <div class="space-y-1.5">
                    <div class="h-4 w-32 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-3 w-40 ks-sk-block ks-sk-shimmer"></div>
                </div>
            </div>
            <div class="h-6 w-20 ks-sk-block ks-sk-shimmer rounded-full"></div>
        </div>

        {{-- Horloge numérique centrale --}}
        <div class="py-4 text-center space-y-2">
            <div class="h-12 w-44 mx-auto ks-sk-block ks-sk-shimmer rounded-lg"></div>
            <div class="h-3.5 w-56 mx-auto ks-sk-block ks-sk-shimmer"></div>
        </div>

        {{-- Horaires prévus (Arrivée & Départ) --}}
        <div class="grid grid-cols-2 gap-3 p-3 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
            <div class="text-center space-y-1">
                <div class="h-3 w-20 mx-auto ks-sk-block ks-sk-shimmer"></div>
                <div class="h-5 w-16 mx-auto ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="text-center space-y-1 border-l border-[#dcdad2] dark:border-[#222630]">
                <div class="h-3 w-20 mx-auto ks-sk-block ks-sk-shimmer"></div>
                <div class="h-5 w-16 mx-auto ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>

        {{-- Gros bouton de pointage --}}
        <div class="pt-2">
            <div class="h-12 w-full rounded-md ks-sk-block ks-sk-shimmer shadow-xs"></div>
        </div>

        {{-- Métadonnées géolocalisation --}}
        <div class="flex items-center justify-center gap-2 pt-1">
            <div class="w-3.5 h-3.5 rounded-full ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-48 ks-sk-block ks-sk-shimmer"></div>
        </div>
    </div>

    {{-- 3. Bouton Historique --}}
    <div class="text-center pt-2">
        <div class="h-9 w-36 mx-auto rounded-[10px] ks-sk-block ks-sk-shimmer"></div>
    </div>
</div>
