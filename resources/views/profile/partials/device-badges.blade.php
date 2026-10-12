@php
    $enrolledDevices = $user->trustedDevices()->activeQrBadges()->get();
@endphp

<div class="ks-sec p-4 sm:p-5 space-y-3">
    <div class="flex items-center justify-between gap-2 border-b border-[#ecebe3] dark:border-[#222630] pb-2.5">
        <h3 class="text-sm font-bold text-[#241f1b] dark:text-white flex items-center gap-2">
            <span class="w-6 h-6 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </span>
            <span>Appareil de pointage QR</span>
        </h3>
        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $enrolledDevices->count() >= 1 ? 'bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 border border-[#d8e0f5] dark:border-blue-900/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
            {{ $enrolledDevices->count() }}/1 appareil
        </span>
    </div>

    <p class="text-[11px] text-[#6e6862] dark:text-slate-400 leading-relaxed">
        Cet appareil est autorisé à effectuer votre pointage instantané en scannant le QR code affiché à la porte.
    </p>

    @if($enrolledDevices->isNotEmpty())
        <div class="space-y-2">
            @foreach($enrolledDevices as $dev)
                <div class="flex items-center justify-between p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#ecebe3] dark:border-[#222630] gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-[#241f1b] dark:text-white truncate">
                                {{ $dev->device_name ?: ($dev->device_label ?: 'Smartphone') }}
                            </h4>
                            <p class="text-[10px] text-[#6e6862] dark:text-slate-400 truncate">
                                {{ $dev->platform ?? 'Mobile' }} • {{ $dev->browser ?? 'Navigateur' }} • Enrôlé le {{ $dev->first_seen_at?->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('presence.devices.revoke', $dev) }}" method="POST"
                          data-swal-title="Révoquer ce téléphone ?"
                          data-swal-text="Il ne pourra plus pointer par QR code. Vous devrez vous reconnecter depuis ce téléphone pour le réenregistrer."
                          data-swal-icon="warning"
                          data-swal-color="#dc2626"
                          data-swal-confirm="Oui, révoquer"
                          class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ks-btn-secondary ks-btn-xs !h-6 !px-2 text-rose-600 dark:text-rose-400 hover:border-rose-400 cursor-pointer" title="Révoquer">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Révoquer</span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-4 px-3 bg-[#faf9f6] dark:bg-[#181c24] rounded-lg border border-dashed border-[#dcdad2] dark:border-[#2b3240]">
            <p class="text-[11px] text-[#6e6862] dark:text-slate-400">
                Aucun téléphone n'est encore configuré.<br>
                Pour l'enregistrer, scannez le QR code de pointage avec votre smartphone.
            </p>
        </div>
    @endif
</div>
