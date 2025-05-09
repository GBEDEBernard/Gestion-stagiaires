@extends('layouts.app')

@section('content')
<div class="bg-blue-900 hover:opacity-90 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 h-[650px] rounded">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Liste des Jours</h1>
        <a href="{{ route('jours.create') }}" class="mt-4 sm:mt-0 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Ajouter une école</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLE EN VERSION LARGE -->
    <div class="hidden lg:block bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase">
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Jours</th>
                   
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($jours as $jour)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $jour->id }}</td>
                        <td class="px-6 py-4">{{ $jour->jour }}</td>
                      
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="{{ route('jours.edit', $jour->id) }}" class="text-yellow-500 hover:underline">Modifier</a>
                            <form action="{{ route('jours.destroy', $jour->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette journée ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- VERSION MOBILE : CARTE -->
    <div class="lg:hidden space-y-4">
        @foreach($jours as $jour)
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-lg font-semibold text-gray-800">{{ $jour->jour }}</h2>
                
                <div class="mt-4 flex justify-end gap-2">
                    <a href="{{ route('jours.edit', $jour->id) }}" class="text-yellow-500 hover:underline">Modifier</a>
                    <form action="{{ route('jours.destroy', $jour->id) }}" method="POST" onsubmit="return confirm('Supprimer cette école ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
