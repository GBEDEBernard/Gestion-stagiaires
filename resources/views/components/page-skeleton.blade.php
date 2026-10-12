@php
    $r = Route::currentRouteName() ?? '';
@endphp

{{-- ── CONTENEUR PRINCIPAL DU SKELETON (Rendu serveur immédiat) ── --}}
<div id="page-skeleton-overlay" class="w-full flex-1 transition-opacity duration-200 pointer-events-none">
    <div id="page-skeleton-content" class="w-full">
        @if(request()->routeIs('student.stage') || $r === 'student.stage')
            <x-skeletons.mon-stage />
        @elseif(request()->routeIs('attendance.tracking.*') || $r === 'attendance.tracking.index')
            <x-skeletons.attendance-tracking />
        @elseif(request()->routeIs('admin.presence.anomalies*') || $r === 'admin.presence.anomalies')
            <x-skeletons.anomalies />
        @elseif(request()->routeIs('presence.pointage') || request()->routeIs('presence.index'))
            <x-skeletons.pointage />
        @elseif(request()->routeIs('presence.historique'))
            <x-skeletons.historique />
        @elseif(request()->routeIs('tasks.*'))
            <x-skeletons.tasks />
        @elseif(request()->routeIs('permissions.*'))
            <x-skeletons.permissions />
        @elseif(request()->routeIs('reports.*'))
            <x-skeletons.reports />
        @elseif(request()->routeIs('summaries.*'))
            <x-skeletons.summaries />
        @elseif(request()->routeIs('profile.*'))
            <x-skeletons.profile />
        @elseif(request()->routeIs('notifications.*'))
            <x-skeletons.notifications />
        @elseif(request()->routeIs('employes.*'))
            <x-skeletons.employes-index />
        @elseif(request()->routeIs('dashboard'))
            @if(auth()->user()?->hasRole('employe'))
                <x-skeletons.employe-dashboard />
            @else
                <x-skeletons.dashboard />
            @endif
        @else
            <x-skeletons.generic />
        @endif
    </div>
</div>

{{-- ── TEMPLATES CLIENTS POUR TRANSITION INSTANTANÉE AU CLIC ── --}}
<template id="sk-tpl-mon-stage"><x-skeletons.mon-stage /></template>
<template id="sk-tpl-attendance-tracking"><x-skeletons.attendance-tracking /></template>
<template id="sk-tpl-anomalies"><x-skeletons.anomalies /></template>
<template id="sk-tpl-pointage"><x-skeletons.pointage /></template>
<template id="sk-tpl-historique"><x-skeletons.historique /></template>
<template id="sk-tpl-tasks"><x-skeletons.tasks /></template>
<template id="sk-tpl-permissions"><x-skeletons.permissions /></template>
<template id="sk-tpl-reports"><x-skeletons.reports /></template>
<template id="sk-tpl-summaries"><x-skeletons.summaries /></template>
<template id="sk-tpl-profile"><x-skeletons.profile /></template>
<template id="sk-tpl-notifications"><x-skeletons.notifications /></template>
<template id="sk-tpl-dashboard"><x-skeletons.dashboard /></template>
<template id="sk-tpl-employe-dashboard"><x-skeletons.employe-dashboard /></template>
<template id="sk-tpl-employes"><x-skeletons.employes-index /></template>
<template id="sk-tpl-generic"><x-skeletons.generic /></template>
