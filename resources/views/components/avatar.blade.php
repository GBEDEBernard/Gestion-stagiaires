@props([
    'name' => '?',
    'size' => 'md',   // xs | sm | md | lg
    'ring' => false,
])
@php
    $clean = trim((string) $name);
    $parts = preg_split('/\s+/', $clean ?: '?');
    $initials = mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1) . (isset($parts[1]) ? mb_substr($parts[1], 0, 1) : ''));

    // Couleur déterministe (palette douce, cohérente clair/sombre).
    $palette = [
        'bg-rose-500', 'bg-orange-500', 'bg-amber-500', 'bg-emerald-500',
        'bg-teal-500', 'bg-sky-500', 'bg-indigo-500', 'bg-violet-500', 'bg-fuchsia-500',
    ];
    $color = $palette[crc32($clean) % count($palette)];

    $sizes = [
        'xs' => 'w-6 h-6 text-[10px]',
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
    ];
    $dim = $sizes[$size] ?? $sizes['md'];
    $ringCls = $ring ? 'ring-2 ring-white dark:ring-slate-800' : '';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full font-semibold text-white select-none $color $dim $ringCls"]) }}
      title="{{ $clean }}">{{ $initials }}</span>
