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

<div class="bg-white shadow-lg rounded-lg p-4 mb-6">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">{{ $title }}</h2>
    <table class="w-full border">
        <thead class="bg-gray-200">
            <tr>
                @foreach($columns as $column)
                    <th class="px-4 py-2">{{ $column }}</th>
                @endforeach
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr class="border">
                    @if($periodColumn)
                        <td class="px-4 py-2">{{ data_get($item, $relationColumn ?? $periodColumn[0]) ?? '—' }}</td>
                        <td class="px-4 py-2">{{ data_get($item, $periodColumn[1]) ?? '—' }}</td>
                    @else
                        @foreach($fields as $field)
                            <td class="px-4 py-2">{{ data_get($item, $field) ?? '—' }}</td>
                        @endforeach
                    @endif
                    <td class="px-4 py-2 flex gap-2">
                        <form action="{{ route($restoreRoute, $item->id) }}" method="POST">
                            @csrf 
                            @method('PATCH')
                            <button class="bg-green-500 text-white px-3 py-1 rounded">Restaurer</button>
                        </form>
                        <form action="{{ route($forceDeleteRoute, $item->id) }}" method="POST">
                            @csrf 
                            @method('DELETE')
                            <button class="bg-red-500 text-white px-3 py-1 rounded">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns)+1 }}" class="px-4 py-2 text-center text-gray-500">Aucun élément</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
