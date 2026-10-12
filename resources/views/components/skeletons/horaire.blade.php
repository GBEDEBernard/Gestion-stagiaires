{{-- Squelette réel de la page Horaire de Référence (/admin/presence/horaire) compact style Mon Stage --}}
<div class="w-full max-w-4xl space-y-3 sm:space-y-3.5">
    {{-- 1. Bannière d'en-tête compacte --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer shrink-0"></div>
            <div class="space-y-1.5 flex-1">
                <div class="h-4 w-44 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-72 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
    </div>

    {{-- 2. Carte formulaire --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="px-4 py-3 bg-[#f8f7f4] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630]">
            <div class="h-4 w-40 ks-sk-block ks-sk-shimmer"></div>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @for($i = 0; $i < 4; $i++)
                <div class="space-y-1.5">
                    <div class="h-3 w-28 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-9 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>
                    <div class="h-2.5 w-48 ks-sk-block ks-sk-shimmer"></div>
                </div>
                @endfor
            </div>

            <div class="h-16 w-full ks-sk-block ks-sk-shimmer rounded-lg"></div>

            <div class="pt-3 border-t border-[#dcdad2]/60 dark:border-[#222630]/60 flex items-center justify-between">
                <div class="h-3 w-40 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-8 w-36 ks-sk-block ks-sk-shimmer rounded-lg"></div>
            </div>
        </div>
    </div>
</div>
