{{-- SQUELETTE DÉDIÉ : UTILISATEURS --}}
<div class="w-full space-y-3.5 pb-8 animate-pulse text-slate-900 dark:text-[#f8fafc]">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="h-3 w-28 bg-slate-200 dark:bg-[#222630] rounded mb-2"></div>
            <div class="h-7 w-48 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-3.5 w-72 bg-slate-100 dark:bg-[#1a1d26] rounded mt-1.5"></div>
        </div>
        <div class="h-8.5 w-36 bg-blue-200 dark:bg-blue-900/40 rounded-lg"></div>
    </div>

    {{-- Filtres --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-4 space-y-3 shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @for($i = 0; $i < 4; $i++)
            <div class="space-y-1.5">
                <div class="h-3 w-16 bg-slate-200 dark:bg-[#222630] rounded"></div>
                <div class="h-9 w-full bg-slate-100 dark:bg-[#131519] border border-slate-200 dark:border-[#222630] rounded-lg"></div>
            </div>
            @endfor
        </div>
        <div class="h-3 w-32 bg-slate-200 dark:bg-[#222630] rounded"></div>
    </div>

    {{-- Tableau des utilisateurs --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-[#222630] bg-slate-50/70 dark:bg-[#131519] flex items-center justify-between">
            <div class="h-4 w-32 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-3 w-24 bg-slate-200 dark:bg-[#222630] rounded"></div>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-[#222630]">
            @for($i = 0; $i < 6; $i++)
            <div class="p-3.5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-full bg-slate-200 dark:bg-[#222630] shrink-0"></div>
                    <div class="space-y-1 min-w-0">
                        <div class="h-4 w-36 bg-slate-200 dark:bg-[#222630] rounded"></div>
                        <div class="h-3 w-44 bg-slate-100 dark:bg-[#1a1d26] rounded"></div>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <div class="h-5 w-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full"></div>
                </div>
                <div class="hidden md:block">
                    <div class="h-5 w-20 bg-indigo-100 dark:bg-indigo-900/30 rounded-full"></div>
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
