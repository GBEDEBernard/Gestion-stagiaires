<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-bold mb-6">Gestion des utilisateurs</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full table-auto border-collapse border border-gray-300 bg-white shadow-sm rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">#</th>
                <th class="border p-2">Nom</th>
                <th class="border p-2">Email</th>
                <th class="border p-2">Rôle(s)</th>
                <th class="border p-2 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="border p-2">{{ $user->id }}</td>
                <td class="border p-2">{{ $user->name }}</td>
                <td class="border p-2">{{ $user->email }}</td>
                <td class="border p-2">{{ $user->roles->pluck('name')->join(', ') }}</td>
                <td class="border p-2 flex justify-center gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline" data-confirm-edit>Modifier</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" data-confirm-delete>
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline" >Supprimer</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center p-4 text-gray-500">Aucun utilisateur trouvé</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
</x-app-layout>
