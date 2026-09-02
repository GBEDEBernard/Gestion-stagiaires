@php
    $enrolledDevices = $user->trustedDevices()->activeQrBadges()->get();
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span>Mes Badges de Pointage par QR</span>
        </h3>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $enrolledDevices->count() >= 2 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300' }}">
            {{ $enrolledDevices->count() }}/1 appareil
        </span>
    </div>

    <div class="p-6">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
            Ces appareils sont autorisés à effectuer votre pointage d'arrivée et de départ <strong>instantanément en scannant le QR code de la porte</strong> sans avoir à vous reconnecter.
        </p>

        @if($enrolledDevices->isNotEmpty())
            <div class="space-y-3">
                @foreach($enrolledDevices as $dev)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $dev->device_name ?: ($dev->device_label ?: 'Smartphone') }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $dev->platform ?? 'Mobile' }} • {{ $dev->browser ?? 'Navigateur' }} • Enrôlé le {{ $dev->first_seen_at?->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('presence.devices.revoke', $dev) }}" method="POST"
                              data-swal-title="Révoquer ce téléphone ?"
                              data-swal-text="Il ne pourra plus pointer par QR code. Vous devrez vous reconnecter depuis ce téléphone pour le réenregistrer."
                              data-swal-icon="warning"
                              data-swal-color="#dc2626"
                              data-swal-confirm="Oui, révoquer">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Révoquer</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 px-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Aucun smartphone n'est encore configuré comme badge.<br>
                    Pour en enregistrer un, scannez le QR code affiché à la porte avec votre téléphone et connectez-vous.
                </p>
            </div>
        @endif
    </div>
</div>
