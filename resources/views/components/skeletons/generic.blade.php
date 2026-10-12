{{-- Squelette générique Mon-Stage --}}
<div class="w-full space-y-4">
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 flex items-center justify-between">
        <div class="space-y-1.5">
            <div class="h-5 w-40 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-3.5 w-64 ks-sk-block ks-sk-shimmer"></div>
        </div>
        <div class="h-8 w-28 ks-sk-block ks-sk-shimmer rounded-md"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @for($i = 0; $i < 3; $i++)
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-3 space-y-2">
            <div class="h-3.5 w-24 ks-sk-block ks-sk-shimmer"></div>
            <div class="h-6 w-16 ks-sk-block ks-sk-shimmer"></div>
        </div>
        @endfor
    </div>

    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-5 space-y-3">
        @for($j = 0; $j < 4; $j++)
        <div class="h-4 w-full ks-sk-block ks-sk-shimmer"></div>
        @endfor
    </div>
</div>
