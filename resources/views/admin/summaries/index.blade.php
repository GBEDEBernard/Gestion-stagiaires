<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Tous les Résumés IA</h1>
                <p class="text-sm text-gray-500 mt-1">Gestion des résumés générés pour tous les utilisateurs</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Période</label>
                        <select name="period" class="rounded-lg border-gray-200 text-sm">
                            <option value="">Toutes</option>
                            <option value="weekly" @selected(request('period') === 'weekly')>Hebdomadaire</option>
                            <option value="monthly" @selected(request('period') === 'monthly')>Mensuel</option>
                            <option value="yearly" @selected(request('period') === 'yearly')>Annuel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Utilisateur</label>
                        <select name="user_id" class="rounded-lg border-gray-200 text-sm">
                            <option value="">Tous</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        Filtrer
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Du</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Au</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Généré le</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($summaries as $summary)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $summary->user->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $summary->period_type === 'weekly' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                        {{ $summary->period_type === 'monthly' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $summary->period_type === 'yearly' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                        {{ ucfirst(__($summary->period_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $summary->period_start->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $summary->period_end->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $summary->generated_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('summaries.show', $summary) }}"
                                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        Voir →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Aucun résumé trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $summaries->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
