{{-- SQUELETTE DÉDIÉ : LOGS --}}
<div class="w-full space-y-3.5 pb-8 animate-pulse text-slate-900 dark:text-[#f8fafc]">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="h-3 w-28 bg-slate-200 dark:bg-[#222630] rounded mb-2"></div>
            <div class="h-7 w-48 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-3.5 w-72 bg-slate-100 dark:bg-[#1a1d26] rounded mt-1.5"></div>
        </div>
        <div class="h-7 w-36 bg-emerald-100 dark:bg-emerald-900/30 rounded-full"></div>
    </div>

    {{-- Stats (4 cartes) --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-3.5 space-y-2 shadow-xs">
            <div class="flex justify-between items-center">
                <div class="h-3 w-16 bg-slate-200 dark:bg-[#222630] rounded"></div>
                <div class="h-4 w-4 bg-slate-200 dark:bg-[#222630] rounded"></div>
            </div>
            <div class="h-7 w-12 bg-slate-200 dark:bg-[#222630] rounded"></div>
        </div>
        @endfor
    </div>

    {{-- Toolbar --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-2.5 flex flex-wrap items-center gap-2 shadow-xs">
        <div class="h-8 w-44 bg-slate-200 dark:bg-[#222630] rounded-lg"></div>
        <div class="h-8 w-48 bg-slate-200 dark:bg-[#222630] rounded-lg"></div>
        <div class="h-8 flex-1 min-w-[140px] bg-slate-100 dark:bg-[#131519] rounded-lg"></div>
        <div class="h-8 w-8 bg-slate-200 dark:bg-[#222630] rounded-lg"></div>
        <div class="h-8 w-8 bg-rose-200 dark:bg-rose-900/40 rounded-lg"></div>
    </div>

    {{-- Timeline items --}}
    <div class="space-y-2.5">
        @for($i = 0; $i < 5; $i++)
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-3.5 shadow-xs flex items-start gap-3">
            <div class="h-8 w-8 rounded-lg bg-slate-200 dark:bg-[#222630] shrink-0 mt-0.5"></div>
            <div class="flex-1 space-y-2 min-w-0">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-14 bg-slate-200 dark:bg-[#222630] rounded"></div>
                    <div class="h-3 w-28 bg-slate-100 dark:bg-[#1a1d26] rounded"></div>
                </div>
                <div class="h-3.5 w-full max-w-lg bg-slate-100 dark:bg-[#1a1d26] rounded"></div>
            </div>
            <div class="h-6 w-16 bg-slate-200 dark:bg-[#222630] rounded-lg shrink-0"></div>
        </div>
        @endfor
    </div>
</div>
