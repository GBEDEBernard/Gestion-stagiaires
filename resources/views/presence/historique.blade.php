<x-app-layout title="Historique de présence">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Historique de présence</h1>
                <p class="mt-2 text-xl text-slate-600">Tous tes pointages par période</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('presence.pointage') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z">
                    </svg>
                    Nouveau pointage
                </a>
            </div>
        </div>

        {{-- Period Tabs --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-1 shadow-sm">
            <nav class="-mb-px flex space-x-8">
                <a href="?period=week" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'week' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ request('period') === 'week' ? '📅 Semaine' : 'Semaine' }}
                </a>
                <a href="?period=month" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'month' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ request('period') === 'month' ? '📊 Mois' : 'Mois' }}
                </a>
                <a href="?period=year" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'year' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ request('period') === 'year' ? '📈 Année' : 'Année' }}
                </a>
            </nav>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-emerald-100">
                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600">Pointages</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $attendanceEvents->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600">Validés</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $attendanceEvents->where('status', 'approved')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-amber-100">
                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a1 1 0 00-1-1H9z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600">En attente</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $attendanceEvents->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    Pointages récents
                    <span class="px-2 py-1 bg-slate-200 text-slate-800 text-xs font-semibold rounded-full">
                        {{ $attendanceEvents->count() }} pointages
                    </span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Date & Heure</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($attendanceEvents as $event)
                        <tr class="hover:bg-slate-50 group transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $event->occurred_at->format('d MMM Y') }}</div>
                                <div class="text-sm text-slate-500">{{ $event->occurred_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($event->event_type === 'check_in')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-sm font-semibold rounded-full">
                                    Arrivée
                                </span>
                                @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                    Départ
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                {{ $event->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                @if($event->site)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                    {{ $event->site->name }}
                                </span>
                                @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-semibold rounded-full">
                                    À distance
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($event->status === 'approved')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-sm font-semibold rounded-full">
                                    Validé
                                </span>
                                @elseif($event->status === 'pending')
                                <span class="px-3 py-1 bg-amber-100 text-amber-800 text-sm font-semibold rounded-full">
                                    En attente
                                </span>
                                @elseif($event->status === 'rejected')
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                                    Rejeté
                                </span>
                                @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-semibold rounded-full">
                                    {{ $event->status }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Aucun pointage trouvé pour cette période.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>