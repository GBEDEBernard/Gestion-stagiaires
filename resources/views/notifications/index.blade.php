<x-app-layout title="Notifications">

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }

        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.04);
            transition: border-color .15s ease;
        }
        .dark .ks-sec {
            background: #131519 !important;
            border-color: #222630 !important;
            box-shadow: none;
        }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #bfbab2;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 500;
            padding: .35rem .75rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary {
            background: #15181f;
            color: #fff;
            border-color: #222630;
        }
        .ks-btn-secondary:hover {
            border-color: #1c3e96;
            color: #1c3e96;
        }

        .ks-btn-xs {
            min-height: 28px !important;
            padding: .25rem .6rem !important;
            font-size: .75rem !important;
            font-weight: 600;
            border-radius: 6px !important;
            white-space: nowrap;
        }
    </style>

    <div class="w-full space-y-3 pb-6">
        {{-- En-tête compact --}}
        <div class="ks-sec p-3 sm:p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#eef1fb] dark:bg-[#1c3e96]/20 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center flex-shrink-0 text-[#1c3e96] dark:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-sm sm:text-base font-bold text-[#241f1b] dark:text-white leading-tight">Notifications</h1>
                            @if($unreadCount > 0)
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#eef1fb] text-[#1c3e96] dark:bg-[#1c3e96]/25 dark:text-blue-300 border border-blue-200/70 dark:border-blue-800/50">
                                    {{ $unreadCount }} non lue{{ $unreadCount > 1 ? 's' : '' }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#ecfdf5] text-[#15803d] dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-800/50">
                                    Tout est à jour
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                            {{ $notifications->total() }} notification{{ $notifications->total() > 1 ? 's' : '' }} au total
                        </p>
                    </div>
                </div>

                @if($unreadCount > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="self-start sm:self-auto">
                        @csrf
                        <button type="submit" class="ks-btn-secondary ks-btn-xs">
                            <svg class="w-3.5 h-3.5 text-[#1c3e96] dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Tout marquer comme lu</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Liste des notifications --}}
        <div class="ks-sec overflow-hidden">
            <div class="divide-y divide-[#dcdad2] dark:divide-[#222630]">
                @forelse($notifications as $notification)
                    <a href="{{ route('notifications.markRead', $notification->id) }}"
                       class="group block px-3.5 py-3 sm:px-4 sm:py-3 transition-colors {{ !$notification->read_at ? 'bg-[#eef1fb]/50 dark:bg-[#1c3e96]/10 hover:bg-[#eef1fb]/80 dark:hover:bg-[#1c3e96]/20' : 'hover:bg-[#f1f0ed]/60 dark:hover:bg-[#181c24]' }}">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center border
                                @if($notification->color === 'blue') bg-[#eef1fb] border-blue-200/60 text-[#1c3e96] dark:bg-blue-950/40 dark:border-blue-900/50 dark:text-blue-400
                                @elseif($notification->color === 'amber') bg-amber-50 border-amber-200/60 text-amber-600 dark:bg-amber-950/40 dark:border-amber-900/50 dark:text-amber-400
                                @else bg-[#ecfdf5] border-emerald-200/60 text-[#15803d] dark:bg-emerald-950/40 dark:border-emerald-900/50 dark:text-emerald-400 @endif">
                                @if($notification->icon)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $notification->icon }}" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs sm:text-sm font-semibold truncate {{ !$notification->read_at ? 'text-[#1c3e96] dark:text-blue-300' : 'text-[#241f1b] dark:text-white' }}">
                                        {{ $notification->title }}
                                    </p>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="text-[11px] text-[#6e6862] dark:text-slate-400 tabular-nums">
                                            {{ $notification->created_at->locale('fr')->diffForHumans() }}
                                        </span>
                                        @if(!$notification->read_at)
                                            <span class="w-2 h-2 rounded-full bg-[#1c3e96] dark:bg-blue-400 ring-2 ring-[#eef1fb] dark:ring-[#1c3e96]/30"></span>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-xs text-[#6e6862] dark:text-slate-300 mt-0.5 leading-relaxed">
                                    {{ $notification->message }}
                                </p>
                            </div>

                            <div class="flex-shrink-0 self-center text-[#bfbab2] dark:text-slate-600 group-hover:text-[#1c3e96] dark:group-hover:text-blue-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-12 text-center">
                        <div class="w-10 h-10 rounded-xl bg-[#f1f0ed] dark:bg-[#1c212c] border border-[#dcdad2] dark:border-[#222630] flex items-center justify-center mx-auto text-[#6e6862] dark:text-slate-400 mb-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Aucune notification</p>
                        <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">Toutes vos notifications sont lues et à jour.</p>
                    </div>
                @endforelse
            </div>

            @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator && $notifications->hasPages())
                <div class="px-4 py-3 border-t border-[#dcdad2] dark:border-[#222630] bg-[#f1f0ed]/30 dark:bg-[#15181f]/40">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>