<x-app-layout>

<div class="hover:opacity-85 max-w-5xl mx-auto px-4 py-16  rounded h-[680px]">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl text-white font-bold ">Les numéro de Badge</h1>
        <a href="{{ route('badges.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Ajouter un niveau
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full table-auto  border-collapse border border-gray-300 bg-white shadow-sm rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2 text-left">#</th>
                <th class="border p-2 text-left">Numéro de Badge</th>
                <th class="border p-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($badges as $badge)
            <tr class="hover:bg-gray-50">
                <td class="border p-2">{{ $badge->id }}</td>
                <td class="border p-2">{{ $badge->badge }}</td>
                <td class="border p-2 flex space-x-2">
                    <a href="{{ route('badges.edit', $badge) }}" class="text-blue-600 hover:underline" data-confirm-edit>Modifier</a>
                    <form action="{{ route('badges.destroy', $badge) }}" method="POST"  class="inline" data-confirm-delete>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center p-4">Aucun type de stage trouvé.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</x-app-layout>