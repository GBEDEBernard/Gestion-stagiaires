<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.roles.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nouveau Rôle</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">Créez un nouveau rôle et attribuez-lui des permissions</p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('admin.roles.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Informations du rôle -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nom du rôle <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                            placeholder="Ex: secretaire, assistant, chef_service...">
                        @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <input type="text" name="description" id="description" value="{{ old('description') }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                            placeholder="Description courte du rôle">
                        @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Permissions -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Permissions
                        </label>
                        <button type="button" onclick="toggleAllPermissions()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                            Tout sélectionner / Tout désélectionner
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($permissions as $entity => $entityPermissions)
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-3">
                                <input type="checkbox" id="select-all-{{ $entity }}"
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 select-all-checkbox"
                                    data-group="{{ $entity }}">
                                <label for="select-all-{{ $entity }}" class="text-sm font-semibold text-gray-900 dark:text-white uppercase cursor-pointer">
                                    {{ $entity }}
                                </label>
                            </div>
                            <div class="space-y-2 pl-6">
                                @foreach($entityPermissions as $permission)
                                <div class="flex items-start gap-2">
                                    <input type="checkbox" name="permissions[]" id="permission-{{ $permission->id }}"
                                        value="{{ $permission->name }}"
                                        {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}
                                        class="mt-1 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 permission-checkbox"
                                        data-group="{{ $entity }}">
                                    <label for="permission-{{ $permission->id }}" class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">Aucune permission disponible</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.roles.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition font-medium shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Gestion du sélection par groupe
        document.querySelectorAll('.select-all-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const group = this.dataset.group;
                const checked = this.checked;

                document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`).forEach(cb => {
                    cb.checked = checked;
                });
            });
        });

        // Mettre à jour la checkbox "tout sélectionner" quand une permission change
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const group = this.dataset.group;
                const allChecked = Array.from(document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`))
                    .every(cb => cb.checked);
                const someChecked = Array.from(document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`))
                    .some(cb => cb.checked);

                const selectAllCheckbox = document.querySelector(`#select-all-${group}`);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }
            });
        });

        // Fonction globale pour tout sélectionner/désélectionner
        function toggleAllPermissions() {
            const allCheckboxes = document.querySelectorAll('.permission-checkbox');
            const anyChecked = Array.from(allCheckboxes).some(cb => cb.checked);

            allCheckboxes.forEach(cb => {
                cb.checked = !anyChecked;
            });

            // Mettre à jour les select-all-checkbox
            document.querySelectorAll('.select-all-checkbox').forEach(checkbox => {
                const group = checkbox.dataset.group;
                const groupCheckboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
                const allGroupChecked = Array.from(groupCheckboxes).every(cb => cb.checked);
                checkbox.checked = allGroupChecked;
            });
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.3);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }
    </style>
</x-app-layout>