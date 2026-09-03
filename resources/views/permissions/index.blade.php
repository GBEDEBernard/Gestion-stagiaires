<x-app-layout title="Mes demandes de permission">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8"
    x-data="permissionsApp()"
    x-init="init()">

    {{-- ── FLASH MESSAGES ── --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
        class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 shadow-sm">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
        <button @click="show=false" class="ml-auto text-emerald-400 hover:text-emerald-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 shadow-sm">
        <ul class="space-y-1 text-sm">@foreach($errors->all() as $e)<li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- ── HEADER ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                Demandes de permission
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gérez vos absences, retards et permissions auprès de vos responsables.</p>
        </div>
        <button @click="openModal()"
            class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-violet-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-violet-500/30 hover:shadow-violet-500/50 hover:scale-105 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle demande
        </button>
    </div>

    {{-- ── STATS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $statItems = [
            ['label'=>'Total','value'=>$stats['total'],'color'=>'slate','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label'=>'En attente','value'=>$stats['pending'],'color'=>'amber','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Approuvées','value'=>$stats['approved'],'color'=>'emerald','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Refusées','value'=>$stats['rejected'],'color'=>'red','icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        $colorMap = ['slate'=>['bg'=>'bg-slate-100 dark:bg-slate-800','text'=>'text-slate-700 dark:text-slate-300','icon'=>'text-slate-500','val'=>'text-slate-900 dark:text-white'],'amber'=>['bg'=>'bg-amber-50 dark:bg-amber-900/20','text'=>'text-amber-700 dark:text-amber-300','icon'=>'text-amber-500','val'=>'text-amber-700 dark:text-amber-300'],'emerald'=>['bg'=>'bg-emerald-50 dark:bg-emerald-900/20','text'=>'text-emerald-700 dark:text-emerald-300','icon'=>'text-emerald-500','val'=>'text-emerald-700 dark:text-emerald-300'],'red'=>['bg'=>'bg-red-50 dark:bg-red-900/20','text'=>'text-red-700 dark:text-red-300','icon'=>'text-red-500','val'=>'text-red-700 dark:text-red-300']];
        @endphp
        @foreach($statItems as $s)
        @php $c=$colorMap[$s['color']]; @endphp
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="{{ $c['bg'] }} rounded-xl p-2.5">
                    <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold {{ $c['text'] }} uppercase tracking-wide">{{ $s['label'] }}</span>
            </div>
            <p class="text-3xl font-bold {{ $c['val'] }}">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── FILTRES ── --}}
    <form method="GET" action="{{ route('permissions.index') }}"
        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 shadow-sm">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-36">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Statut</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-3 py-2 focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none transition">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>En attente</option>
                    <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approuvé</option>
                    <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Refusé</option>
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Annulé</option>
                </select>
            </div>
            <div class="flex-1 min-w-36">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Type</label>
                <select name="type_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-3 py-2 focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none transition">
                    <option value="">Tous types</option>
                    @foreach($types as $t)
                    <option value="{{ $t->id }}" {{ request('type_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-32">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Du</label>
                <input type="date" name="from" value="{{ request('from') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-3 py-2 focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none transition">
            </div>
            <div class="flex-1 min-w-32">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Au</label>
                <input type="date" name="to" value="{{ request('to') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-3 py-2 focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none transition">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition-colors">
                    Filtrer
                </button>
                @if(request()->hasAny(['status','type_id','from','to']))
                <a href="{{ route('permissions.index') }}"
                    class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-semibold hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- ── LISTE DES DEMANDES ── --}}
    <div class="space-y-3">
        @forelse($requests as $req)
        @php
        $statusColors = [
            'pending'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            'cancelled'=> 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
        ];
        $typeColorBg = [
            'red'    => 'bg-red-500/10 text-red-400 border-red-500/20',
            'amber'  => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            'orange' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
            'purple' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
            'green'  => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            'blue'   => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
        ];
        $tc = $typeColorBg[$req->type->color] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
        $sc = $statusColors[$req->status] ?? 'bg-slate-100 text-slate-600';
        @endphp
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-violet-200 dark:hover:border-violet-800 transition-all duration-200 group">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                {{-- Type badge --}}
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl border {{ $tc }} flex items-center justify-center text-lg">
                        @php
                        $icons = ['calendar-x'=>'📅','clock'=>'⏰','log-out'=>'🚪','gift'=>'🎁'];
                        echo $icons[$req->type->icon] ?? '📋';
                        @endphp
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $req->type->name }}</p>
                        <p class="text-xs text-slate-400">{{ $req->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-600 dark:text-slate-400 truncate">{{ $req->fieldSummary() ?: 'Aucun détail disponible' }}</p>
                    {{-- Recipients --}}
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        @foreach($req->recipients as $r)
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full
                            @if($r->status==='validated') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300
                            @elseif($r->status==='rejected') bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300
                            @elseif($r->status==='skipped') bg-slate-100 text-slate-500
                            @else bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300
                            @endif">
                            {{ $r->signataire->nom }}
                            @if($r->status==='validated') ✓
                            @elseif($r->status==='rejected') ✕
                            @elseif($r->status==='skipped') –
                            @else ···
                            @endif
                        </span>
                        @endforeach
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $sc }}">
                        {{ $req->statusLabel() }}
                    </span>
                    <button @click="viewDetail({{ $req->id }})"
                        class="p-2 rounded-xl text-slate-400 hover:text-violet-600 hover:bg-violet-50 dark:hover:bg-violet-900/30 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    @if($req->isPending())
                    <form method="POST" action="{{ route('permissions.cancel', $req->id) }}"
                        onsubmit="return confirm('Annuler cette demande ?')">
                        @csrf
                        <button type="submit"
                            class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Decision feedback --}}
            @if($req->decision_comment && in_array($req->status, ['approved','rejected']))
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-start gap-2">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-xs text-slate-500 dark:text-slate-400 italic">{{ $req->decision_comment }}</p>
                <span class="ml-auto text-xs text-slate-400 flex-shrink-0">par {{ $req->decider?->name }}</span>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white dark:bg-slate-900 border border-dashed border-slate-300 dark:border-slate-700 rounded-3xl p-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-3xl bg-gradient-to-br from-violet-100 to-purple-100 dark:from-violet-900/30 dark:to-purple-900/30 flex items-center justify-center">
                <svg class="w-8 h-8 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-700 dark:text-slate-300 mb-1">Aucune demande trouvée</h3>
            <p class="text-sm text-slate-400 mb-4">Commencez par soumettre votre première demande de permission.</p>
            <button @click="openModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle demande
            </button>
        </div>
        @endforelse
    </div>

    {{-- ── PAGINATION ── --}}
    @if($requests->hasPages())
    <div class="flex justify-center">{{ $requests->links() }}</div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- ── MODAL NOUVELLE DEMANDE ── --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div x-show="modalOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>

        {{-- Panel --}}
        <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">

            {{-- Modal header --}}
            <div class="flex items-center justify-between px-7 pt-7 pb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Nouvelle demande</h2>
                        <p class="text-xs text-slate-400">
                            <span x-show="step===1">Étape 1 — Choisissez un type</span>
                            <span x-show="step===2">Étape 2 — Complétez votre demande</span>
                        </p>
                    </div>
                </div>
                {{-- Step indicator --}}
                <div class="flex items-center gap-2 mr-10">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold" :class="step>=1?'bg-violet-600 text-white':'bg-slate-200 text-slate-500'">1</div>
                    <div class="w-8 h-0.5 rounded" :class="step>=2?'bg-violet-600':'bg-slate-200'"></div>
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold" :class="step>=2?'bg-violet-600 text-white':'bg-slate-200 text-slate-500'">2</div>
                </div>
                <button @click="closeModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="flex-1 overflow-y-auto px-7 py-6">

                {{-- STEP 1 — Type selection --}}
                <div x-show="step===1">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Sélectionnez le type de permission que vous souhaitez demander :</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($types as $t)
                        @php
                        $typeIcons = ['calendar-x'=>'📅','clock'=>'⏰','log-out'=>'🚪','gift'=>'🎁'];
                        $tc2 = $typeColorBg[$t->color] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                        @endphp
                        <button type="button" @click="selectType({{ $t->id }}, '{{ addslashes($t->name) }}', @js($t->fields_config))"
                            class="group relative flex items-start gap-4 p-4 rounded-2xl border-2 text-left transition-all duration-200 hover:scale-[1.02]"
                            :class="selectedTypeId==={{ $t->id }} ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20 shadow-md shadow-violet-500/10' : 'border-slate-200 dark:border-slate-700 hover:border-violet-300 dark:hover:border-violet-600 bg-white dark:bg-slate-800'">
                            <div class="flex-shrink-0 w-11 h-11 rounded-2xl border {{ $tc2 }} flex items-center justify-center text-xl">{{ $typeIcons[$t->icon] ?? '📋' }}</div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 group-hover:text-violet-700 dark:group-hover:text-violet-300 transition-colors">{{ $t->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">{{ $t->description }}</p>
                            </div>
                            <div x-show="selectedTypeId==={{ $t->id }}" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-violet-500 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- STEP 2 — Form --}}
                <div x-show="step===2">
                    <form id="permissionForm" method="POST" action="{{ route('permissions.store') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="permission_type_id" :value="selectedTypeId">

                        {{-- Dynamic fields --}}
                        <template x-for="field in selectedFields" :key="field.key">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5" x-text="field.label + (field.required ? ' *' : '')"></label>
                                <template x-if="field.type==='date'">
                                    <input type="date" :name="'fields_data['+field.key+']'"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none transition"
                                        :required="field.required">
                                </template>
                                <template x-if="field.type==='time'">
                                    <input type="time" :name="'fields_data['+field.key+']'"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none transition"
                                        :required="field.required">
                                </template>
                                <template x-if="field.type==='textarea'">
                                    <textarea :name="'fields_data['+field.key+']'" rows="3"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none transition resize-none"
                                        :required="field.required"></textarea>
                                </template>
                                <template x-if="field.type==='text'">
                                    <input type="text" :name="'fields_data['+field.key+']'"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none transition"
                                        :required="field.required">
                                </template>
                            </div>
                        </template>

                        {{-- Note optionnelle --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Note complémentaire <span class="text-slate-400 font-normal">(optionnel)</span></label>
                            <textarea name="note" rows="2"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none transition resize-none"
                                placeholder="Informations complémentaires..."></textarea>
                        </div>

                        {{-- Destinataires --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Destinataires / Signataires <span class="text-red-500">*</span></label>
                            <p class="text-xs text-slate-400 mb-3">Sélectionnez les responsables auxquels envoyer cette demande. La première validation reçue sera définitive.</p>
                            <div class="space-y-2 max-h-44 overflow-y-auto pr-1">
                                @foreach($signataires as $sig)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-violet-50 dark:hover:bg-violet-900/20 hover:border-violet-300 dark:hover:border-violet-700 cursor-pointer transition-all duration-150 group">
                                    <input type="checkbox" name="signataire_ids[]" value="{{ $sig->id }}"
                                        class="w-4 h-4 rounded text-violet-600 border-slate-300 focus:ring-violet-500">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($sig->nom, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $sig->nom }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $sig->poste }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal footer --}}
            <div class="flex items-center justify-between px-7 py-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <button x-show="step===2" @click="step=1"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Retour
                </button>
                <div x-show="step===1" class="text-xs text-slate-400">Sélectionnez un type pour continuer</div>

                <div class="flex gap-2 ml-auto">
                    <button @click="closeModal()"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Annuler
                    </button>
                    <button x-show="step===1" @click="goToStep2()"
                        :disabled="!selectedTypeId"
                        :class="selectedTypeId ? 'bg-violet-600 hover:bg-violet-700 cursor-pointer' : 'bg-slate-300 cursor-not-allowed'"
                        class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white transition-colors">
                        Suivant
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button x-show="step===2" @click="submitForm()"
                        class="flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-violet-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-violet-500/30 hover:shadow-violet-500/50 hover:scale-105 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Soumettre
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- ── MODAL DÉTAIL ── --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div x-show="detailOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="detailOpen=false"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg max-h-[85vh] overflow-y-auto"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-7 pt-7 pb-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="detail.type || 'Détail de la demande'"></h3>
                <button @click="detailOpen=false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-7 py-6 space-y-5" x-show="!detailLoading">
                {{-- Status --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Statut</span>
                    <span class="text-sm font-semibold px-3 py-1 rounded-full"
                        :class="{
                            'bg-amber-100 text-amber-700': detail.status==='pending',
                            'bg-emerald-100 text-emerald-700': detail.status==='approved',
                            'bg-red-100 text-red-700': detail.status==='rejected',
                            'bg-slate-100 text-slate-600': detail.status==='cancelled'
                        }" x-text="detail.status_label"></span>
                </div>

                {{-- Fields --}}
                <div class="space-y-2">
                    <template x-for="field in (detail.fields_config||[])" :key="field.key">
                        <div x-show="detail.fields_data && detail.fields_data[field.key]" class="flex justify-between py-2.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-sm text-slate-500" x-text="field.label"></span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="detail.fields_data ? detail.fields_data[field.key] : ''"></span>
                        </div>
                    </template>
                </div>

                {{-- Note --}}
                <div x-show="detail.note" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-1">Note</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300" x-text="detail.note"></p>
                </div>

                {{-- Decision --}}
                <div x-show="detail.decided_at" class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Décision</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300">Par <strong x-text="detail.decider"></strong> — <span x-text="detail.decided_at"></span></p>
                    <p x-show="detail.decision_comment" class="text-sm text-slate-500 mt-1 italic" x-text="'« ' + detail.decision_comment + ' »'"></p>
                </div>

                {{-- Recipients --}}
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Destinataires</p>
                    <div class="space-y-2">
                        <template x-for="r in (detail.recipients||[])" :key="r.nom">
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white text-xs font-bold flex-shrink-0" x-text="r.nom.substring(0,2).toUpperCase()"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="r.nom"></p>
                                    <p class="text-xs text-slate-500" x-text="r.poste"></p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full font-semibold"
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

                <p class="text-xs text-slate-400 text-right" x-text="'Soumis le ' + detail.created_at"></p>
            </div>

            {{-- Loading --}}
            <div x-show="detailLoading" class="py-16 text-center">
                <div class="w-8 h-8 mx-auto border-2 border-violet-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm text-slate-400 mt-3">Chargement...</p>
            </div>
        </div>
    </div>

</div>

<script>
function permissionsApp() {
    return {
        modalOpen: false,
        detailOpen: false,
        detailLoading: false,
        step: 1,
        selectedTypeId: null,
        selectedTypeName: '',
        selectedFields: [],
        detail: {},

        init() {
            @if($errors->any())
            this.modalOpen = true;
            this.step = 2;
            this.selectedTypeId = {{ old('permission_type_id', 'null') }};
            @endif
        },

        openModal() {
            this.step = 1;
            this.selectedTypeId = null;
            this.selectedTypeName = '';
            this.selectedFields = [];
            this.modalOpen = true;
        },

        closeModal() {
            this.modalOpen = false;
        },

        selectType(id, name, fields) {
            this.selectedTypeId = id;
            this.selectedTypeName = name;
            this.selectedFields = fields;
        },

        goToStep2() {
            if (!this.selectedTypeId) return;
            this.step = 2;
        },

        submitForm() {
            document.getElementById('permissionForm').submit();
        },

        async viewDetail(id) {
            this.detail = {};
            this.detailLoading = true;
            this.detailOpen = true;
            try {
                const resp = await fetch(`/permissions/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.detail = await resp.json();
            } catch(e) {
                this.detail = { type: 'Erreur', status_label: 'Impossible de charger' };
            } finally {
                this.detailLoading = false;
            }
        }
    }
}
</script>
</x-app-layout>
