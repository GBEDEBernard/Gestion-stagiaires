@php
    $day        = $attendanceDay ?? null;

    $checkedIn  = $hasCheckedIn ?? false;
    $checkedOut = $hasCheckedOut ?? false;
    $expIn      = $expectedIn  ?? null;
    $expOut     = $expectedOut ?? null;
    $late       = ($isLateNow ?? false) && !$checkedIn;

    $etat = !$checkedIn ? 'arrivee' : (!$checkedOut ? 'depart' : 'termine');

    $moi     = auth()->user();
    $prenom  = $moi?->personnel?->prenom ?: ($moi?->prenom ?: $moi?->name);
@endphp

<style>
    body { background: #f1f0ed !important; }
    .dark body { background: #15181f !important; }
    header { background-color: transparent !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }
</style>

<x-app-layout>
    <div class="w-full max-w-4xl mx-auto px-3 sm:px-6 pt-3 sm:pt-4 pb-6 sm:pb-8">

        {{-- Sortir du template par un return laisserait un tampon de sortie
             ouvert : on branche sur @else plutôt que d'interrompre le rendu. --}}
        @if(!($activeStage ?? null))
            <div class="rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] p-8 text-center shadow-sm">
                <p class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">Aucun stage actif aujourd'hui</p>
                <p class="mt-1 text-sm text-[#6e6862] dark:text-[#a89f93]">
                    Le pointage s'active dès que votre stage démarre.
                </p>
            </div>
        @else

        {{-- ── Messages & Alertes : Mini-banner mono-ligne fermable (Palette Mon Stage) ── --}}
        @if(session('success'))
            <div x-data="{ dismissed: false }" x-show="!dismissed" x-transition:leave="transition ease-out duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mb-3 sm:mb-4 flex items-center justify-between gap-3 px-3.5 py-2 rounded-[10px] bg-[#dcfce7] dark:bg-[rgba(22,128,74,0.22)] border border-[#15803d]/30 text-[#15803d] dark:text-[#4ade80] text-xs sm:text-sm font-medium shadow-sm backdrop-blur-md">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="h-2 w-2 rounded-full bg-[#15803d] shrink-0"></span>
                    <span class="truncate">{{ session('success') }}</span>
                </div>
                <button type="button" @click="dismissed = true" title="Fermer" class="p-1 -mr-1 rounded-lg hover:bg-[#15803d]/15 transition-colors shrink-0 text-[#15803d] dark:text-[#4ade80]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        @if(session('info'))
            <div x-data="{ dismissed: false }" x-show="!dismissed" x-transition:leave="transition ease-out duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mb-3 sm:mb-4 flex items-center justify-between gap-3 px-3.5 py-2 rounded-[10px] bg-[#eef1fb] dark:bg-[rgba(28,62,150,0.25)] border border-[#1c3e96]/30 text-[#1c3e96] dark:text-[#60a5fa] text-xs sm:text-sm font-medium shadow-sm backdrop-blur-md">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="h-2 w-2 rounded-full bg-[#1c3e96] shrink-0"></span>
                    <span class="truncate">{{ session('info') }}</span>
                </div>
                <button type="button" @click="dismissed = true" title="Fermer" class="p-1 -mr-1 rounded-lg hover:bg-[#1c3e96]/15 transition-colors shrink-0 text-[#1c3e96] dark:text-[#60a5fa]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ dismissed: false }" x-show="!dismissed" x-transition:leave="transition ease-out duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mb-3 sm:mb-4 flex items-center justify-between gap-3 px-3.5 py-2 rounded-[10px] bg-[#fee2e2] dark:bg-[rgba(185,28,28,0.22)] border border-[#b91c1c]/30 text-[#b91c1c] dark:text-[#f87171] text-xs sm:text-sm font-medium shadow-sm backdrop-blur-md">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#b91c1c] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#b91c1c]"></span>
                    </span>
                    <span class="truncate">{{ session('error') }}</span>
                </div>
                <button type="button" @click="dismissed = true" title="Fermer" class="p-1 -mr-1 rounded-lg hover:bg-[#b91c1c]/15 transition-colors shrink-0 text-[#b91c1c] dark:text-[#f87171]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ dismissed: false }" x-show="!dismissed" x-transition:leave="transition ease-out duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mb-3 sm:mb-4 px-3.5 py-2 rounded-[10px] bg-[#fee2e2] dark:bg-[rgba(185,28,28,0.22)] border border-[#b91c1c]/30 text-[#b91c1c] dark:text-[#f87171] text-xs sm:text-sm shadow-sm backdrop-blur-md">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 font-semibold text-[#b91c1c] dark:text-[#f87171]">
                        <span class="h-2 w-2 rounded-full bg-[#b91c1c] shrink-0"></span>
                        <span>Erreur de validation</span>
                    </div>
                    <button type="button" @click="dismissed = true" title="Fermer" class="p-1 -mr-1 rounded-lg hover:bg-[#b91c1c]/15 transition-colors shrink-0 text-[#b91c1c] dark:text-[#f87171]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-xs opacity-90 pl-3 mt-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @if($todayHoliday ?? null)
            @php
                $ouvert = ($canBypassHoliday ?? false) || ($isEmergencyExempted ?? false);
                $badge  = ($isEmergencyExempted ?? false) ? 'appelé en urgence' : (($canBypassHoliday ?? false) ? 'Pointage autorisé' : 'Le pointage est désactivé');
            @endphp
            <div x-data="{ dismissed: false }" x-show="!dismissed" x-transition:leave="transition ease-out duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mb-3 sm:mb-4 flex items-center justify-between gap-3 px-3.5 py-2 rounded-[10px] shadow-sm backdrop-blur-md {{ $ouvert ? 'bg-[#fef3c7] dark:bg-[rgba(180,83,9,0.22)] border border-[#b45309]/30 text-[#b45309] dark:text-[#fbbf24]' : 'bg-[#faf5ff] dark:bg-[rgba(126,34,206,0.25)] border border-[#7e22ce]/30 text-[#7e22ce] dark:text-[#c084fc]' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="w-4 h-4 shrink-0 {{ $ouvert ? 'text-[#b45309] dark:text-[#fbbf24]' : 'text-[#7e22ce] dark:text-[#c084fc]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div class="text-xs sm:text-sm font-medium truncate">
                        <span class="font-semibold">Férié :</span> {{ $todayHoliday->label }} — <span class="opacity-80">{{ $badge }}</span>
                    </div>
                </div>
                <button type="button" @click="dismissed = true" title="Fermer" class="p-1 -mr-1 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 transition-colors shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if($late)
            <div x-data="{ dismissed: false }" x-show="!dismissed" x-transition:leave="transition ease-out duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mb-3 sm:mb-4 flex items-center justify-between gap-3 px-3.5 py-2 rounded-[10px]
                        bg-[#fef3c7] dark:bg-[rgba(180,83,9,0.22)]
                        border border-[#b45309]/30 text-[#b45309] dark:text-[#fbbf24] shadow-sm backdrop-blur-md">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#b45309] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#b45309]"></span>
                    </span>
                    <div class="text-xs sm:text-sm font-medium truncate">
                        <span class="font-semibold">Retard détecté</span> — prévue : {{ $expIn?->format('H:i') ?? '—' }} <span class="text-[#b45309]/80 dark:text-[#fbbf24]/80 text-[11px] hidden xs:inline">(motif requis)</span>
                    </div>
                </div>
                <button type="button" @click="dismissed = true" title="Fermer" class="p-1 -mr-1 rounded-lg text-[#b45309]/70 hover:text-[#b45309] dark:text-[#fbbf24]/70 dark:hover:text-[#fbbf24] hover:bg-[#b45309]/15 transition-colors shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(($arriveeBloquee ?? false) && ($journeeBloqueuse ?? null))
            <div x-data="{ dismissed: false }" x-show="!dismissed" x-transition:leave="transition ease-out duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mb-3 sm:mb-4 flex items-center justify-between gap-3 px-3.5 py-2 rounded-[10px]
                        bg-[#fee2e2] dark:bg-[rgba(185,28,28,0.22)] border border-[#b91c1c]/30 text-[#b91c1c] dark:text-[#f87171] shadow-sm backdrop-blur-md">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#b91c1c] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#b91c1c]"></span>
                    </span>
                    <div class="text-xs sm:text-sm font-medium truncate">
                        <span class="font-semibold">Arrivée bloquée :</span> départ du {{ $journeeBloqueuse->attendance_date->format('d/m') }} non pointé
                    </div>
                </div>
                <button type="button" @click="dismissed = true" title="Fermer" class="p-1 -mr-1 rounded-lg hover:bg-[#b91c1c]/15 transition-colors shrink-0 text-[#b91c1c] dark:text-[#f87171]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        {{-- L'absence de rapport ne se signale plus ici : le clic sur « Pointer
             mon départ » ouvre le constat qui l'explique et donne le chemin
             pour le déposer. Deux messages pour un seul fait, c'en était un de
             trop, et le bandeau rouge était le plus visible des deux. --}}

        @include('presence.partials.carte', [
            'lieu'          => $activeStage->site?->name ?? 'Site',
            'prenom'        => $prenom,
            'day'           => $day,
            'expIn'         => $expIn,
            'expOut'        => $expOut,
            'etat'          => $etat,
            'late'          => $late,
            'departBloque'  => $etat === 'depart' && !($canCheckOutNow ?? true),
            'arriveeBloquee' => $arriveeBloquee ?? false,
            'rapportSoumis' => $rapportSoumis ?? true,
            'action'        => $etat === 'arrivee' ? route('presence.checkin') : route('presence.checkout'),
            'champs'        => ['stage_id' => $activeStage->id],
            'isWorkDay'     => $isWorkDay ?? true,
            'workDaysLabel' => $workDaysLabel ?? null,
            'historiqueUrl' => route('presence.historique'),
            'journeeOubliee' => $journeeOubliee ?? null,
            'declarationUrl' => route('presence.depart-oublie'),
        ])

        @endif
    </div>

    @include('presence.partials.badge-popup')
    @include('presence.partials.pointage-script')
</x-app-layout>
