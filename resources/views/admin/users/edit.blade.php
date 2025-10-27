<x-app-layout>
<div class="max-w-3xl mx-auto px-6 py-10 font-serif">
    <h1 class="text-3xl font-extrabold mb-8 text-gray-800">Modifier {{ $user->name }}</h1>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white shadow-lg rounded-xl p-8 space-y-6">
        @csrf 
        @method('put')
                <!-- Nom -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


        <!-- Rôles -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">Rôles</h2>
            <div class="flex flex-wrap gap-3">
                @foreach($roles as $role)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                               @checked($user->hasRole($role->name))
                               class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-medium shadow-sm">
                            {{ $role->name }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
       
        <!-- Permissions -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">Permissions spécifiques</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($permissions as $permission)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                               @checked($user->hasPermissionTo($permission->name))
                               class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-800 text-sm font-medium hover:bg-gray-200 shadow-sm transition"
                              title="{{ $permission->name }}">
                            {{ Str::limit($permission->name, 20) }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4 pt-4 border-t">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-blue-700 transition duration-150">
                Enregistrer
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Annuler</a>
        </div>
    </form>
</div>
</x-app-layout>
