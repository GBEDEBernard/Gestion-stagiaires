<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Mes Résumés IA</h1>
                    <p class="text-sm text-gray-500 mt-1">Résumés automatiques de vos rapports</p>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('summaries.generate') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="period" value="weekly">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Résumé cette semaine
                        </button>
                    </form>
                    <form action="{{ route('summaries.generate') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="period" value="monthly">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition">
                            Résumé ce mois
                        </button>
                    </form>
                    <form action="{{ route('summaries.generate') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="period" value="yearly">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                            Résumé cette année
                        </button>
                    </form>
                </div>
            </div>

            @if($summaries->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Aucun résumé pour le moment</h3>
                    <p class="text-gray-500">Génère un résumé IA de tes rapports pour faire le point.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($summaries as $summary)
                        <a href="{{ route('summaries.show', $summary) }}"
                           class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all p-5">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $summary->period_type === 'weekly' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                        {{ $summary->period_type === 'monthly' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $summary->period_type === 'yearly' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                        {{ ucfirst(__($summary->period_type)) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $summary->period_start->format('d/m/Y') }} → {{ $summary->period_end->format('d/m/Y') }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400">
                                    Généré le {{ $summary->generated_at?->format('d/m/Y à H:i') }}
                                </span>
                            </div>
                            <p class="text-gray-700 text-sm line-clamp-3">{{ Str::limit(strip_tags($summary->summary), 200) }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
