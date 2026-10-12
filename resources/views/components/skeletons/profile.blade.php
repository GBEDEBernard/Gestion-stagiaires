{{-- Squelette réel de la page Profil (/profile) --}}
<div class="w-full space-y-4">
    {{-- 1. Grande bannière Profil avec dégradé & avatar --}}
    <div class="rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-700 p-5 sm:p-6 text-white relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
            <div class="w-20 h-20 rounded-full ks-sk-block ks-sk-shimmer border-4 border-white/20 flex-shrink-0"></div>
            <div class="space-y-2 text-center sm:text-left flex-1">
                <div class="h-6 w-44 ks-sk-block ks-sk-shimmer bg-white/20"></div>
                <div class="h-3.5 w-60 ks-sk-block ks-sk-shimmer bg-white/20"></div>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                    <div class="h-5 w-24 rounded-full ks-sk-block ks-sk-shimmer bg-white/20"></div>
                    <div class="h-5 w-20 rounded-full ks-sk-block ks-sk-shimmer bg-white/20"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Grille des formulaires (Informations & Sécurité) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Formulaire Infos personnelles --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 space-y-3.5">
            <div class="h-4 w-40 ks-sk-block ks-sk-shimmer pb-1"></div>
            <div class="space-y-3">
                @for($i = 0; $i < 4; $i++)
                <div class="space-y-1">
                    <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-md"></div>
                </div>
                @endfor
            </div>
            <div class="pt-2 flex justify-end">
                <div class="h-8 w-24 ks-sk-block ks-sk-shimmer rounded-md"></div>
            </div>
        </div>

        {{-- Formulaire Sécurité & Appareils --}}
        <div class="space-y-4">
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 space-y-3.5">
                <div class="h-4 w-36 ks-sk-block ks-sk-shimmer pb-1"></div>
                <div class="space-y-3">
                    @for($j = 0; $j < 2; $j++)
                    <div class="space-y-1">
                        <div class="h-3 w-24 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-8 w-full ks-sk-block ks-sk-shimmer rounded-md"></div>
                    </div>
                    @endfor
                </div>
                <div class="pt-2 flex justify-end">
                    <div class="h-8 w-32 ks-sk-block ks-sk-shimmer rounded-md"></div>
                </div>
            </div>

            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 space-y-2">
                <div class="h-4 w-36 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-full ks-sk-block ks-sk-shimmer"></div>
                <div class="h-10 w-full rounded-md ks-sk-block ks-sk-shimmer mt-2"></div>
            </div>
        </div>
    </div>
</div>
