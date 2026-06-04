@props(['status' => 'pending'])
@php
    $map = [
        'pending'           => ['À faire', 'bg-slate-100 text-slate-600 dark:bg-slate-700/40 dark:text-slate-300'],
        'in_progress'       => ['En cours', 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'blocked'           => ['Bloquée', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'changes_requested' => ['Corrections demandées', 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
        'completed'         => ['Terminée', 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'],
    ];
    [$label, $classes] = $map[$status] ?? [$status, 'bg-slate-100 text-slate-600'];
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium $classes"]) }}>{{ $label }}</span>
