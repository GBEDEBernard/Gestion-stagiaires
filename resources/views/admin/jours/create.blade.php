<x-app-layout class="">

<div class="max-w-xl mx-auto  p-6 bg-blue-900 hover:opacity-90">
    <h1 class="text-2xl font-bold mb-4">Ajouter une Journée</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('jours.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Jours</label>
            <input type="text" name="jour" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Enregistrer</button>
    </form>
</div>
</x-app-layout>