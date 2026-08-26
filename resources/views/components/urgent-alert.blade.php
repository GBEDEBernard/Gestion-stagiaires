@if(isset($urgentNotifications) && $urgentNotifications->isNotEmpty())
@php
    $notificationsArray = $urgentNotifications->map(function($notif) {
        return [
            'id' => $notif->id,
            'title' => $notif->title,
            'message' => $notif->message,
            'url' => $notif->url,
            'sender_name' => $notif->sender?->name ?? 'La Direction',
            'created_at' => $notif->created_at->format('d/m/Y à H:i'),
        ];
    })->values()->toArray();
@endphp

<div class="relative" 
     x-data="urgentAlertDropdown(@js($notificationsArray))" 
     x-show="alerts.length > 0"
     x-transition>

    {{-- Bouton Déclencheur dans le Header --}}
    <button @click="open = !open" 
            type="button" 
            title="Consulter les messages d'urgence"
            class="group relative inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white shadow-lg shadow-red-600/30 hover:shadow-red-600/50 hover:scale-[1.03] active:scale-95 transition-all duration-300 border border-red-400/40 ring-2 ring-red-500/20 cursor-pointer">
        
        {{-- Gyrophare animé pro --}}
        <div class="relative flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 text-amber-300 urgent-beacon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
        </div>

        {{-- Texte animé "Message d'urgence" avec Shimmer & Glow Pro --}}
        <div class="flex items-center">
            <span class="urgent-text-animated text-[11px] sm:text-xs whitespace-nowrap">
                Message d'urgence
            </span>
        </div>

        {{-- Badge compteur --}}
        <span class="inline-flex items-center justify-center min-w-[18px] sm:min-w-[20px] h-4.5 sm:h-5 px-1.5 rounded-full bg-white text-red-700 text-[10px] sm:text-[11px] font-black shadow-inner"
              x-text="alerts.length">
        </span>

        {{-- Petite flèche animée --}}
        <svg class="w-3.5 h-3.5 text-amber-200 group-hover:translate-y-0.5 transition-transform shrink-0" 
             :class="open ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown Déroulant (Format cloche de notification) --}}
    <div x-show="open" 
         @click.away="open = false" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         class="absolute right-0 mt-2 w-80 sm:w-96 rounded-3xl bg-white dark:bg-gray-800 shadow-2xl border-2 border-red-500/40 overflow-hidden z-[99999] divide-y divide-gray-100 dark:divide-gray-700">
        
        {{-- En-tête du Dropdown --}}
        <div class="px-4 py-3 bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-base animate-bounce">🚨</span>
                <div>
                    <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-white">Alertes Urgentes</h3>
                    <p class="text-[10px] text-red-100 font-medium" x-text="alerts.length + ' message(s) en attente'"></p>
                </div>
            </div>

            {{-- Bouton Tout acquitter --}}
            <button x-show="alerts.length > 1"
                    @click="acknowledgeAll()" 
                    :disabled="loadingAll"
                    type="button"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black text-red-950 bg-amber-300 hover:bg-amber-200 transition shadow-sm hover:scale-105 active:scale-95 disabled:opacity-50 cursor-pointer">
                <template x-if="!loadingAll">
                    <span>Tout acquitter</span>
                </template>
                <template x-if="loadingAll">
                    <span>Validation…</span>
                </template>
            </button>
        </div>

        {{-- Liste scrollable des alertes --}}
        <div class="max-h-96 overflow-y-auto custom-scrollbar divide-y divide-gray-100 dark:divide-gray-700/80">
            <template x-for="alert in alerts" :key="alert.id">
                <div x-show="!acknowledgedIds.includes(alert.id)"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 max-h-48"
                     x-transition:leave-end="opacity-0 max-h-0 py-0"
                     class="p-4 bg-red-50/40 dark:bg-red-950/20 hover:bg-red-50/80 dark:hover:bg-red-950/40 transition space-y-2">
                    
                    {{-- Badge & Horodatage --}}
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-red-100 dark:bg-red-900/60 text-red-700 dark:text-red-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-ping"></span>
                            Direction
                        </span>
                        <span class="text-gray-400 dark:text-gray-400 text-[10px]" x-text="alert.created_at"></span>
                    </div>

                    {{-- Titre & Message --}}
                    <div>
                        <h4 class="text-xs sm:text-sm font-extrabold text-gray-900 dark:text-white leading-snug" x-text="alert.title"></h4>
                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed mt-1 break-words" x-text="alert.message"></p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between gap-2 pt-1">
                        <div>
                            <template x-if="alert.url">
                                <a :href="alert.url" 
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    <span>Ouvrir le lien</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </template>
                        </div>

                        {{-- Bouton acquittement --}}
                        <button @click="acknowledgeOne(alert.id)" 
                                :disabled="loadingIds.includes(alert.id)"
                                type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black text-white bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 transition shadow-sm hover:scale-105 active:scale-95 disabled:opacity-50 cursor-pointer">
                            <template x-if="!loadingIds.includes(alert.id)">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>J'ai pris connaissance</span>
                                </div>
                            </template>
                            <template x-if="loadingIds.includes(alert.id)">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3 h-3 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span>Validation…</span>
                                </div>
                            </template>
                        </button>
                    </div>

                </div>
            </template>
        </div>

        {{-- Pied du Dropdown --}}
        <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900/60 text-center">
            <p class="text-[10px] text-gray-400 dark:text-gray-500">
                Ces alertes sont prioritaires et nécessitent votre confirmation de lecture.
            </p>
        </div>

    </div>

</div>

<script>
    function urgentAlertDropdown(initialAlerts) {
        return {
            open: false,
            alerts: initialAlerts || [],
            acknowledgedIds: [],
            loadingIds: [],
            loadingAll: false,

            async acknowledgeOne(notificationId) {
                if (this.loadingIds.includes(notificationId)) return;
                this.loadingIds.push(notificationId);

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(`/notifications/${notificationId}/acknowledge`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.acknowledgedIds.push(notificationId);
                        setTimeout(() => {
                            this.alerts = this.alerts.filter(a => a.id !== notificationId);
                            if (this.alerts.length === 0) {
                                this.open = false;
                            }
                        }, 250);
                    }
                } catch (e) {
                    console.error('Erreur acquittement:', e);
                } finally {
                    this.loadingIds = this.loadingIds.filter(id => id !== notificationId);
                }
            },

            async acknowledgeAll() {
                this.loadingAll = true;
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch('/notifications/acknowledge-all', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.alerts.forEach(a => this.acknowledgedIds.push(a.id));
                        setTimeout(() => {
                            this.alerts = [];
                            this.open = false;
                        }, 250);
                    }
                } catch (e) {
                    console.error('Erreur acquittement total:', e);
                } finally {
                    this.loadingAll = false;
                }
            }
        }
    }
</script>
@endif
