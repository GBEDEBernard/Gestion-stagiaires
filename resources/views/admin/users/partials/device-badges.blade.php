@php
    $badgeDevices = $user->trustedDevices()->activeQrBadges()->orderByDesc('last_seen_at')->get();
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Appareils de pointage
        </h3>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
            {{ $badgeDevices->count() }}/2
        </span>
    </div>

    <div class="p-6">
        @if($badgeDevices->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Aucun appareil n'est enrôlé comme badge pour cet utilisateur. Il doit se connecter
                depuis son téléphone après un scan pour en configurer un.
            </p>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                Ces téléphones peuvent pointer par QR code sans reconnexion. Révoquez-en un
                en cas de perte, de vol ou de départ : l'appareil est coupé immédiatement,
                sans désactiver le compte.
            </p>

            <div class="space-y-3">
                @foreach($badgeDevices as $dev)
                    <div class="flex items-center justify-between gap-3 p-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700">
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                {{ $dev->device_name ?: ($dev->device_label ?: 'Smartphone') }}
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $dev->platform ?? 'Mobile' }} • {{ $dev->browser ?? 'Navigateur' }}
                                @if($dev->last_seen_at)
                                    • Vu le {{ $dev->last_seen_at->format('d/m/Y à H:i') }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                @if($dev->token_expires_at)
                                    Expire le {{ $dev->token_expires_at->format('d/m/Y') }}
                                @else
                                    Sans date d'expiration
                                @endif
                                @if($dev->last_ip_address) • IP {{ $dev->last_ip_address }} @endif
                            </p>
                        </div>

                        <form method="POST"
                              action="{{ route('admin.users.devices.revoke', [$user, $dev]) }}"
                              onsubmit="return confirm('Révoquer cet appareil ? {{ $user->name }} ne pourra plus pointer avec ce téléphone tant qu\'il ne l\'aura pas réenrôlé.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/50 transition text-xs font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                Révoquer
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
