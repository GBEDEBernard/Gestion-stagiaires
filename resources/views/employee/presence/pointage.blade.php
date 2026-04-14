<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-6">

        {{-- Errors --}}
        @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-red-700 text-sm">
            <strong>Erreur :</strong>
            <ul class="mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow border overflow-hidden">

            {{-- Header --}}
            <div class="p-5 border-b bg-gray-50">
                <h1 class="text-xl md:text-2xl font-bold">Pointage de présence - Employé</h1>
                <p class="text-sm text-gray-500">Rapide • sécurisé • géolocalisé</p>
            </div>

            <div class="p-5 space-y-6">

                {{-- Messages --}}
                @if(session('success'))
                <div class="p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
                @endif

                {{-- Info employé --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-50 rounded-xl border">
                        <p class="text-sm text-blue-700 font-semibold">Employé</p>
                        <p class="text-lg font-bold text-blue-900">{{ $user->name }}</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-xl border">
                        <p class="text-sm text-purple-700 font-semibold">Domaine</p>
                        <p class="text-lg font-bold text-purple-900">{{ $user->domaine->nom }}</p>
                    </div>
                </div>

                {{-- Statut du jour --}}
                <div class="p-4 bg-gray-50 rounded-xl border">
                    <h3 class="font-semibold mb-3">Statut d'aujourd'hui</h3>
                    @if($attendanceDay)
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Arrivée:</span>
                            <span class="font-semibold">{{ $attendanceDay->first_check_in_at?->format('H:i') ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Départ:</span>
                            <span class="font-semibold">{{ $attendanceDay->last_check_out_at?->format('H:i') ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @else
                    <p class="text-gray-600">Aucun pointage aujourd'hui</p>
                    @endif
                </div>

                {{-- Boutons d'action --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <form action="{{ route('employee.presence.prepareCheckin') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-xl transition shadow-lg shadow-green-600/20 flex items-center justify-center gap-3"
                            {{ $attendanceDay && $attendanceDay->first_check_in_at ? 'disabled class="opacity-50 cursor-not-allowed"' : '' }}>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Pointer mon arrivée
                        </button>
                    </form>

                    <form action="{{ route('employee.presence.prepareCheckout') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-4 px-6 rounded-xl transition shadow-lg shadow-red-600/20 flex items-center justify-center gap-3"
                            {{ !$attendanceDay || !$attendanceDay->first_check_in_at ? 'disabled class="opacity-50 cursor-not-allowed"' : '' }}>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Pointer mon départ
                        </button>
                    </form>
                </div>

                {{-- Historique rapide --}}
                <div class="pt-4 border-t">
                    <a href="{{ route('employee.presence.historique') }}"
                        class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Voir mon historique
                    </a>
                </div>

                @endif
            </div>
        </div>
    </div>
</x-app-layout>