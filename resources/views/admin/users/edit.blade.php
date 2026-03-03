<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.users.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Modifier l'Utilisateur</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">Modifiez les informations de {{ $user->name }}</p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ encrypted_route('admin.users.update', $user) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                        placeholder="Ex: Jean Dupont">
                    @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rôles - Réservé aux administrateurs -->
                @can('users.edit')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Rôles</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach($roles as $role)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-green-500 rounded-lg"
                                {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                            <span class="px-3 py-1.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-medium text-sm">{{ $role->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Permissions - Réservé aux administrateurs -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Permissions</label>
                    <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                        @foreach($permissions as $permission)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500 rounded"
                                {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                            <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium" title="{{ $permission->name }}">
                                {{ Str::limit($permission->name, 15) }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endcan

                <!-- Boutons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-xl hover:from-green-700 hover:to-emerald-800 transition font-medium shadow-lg shadow-green-600/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>