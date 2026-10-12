{{-- Squelette réel du Dashboard (/dashboard) --}}
<div class="w-full space-y-4">
    {{-- 1. Grande bannière accueil --}}
    <div class="rounded-2xl bg-[#e4e1d9] dark:bg-[#181c24] p-6 space-y-2 ks-sk-shimmer">
        <div class="h-6 w-48 ks-sk-block"></div>
        <div class="h-4 w-72 ks-sk-block"></div>
    </div>

    {{-- 2. 4 KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-2xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 space-y-3">
            <div class="flex items-center justify-between">
                <div class="w-9 h-9 rounded-xl ks-sk-block ks-sk-shimmer"></div>
                <div class="h-4 w-12 ks-sk-block ks-sk-shimmer rounded"></div>
            </div>
            <div class="h-7 w-20 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3 w-28 ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- 3. Blocs de contenu --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-2xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-5 space-y-3">
            <div class="h-4 w-36 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-32 w-full rounded-xl ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="rounded-2xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-5 space-y-3">
            <div class="h-4 w-40 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-32 w-full rounded-xl ks-sk-block ks-sk-shimmer"></div>
        </div>
    </div>
</div>
