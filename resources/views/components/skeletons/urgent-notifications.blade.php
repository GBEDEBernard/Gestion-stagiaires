{{-- SQUELETTE DÉDIÉ : NOTIFICATIONS URGENTES --}}
<div class="w-full space-y-3.5 pb-8 animate-pulse text-slate-900 dark:text-[#f8fafc]">
    {{-- En-tête --}}
    <div class="rounded-xl border border-rose-200/60 dark:border-rose-900/40 bg-[#fffefa] dark:bg-[#161a22] p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
        <div>
            <div class="h-3 w-32 bg-rose-200 dark:bg-rose-900/40 rounded mb-2"></div>
            <div class="h-6 w-64 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-3.5 w-80 bg-slate-100 dark:bg-[#1a1d26] rounded mt-1.5"></div>
        </div>
        <div class="h-10 w-28 bg-slate-200 dark:bg-[#222630] rounded-xl"></div>
    </div>

    {{-- Formulaire card --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-4 space-y-4 shadow-xs">
        <div class="h-4 w-44 bg-slate-200 dark:bg-[#222630] rounded"></div>
        <div class="space-y-1.5">
            <div class="h-3 w-24 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-9 w-full bg-slate-100 dark:bg-[#131519] rounded-lg"></div>
        </div>
        <div class="space-y-1.5">
            <div class="h-3 w-28 bg-slate-200 dark:bg-[#222630] rounded"></div>
            <div class="h-20 w-full bg-slate-100 dark:bg-[#131519] rounded-lg"></div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2">
            @for($i = 0; $i < 4; $i++)
            <div class="h-12 bg-slate-100 dark:bg-[#131519] border border-slate-200 dark:border-[#222630] rounded-lg"></div>
            @endfor
        </div>
        <div class="flex justify-end pt-2">
            <div class="h-8.5 w-40 bg-rose-200 dark:bg-rose-900/40 rounded-lg"></div>
        </div>
    </div>

    {{-- Historique card --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] p-4 space-y-3 shadow-xs">
        <div class="h-4 w-52 bg-slate-200 dark:bg-[#222630] rounded"></div>
        @for($i = 0; $i < 2; $i++)
        <div class="p-3 rounded-lg border border-slate-100 dark:border-[#222630] bg-slate-50/50 dark:bg-[#131519] space-y-2">
            <div class="flex justify-between items-center">
                <div class="h-4 w-36 bg-slate-200 dark:bg-[#222630] rounded"></div>
                <div class="h-3 w-24 bg-slate-100 dark:bg-[#1a1d26] rounded"></div>
            </div>
            <div class="h-3 w-72 bg-slate-100 dark:bg-[#1a1d26] rounded"></div>
        </div>
        @endfor
    </div>
</div>
