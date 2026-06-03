@props(['percent' => 0, 'showLabel' => true])
@php
    $p = max(0, min(100, (int) $percent));
    $color = $p >= 100 ? 'bg-emerald-500' : ($p >= 50 ? 'bg-blue-500' : 'bg-amber-500');
@endphp
<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <div class="flex-1 h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
        {{-- width dynamique => style inline obligatoire (valeur runtime, non exprimable en Tailwind) --}}
        <div class="h-full {{ $color }} rounded-full transition-all duration-300" style="width: {{ $p }}%"></div>
    </div>
    @if($showLabel)
        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 w-9 text-right">{{ $p }}%</span>
    @endif
</div>
