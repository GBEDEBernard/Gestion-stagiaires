@props(['priority' => 'normal', 'withLabel' => false])
@php
    $map = [
        'low'    => ['Basse', 'bg-slate-400'],
        'normal' => ['Normale', 'bg-blue-500'],
        'high'   => ['Haute', 'bg-amber-500'],
        'urgent' => ['Urgente', 'bg-red-500'],
    ];
    [$label, $color] = $map[$priority] ?? ['—', 'bg-slate-400'];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5']) }} title="Priorité : {{ $label }}">
    <span class="w-2 h-2 rounded-full {{ $color }} flex-shrink-0"></span>
    @if($withLabel)<span class="text-xs text-slate-500 dark:text-slate-400">{{ $label }}</span>@endif
</span>
