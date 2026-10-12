<x-app-layout title="Horaire de référence">

<div class="w-full max-w-4xl space-y-3 sm:space-y-4 pb-6 text-slate-900 dark:text-slate-100">

    {{-- ── 1. En-tête compact Mon Stage ────────────── --}}
    <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#e4e1d9]/60 dark:bg-[#161a22] px-3.5 sm:px-4 py-2.5 sm:py-3 transition-colors">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-[#1c3e96]/15 dark:bg-[#1c3e96]/30 text-[#1c3e96] dark:text-[#60a5fa] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-sm sm:text-base font-semibold text-[#111827] dark:text-[#f8fafc] truncate">Horaire de référence</h1>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-[#1c3e96]/10 dark:bg-[#1c3e96]/20 text-[#1c3e96] dark:text-[#93c5fd]">
                        Entreprise
                    </span>
                </div>
                <p class="text-xs text-[#64748b] dark:text-[#94a3b8] truncate">
                    Règles d'arrivée, de départ et seuils de calcul automatique des retards
                </p>
            </div>
        </div>
    </div>

    {{-- ── Alertes de statut ───────────────────────── --}}
    @if(session('success'))
        <div class="px-3.5 py-2.5 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-200/60 dark:border-emerald-800/40 text-xs text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="px-3.5 py-2.5 rounded-lg bg-red-500/10 dark:bg-red-500/20 border border-red-200/60 dark:border-red-800/40 text-xs text-red-800 dark:text-red-300">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- ── 2. Formulaire Mon Stage ────────────────── --}}
    <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] overflow-hidden transition-colors shadow-sm">
        <div class="px-4 py-3 bg-[#f8f7f4] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630]">
            <h3 class="text-xs sm:text-sm font-semibold text-[#111827] dark:text-[#f8fafc]">
                Paramètres des horaires généraux
            </h3>
            <p class="text-[11px] text-[#64748b] dark:text-[#94a3b8] mt-0.5">
                S'applique par défaut aux employés et aux stagiaires sans horaire individualisé.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.presence.horaire.update') }}" class="p-4 sm:p-5 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Arrivée attendue --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-[#111827] dark:text-[#f8fafc]">
                        Arrivée attendue <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="start_time" required
                        value="{{ old('start_time', substr($setting->start_time, 0, 5)) }}"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#111827] dark:text-[#f8fafc] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                    <p class="text-[10px] text-[#64748b] dark:text-[#94a3b8]">Au-delà de cette heure, le pointage est comptabilisé en retard.</p>
                </div>

                {{-- Départ attendu --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-[#111827] dark:text-[#f8fafc]">
                        Départ attendu <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="end_time" required
                        value="{{ old('end_time', substr($setting->end_time, 0, 5)) }}"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#111827] dark:text-[#f8fafc] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                    <p class="text-[10px] text-[#64748b] dark:text-[#94a3b8]">Heure normale de fin de journée de travail.</p>
                </div>

                {{-- Ouverture du pointage --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-[#111827] dark:text-[#f8fafc]">
                        Ouverture du pointage <span class="text-[10px] font-normal text-[#64748b] dark:text-[#94a3b8]">(optionnelle)</span>
                    </label>
                    <input type="time" name="check_in_opens_at"
                        value="{{ old('check_in_opens_at', $setting->check_in_opens_at ? substr($setting->check_in_opens_at, 0, 5) : '') }}"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#111827] dark:text-[#f8fafc] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                    <p class="text-[10px] text-[#64748b] dark:text-[#94a3b8]">
                        Avant cette heure, l'arrivée ne peut pas être pointée (laisser vide pour autoriser en tout temps).
                    </p>
                </div>

                {{-- Pause en heures --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-[#111827] dark:text-[#f8fafc]">
                        Pause déduite <span class="text-[10px] font-normal text-[#64748b] dark:text-[#94a3b8]">(en heures)</span>
                    </label>
                    <input type="number" name="break_hours" min="0" max="8" step="0.5"
                        value="{{ old('break_hours', rtrim(rtrim(number_format($setting->break_minutes / 60, 2, '.', ''), '0'), '.') ?: '0') }}"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-[#111827] dark:text-[#f8fafc] focus:outline-none focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                    <p class="text-[10px] text-[#64748b] dark:text-[#94a3b8]">Ex : 1 ou 1.5. Temps automatiquement déduit du total d'heures travaillées.</p>
                </div>
            </div>

            {{-- Note d'information --}}
            <div class="p-3 rounded-lg bg-amber-500/10 dark:bg-amber-500/15 border border-amber-200/60 dark:border-amber-800/40 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="space-y-0.5 leading-relaxed">
                    <p class="font-medium">Impact des modifications</p>
                    <p class="text-[11px] opacity-90">
                        Ce changement ne recalcule pas les pointages passés et prend effet dès la prochaine arrivée. Les stages disposant d'un aménagement ou d'un horaire propre conservent leur propre grille.
                    </p>
                </div>
            </div>

            {{-- Footer et Enregistrement --}}
            <div class="pt-3 border-t border-[#dcdad2]/60 dark:border-[#222630]/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                @if($setting->exists && $setting->editor)
                    <p class="text-[11px] text-[#64748b] dark:text-[#94a3b8]">
                        Dernière modification par <span class="font-medium text-[#111827] dark:text-[#f8fafc]">{{ $setting->editor->name }}</span>
                        le {{ $setting->updated_at?->format('d/m/Y à H:i') }}.
                    </p>
                @else
                    <div></div>
                @endif

                <button type="submit"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-[#1c3e96] hover:bg-[#162f73] text-white shadow-sm transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Enregistrer l'horaire</span>
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
