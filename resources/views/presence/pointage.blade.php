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

<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">

        {{-- Sortir du template par un return laisserait un tampon de sortie
             ouvert : on branche sur @else plutôt que d'interrompre le rendu. --}}
        @if(!($activeStage ?? null))
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-8 text-center">
                <p class="font-medium text-gray-900 dark:text-white">Aucun stage actif aujourd'hui</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Le pointage s'active dès que votre stage démarre.
                </p>
            </div>
        @else

        {{-- ── Messages ── --}}
        @foreach(['success' => 'emerald', 'info' => 'blue', 'error' => 'red'] as $cle => $ton)
            @if(session($cle))
                <div class="mb-5 flex gap-3 px-4 py-3 rounded-xl border
                            bg-{{ $ton }}-50 dark:bg-{{ $ton }}-900/20
                            border-{{ $ton }}-200 dark:border-{{ $ton }}-800/50
                            text-sm text-{{ $ton }}-800 dark:text-{{ $ton }}-300">
                    <span>{{ session($cle) }}</span>
                </div>
            @endif
        @endforeach

        @if($errors->any())
            <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm text-red-800 dark:text-red-300">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Trois situations distinctes derrière un même jour férié : dire
             seulement « c'est férié » laisserait la personne sans savoir si
             elle peut pointer ou non. --}}
        @if($todayHoliday ?? null)
            @php
                $ouvert = ($canBypassHoliday ?? false) || ($isEmergencyExempted ?? false);
                $ton    = $ouvert ? 'amber' : 'violet';
            @endphp
            <div class="mb-5 flex items-start gap-3 px-4 py-3.5 rounded-xl
                        bg-{{ $ton }}-50 dark:bg-{{ $ton }}-900/20
                        border border-{{ $ton }}-200 dark:border-{{ $ton }}-800/50">
                <svg class="w-5 h-5 shrink-0 mt-0.5 text-{{ $ton }}-600 dark:text-{{ $ton }}-400"
                     fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-{{ $ton }}-800 dark:text-{{ $ton }}-300">
                        Jour férié — {{ $todayHoliday->label }}
                    </p>
                    <p class="mt-0.5 text-sm text-{{ $ton }}-700/85 dark:text-{{ $ton }}-400/85">
                        @if($isEmergencyExempted ?? false)
                            Vous avez été appelé en urgence : vous pouvez pointer normalement.
                        @elseif($canBypassHoliday ?? false)
                            Vous disposez d'une autorisation de pointer aujourd'hui.
                        @else
                            Le pointage est désactivé. En cas d'urgence, votre responsable peut vous contacter.
                        @endif
                    </p>
                </div>
            </div>
        @endif

        @include('presence.partials.carte', [
            'lieu'          => $activeStage->site?->name ?? 'Site',
            'prenom'        => $prenom,
            'day'           => $day,
            'expIn'         => $expIn,
            'expOut'        => $expOut,
            'etat'          => $etat,
            'late'          => $late,
            'departBloque'  => $etat === 'depart' && !($canCheckOutNow ?? true),
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
