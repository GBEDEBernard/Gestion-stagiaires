@props([
'items',
'title',
'columns' => [],
'restoreRoute',
'restoreMethod' => 'PUT',
'forceDeleteRoute',
'fields' => [],
'periodColumn' => null,
'relationColumn' => null,
'color' => 'slate',
'icon' => null,
])

@php
$colorMap = [
'teal' => ['bg' => 'bg-teal-50 dark:bg-teal-950', 'border' => 'border-teal-200 dark:border-teal-800', 'icon' => 'bg-teal-100 text-teal-600 dark:bg-teal-900 dark:text-teal-400', 'badge' => 'bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300', 'header' => 'bg-teal-50 dark:bg-teal-950/60', 'dot' => 'bg-teal-400'],
'red' => ['bg' => 'bg-red-50 dark:bg-red-950', 'border' => 'border-red-200 dark:border-red-800', 'icon' => 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400', 'badge' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300', 'header' => 'bg-red-50 dark:bg-red-950/60', 'dot' => 'bg-red-400'],
'violet' => ['bg' => 'bg-violet-50 dark:bg-violet-950', 'border' => 'border-violet-200 dark:border-violet-800', 'icon' => 'bg-violet-100 text-violet-600 dark:bg-violet-900 dark:text-violet-400', 'badge' => 'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300', 'header' => 'bg-violet-50 dark:bg-violet-950/60', 'dot' => 'bg-violet-400'],
'amber' => ['bg' => 'bg-amber-50 dark:bg-amber-950', 'border' => 'border-amber-200 dark:border-amber-800', 'icon' => 'bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400', 'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300', 'header' => 'bg-amber-50 dark:bg-amber-950/60', 'dot' => 'bg-amber-400'],
'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-950', 'border' => 'border-indigo-200 dark:border-indigo-800', 'icon' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400', 'badge' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300', 'header' => 'bg-indigo-50 dark:bg-indigo-950/60', 'dot' => 'bg-indigo-400'],
'slate' => ['bg' => 'bg-slate-50 dark:bg-slate-900', 'border' => 'border-slate-200 dark:border-slate-700', 'icon' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400', 'badge' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300', 'header' => 'bg-slate-50 dark:bg-slate-900', 'dot' => 'bg-slate-400'],
];

$c = $colorMap[$color] ?? $colorMap['slate'];
$count = $items->count();
$sectionId = 'trash-' . Str::slug($title);

// ✅ Closure au lieu de fonction nommée
$getNestedValue = function($item, $path) {
$keys = explode('.', $path);
$value = $item;
foreach ($keys as $key) {
if (is_object($value)) {
$value = $value->{$key} ?? null;
} elseif (is_array($value)) {
$value = $value[$key] ?? null;
} else {
return null;
}
}
return $value;
};
@endphp

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
    x-data="{ open: true }">

    {{-- ── Header accordéon ── --}}
    <button type="button"
        @click="open = !open"
        class="flex w-full items-center justify-between px-5 py-4 text-left transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/60"
        :aria-expanded="open">

        <div class="flex items-center gap-3">
            {{-- Icône colorée --}}
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $c['icon'] }}">
                @if ($icon)
                {!! $icon !!}
                @else
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                @endif
            </div>

            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $title }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    {{ $count }} {{ $count <= 1 ? 'élément supprimé' : 'éléments supprimés' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($count > 0)
            <span class="rounded-full {{ $c['badge'] }} px-2.5 py-0.5 text-xs font-semibold">
                {{ $count }}
            </span>
            @endif
            <svg class="h-4 w-4 text-slate-400 transition-transform duration-200"
                :class="open ? 'rotate-180' : ''"
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>

    {{-- ── Corps ── --}}
    <div x-show="open"
        x-collapse
        class="border-t border-slate-100 dark:border-slate-700/60">

        @if ($count === 0)
        {{-- État vide --}}
        <div class="flex flex-col items-center justify-center px-6 py-10 text-center">
            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun élément supprimé</p>
            <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">Cette corbeille est vide</p>
        </div>

        @else
        {{-- Tableau --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50">
                        @foreach ($columns as $col)
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            {{ $col }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach ($items as $item)
                    <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/40">

                        {{-- Colonne principale (relation ou champ) --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                {{-- Avatar initiales --}}
                                @php
                                $mainLabel = $relationColumn
                                ? $getNestedValue($item, $relationColumn)
                                : (isset($fields[0]) ? $getNestedValue($item, $fields[0]) : '—');
                                $initials = collect(explode(' ', (string) $mainLabel))
                                ->take(2)
                                ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                ->implode('');
                                @endphp
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold {{ $c['icon'] }}">
                                    {{ $initials ?: '?' }}
                                </div>
                                <span class="font-medium text-slate-900 dark:text-white">
                                    {{ $mainLabel ?? '—' }}
                                </span>
                            </div>
                        </td>

                        {{-- Colonnes supplémentaires (fields[1..n] ou periode) --}}
                        @if ($periodColumn)
                        <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400">
                            {{ \Carbon\Carbon::parse($item->{$periodColumn[0]})->format('d/m/Y') }}
                            →
                            {{ \Carbon\Carbon::parse($item->{$periodColumn[1]})->format('d/m/Y') }}
                        </td>
                        @else
                        @foreach (array_slice($fields, 1) as $field)
                        <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400">
                            {{ $getNestedValue($item, $field) ?? '—' }}
                        </td>
                        @endforeach
                        @endif

                        {{-- Date de suppression --}}
                        <td class="px-5 py-3.5 text-xs text-slate-400 dark:text-slate-500">
                            @if ($item->deleted_at)
                            <span title="{{ $item->deleted_at->format('d/m/Y H:i') }}">
                                {{ $item->deleted_at->diffForHumans() }}
                            </span>
                            @else
                            —
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">

                                {{-- Restaurer --}}
                                <form method="POST"
                                    action="{{ route($restoreRoute, $item->id) }}"
                                    class="inline">
                                    @csrf
                                    @method($restoreMethod)
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 transition-colors hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-400 dark:hover:bg-emerald-900">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Restaurer
                                    </button>
                                </form>

                                {{-- Supprimer définitivement --}}
                                <form method="POST"
                                    action="{{ route($forceDeleteRoute, $item->id) }}"
                                    class="inline"
                                    onsubmit="return confirm('Supprimer définitivement cet élément ? Cette action est irréversible.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-100 dark:border-red-800 dark:bg-red-950 dark:text-red-400 dark:hover:bg-red-900">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
        @endif

    </div>
</div>