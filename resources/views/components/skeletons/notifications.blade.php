{{-- Squelette réel de la page Notifications (/notifications) --}}
<div class="w-full space-y-3">
    {{-- 1. En-tête compact Notifications --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 sm:p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer"></div>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-28 ks-sk-block ks-sk-shimmer"></div>
                    <div class="h-4 w-16 ks-sk-block ks-sk-shimmer rounded-full"></div>
                </div>
                <div class="h-3 w-36 ks-sk-block ks-sk-shimmer"></div>
            </div>
        </div>
        <div class="h-7 w-32 ks-sk-block ks-sk-shimmer rounded-md"></div>
    </div>

    {{-- 2. Liste des notifications --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] divide-y divide-[#dcdad2] dark:divide-[#222630] overflow-hidden">
        @for($i = 0; $i < 5; $i++)
        <div class="p-3 sm:p-4 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg ks-sk-block ks-sk-shimmer flex-shrink-0 mt-0.5"></div>
            <div class="flex-1 min-w-0 space-y-1.5">
                <div class="flex items-center justify-between">
                    <div class="h-4 w-52 ks-sk-block ks-sk-shimmer"></div>
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-16 ks-sk-block ks-sk-shimmer"></div>
                        <div class="w-2 h-2 rounded-full ks-sk-block ks-sk-shimmer"></div>
                    </div>
                </div>
                <div class="h-3 w-full ks-sk-block ks-sk-shimmer"></div>
            </div>
            <div class="w-4 h-4 ks-sk-block ks-sk-shimmer rounded self-center flex-shrink-0"></div>
        </div>
        @endfor
    </div>
</div>
