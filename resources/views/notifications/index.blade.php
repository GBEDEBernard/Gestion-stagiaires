<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- ════════════════════════════════════════════════════════
             BANNER
        ════════════════════════════════════════════════════════ --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-700 to-indigo-800 rounded-2xl mt-8">
            <div class="absolute inset-0 bg-white/5"></div>
            <div class="relative px-8 py-0">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2">Notifications</h1>
                        <p class="text-violet-200 text-lg">Toutes vos notifications</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-white/10 self-start">
                        <div class="flex items-center gap-2 text-white">
                            <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="font-medium">{{ $unreadCount ?? $notificationCount ?? 0 }} non lu(s)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 80" fill="none" class="w-full h-12">
                    <path d="M0 80C240 80 480 80 720 60C960 40 1200 40 1440 60L1440 80H0Z"
                        fill="rgb(249,250,251)" class="dark:fill-gray-900" />
                </svg>
            </div>
        </div>

        <div class="space-y-6">
        {{-- Actions --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $notifications->count() }} notification(s) au total
            </div>
            @if($unreadCount > 0)
            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tout marquer comme lu
                </button>
            </form>
            @endif
        </div>

        {{-- Liste des notifications --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            @forelse($notifications as $notification)
            <a href="{{ $notification->url }}"
                onclick="event.preventDefault(); document.getElementById('notif-form-{{ $notification->id }}').submit();"
                class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition border-b border-gray-100 dark:border-gray-700 last:border-0 {{ !$notification->read_at ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                <form id="notif-form-{{ $notification->id }}" action="{{ route('notifications.markRead', $notification->id) }}" method="GET" style="display:none;"></form>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center
                        @if($notification->color === 'blue') bg-blue-100 text-blue-600 dark:bg-blue-900/30
                        @elseif($notification->color === 'amber') bg-amber-100 text-amber-600 dark:bg-amber-900/30
                        @else bg-green-100 text-green-600 dark:bg-green-900/30 @endif">
                        @if($notification->icon)
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $notification->icon }}" />
                        </svg>
                        @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 {{ !$notification->read_at ? 'text-blue-700 dark:text-blue-300' : '' }}">{{ $notification->title }}</p>
                            @if(!$notification->read_at)
                            <span class="w-2.5 h-2.5 bg-blue-500 rounded-full flex-shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->locale('fr')->diffForHumans() }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </a>
            @empty
            <div class="px-6 py-16 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <p class="text-xl font-semibold text-gray-500 dark:text-gray-400 mb-2">Aucune notification</p>
                <p class="text-gray-400 dark:text-gray-500">Vous êtes à jour !</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

</x-app-layout>
</xai:function_call<xai:function_call name="edit_file">
