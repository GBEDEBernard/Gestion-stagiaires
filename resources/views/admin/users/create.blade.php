<x-app-layout>
<div class="max-w-3xl mx-auto px-6 py-10 font-serif bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
    <h1 class="text-3xl font-extrabold mb-8 text-gray-800">Créer un nouvel utilisateur</h1>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 mb-6 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <!-- Nom -->
        <div class="mb-4">
            <label class="block font-semibold mb-1">Nom</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name') }}" required>
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" value="{{ old('email') }}" required>
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block font-semibold mb-1">Mot de passe</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
        </div>

        <!-- Password confirmation -->
        <div class="mb-4">
            <label class="block font-semibold mb-1">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
        </div>

        <!-- Rôles -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Rôles</h2>
            <div class="flex flex-wrap gap-3">
                @foreach($roles as $role)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-medium">{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Permissions -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Permissions spécifiques</h2>
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($permissions as $permission)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-800 text-sm font-medium hover:bg-gray-200 transition" title="{{ $permission->name }}">
                            {{ \Illuminate\Support\Str::limit($permission->name, 20) }}
                        </span>
                    </label>
                @endforeach
            </div>

            <!-- Ajouter une nouvelle permission -->
            {{-- <div class="mt-4">
                <form action="{{ route('admin.permissions.store') }}" method="POST" class="flex gap-2 items-center">
                    @csrf
                    <input type="text" name="name" placeholder="Nouvelle permission" class="border rounded px-3 py-2 flex-1">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Ajouter</button>
                </form>
            </div> --}}
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded shadow hover:bg-blue-700 transition">Créer</button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Annuler</a>
        </div>
    </form>
</div>
</x-app-layout>
