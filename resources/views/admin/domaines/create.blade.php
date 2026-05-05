<x-app-layout>
    <div class="max-w-2xl mx-auto">

        {{-- En-tête --}}
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('domaines.index') }}"
                    class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nouveau Domaine</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">
                        Créez un domaine de travail et associez-le à un ou plusieurs sites.
                    </p>
                </div>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('domaines.store') }}" method="POST" class="divide-y divide-gray-100 dark:divide-gray-700">
                @csrf

                {{-- Section Informations --}}
                <div class="p-6 space-y-5">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">1</span>
                            Informations du domaine
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-8">Nom et description du domaine de travail.</p>
                    </div>

                    <div class="ml-8 space-y-4">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nom du domaine <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required
                                autofocus
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                                placeholder="Ex: Direction Technique, Service Opérationnel...">
                            @error('nom')
                                <p class="mt-1.5 text-sm text-red-500 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description
                                <span class="text-gray-400 font-normal">(optionnel)</span>
                            </label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white placeholder-gray-400 resize-none"
                                placeholder="Décrivez brièvement ce domaine de travail...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section Sites --}}
                <div class="p-6 space-y-5">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">2</span>
                            Sites associés
                            <span class="text-red-500">*</span>
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-8">
                            Sélectionnez le ou les sites où ce domaine existe.
                        </p>
                    </div>

                    <div class="ml-8">
                        @if(($sites ?? collect())->isEmpty())
                            <div class="flex items-center gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl text-amber-700 dark:text-amber-400 text-sm">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Aucun site disponible. <a href="{{ route('sites.create') }}" class="font-semibold underline ml-1">Créer un site</a>
                            </div>
                        @else
                            {{-- Tout sélectionner --}}
                            <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-100 dark:border-gray-700">
                                <label class="flex items-center gap-2.5 cursor-pointer select-none group">
                                    <input type="checkbox" id="select_all_sites"
                                        class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                        onchange="toggleAllSites(this)">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                        Tout sélectionner
                                    </span>
                                </label>
                                <span id="sites_count" class="text-xs text-gray-400 dark:text-gray-500">
                                    0 / {{ $sites->count() }} sélectionné(s)
                                </span>
                            </div>

                            {{-- Liste des sites --}}
                            <div class="space-y-2">
                                @foreach($sites as $site)
                                <label class="site-card flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition-all duration-200
                                    {{ in_array($site->id, old('site_ids', [])) 
                                        ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 shadow-sm shadow-indigo-100 dark:shadow-none' 
                                        : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10' }}">
                                    <input type="checkbox"
                                        name="site_ids[]"
                                        value="{{ $site->id }}"
                                        class="site-checkbox mt-0.5 w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                        {{ in_array($site->id, old('site_ids', [])) ? 'checked' : '' }}
                                        onchange="syncSelectAll()">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $site->name }}
                                            </span>
                                            @if($site->code)
                                            <span class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded text-xs font-mono">
                                                {{ $site->code }}
                                            </span>
                                            @endif
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                                {{ $site->is_active 
                                                    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' 
                                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $site->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                {{ $site->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                        @if($site->city || $site->address)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                            📍 {{ implode(' · ', array_filter([$site->city, $site->address])) }}
                                        </p>
                                        @endif
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        @endif

                        @error('site_ids')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-end gap-3">
                    <a href="{{ route('domaines.index') }}"
                        class="px-5 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition font-medium text-sm">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl transition font-medium text-sm shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer le domaine
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAllSites(master) {
            const checkboxes = document.querySelectorAll('.site-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = master.checked;
                updateCardStyle(cb);
            });
            updateCount();
        }

        function syncSelectAll() {
            const all = document.querySelectorAll('.site-checkbox');
            const checked = document.querySelectorAll('.site-checkbox:checked');
            const master = document.getElementById('select_all_sites');
            if (!master) return;

            master.indeterminate = checked.length > 0 && checked.length < all.length;
            master.checked = checked.length === all.length && all.length > 0;

            all.forEach(cb => updateCardStyle(cb));
            updateCount();
        }

        function updateCardStyle(checkbox) {
            const card = checkbox.closest('label');
            if (!card) return;
            if (checkbox.checked) {
                card.classList.add('border-indigo-400', 'bg-indigo-50', 'shadow-sm');
                card.classList.remove('border-gray-200', 'bg-gray-50');
            } else {
                card.classList.remove('border-indigo-400', 'bg-indigo-50', 'shadow-sm');
                card.classList.add('border-gray-200', 'bg-gray-50');
            }
        }

        function updateCount() {
            const total = document.querySelectorAll('.site-checkbox').length;
            const checked = document.querySelectorAll('.site-checkbox:checked').length;
            const counter = document.getElementById('sites_count');
            if (counter) counter.textContent = `${checked} / ${total} sélectionné(s)`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            syncSelectAll();
        });
    </script>
</x-app-layout>