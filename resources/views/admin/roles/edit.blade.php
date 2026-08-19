<x-app-layout>
    @php
        $labelService = app(\App\Services\PermissionLabelService::class);
    @endphp

    <style>
        /* ── Grid permissions : 2 colonnes ── */
        .perm-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.125rem 0.75rem;
        }
        @media (max-width: 640px)  { .perm-grid { grid-template-columns: 1fr; } }

        /* ── Item permission ── */
        .perm-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.45rem 0.6rem;
            border-radius: 0.5rem;
            transition: background 0.12s;
        }
        .perm-item:hover { background: rgba(99,102,241,0.06); }
        .dark .perm-item:hover { background: rgba(99,102,241,0.12); }

        .perm-item label {
            font-size: 0.8rem;
            line-height: 1.35;
            color: #4b5563;
            cursor: pointer;
            user-select: none;
            transition: color 0.12s;
        }
        .dark .perm-item label { color: #9ca3af; }
        .perm-item:hover label { color: #1f2937; }
        .dark .perm-item:hover label { color: #f3f4f6; }

        /* ── Grille des groupes d'accordéons : 4 par ligne ── */
        .accordion-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }
        @media (max-width: 1024px) { .accordion-grid { grid-template-columns: repeat(2, 1fr); gap: 0.625rem; } }
        @media (max-width: 640px)  { .accordion-grid { grid-template-columns: 1fr; gap: 0.5rem; } }

        /* ── Accordion body ── */
        .acc-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.32s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    <div class="max-w-4xl mx-auto">

        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.roles.index') }}"
                    class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Modifier le Rôle</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">Modifiez les informations et les permissions du rôle</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ encrypted_route('admin.roles.update', $role) }}" method="POST" class="p-6 space-y-8">
                @csrf @method('PUT')

                {{-- Nom du rôle --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nom du rôle <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                        class="w-full md:w-1/2 px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Section Permissions --}}
                <div>

                    {{-- En-tête section --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Permissions
                            </h3>
                            <span id="total-count-badge"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 transition-all">
                                0 sélectionnée(s)
                            </span>
                        </div>
                        <button type="button" onclick="toggleAllPermissions()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tout sélectionner / Désélectionner
                        </button>
                    </div>

                    {{-- Barre de recherche --}}
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                            </svg>
                        </div>
                        <input type="text" id="permission-search"
                            placeholder="Rechercher une permission ou un groupe..."
                            class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white placeholder-gray-400">
                        <button type="button" id="clear-search"
                            class="absolute inset-y-0 right-0 pr-3.5 items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                            style="display:none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Accordéons --}}
                    <div class="accordion-grid" id="permissions-accordion">

                        @forelse($permissions as $entity => $entityPermissions)
                        <div class="accordion-item border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                             data-group="{{ $entity }}"
                             data-group-label="{{ strtolower($labelService->getGroupLabel($entity)) }}">

                            {{-- ── Header ── --}}
                            <div class="accordion-header flex items-center gap-3 px-4 py-3.5 bg-white dark:bg-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors select-none"
                                 onclick="toggleAccordion('{{ $entity }}')">

                                {{-- Checkbox "tout le groupe" --}}
                                <div class="flex-shrink-0" onclick="event.stopPropagation()">
                                    <input type="checkbox"
                                        id="select-all-{{ $entity }}"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 select-all-checkbox cursor-pointer"
                                        data-group="{{ $entity }}"
                                        title="Sélectionner / désélectionner tout le groupe">
                                </div>

                                {{-- Initiales --}}
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                            bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800/40">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ strtoupper(substr($labelService->getGroupLabel($entity), 0, 2)) }}
                                    </span>
                                </div>

                                {{-- Libellé groupe --}}
                                <span class="flex-1 text-sm font-semibold text-gray-800 dark:text-white truncate">
                                    {{ $labelService->getGroupLabel($entity) }}
                                </span>

                                {{-- Badge compteur + chevron --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="group-count-badge inline-flex items-center justify-center
                                                 min-w-[3.5rem] px-2 py-0.5 rounded-full text-xs font-medium
                                                 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400
                                                 transition-all duration-200"
                                          data-group="{{ $entity }}"
                                          data-total="{{ count($entityPermissions) }}">
                                        0 / {{ count($entityPermissions) }}
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                         id="chevron-{{ $entity }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            {{-- ── Body ── --}}
                            <div id="body-{{ $entity }}"
                                 class="acc-body"
                                 data-open="false">
                                <div class="px-5 pb-5 pt-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/40 dark:bg-gray-900/20">
                                    <div class="perm-grid">
                                        @foreach($entityPermissions as $permission)
                                        <div class="perm-item permission-item"
                                             data-label="{{ strtolower($labelService->getLabel($permission->name)) }}">
                                            <input type="checkbox" name="permissions[]"
                                                id="permission-{{ $permission->id }}"
                                                value="{{ $permission->name }}"
                                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                                class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 permission-checkbox cursor-pointer flex-shrink-0"
                                                data-group="{{ $entity }}">
                                            <label for="permission-{{ $permission->id }}">
                                                {{ $labelService->getLabel($permission->name) }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                        @empty
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Aucune permission disponible</p>
                        </div>
                        @endforelse

                        {{-- Aucun résultat de recherche --}}
                        <div id="no-results" class="hidden text-center py-8">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucun résultat pour votre recherche.</p>
                        </div>

                    </div>
                </div>

                {{-- Actions formulaire --}}
                <div class="flex items-center justify-end gap-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.roles.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition font-medium shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre à jour
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
    // ─────────────────────────────────────────────────────────────────────────────
    // Accordion EXCLUSIF : un seul ouvert à la fois
    // ─────────────────────────────────────────────────────────────────────────────

    let currentOpen = null;

    function openGroup(group) {
        const body = document.getElementById('body-' + group);
        const chev = document.getElementById('chevron-' + group);
        if (!body) return;
        body.style.maxHeight = body.scrollHeight + 'px';
        body.dataset.open    = 'true';
        chev.style.transform = 'rotate(180deg)';
        body.addEventListener('transitionend', function once() {
            if (body.dataset.open === 'true') body.style.maxHeight = 'none';
            body.removeEventListener('transitionend', once);
        });
        currentOpen = group;
    }

    function closeGroup(group) {
        const body = document.getElementById('body-' + group);
        const chev = document.getElementById('chevron-' + group);
        if (!body) return;
        if (body.style.maxHeight === 'none') {
            body.style.maxHeight = body.scrollHeight + 'px';
        }
        requestAnimationFrame(() => requestAnimationFrame(() => {
            body.style.maxHeight = '0px';
        }));
        body.dataset.open    = 'false';
        chev.style.transform = 'rotate(0deg)';
        if (currentOpen === group) currentOpen = null;
    }

    function toggleAccordion(group) {
        const body   = document.getElementById('body-' + group);
        const isOpen = body && body.dataset.open === 'true';

        if (currentOpen && currentOpen !== group) {
            closeGroup(currentOpen);
        }

        if (isOpen) {
            closeGroup(group);
        } else {
            openGroup(group);
        }
    }

    // ── Ouverture/fermeture instantanée (recherche) ───────────────────────────────
    function openAccordionDirect(group) {
        const body = document.getElementById('body-' + group);
        const chev = document.getElementById('chevron-' + group);
        if (!body) return;
        body.style.transition = 'none';
        body.style.maxHeight  = 'none';
        body.dataset.open     = 'true';
        chev.style.transform  = 'rotate(180deg)';
        requestAnimationFrame(() => { body.style.transition = ''; });
        currentOpen = group;
    }

    function closeAccordionDirect(group) {
        const body = document.getElementById('body-' + group);
        const chev = document.getElementById('chevron-' + group);
        if (!body) return;
        body.style.transition = 'none';
        body.style.maxHeight  = '0px';
        body.dataset.open     = 'false';
        chev.style.transform  = 'rotate(0deg)';
        requestAnimationFrame(() => { body.style.transition = ''; });
        if (currentOpen === group) currentOpen = null;
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Badges compteurs
    // ─────────────────────────────────────────────────────────────────────────────
    const BADGE_NONE  = ['bg-gray-100','dark:bg-gray-700','text-gray-500','dark:text-gray-400'];
    const BADGE_SOME  = ['bg-indigo-100','dark:bg-indigo-900','text-indigo-700','dark:text-indigo-300'];
    const BADGE_ALL   = ['bg-indigo-600','text-white'];
    const ALL_BADGE_C = [...BADGE_NONE, ...BADGE_SOME, ...BADGE_ALL];

    function updateGroupCount(group) {
        const all     = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
        const checked = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]:checked`);
        const badge   = document.querySelector(`.group-count-badge[data-group="${group}"]`);
        const item    = document.querySelector(`.accordion-item[data-group="${group}"]`);
        if (!badge || !item) return;

        const n = checked.length, t = all.length;
        badge.textContent = n + ' / ' + t;
        ALL_BADGE_C.forEach(c => badge.classList.remove(c));

        if (n === 0) {
            BADGE_NONE.forEach(c => badge.classList.add(c));
            item.style.borderColor = '';
        } else if (n === t) {
            BADGE_ALL.forEach(c => badge.classList.add(c));
            item.style.borderColor = 'rgb(99 102 241 / 0.55)';
        } else {
            BADGE_SOME.forEach(c => badge.classList.add(c));
            item.style.borderColor = 'rgb(99 102 241 / 0.3)';
        }

        updateSelectAllState(group);
        updateTotalCount();
    }

    function updateTotalCount() {
        const n     = document.querySelectorAll('.permission-checkbox:checked').length;
        const badge = document.getElementById('total-count-badge');
        if (!badge) return;
        badge.textContent = n + ' sélectionnée(s)';
        badge.className   = n > 0
            ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 transition-all'
            : 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 transition-all';
    }

    function updateSelectAllState(group) {
        const cbs  = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
        const allC = Array.from(cbs).every(cb => cb.checked);
        const smC  = Array.from(cbs).some(cb => cb.checked);
        const sa   = document.getElementById('select-all-' + group);
        if (sa) { sa.checked = allC; sa.indeterminate = smC && !allC; }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Listeners
    // ─────────────────────────────────────────────────────────────────────────────
    document.querySelectorAll('.select-all-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const g = this.dataset.group;
            document.querySelectorAll(`.permission-checkbox[data-group="${g}"]`)
                .forEach(c => { c.checked = this.checked; });
            updateGroupCount(g);
        });
    });

    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.addEventListener('change', function () { updateGroupCount(this.dataset.group); });
    });

    // ── Tout sélectionner / désélectionner ───────────────────────────────────────
    function toggleAllPermissions() {
        const all   = document.querySelectorAll('.permission-checkbox');
        const anyOn = Array.from(all).some(cb => cb.checked);
        all.forEach(cb => { cb.checked = !anyOn; });
        const groups = new Set(Array.from(all).map(cb => cb.dataset.group));
        groups.forEach(g => updateGroupCount(g));
    }

    // ── Recherche ─────────────────────────────────────────────────────────────────
    const searchInput = document.getElementById('permission-search');
    const clearBtn    = document.getElementById('clear-search');

    searchInput.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        clearBtn.style.display = q ? 'flex' : 'none';
        filterPermissions(q);
    });

    clearBtn.addEventListener('click', function () {
        searchInput.value  = '';
        this.style.display = 'none';
        filterPermissions('');
    });

    function filterPermissions(q) {
        let visible = 0;
        document.querySelectorAll('.accordion-item').forEach(item => {
            const gLabel = (item.dataset.groupLabel || '').toLowerCase();
            const group  = item.dataset.group;
            const pItems = item.querySelectorAll('.permission-item');

            if (!q) {
                pItems.forEach(pi => { pi.style.display = ''; });
                item.style.display = '';
                closeAccordionDirect(group);
                return;
            }

            const groupMatch = gLabel.includes(q);
            let matchCount   = 0;

            pItems.forEach(pi => {
                const show = groupMatch || (pi.dataset.label || '').toLowerCase().includes(q);
                pi.style.display = show ? '' : 'none';
                if (show) matchCount++;
            });

            if (matchCount > 0) {
                item.style.display = '';
                visible++;
                openAccordionDirect(group);
            } else {
                item.style.display = 'none';
            }
        });

        const noRes = document.getElementById('no-results');
        if (noRes) noRes.classList.toggle('hidden', !q || visible > 0);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Init : recalculer les badges (permissions pré-cochées au chargement)
    // ─────────────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const groups = new Set(
            Array.from(document.querySelectorAll('.permission-checkbox')).map(cb => cb.dataset.group)
        );
        groups.forEach(g => updateGroupCount(g));
    });
    </script>

</x-app-layout>