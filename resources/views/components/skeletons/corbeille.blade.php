{{-- SQUELETTE DÉDIÉ : CORBEILLE --}}
<div class="w-full space-y-3.5 pb-8 animate-pulse text-slate-900 dark:text-[#f8fafc]">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="h-3 w-28 bg-rose-200 dark:bg-rose-900/40 rounded mb-2"></div>
            <div class="h-7 w-52 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-3.5 w-80 bg-slate-100 dark:bg-[#1a1d26] rounded mt-1.5"></div>
        </div>
        <div class="h-8.5 w-36 bg-slate-200 dark:bg-[#222630] rounded-lg"></div>
    </div>

    {{-- Stats Cards (5 cartes) --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @for($i = 0; $i < 5; $i++)
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-3.5 space-y-2 shadow-xs">
            <div class="h-3 w-16 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-7 w-12 bg-slate-200 dark:bg-[#222630] rounded"></div>
        </div>
        @endfor
    </div>

    {{-- Accordéons --}}
    <div class="space-y-3">
        @for($i = 0; $i < 3; $i++)
        <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
            <div class="p-3.5 flex items-center justify-between border-b border-slate-100 dark:border-[#222630]">
                <div class="flex items-center gap-3">
                    <div class="h-5 w-5 bg-slate-200 dark:bg-[#222630] rounded"></div>
                    <div class="h-4 w-40 bg-slate-200 dark:bg-[#222630] rounded"></div>
                    <div class="h-5 w-8 bg-slate-200 dark:bg-[#222630] rounded-full"></div>
                </div>
                <div class="h-4 w-4 bg-slate-200 dark:bg-[#222630] rounded"></div>
            </div>
            <div class="p-3.5 space-y-2.5">
                @for($j = 0; $j < 2; $j++)
                <div class="flex items-center justify-between gap-3 py-1.5 border-b border-slate-50 dark:border-[#1a1d26] last:border-0">
                    <div class="h-4 w-48 bg-slate-200 dark:bg-[#222630] rounded"></div>
                    <div class="h-4 w-32 bg-slate-100 dark:bg-[#1a1d26] rounded"></div>
                    <div class="flex gap-2">
                        <div class="h-6 w-16 bg-emerald-200 dark:bg-emerald-900/30 rounded"></div>
                        <div class="h-6 w-20 bg-rose-200 dark:bg-rose-900/30 rounded"></div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
        @endfor
    </div>
</div>
