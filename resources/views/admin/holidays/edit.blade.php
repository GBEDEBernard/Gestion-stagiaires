<x-app-layout title="Modifier le jour férié">

<div class="w-full max-w-2xl mx-auto space-y-3 sm:space-y-4 pb-6 text-slate-900 dark:text-slate-100">

    {{-- ── 1. En-tête compact Mon Stage ────────────── --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3 transition-colors">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.holidays.index') }}"
               class="p-1.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#64748b] dark:text-[#94a3b8] hover:text-[#111827] dark:hover:text-[#f8fafc] transition-colors"
               title="Retour aux jours fériés">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-sm sm:text-base font-semibold text-[#111827] dark:text-[#f8fafc] truncate">Modifier le jour férié</h1>
                <p class="text-xs text-[#64748b] dark:text-[#94a3b8] truncate">{{ $holiday->label }} — {{ $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
            </div>
        </div>
    </div>

    {{-- ── 2. Formulaire Mon Stage ────────────────── --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 transition-colors shadow-sm">
        <form method="POST" action="{{ route('admin.holidays.update', $holiday) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="date" class="block text-xs font-semibold text-[#111827] dark:text-[#f8fafc] mb-1">
                    Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="date" name="date" value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required
                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#111827] dark:text-[#f8fafc] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                @error('date')
                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="label" class="block text-xs font-semibold text-[#111827] dark:text-[#f8fafc] mb-1">
                    Libellé <span class="text-red-500">*</span>
                </label>
                <input type="text" id="label" name="label" value="{{ old('label', $holiday->label) }}" required
                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#111827] dark:text-[#f8fafc] placeholder-[#94a3b8] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                @error('label')
                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold text-[#111827] dark:text-[#f8fafc] mb-1">
                    Description <span class="text-[#64748b] dark:text-[#94a3b8] font-normal">(optionnelle)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#111827] dark:text-[#f8fafc] placeholder-[#94a3b8] resize-none focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">{{ old('description', $holiday->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2 pt-2 border-t border-[#dcdad2]/60 dark:border-[#222630]/60">
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-medium bg-[#1c3e96] hover:bg-[#162f73] text-white shadow-sm transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Mettre à jour</span>
                </button>
                <a href="{{ route('admin.holidays.index') }}"
                    class="px-3.5 py-1.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] text-xs font-medium text-[#64748b] dark:text-[#94a3b8] hover:bg-[#dcdad2]/30 dark:hover:bg-[#222630] transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
