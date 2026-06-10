<x-app-layout title="Tâches">

    @php
        $user = auth()->user();
        $canCreate = $user->hasAnyRole(['etudiant', 'employe']);
        $qs = collect(['status' => $status, 'q' => request('q')])->filter()->all();
        $totalTasks = collect($stats ?? [])->sum();
        $activeTasks = ($stats['pending'] ?? 0) + ($stats['in_progress'] ?? 0) + ($stats['blocked'] ?? 0);
        $completionRate = $totalTasks > 0 ? round((($stats['completed'] ?? 0) / $totalTasks) * 100) : 0;
        $filterItems = [
            '' => ['label' => 'Toutes', 'count' => $totalTasks],
            'pending' => ['label' => 'À faire', 'count' => $stats['pending'] ?? 0],
            'in_progress' => ['label' => 'En cours', 'count' => $stats['in_progress'] ?? 0],
            'blocked' => ['label' => 'Bloquées', 'count' => $stats['blocked'] ?? 0],
            'completed' => ['label' => 'Terminées', 'count' => $stats['completed'] ?? 0],
        ];
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap');

        .ws-root * { font-family: 'DM Sans', system-ui, sans-serif; }
        .ws-mono { font-family: 'DM Mono', monospace; }

        @keyframes ws-in {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes ws-modal-in {
            from { opacity: 0; transform: scale(.97) translateY(8px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes ws-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0; transform: scale(1.9); }
        }

        .ws-appear          { animation: ws-in .4s cubic-bezier(.16,1,.3,1) both; }
        .ws-appear-2        { animation: ws-in .4s .06s cubic-bezier(.16,1,.3,1) both; }
        .ws-appear-3        { animation: ws-in .4s .12s cubic-bezier(.16,1,.3,1) both; }
        .ws-modal-appear    { animation: ws-modal-in .28s cubic-bezier(.16,1,.3,1) both; }
        .ws-live-ring       { animation: ws-pulse 1.8s ease-in-out infinite; }

        .ws-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,.08) transparent;
        }
        .ws-scroll::-webkit-scrollbar { width: 4px; }
        .ws-scroll::-webkit-scrollbar-track { background: transparent; }
        .ws-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,.1); border-radius: 99px; }
        .dark .ws-scroll { scrollbar-color: rgba(255,255,255,.08) transparent; }
        .dark .ws-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); }

        .ws-card {
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(0,0,0,.06);
            box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 32px rgba(0,0,0,.05);
            backdrop-filter: blur(20px);
        }
        .dark .ws-card {
            background: rgba(255,255,255,.035);
            border-color: rgba(255,255,255,.08);
            box-shadow: 0 1px 3px rgba(0,0,0,.3), 0 8px 32px rgba(0,0,0,.25);
        }

        .ws-task-item {
            border: 1px solid transparent;
            transition: border-color .18s, background .18s, transform .18s;
        }
        .ws-task-item:hover {
            background: rgba(0,0,0,.025);
            border-color: rgba(0,0,0,.06);
            transform: translateY(-1px);
        }
        .dark .ws-task-item:hover {
            background: rgba(255,255,255,.04);
            border-color: rgba(255,255,255,.08);
        }
        .ws-task-item.ws-active {
            background: #0a0a0a;
            border-color: transparent;
        }
        .dark .ws-task-item.ws-active {
            background: #fff;
        }

        .ws-input {
            background: rgba(0,0,0,.03);
            border: 1px solid rgba(0,0,0,.07);
            transition: border-color .15s, background .15s, box-shadow .15s;
        }
        .ws-input:focus {
            background: #fff;
            border-color: rgba(0,0,0,.2);
            box-shadow: 0 0 0 3px rgba(0,0,0,.05);
            outline: none;
        }
        .dark .ws-input {
            background: rgba(255,255,255,.04);
            border-color: rgba(255,255,255,.09);
            color: #fff;
        }
        .dark .ws-input:focus {
            background: rgba(255,255,255,.07);
            border-color: rgba(255,255,255,.2);
            box-shadow: 0 0 0 3px rgba(255,255,255,.06);
        }

        .ws-btn-primary {
            background: #0a0a0a;
            color: #fff;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,.15), 0 4px 16px rgba(0,0,0,.12);
            transition: transform .18s, box-shadow .18s, background .18s;
        }
        .ws-btn-primary:hover {
            background: #222;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0,0,0,.2), 0 8px 24px rgba(0,0,0,.16);
        }
        .dark .ws-btn-primary { background: #fff; color: #0a0a0a; }
        .dark .ws-btn-primary:hover { background: #e5e5e5; }

        .ws-filter-pill {
            transition: background .15s, color .15s;
            color: rgba(0,0,0,.45);
        }
        .ws-filter-pill:hover { background: rgba(0,0,0,.05); color: rgba(0,0,0,.8); }
        .ws-filter-pill.active { background: #0a0a0a; color: #fff; }
        .dark .ws-filter-pill { color: rgba(255,255,255,.4); }
        .dark .ws-filter-pill:hover { background: rgba(255,255,255,.07); color: rgba(255,255,255,.85); }
        .dark .ws-filter-pill.active { background: #fff; color: #0a0a0a; }

        .ws-stat-card {
            background: rgba(0,0,0,.025);
            border: 1px solid rgba(0,0,0,.06);
        }
        .dark .ws-stat-card {
            background: rgba(255,255,255,.03);
            border-color: rgba(255,255,255,.07);
        }

        .ws-range { accent-color: #0a0a0a; }
        .dark .ws-range { accent-color: #fff; }

        .ws-modal-backdrop {
            background: rgba(0,0,0,.4);
            backdrop-filter: blur(12px);
        }
        .dark .ws-modal-backdrop { background: rgba(0,0,0,.65); }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }
    </style>

    <div class="ws-root -m-3 sm:-m-4 md:-m-6 min-h-screen" style="background: #f7f7f6;" x-data="{ openCreate: {{ (isset($errors) && $errors->any()) && $canCreate ? 'true' : 'false' }} }">
        <div class="dark" style="display:none"></div>

        <div class="mx-auto max-w-[1560px] px-4 py-6 sm:px-6 sm:py-7 lg:px-8 lg:py-8" style="color: #0a0a0a;">

            {{-- ── HEADER ─────────────────────────────────────── --}}
            <header class="ws-appear mb-6">
                <div class="ws-card rounded-2xl p-5 sm:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" style="background:#0a0a0a;">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-[10px] font-semibold uppercase tracking-[.16em]" style="color:rgba(0,0,0,.4);">Workspace</span>
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="ws-live-ring absolute inline-flex h-full w-full rounded-full" style="background:#10b981; opacity:.5;"></span>
                                        <span class="relative inline-flex h-1.5 w-1.5 rounded-full" style="background:#10b981;"></span>
                                    </span>
                                </div>
                                <h1 class="text-xl font-semibold tracking-tight" style="letter-spacing:-.02em;">Espace de travail</h1>
                                <p class="mt-0.5 text-sm" style="color:rgba(0,0,0,.45);">Tâches, rapports et échanges en un seul endroit.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="ws-stat-card rounded-xl px-4 py-2.5 text-center min-w-[80px]">
                                    <p class="text-[10px] font-medium uppercase tracking-[.14em]" style="color:rgba(0,0,0,.4);">Actives</p>
                                    <p class="mt-0.5 text-2xl font-semibold ws-mono" style="letter-spacing:-.02em;">{{ $activeTasks }}</p>
                                </div>
                                <div class="ws-stat-card rounded-xl px-4 py-2.5 text-center min-w-[80px]">
                                    <p class="text-[10px] font-medium uppercase tracking-[.14em]" style="color:rgba(0,0,0,.4);">Terminées</p>
                                    <p class="mt-0.5 text-2xl font-semibold ws-mono" style="color:#059669; letter-spacing:-.02em;">{{ $stats['completed'] ?? 0 }}</p>
                                </div>
                                <div class="ws-stat-card rounded-xl px-4 py-2.5 text-center min-w-[80px]">
                                    <p class="text-[10px] font-medium uppercase tracking-[.14em]" style="color:rgba(0,0,0,.4);">Taux</p>
                                    <p class="mt-0.5 text-2xl font-semibold ws-mono" style="letter-spacing:-.02em;">{{ $completionRate }}%</p>
                                </div>
                            </div>

                            @if($canCreate)
                            <button type="button" @click="openCreate = true"
                                class="ws-btn-primary inline-flex h-10 items-center gap-2 rounded-xl px-4 text-sm font-medium">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                                </svg>
                                Nouvelle tâche
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            {{-- ── BODY ────────────────────────────────────────── --}}
            <div class="grid gap-4 lg:grid-cols-[340px_minmax(0,1fr)] xl:grid-cols-[380px_minmax(0,1fr)]">

                {{-- LIST PANEL --}}
                <aside class="ws-appear-2 min-w-0">
                    <div class="ws-card sticky top-20 rounded-2xl overflow-hidden">

                        {{-- Search --}}
                        <div class="p-3 border-b" style="border-color:rgba(0,0,0,.06);">
                            <form method="GET" action="{{ route('tasks.index') }}">
                                @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                                <label class="relative flex items-center">
                                    <svg class="pointer-events-none absolute left-3 h-3.5 w-3.5" style="color:rgba(0,0,0,.35);" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.2-5.2m1.7-4.3a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/>
                                    </svg>
                                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher…"
                                        class="ws-input h-9 w-full rounded-lg pl-9 pr-3 text-sm">
                                </label>
                            </form>

                            {{-- Filters --}}
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach($filterItems as $key => $item)
                                @php
                                    $isActive = ($key === '' && empty($status)) || (($status ?? null) === $key);
                                    $url = $key === ''
                                        ? route('tasks.index', array_filter(['q' => request('q')]))
                                        : route('tasks.index', array_filter(['status' => $key, 'q' => request('q')]));
                                @endphp
                                <a href="{{ $url }}"
                                   class="ws-filter-pill {{ $isActive ? 'active' : '' }} inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-medium">
                                    {{ $item['label'] }}
                                    <span class="ws-mono text-[10px] opacity-70">{{ $item['count'] }}</span>
                                </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Task list --}}
                        <div class="ws-scroll overflow-y-auto" style="max-height: calc(100vh - 20rem); min-height: 320px;">
                            @forelse($tasks as $t)
                            @php
                                $active = $selected && $selected->id === $t->id;
                                $pct = max(0, min(100, (int) $t->last_progress_percent));
                                $barColor = match($t->status) {
                                    'completed' => '#10b981',
                                    'blocked'   => '#ef4444',
                                    'in_progress' => '#3b82f6',
                                    default     => 'rgba(0,0,0,.25)',
                                };
                            @endphp
                            <a href="{{ encrypted_route('tasks.show', $t) }}{{ $qs ? '?'.http_build_query($qs) : '' }}"
                               class="ws-task-item {{ $active ? 'ws-active' : '' }} relative block rounded-xl mx-2 my-1 p-3 overflow-hidden">

                                {{-- Active indicator bar --}}
                                <div class="absolute left-0 inset-y-0 w-[3px] rounded-r-full" style="background: {{ $active ? '#fff' : $barColor }}; opacity: {{ $active ? '.5' : '1' }};"></div>

                                <div class="pl-2">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <p class="text-sm font-medium line-clamp-2 leading-5" style="color: {{ $active ? '#fff' : '#0a0a0a' }};">{{ $t->title }}</p>
                                        <span class="ws-mono shrink-0 text-[11px] font-medium" style="color: {{ $active ? 'rgba(255,255,255,.5)' : 'rgba(0,0,0,.35)' }};">{{ $pct }}%</span>
                                    </div>

                                    <div class="h-[2px] rounded-full overflow-hidden mb-2.5" style="background: {{ $active ? 'rgba(255,255,255,.15)' : 'rgba(0,0,0,.07)' }};">
                                        <div class="h-full rounded-full transition-all duration-500" style="width:{{ $pct }}%; background:{{ $active ? '#fff' : $barColor }};"></div>
                                    </div>

                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[11px] truncate" style="color: {{ $active ? 'rgba(255,255,255,.45)' : 'rgba(0,0,0,.35)' }};">{{ $t->owner?->name ?? '—' }}</span>
                                        @if($t->due_date)
                                        <span class="ws-mono text-[11px] shrink-0" style="color: {{ $active ? 'rgba(255,255,255,.4)' : ($t->isOverdue() ? '#ef4444' : 'rgba(0,0,0,.3)') }};">
                                            {{ $t->due_date->format('d/m/Y') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @empty
                            <div class="flex min-h-[280px] flex-col items-center justify-center px-6 text-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl mb-3" style="background:rgba(0,0,0,.05);">
                                    <svg class="h-5 w-5" style="color:rgba(0,0,0,.3);" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium" style="color:rgba(0,0,0,.7);">Aucune tâche</p>
                                <p class="mt-1 text-xs" style="color:rgba(0,0,0,.35);">Ajuste le filtre ou crée une tâche.</p>
                                @if($canCreate)
                                <button type="button" @click="openCreate = true"
                                    class="ws-btn-primary mt-4 inline-flex h-9 items-center gap-1.5 rounded-xl px-3.5 text-xs font-medium">
                                    Créer une tâche
                                </button>
                                @endif
                            </div>
                            @endforelse
                        </div>
                    </div>
                </aside>

                {{-- DETAIL PANEL --}}
                <main class="ws-appear-3 min-w-0">
                    @if($selected)
                        @include('tasks.partials.detail', ['task' => $selected, 'todayReport' => $todayReport])
                    @else
                    <div class="ws-card flex min-h-[560px] items-center justify-center rounded-2xl p-8 text-center"
                         style="border-style: dashed; border-color: rgba(0,0,0,.1); background: transparent; box-shadow: none;">
                        <div class="max-w-xs">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl mb-4" style="background:#0a0a0a;">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V4.75A2.75 2.75 0 0 1 10.75 2h2.5A2.75 2.75 0 0 1 16 4.75V7m-8 0h8m-8 0H6.75A2.75 2.75 0 0 0 4 9.75v8.5A2.75 2.75 0 0 0 6.75 21h10.5A2.75 2.75 0 0 0 20 18.25v-8.5A2.75 2.75 0 0 0 17.25 7H16"/>
                                </svg>
                            </div>
                            <h2 class="text-base font-semibold" style="letter-spacing:-.01em;">Sélectionne une tâche</h2>
                            <p class="mt-1.5 text-sm leading-6" style="color:rgba(0,0,0,.4);">Choisis un élément dans la liste pour afficher son détail.</p>
                            @if($canCreate)
                            <button type="button" @click="openCreate = true"
                                class="ws-btn-primary mt-5 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                                </svg>
                                Créer une tâche
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif
                </main>
            </div>
        </div>

        {{-- ── MODAL CRÉATION ──────────────────────────────────── --}}
        @if($canCreate)
        <div x-show="openCreate" x-cloak
             class="ws-modal-backdrop fixed inset-0 z-50 flex items-end justify-center sm:items-center p-0 sm:p-4"
             x-transition.opacity @keydown.escape.window="openCreate = false">
            <div class="ws-modal-appear w-full max-w-lg" @click.outside="openCreate = false">
                <div class="overflow-hidden rounded-t-2xl sm:rounded-2xl ws-card" style="background:#fff; border-color:rgba(0,0,0,.09);">
                    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(0,0,0,.06);">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[.16em]" style="color:rgba(0,0,0,.4);">Nouvelle tâche</p>
                            <h2 class="mt-0.5 text-base font-semibold" style="letter-spacing:-.01em;">Créer une tâche</h2>
                        </div>
                        <button type="button" @click="openCreate = false"
                            class="flex h-8 w-8 items-center justify-center rounded-lg transition" style="color:rgba(0,0,0,.4);"
                            onmouseenter="this.style.background='rgba(0,0,0,.05)'" onmouseleave="this.style.background='transparent'">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('tasks.store') }}" class="p-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-[.14em] mb-1.5" style="color:rgba(0,0,0,.4);">Titre <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="title" required autofocus value="{{ old('title') }}" placeholder="Nom de la tâche…"
                                class="ws-input h-10 w-full rounded-xl px-3.5 text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-[.14em] mb-1.5" style="color:rgba(0,0,0,.4);">Description</label>
                            <textarea name="description" rows="3" placeholder="Détails optionnels…"
                                class="ws-input w-full rounded-xl px-3.5 py-2.5 text-sm resize-none">{{ old('description') }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-[.14em] mb-1.5" style="color:rgba(0,0,0,.4);">Priorité</label>
                                <select name="priority" class="ws-input h-10 w-full rounded-xl px-3.5 text-sm font-medium appearance-none">
                                    @foreach(['low' => 'Basse', 'normal' => 'Normale', 'high' => 'Haute', 'urgent' => 'Urgente'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('priority', 'normal') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-[.14em] mb-1.5" style="color:rgba(0,0,0,.4);">Échéance</label>
                                <input type="date" name="due_date" value="{{ old('due_date') }}"
                                    class="ws-input h-10 w-full rounded-xl px-3.5 text-sm">
                            </div>
                        </div>

                        @if(isset($errors) && $errors->any())
                        <div class="rounded-xl px-3.5 py-2.5 text-sm" style="background:rgba(239,68,68,.06); color:#b91c1c; border:1px solid rgba(239,68,68,.15);">
                            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                        </div>
                        @endif

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button type="button" @click="openCreate = false"
                                class="h-10 rounded-xl border px-4 text-sm font-medium transition" style="border-color:rgba(0,0,0,.1); color:rgba(0,0,0,.6);"
                                onmouseenter="this.style.background='rgba(0,0,0,.04)'" onmouseleave="this.style.background='transparent'">
                                Annuler
                            </button>
                            <button type="submit" class="ws-btn-primary inline-flex h-10 items-center gap-2 rounded-xl px-4 text-sm font-medium">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Créer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Toast --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3500)"
             class="fixed bottom-5 right-5 z-50 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-white"
             style="background:#0a0a0a; box-shadow: 0 4px 24px rgba(0,0,0,.25); max-width: 340px;">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full" style="background:rgba(16,185,129,.2);">
                <svg class="h-3.5 w-3.5" style="color:#10b981;" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            {{ session('success') }}
        </div>
        @endif
    </div>

</x-app-layout>
