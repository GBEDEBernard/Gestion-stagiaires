<x-app-layout>
    <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Messages reçus</h2>

    <div class="p-4 bg-blue-600 rounded-lg shadow flex justify-center text-white  hover:opacity-90 w-5/6 ml-32">

        @if ($contacts->isEmpty())
            <p class="text-gray-600">Aucun message pour le moment.</p>
        @else
            <table class="w-5/6 table-auto border border-gray-200 ">
                <thead class="bg-gray-100 text-black">
                    <tr>
                        <th class="px-4 py-2 text-left">Nom</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Sujet</th>
                        <th class="px-4 py-2 text-left">Message</th>
                        <th class="px-4 py-2 text-left">Reçu le</th>
                    </tr>
                </thead>
                <tbody class="text-black bg-gray-200">
                    @foreach ($contacts as $contact)
                        <tr class="border-t border-gray-200">
                            <td class="px-4 py-2">{{ $contact->name }}</td>
                            <td class="px-4 py-2">{{ $contact->email }}</td>
                            <td class="px-4 py-2">{{ $contact->subject }}</td>
                            <td class="px-4 py-2">{{ Str::limit($contact->message, 50) }}</td>
                            <td class="px-4 py-2 text-sm ">{{ $contact->created_at}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
