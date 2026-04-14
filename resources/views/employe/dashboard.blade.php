<x-app-layout>
    <div class="relative ml-4 overflow-hidden bg-gradient-to-br from-sky-600 via-cyan-600 to-indigo-700 rounded-2xl mb-8">
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.25),_transparent_40%)]"></div>
        <div class="relative px-8 py-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2">Tableau de bord Employé</h1>
                    <p class="text-cyan-100 text-lg">Vos rapports et votre pointage en un coup d'œil.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-white/10 self-start">
                    <div class="flex items-center gap-2 text-white">
                        <svg class="w-5 h-5 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">{{ now()->locale('fr')->isoFormat('DD MMMM YYYY') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3 ml-4">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Pointage aujourd'hui</h2>
            <p class="mt-4 text-3xl font-bold text-slate-900 dark:text-white">{{ $todayAttendance ? 'Enregistré' : 'Aucun pointage' }}</p>
            @if($todayAttendance)
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Arrivée : {{ $todayAttendance->first_check_in_at?->format('H:i') ?? '--' }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Départ : {{ $todayAttendance->last_check_out_at?->format('H:i') ?? '--' }}</p>
            @else
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Votre pointage n'a pas encore été enregistré aujourd'hui.</p>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Rapports</h2>
            <p class="mt-4 text-3xl font-bold text-slate-900 dark:text-white">{{ $attendanceEventsThisWeek }}</p>
            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Événements de pointage cette semaine</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Suivi hebdomadaire</h2>
            <p class="mt-4 text-3xl font-bold text-slate-900 dark:text-white">{{ $daysPresentThisWeek }}/{{ $daysTrackedThisWeek }}</p>
            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Jours où le pointage a été validé cette semaine</p>
        </div>
    </div>

    <div class="mt-8 ml-4 grid gap-6 lg:grid-cols-2">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Votre domaine</h3>
            <p class="mt-3 text-slate-600 dark:text-slate-300">{{ $user->domaine?->nom ?? 'Non défini' }}</p>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Utilisez ce tableau de bord pour accéder rapidement à votre pointage et à votre présence.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Actions rapides</h3>
            <div class="mt-4 space-y-3">
                <a href="{{ route('presence.pointage') }}" class="block rounded-2xl bg-slate-900 text-white px-4 py-3 hover:bg-slate-800 transition">Voir le pointage</a>
                <a href="{{ route('presence.historique') }}" class="block rounded-2xl border border-slate-200 dark:border-gray-700 text-slate-900 dark:text-white px-4 py-3 hover:bg-slate-50 dark:hover:bg-gray-900 transition">Historique des pointages</a>
            </div>
        </div>
    </div>
</x-app-layout>
