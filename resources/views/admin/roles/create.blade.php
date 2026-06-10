<x-app-layout>
    @php
        $labelService = app(\App\Services\PermissionLabelService::class);
    @endphp

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
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Créer un Rôle</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">Créez un nouveau rôle et définissez ses permissions</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('admin.roles.store') }}" method="POST" class="p-6 space-y-8">
                @csrf

                {{-- Role Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nom du rôle <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        placeholder="Ex : Responsable RH, Superviseur, Comptable..."
                        class="w-full md:w-1/2 px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white placeholder-gray-400">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Permissions Section --}}
                <div>

                    {{-- Section header --}}
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

                    {{-- Search bar --}}
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

                    {{-- Accordion groups --}}
                    <div class="space-y-2" id="permissions-accordion">

                        @forelse($permissions as $entity => $entityPermissions)
                        <div class="accordion-item border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                             data-group="{{ $entity }}"
                             data-group-label="{{ strtolower($labelService->getGroupLabel($entity)) }}">

                            {{-- ── Accordion Header ── --}}
                            <div class="accordion-header flex items-center gap-3 px-4 py-3.5 bg-white dark:bg-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors select-none"
                                 onclick="toggleAccordion('{{ $entity }}')">

                                {{-- "Select all" checkbox for this group --}}
                                <div class="flex-shrink-0" onclick="event.stopPropagation()">
                                    <input type="checkbox"
                                        id="select-all-{{ $entity }}"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 select-all-checkbox cursor-pointer"
                                        data-group="{{ $entity }}"
                                        title="Sélectionner / désélectionner tout le groupe">
                                </div>

                                {{-- Group initials badge --}}
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                            bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800/40">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ strtoupper(substr($labelService->getGroupLabel($entity), 0, 2)) }}
                                    </span>
                                </div>

                                {{-- Group label --}}
                                <span class="flex-1 text-sm font-semibold text-gray-800 dark:text-white truncate">
                                    {{ $labelService->getGroupLabel($entity) }}
                                </span>

                                {{-- Count badge + chevron --}}
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

                            {{-- ── Accordion Body ── --}}
                            <div id="body-{{ $entity }}"
                                 data-open="false"
                                 style="max-height:0; overflow:hidden; transition: max-height .3s cubic-bezier(.4,0,.2,1)">
                                <div class="px-4 pb-4 pt-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/40 dark:bg-gray-900/20">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-0.5">
                                        @foreach($entityPermissions as $permission)
                                        <div class="permission-item flex items-center gap-2.5 px-2 py-2 rounded-lg hover:bg-white dark:hover:bg-gray-800/60 transition-colors"
                                             data-label="{{ strtolower($labelService->getLabel($permission->name)) }}">
                                            <input type="checkbox" name="permissions[]"
                                                id="permission-{{ $permission->id }}"
                                                value="{{ $permission->name }}"
                                                class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500 permission-checkbox cursor-pointer flex-shrink-0"
                                                data-group="{{ $entity }}">
                                            <label for="permission-{{ $permission->id }}"
                                                class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer hover:text-gray-900 dark:hover:text-gray-100 transition-colors select-none leading-snug">
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

                        {{-- No search results --}}
                        <div id="no-results" class="hidden text-center py-8">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucun résultat pour votre recherche.</p>
                        </div>

                    </div>
                </div>

                {{-- Form Actions --}}
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
                        Créer le rôle
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
    // ── Accordion ────────────────────────────────────────────────────────────────
    function toggleAccordion(group) {
        const body   = document.getElementById('body-' + group);
        const chev   = document.getElementById('chevron-' + group);
        const isOpen = body.dataset.open === 'true';

        if (isOpen) {
            // Freeze explicit height, then animate to 0
            if (body.style.maxHeight === 'none') body.style.maxHeight = body.scrollHeight + 'px';
            requestAnimationFrame(() => requestAnimationFrame(() => { body.style.maxHeight = '0px'; }));
            body.dataset.open   = 'false';
            chev.style.transform = 'rotate(0deg)';
        } else {
            body.style.maxHeight = body.scrollHeight + 'px';
            body.dataset.open   = 'true';
            chev.style.transform = 'rotate(180deg)';
            body.addEventListener('transitionend', function once() {
                if (body.dataset.open === 'true') body.style.maxHeight = 'none';
                body.removeEventListener('transitionend', once);
            });
        }
    }

    // Instant open/close (used by search & init – no animation flicker)
    function openAccordionDirect(group) {
        const body = document.getElementById('body-' + group);
        const chev = document.getElementById('chevron-' + group);
        body.style.transition = 'none';
        body.style.maxHeight  = 'none';
        body.dataset.open     = 'true';
        chev.style.transform  = 'rotate(180deg)';
        requestAnimationFrame(() => { body.style.transition = ''; });
    }

    function closeAccordionDirect(group) {
        const body = document.getElementById('body-' + group);
        const chev = document.getElementById('chevron-' + group);
        body.style.transition = 'none';
        body.style.maxHeight  = '0px';
        body.dataset.open     = 'false';
        chev.style.transform  = 'rotate(0deg)';
        requestAnimationFrame(() => { body.style.transition = ''; });
    }

    // ── Count badges ─────────────────────────────────────────────────────────────
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

    // ── Event listeners ──────────────────────────────────────────────────────────
    document.querySelectorAll('.select-all-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const g = this.dataset.group;
            document.querySelectorAll(`.permission-checkbox[data-group="${g}"]`).forEach(c => { c.checked = this.checked; });
            updateGroupCount(g);
        });
    });

    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.addEventListener('change', function () { updateGroupCount(this.dataset.group); });
    });

    // ── Toggle All ───────────────────────────────────────────────────────────────
    function toggleAllPermissions() {
        const all   = document.querySelectorAll('.permission-checkbox');
        const anyOn = Array.from(all).some(cb => cb.checked);
        all.forEach(cb => { cb.checked = !anyOn; });
        const groups = new Set(Array.from(all).map(cb => cb.dataset.group));
        groups.forEach(g => updateGroupCount(g));
    }

    // ── Search ───────────────────────────────────────────────────────────────────
    const searchInput = document.getElementById('permission-search');
    const clearBtn    = document.getElementById('clear-search');

    searchInput.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        clearBtn.style.display = q ? 'flex' : 'none';
        filterPermissions(q);
    });

    clearBtn.addEventListener('click', function () {
        searchInput.value      = '';
        this.style.display     = 'none';
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

    // ── Init ─────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const groups = new Set(
            Array.from(document.querySelectorAll('.permission-checkbox')).map(cb => cb.dataset.group)
        );
        groups.forEach(g => updateGroupCount(g));
    });
    </script>

</x-app-layout>
