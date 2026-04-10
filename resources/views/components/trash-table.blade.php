@props([
'items',
'title',
'columns',
'restoreRoute',
'forceDeleteRoute',
<<<<<<< HEAD
=======
'restoreMethod' => 'PUT',
>>>>>>> e9635ab
'fields' => [],
'periodColumn' => null,
'relationColumn' => null,
'icon' => '🗂️',
'color' => 'red'
])

@php
$colorClasses = match($color) {
'red' => 'from-red-500 to-rose-600 bg-red-500/10 border-red-500/20',
'blue' => 'from-blue-500 to-indigo-600 bg-blue-500/10 border-blue-500/20',
'green' => 'from-emerald-500 to-teal-600 bg-emerald-500/10 border-emerald-500/20',
'purple' => 'from-violet-500 to-purple-600 bg-violet-500/10 border-violet-500/20',
'orange' => 'from-orange-500 to-amber-600 bg-orange-500/10 border-orange-500/20',
default => 'from-red-500 to-rose-600 bg-red-500/10 border-red-500/20',
};

$iconColorClasses = match($color) {
'red' => 'text-red-500',
'blue' => 'text-blue-500',
'green' => 'text-emerald-500',
'purple' => 'text-violet-500',
'orange' => 'text-orange-500',
default => 'text-red-500',
};
@endphp

<div class="group relative bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-600">
    <!-- Header avec dégradé subtil -->
    <div class="relative px-6 py-5 bg-gradient-to-r {{ $colorClasses }} dark:opacity-90">
        <div class="absolute inset-0 bg-white/10 dark:bg-black/10"></div>
        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-white/20 backdrop-blur-sm">
                    <span class="text-xl">{{ $icon }}</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
                    <p class="text-xs text-slate-700 dark:text-slate-300 font-medium">
                        {{ $items->count() }} élément{{ $items->count() > 1 ? 's' : '' }} supprimé{{ $items->count() > 1 ? 's' : '' }}
                    </p>
                </div>
            </div>

            <!-- Badge de compteur -->
            @if($items->count() > 0)
            <div class="px-3 py-1.5 rounded-full bg-white/30 backdrop-blur-sm">
                <span class="text-sm font-bold text-slate-900">{{ $items->count() }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Contenu -->
    <div class="p-0">
        @if($items->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        @foreach($columns as $column)
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            {{ $column }}
                        </th>
                        @endforeach
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($items as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-200">
                        @if($periodColumn)
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-{{ $color }}-600 dark:text-{{ $color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="font-medium text-slate-900 dark:text-white">{{ data_get($item, $relationColumn ?? $periodColumn[0]) ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-slate-600 dark:text-slate-300">
                                    {{ data_get($item, $periodColumn[1]) ? \Carbon\Carbon::parse(data_get($item, $periodColumn[1]))->format('d/m/Y') : '—' }}
                                </span>
                            </div>
                        </td>
                        @else
                        @foreach($fields as $index => $field)
                        <td class="px-4 py-3.5">
                            @if($index === 0)
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 flex items-center justify-center">
                                    <span class="text-xs font-bold text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                        {{ strtoupper(substr(data_get($item, $field) ?? '?', 0, 1)) }}
                                    </span>
                                </div>
                                <span class="font-medium text-slate-900 dark:text-white">{{ data_get($item, $field) ?? '—' }}</span>
                            </div>
                            @else
                            <span class="text-slate-600 dark:text-slate-400">{{ data_get($item, $field) ?? '—' }}</span>
                            @endif
                        </td>
                        @endforeach
                        @endif

                        <!-- Date de suppression -->
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $item->deleted_at ? $item->deleted_at->diffForHumans() : 'Récemment' }}
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Restaurer -->
                                <form action="{{ route($restoreRoute, $item->id) }}" method="POST" class="inline">
                                    @csrf
<<<<<<< HEAD
                                    @method('PATCH')
=======
                                    @method($restoreMethod)
>>>>>>> e9635ab
                                    <button type="submit"
                                        class="group/btn flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-all duration-200"
                                        onclick="return confirm('Voulez-vous restaurer cet élément ?')">
                                        <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Restaurer
                                    </button>
                                </form>

                                <!-- Supprimer définitivement -->
                                <form action="{{ route($forceDeleteRoute, $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="group/btn flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-semibold hover:bg-red-200 dark:hover:bg-red-900/50 transition-all duration-200"
                                        onclick="return confirm('⚠️ Cette suppression est définitive. Continuer ?')">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- État vide moderne -->
        <div class="flex flex-col items-center justify-center py-12 px-6">
            <div class="relative mb-4">
                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                    <svg class="w-10 h-10 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center shadow-lg">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-slate-600 dark:text-slate-400 font-medium">Aucun élément supprimé</p>
            <p class="text-sm text-slate-500 dark:text-slate-500 mt-1">Cette corbeille est vide</p>
        </div>
        @endif
    </div>
<<<<<<< HEAD
</div>
=======
</div>
>>>>>>> e9635ab
