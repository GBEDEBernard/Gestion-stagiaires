{{-- Squelette réel du Dashboard (/dashboard) compact style Mon Stage --}}
<div class="w-full space-y-3 sm:space-y-3.5">
    {{-- 1. Bannière admin compacte --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3 mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
            <div class="space-y-1.5">
                <div class="h-5 w-44 ks-sk-block ks-sk-shimmer"></div>
                <div class="h-3 w-64 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-lg"></div>
                <div class="h-7 w-36 ks-sk-block ks-sk-shimmer rounded-lg"></div>
                <div class="h-7 w-28 ks-sk-block ks-sk-shimmer rounded-lg"></div>
            </div>
        </div>
    </div>

    {{-- 2. 8 KPI Cards (2 rangées de 4) --}}
    @for($row = 0; $row < 2; $row++)
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex flex-col justify-between h-[104px]">
            <div class="flex items-center justify-between">
                <div class="h-3 w-24 ks-sk-block ks-sk-shimmer"></div>
                <div class="w-[30px] h-[30px] rounded-[6px] ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="h-6 w-14 ks-sk-block ks-sk-shimmer my-1"></div>
            <div class="flex items-center justify-between">
                <div class="h-3.5 w-12 ks-sk-block ks-sk-shimmer rounded"></div>
                <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        @endfor
    </div>
    @endfor

    {{-- 3. Suivi des Pointages --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="px-3.5 py-2.5 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-36 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="h-6 w-24 rounded ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @for($p = 0; $p < 4; $p++)
                <div class="p-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#161a22] space-y-1.5">
                    <div class="h-3 w-20 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-5 w-10 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-2.5 w-16 ks-sk-block ks-sk-shimmer"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- 4. Demandes de permission (3 cartes) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
        @for($d = 0; $d < 3; $d++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 flex flex-col justify-between h-[96px]">
            <div class="flex items-center justify-between">
                <div class="h-3 w-32 ks-sk-block ks-sk-shimmer"></div>
                <div class="w-6 h-6 rounded ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="h-5 w-8 ks-sk-block ks-sk-shimmer"></div>
            <div class="flex items-center justify-between">
                <div class="h-3.5 w-16 ks-sk-block ks-sk-shimmer rounded"></div>
                <div class="h-3 w-10 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        @endfor
    </div>

    {{-- 5. Suivi des Tâches --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
        <div class="px-3.5 py-2.5 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-32 ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="h-6 w-28 rounded ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="p-3 space-y-3">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @for($t = 0; $t < 4; $t++)
                <div class="p-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#161a22] space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
                        <div class="h-3 w-12 ks-sk-block ks-sk-shimmer rounded"></div>
                    </div>
                    <div class="h-5 w-8 ks-sk-block ks-sk-shimmer"></div>
                </div>
                @endfor
            </div>
            <div class="h-[185px] w-full rounded-lg ks-sk-block ks-sk-shimmer"></div>
        </div>
    </div>

    {{-- 6. Répartition Utilisateurs & Statut des Comptes --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
        @for($c = 0; $c < 2; $c++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
            <div class="px-3.5 py-2.5 border-b border-[#dcdad2] dark:border-[#222630] flex items-center justify-between">
                <div class="h-4 w-36 ks-sk-block ks-sk-shimmer"></div>
                @if($c === 0)
                <div class="h-6 w-24 rounded ks-sk-block ks-sk-shimmer"></div>
                @endif
            </div>
            <div class="p-4 flex items-center justify-center">
                <div class="w-36 h-36 rounded-full border-8 border-[#e4e1d9] dark:border-[#1e222c] ks-sk-shimmer flex items-center justify-center">
                    <div class="h-5 w-12 ks-sk-block"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
