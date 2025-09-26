<x-app-layout>
<div class="max-w-2xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-bold mb-6">Modifier le rôle de {{ $user->name }}</h1>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf @method('PATCH')
        <div class="mb-4">
            <label class="block mb-2">Rôle :</label>
            <select name="role" class="border rounded px-3 py-2 w-full">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Enregistrer</button>
        <a href="{{ route('admin.users.index') }}" class="ml-4 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
</x-app-layout>
