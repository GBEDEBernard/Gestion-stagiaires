<x-app-layout>

<div class="max-w-xl mx-auto p-8 h-[650px] shadow bg-gray-100  bg-blue-900 hover:opacity-90 rounded">
    <h1 class="text-2xl font-bold mb-4">Modifier La journée</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('jours.update', $jour->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="jour" class="block text-xl font-bold text-black">Jours</label>
            <input type="text" name="jour"  id="jour" value="{{ old('jour', $jour->jour) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

      
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Mettre à jour</button>
    </form>

  
</div>
</x-app-layout>