{{-- SQUELETTE DÉDIÉ : RÔLES --}}
<div class="w-full space-y-3.5 pb-8 animate-pulse text-slate-900 dark:text-[#f8fafc]">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="h-3 w-28 bg-slate-200 dark:bg-[#222630] rounded mb-2"></div>
            <div class="h-7 w-48 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-3.5 w-72 bg-slate-100 dark:bg-[#1a1d26] rounded mt-1.5"></div>
        </div>
        <div class="h-8.5 w-32 bg-indigo-200 dark:bg-indigo-900/40 rounded-lg"></div>
    </div>

    {{-- Stats (Total + rôles) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="col-span-2 sm:col-span-1 rounded-xl bg-indigo-200/50 dark:bg-indigo-950/40 p-3.5 space-y-2 border border-indigo-200 dark:border-indigo-900/50">
            <div class="h-3 w-20 bg-indigo-300 dark:bg-indigo-800 rounded"></div>
            <div class="h-7 w-12 bg-indigo-300 dark:bg-indigo-800 rounded"></div>
        </div>
        @for($i = 0; $i < 4; $i++)
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-3.5 space-y-2 shadow-xs">
            <div class="h-3 w-16 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-7 w-10 bg-slate-200 dark:bg-[#222630] rounded"></div>
        </div>
        @endfor
    </div>

    {{-- Tableau des rôles --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
        <div class="divide-y divide-slate-100 dark:divide-[#222630]">
            @for($i = 0; $i < 5; $i++)
            <div class="p-3.5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-slate-200 dark:bg-[#222630] shrink-0"></div>
                    <div class="space-y-1">
                        <div class="h-4 w-28 bg-slate-200 dark:bg-[#222630] rounded"></div>
                        <div class="h-3 w-36 bg-slate-100 dark:bg-[#1a1d26] rounded"></div>
                    </div>
                </div>
                <div class="hidden md:flex gap-1.5 flex-1 max-w-xs">
                    <div class="h-5 w-16 bg-slate-200 dark:bg-[#222630] rounded"></div>
                    <div class="h-5 w-20 bg-slate-200 dark:bg-[#222630] rounded"></div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <div class="h-7 w-7 bg-slate-200 dark:bg-[#222630] rounded-lg"></div>
                    <div class="h-7 w-7 bg-slate-200 dark:bg-[#222630] rounded-lg"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
