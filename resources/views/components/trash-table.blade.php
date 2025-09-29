@props([
    'items',
    'title',
    'columns',
    'restoreRoute',
    'forceDeleteRoute',
    'fields' => [],
    'periodColumn' => null,
    'relationColumn' => null
])

<div class="bg-white shadow-md rounded-xl p-6 mb-8 border border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2 mb-4">
        🗂️ {{ $title }}
    </h2>

    <div class="overflow-x-auto rounded-lg">
        <table class="w-full border-collapse text-left">
            <thead class="bg-gray-100 border-b">
                <tr>
                    @foreach($columns as $column)
                        <th class="px-4 py-3 text-sm font-medium text-gray-700 uppercase tracking-wide">
                            {{ $column }}
                        </th>
                    @endforeach
                    <th class="px-4 py-3 text-sm font-medium text-gray-700 uppercase tracking-wide">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition">
                        @if($periodColumn)
                            <td class="px-4 py-3">{{ data_get($item, $relationColumn ?? $periodColumn[0]) ?? '—' }}</td>
                            <td class="px-4 py-3">{{ data_get($item, $periodColumn[1]) ?? '—' }}</td>
                        @else
                            @foreach($fields as $field)
                                <td class="px-4 py-3">{{ data_get($item, $field) ?? '—' }}</td>
                            @endforeach
                        @endif

                        <td class="px-4 py-3 flex items-center gap-3">
                            <!-- Restaurer -->
                            <form action="{{ route($restoreRoute, $item->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment restaurer cet élément ?')">
                                @csrf 
                                @method('PATCH')
                                <button class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded-lg shadow transition">
                                    ♻️ Restaurer
                                </button>
                            </form>
                            <!-- Supprimer -->
                            <form action="{{ route($forceDeleteRoute, $item->id) }}" method="POST" onsubmit="return confirm('⚠️ Cette suppression est définitive. Continuer ?')">
                                @csrf 
                                @method('DELETE')
                                <button class="flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1.5 rounded-lg shadow transition">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns)+1 }}" class="px-4 py-6 text-center text-gray-500 italic">
                            Aucun élément trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
