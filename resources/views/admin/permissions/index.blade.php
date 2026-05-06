<x-app-layout title="Gestion des permissions">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8"
    x-data="adminPermissionsApp()"
    x-init="init()">

    {{-- ── HEADER ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </span>
                Demandes de permission
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Vue d'ensemble et gestion de toutes les demandes soumises par les stagiaires.</p>
        </div>
        <div class="flex items-center gap-3 text-xs text-slate-400">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Temps réel
            </span>
        </div>
    </div>

    {{-- ── STATS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $statItems = [
            ['label'=>'Total','value'=>$stats['total'],'color'=>'indigo','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','sub'=>'toutes périodes'],
            ['label'=>'En attente','value'=>$stats['pending'],'color'=>'amber','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','sub'=>'à traiter'],
            ['label'=>'Approuvées','value'=>$stats['approved'],'color'=>'emerald','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','sub'=>'validées'],
            ['label'=>'Refusées','value'=>$stats['rejected'],'color'=>'red','icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z','sub'=>'rejetées'],
        ];
        $colorMap = [
            'indigo' =>['grad'=>'from-indigo-500 to-violet-600','bg'=>'bg-indigo-50 dark:bg-indigo-900/20','text'=>'text-indigo-700 dark:text-indigo-300','icon'=>'text-indigo-500','shadow'=>'shadow-indigo-500/20'],
            'amber'  =>['grad'=>'from-amber-400 to-orange-500','bg'=>'bg-amber-50 dark:bg-amber-900/20','text'=>'text-amber-700 dark:text-amber-300','icon'=>'text-amber-500','shadow'=>'shadow-amber-500/20'],
            'emerald'=>['grad'=>'from-emerald-400 to-teal-500','bg'=>'bg-emerald-50 dark:bg-emerald-900/20','text'=>'text-emerald-700 dark:text-emerald-300','icon'=>'text-emerald-500','shadow'=>'shadow-emerald-500/20'],
            'red'    =>['grad'=>'from-red-400 to-rose-600','bg'=>'bg-red-50 dark:bg-red-900/20','text'=>'text-red-700 dark:text-red-300','icon'=>'text-red-500','shadow'=>'shadow-red-500/20'],
        ];
        @endphp
        @foreach($statItems as $s)
        @php $c=$colorMap[$s['color']]; @endphp
        <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl p-5 shadow-sm hover:shadow-lg {{ $c['shadow'] }} transition-all duration-300 overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br {{ $c['grad'] }} opacity-0 group-hover:opacity-[0.03] transition-opacity duration-300 rounded-3xl"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="{{ $c['bg'] }} rounded-2xl p-2.5">
                    <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold {{ $c['text'] }} uppercase tracking-widest">{{ $s['label'] }}</span>
                    <p class="text-[10px] text-slate-400 leading-none mt-0.5">{{ $s['sub'] }}</p>
                </div>
            </div>
            <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── FILTRES AVANCÉS ── --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtres
            </div>
            @if(request()->hasAny(['status','type_id','user_id','from','to']))
            <a href="{{ route('admin.permissions.index') }}"
                class="text-xs text-red-500 hover:text-red-700 font-semibold flex items-center gap-1 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Réinitialiser
            </a>
            @endif
        </div>
        <form method="GET" action="{{ route('admin.permissions.index') }}" class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                {{-- Utilisateur --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Stagiaire</label>
                    <select name="user_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition">
                        <option value="">Tous</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Statut --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Statut</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition">
                        <option value="">Tous</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>En attente</option>
                        <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approuvé</option>
                        <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Refusé</option>
                        <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Annulé</option>
                    </select>
                </div>
                {{-- Type --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Type</label>
                    <select name="type_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition">
                        <option value="">Tous types</option>
                        @foreach($types as $t)
                        <option value="{{ $t->id }}" {{ request('type_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Dates --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Du</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Au</label>
                    <div class="flex gap-2">
                        <input type="date" name="to" value="{{ request('to') }}"
                            class="flex-1 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition">
                        <button type="submit"
                            class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-colors flex-shrink-0 shadow-md shadow-indigo-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ── TABLEAU DES DEMANDES ── --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">

        {{-- Table header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                    {{ $requests->total() }} demande{{ $requests->total() > 1 ? 's' : '' }}
                </span>
                @if(request()->hasAny(['status','type_id','user_id','from','to']))
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                    Filtres actifs
                </span>
                @endif
            </div>
            <span class="text-xs text-slate-400">Page {{ $requests->currentPage() }} / {{ $requests->lastPage() }}</span>
        </div>

        {{-- Desktop table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800">
                        <th class="text-left px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Stagiaire</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Type</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Détails</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Destinataires</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Statut</th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($requests as $req)
                    @php
                    $statusConf = [
                        'pending'   => ['badge'=>'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300','dot'=>'bg-amber-400','label'=>'En attente'],
                        'approved'  => ['badge'=>'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300','dot'=>'bg-emerald-400','label'=>'Approuvé'],
                        'rejected'  => ['badge'=>'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300','dot'=>'bg-red-400','label'=>'Refusé'],
                        'cancelled' => ['badge'=>'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400','dot'=>'bg-slate-400','label'=>'Annulé'],
                    ];
                    $typeColorBg = [
                        'red'    => 'bg-red-500/10 text-red-500 border-red-500/20',
                        'amber'  => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                        'orange' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                        'purple' => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
                        'green'  => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                        'blue'   => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                    ];
                    $tc = $typeColorBg[$req->type->color] ?? 'bg-slate-500/10 text-slate-500 border-slate-500/20';
                    $sc = $statusConf[$req->status] ?? $statusConf['cancelled'];
                    $typeIcons = ['calendar-x'=>'📅','clock'=>'⏰','log-out'=>'🚪','gift'=>'🎁'];
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 group">
                        {{-- Stagiaire --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-md shadow-indigo-500/20">
                                    {{ strtoupper(substr($req->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $req->user->name }}</p>
                                    <p class="text-xs text-slate-400 truncate max-w-[130px]">{{ $req->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- Type --}}
                        <td class="px-4 py-4">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border {{ $tc }} text-xs font-semibold">
                                <span>{{ $typeIcons[$req->type->icon] ?? '📋' }}</span>
                                {{ $req->type->name }}
                            </div>
                        </td>
                        {{-- Détails --}}
                        <td class="px-4 py-4 max-w-[180px]">
                            <p class="text-xs text-slate-600 dark:text-slate-400 truncate">{{ $req->fieldSummary() ?: '—' }}</p>
                            @if($req->note)
                            <p class="text-[10px] text-slate-400 italic mt-0.5 truncate">💬 {{ $req->note }}</p>
                            @endif
                        </td>
                        {{-- Destinataires --}}
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($req->recipients as $r)
                                <span class="inline-flex items-center gap-0.5 text-[10px] px-1.5 py-0.5 rounded-full font-semibold
                                    @if($r->status==='validated') bg-emerald-100 text-emerald-700
                                    @elseif($r->status==='rejected') bg-red-100 text-red-700
                                    @elseif($r->status==='skipped') bg-slate-100 text-slate-500
                                    @else bg-amber-100 text-amber-700
                                    @endif"
                                    title="{{ $r->signataire->poste }}">
                                    {{ Str::limit($r->signataire->nom, 12) }}
                                    @if($r->status==='validated') ✓
                                    @elseif($r->status==='rejected') ✕
                                    @elseif($r->status==='skipped') –
                                    @else ···
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </td>
                        {{-- Date --}}
                        <td class="px-4 py-4">
                            <p class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $req->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-slate-400">{{ $req->created_at->format('H:i') }}</p>
                        </td>
                        {{-- Statut --}}
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $sc['badge'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }} @if($req->status==='pending') animate-pulse @endif"></span>
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        {{-- Actions --}}
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Voir détail --}}
                                <button @click="viewDetail({{ $req->id }})"
                                    class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-all duration-150"
                                    title="Voir le détail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                {{-- Approuver / Refuser si pending --}}
                                @if($req->isPending())
                                <button @click="openDecide({{ $req->id }}, '{{ addslashes($req->user->name) }}', '{{ addslashes($req->type->name) }}')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-md shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:scale-105 transition-all duration-150">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Décider
                                </button>
                                @else
                                <span class="text-[10px] text-slate-400 italic px-2">
                                    {{ $req->decided_at?->diffForHumans() }}
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-3xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/30 dark:to-violet-900/30 flex items-center justify-center">
                                <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-slate-600 dark:text-slate-300 mb-1">Aucune demande trouvée</h3>
                            <p class="text-sm text-slate-400">Aucun résultat ne correspond aux filtres sélectionnés.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="lg:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($requests as $req)
            @php
            $sc2 = $statusConf[$req->status] ?? $statusConf['cancelled'];
            $tc2 = $typeColorBg[$req->type->color] ?? 'bg-slate-500/10 text-slate-500 border-slate-500/20';
            @endphp
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($req->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $req->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $req->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $sc2['badge'] }} flex-shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full {{ $sc2['dot'] }}"></span>
                        {{ $sc2['label'] }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border {{ $tc2 }} text-xs font-semibold">
                        {{ $typeIcons[$req->type->icon] ?? '📋' }} {{ $req->type->name }}
                    </span>
                    <span class="text-xs text-slate-500 truncate flex-1">{{ $req->fieldSummary() ?: '' }}</span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex flex-wrap gap-1">
                        @foreach($req->recipients->take(3) as $r)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold
                            @if($r->status==='validated') bg-emerald-100 text-emerald-700
                            @elseif($r->status==='rejected') bg-red-100 text-red-700
                            @elseif($r->status==='skipped') bg-slate-100 text-slate-500
                            @else bg-amber-100 text-amber-700
                            @endif">{{ Str::limit($r->signataire->nom, 10) }}</span>
                        @endforeach
                        @if($req->recipients->count() > 3)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-500">+{{ $req->recipients->count()-3 }}</span>
                        @endif
                    </div>
                    <div class="flex gap-1.5">
                        <button @click="viewDetail({{ $req->id }})"
                            class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        @if($req->isPending())
                        <button @click="openDecide({{ $req->id }}, '{{ addslashes($req->user->name) }}', '{{ addslashes($req->type->name) }}')"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-md">
                            Décider
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="py-16 text-center text-slate-400 text-sm">Aucune demande</div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-center">
            {{ $requests->links() }}
        </div>
        @endif
    </div>


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- ── MODAL DÉTAIL ── --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="detailOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="detailOpen=false"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg max-h-[88vh] overflow-y-auto"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="sticky top-0 z-10 flex items-center justify-between px-7 pt-7 pb-5 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-t-3xl">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-white w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="detail.type || 'Détail'"></h3>
                        <p class="text-xs text-slate-400" x-text="detail.user || ''"></p>
                    </div>
                </div>
                <button @click="detailOpen=false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors absolute top-5 right-5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Content --}}
            <div x-show="!detailLoading" class="px-7 py-6 space-y-5">
                {{-- Status + User --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xs font-bold"
                            x-text="(detail.user || '??').substring(0,2).toUpperCase()"></div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100" x-text="detail.user"></p>
                            <p class="text-xs text-slate-400" x-text="detail.user_email"></p>
                        </div>
                    </div>
                    <span class="text-sm font-bold px-3 py-1.5 rounded-full"
                        :class="{
                            'bg-amber-100 text-amber-700': detail.status==='pending',
                            'bg-emerald-100 text-emerald-700': detail.status==='approved',
                            'bg-red-100 text-red-700': detail.status==='rejected',
                            'bg-slate-100 text-slate-600': detail.status==='cancelled'
                        }" x-text="detail.status_label"></span>
                </div>

                {{-- Champs dynamiques --}}
                <div class="rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden">
                    <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Informations de la demande</p>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        <template x-for="field in (detail.fields_config||[])" :key="field.key">
                            <div x-show="detail.fields_data && detail.fields_data[field.key]"
                                class="flex justify-between items-center px-4 py-3">
                                <span class="text-sm text-slate-500" x-text="field.label"></span>
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-100 text-right" x-text="detail.fields_data ? detail.fields_data[field.key] : ''"></span>
                            </div>
                        </template>
                        <template x-if="!detail.fields_config || detail.fields_config.length === 0">
                            <div class="px-4 py-3 text-sm text-slate-400 italic">Aucun détail</div>
                        </template>
                    </div>
                </div>

                {{-- Note --}}
                <div x-show="detail.note" class="flex items-start gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-4">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <div>
                        <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wide mb-1">Note complémentaire</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300" x-text="detail.note"></p>
                    </div>
                </div>

                {{-- Destinataires --}}
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Destinataires & validation</p>
                    <div class="space-y-2">
                        <template x-for="r in (detail.recipients||[])" :key="r.nom">
                            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                    x-text="r.nom.substring(0,2).toUpperCase()"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="r.nom"></p>
                                    <p class="text-xs text-slate-400" x-text="r.poste"></p>
                                </div>
                                <span class="text-xs px-2.5 py-1 rounded-full font-bold"
                                    :class="{
                                        'bg-emerald-100 text-emerald-700': r.state==='validated',
                                        'bg-red-100 text-red-700': r.state==='rejected',
                                        'bg-slate-200 text-slate-600': r.state==='skipped',
                                        'bg-amber-100 text-amber-700': r.state==='pending'
                                    }" x-text="r.status"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Décision --}}
                <div x-show="detail.decided_at" class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-4 border border-slate-100 dark:border-slate-700">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Décision rendue</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300">
                        Par <strong x-text="detail.decider"></strong>
                        <span class="text-slate-400"> · </span>
                        <span class="text-slate-500" x-text="detail.decided_at"></span>
                    </p>
                    <p x-show="detail.decision_comment" class="text-sm text-slate-500 italic mt-1.5"
                        x-text="'« ' + detail.decision_comment + ' »'"></p>
                </div>

                <p class="text-[11px] text-slate-400 text-right" x-text="'Soumis le ' + detail.created_at"></p>
            </div>

            {{-- Loading --}}
            <div x-show="detailLoading" class="py-20 text-center">
                <div class="w-10 h-10 mx-auto border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm text-slate-400 mt-3">Chargement...</p>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- ── MODAL DÉCISION ── --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="decideOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="decideOpen=false"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-md"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="px-7 pt-7 pb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Rendre une décision</h2>
                        <p class="text-xs text-slate-400">Cette action est irréversible et notifie immédiatement le stagiaire.</p>
                    </div>
                </div>
                <button @click="decideOpen=false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-7 py-6 space-y-5">
                {{-- Info de la demande --}}
                <div class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xs font-bold"
                            x-text="(decideTarget.user || 'XX').substring(0,2).toUpperCase()"></div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100" x-text="decideTarget.user"></p>
                            <p class="text-xs text-slate-500" x-text="decideTarget.type"></p>
                        </div>
                    </div>
                </div>

                {{-- Choix Approuver / Refuser --}}
                <div class="grid grid-cols-2 gap-3">
                    <button @click="decideChoice='approve'"
                        :class="decideChoice==='approve'
                            ? 'ring-2 ring-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700'
                            : 'border-slate-200 dark:border-slate-700 hover:border-emerald-300'"
                        class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all duration-200 group">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center"
                            :class="decideChoice==='approve' ? 'bg-emerald-500' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-emerald-50'">
                            <svg class="w-6 h-6" :class="decideChoice==='approve' ? 'text-white' : 'text-slate-400 group-hover:text-emerald-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold" :class="decideChoice==='approve' ? 'text-emerald-700' : 'text-slate-600 dark:text-slate-400'">Approuver</span>
                    </button>

                    <button @click="decideChoice='reject'"
                        :class="decideChoice==='reject'
                            ? 'ring-2 ring-red-500 bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700'
                            : 'border-slate-200 dark:border-slate-700 hover:border-red-300'"
                        class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all duration-200 group">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center"
                            :class="decideChoice==='reject' ? 'bg-red-500' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-red-50'">
                            <svg class="w-6 h-6" :class="decideChoice==='reject' ? 'text-white' : 'text-slate-400 group-hover:text-red-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold" :class="decideChoice==='reject' ? 'text-red-700' : 'text-slate-600 dark:text-slate-400'">Refuser</span>
                    </button>
                </div>

                {{-- Commentaire --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Commentaire
                        <span class="text-slate-400 font-normal" x-text="decideChoice==='reject' ? '(recommandé)' : '(optionnel)'"></span>
                    </label>
                    <textarea x-model="decideComment" rows="3"
                        :placeholder="decideChoice==='reject' ? 'Expliquez la raison du refus...' : 'Message optionnel pour le stagiaire...'"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition resize-none placeholder:text-slate-400"></textarea>
                </div>

                {{-- Avertissement --}}
                <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        La première décision validée sera définitive. Les autres destinataires seront automatiquement informés.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-7 py-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 rounded-b-3xl">
                <button @click="decideOpen=false"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Annuler
                </button>
                <button @click="submitDecision()"
                    :disabled="!decideChoice || submitting"
                    :class="decideChoice
                        ? (decideChoice==='approve' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 shadow-emerald-500/30' : 'bg-gradient-to-r from-red-500 to-rose-600 shadow-red-500/30')
                        : 'bg-slate-300 cursor-not-allowed'"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-bold shadow-lg hover:scale-105 transition-all duration-200 disabled:hover:scale-100">
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="submitting ? 'Enregistrement...' : (decideChoice==='approve' ? 'Confirmer l\'approbation' : (decideChoice==='reject' ? 'Confirmer le refus' : 'Choisissez une action'))"></span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function adminPermissionsApp() {
    return {
        // Detail modal
        detailOpen: false,
        detailLoading: false,
        detail: {},

        // Decide modal
        decideOpen: false,
        decideId: null,
        decideTarget: { user: '', type: '' },
        decideChoice: null,
        decideComment: '',
        submitting: false,

        init() {
            // nothing to init
        },

        async viewDetail(id) {
            this.detail = {};
            this.detailLoading = true;
            this.detailOpen = true;
            try {
                const resp = await fetch(`/admin/permissions/${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                this.detail = await resp.json();
            } catch(e) {
                this.detail = { type: 'Erreur', status_label: 'Impossible de charger', user: '' };
            } finally {
                this.detailLoading = false;
            }
        },

        openDecide(id, userName, typeName) {
            this.decideId = id;
            this.decideTarget = { user: userName, type: typeName };
            this.decideChoice = null;
            this.decideComment = '';
            this.submitting = false;
            this.decideOpen = true;
        },

        async submitDecision() {
            if (!this.decideChoice || this.submitting) return;

            this.submitting = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const resp = await fetch(`/admin/permissions/${this.decideId}/decide`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        decision: this.decideChoice,
                        comment: this.decideComment || null,
                    })
                });

                const data = await resp.json();

                if (resp.ok && data.success) {
                    this.decideOpen = false;
                    // Show success toast then reload
                    this.showToast(
                        this.decideChoice === 'approve' ? 'Permission approuvée avec succès ✓' : 'Permission refusée.',
                        this.decideChoice === 'approve' ? 'emerald' : 'red'
                    );
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    this.showToast(data.message || 'Une erreur s\'est produite.', 'red');
                    this.submitting = false;
                }
            } catch(e) {
                this.showToast('Erreur réseau. Veuillez réessayer.', 'red');
                this.submitting = false;
            }
        },

        showToast(message, color = 'emerald') {
            const toast = document.createElement('div');
            const colorClasses = {
                emerald: 'bg-emerald-50 border-emerald-200 text-emerald-800',
                red: 'bg-red-50 border-red-200 text-red-800',
                indigo: 'bg-indigo-50 border-indigo-200 text-indigo-800',
            };
            toast.className = `fixed top-5 right-5 z-[9999] flex items-center gap-3 px-5 py-4 rounded-2xl border shadow-xl text-sm font-semibold transition-all duration-300 ${colorClasses[color] || colorClasses.emerald}`;
            toast.innerHTML = `<span>${message}</span>`;
            document.body.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.style.transform = 'translateY(0)';
                toast.style.opacity = '1';
            });

            // Remove after 3s
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px)';
                setTimeout(() => toast.remove(), 300);
            }, 2800);
        }
    }
}
</script>
</x-app-layout>
