<x-app-layout>
<div class="max-w-5xl mx-auto px-6 py-4 font-serif">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-extrabold text-gray-800">Numéros de Badge</h1>
        <div class="flex gap-3">
            <a href="{{ route('dashboard') }}"
               class="inline-block px-5 py-2 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition">
                ← Retour
            </a>
            <a href="{{ route('badges.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow-md transition">
                + Ajouter un niveau
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 mb-6 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto shadow-lg rounded-lg bg-white">
        <table class="min-w-full table-auto divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro de Badge</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($badges as $badge)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 text-gray-700">{{ $badge->id }}</td>
                    <td class="px-6 py-4 text-gray-800 font-medium">{{ $badge->badge }}</td>
                    <td class="px-6 py-4 text-center flex justify-center gap-3">
                        <a href="{{ encrypted_route('badges.edit', $badge) }}" 
                           class="bg-yellow-500 text-white px-3 py-1 rounded shadow hover:bg-yellow-600 transition" data-confirm-edit>
                            Modifier
                        </a>
                        <form action="{{ encrypted_route('badges.destroy', $badge) }}" method="POST" data-confirm-delete>
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 text-white px-3 py-1 rounded shadow hover:bg-red-700 transition">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-6 text-gray-400 italic">Aucun badge trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $badges->links() }}
    </div>
</div>
</x-app-layout>
