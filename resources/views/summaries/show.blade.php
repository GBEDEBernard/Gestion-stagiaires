<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('summaries.index') }}"
                   class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Retour aux résumés
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Résumé {{ __($summary->period_type) }}</h1>
                            <p class="text-sm text-gray-500 mt-1">
                                du {{ $summary->period_start->format('d/m/Y') }} au {{ $summary->period_end->format('d/m/Y') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $summary->period_type === 'weekly' ? 'bg-indigo-100 text-indigo-700' : '' }}
                            {{ $summary->period_type === 'monthly' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $summary->period_type === 'yearly' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                            {{ ucfirst(__($summary->period_type)) }}
                        </span>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="prose prose-indigo max-w-none">
                        @php
                            $lines = explode("\n", $summary->summary);
                        @endphp
                        @foreach($lines as $line)
                            @if(preg_match('/^\*\*(.+)\*\*/', $line, $m))
                                <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-2">{{ trim($m[1], '* ') }}</h3>
                            @elseif(preg_match('/^[*-]\s(.+)/', $line, $m))
                                <ul class="list-disc pl-5 text-gray-700 mb-1">
                                    <li>{{ $m[1] }}</li>
                                </ul>
                            @elseif(trim($line) !== '')
                                <p class="text-gray-700 mb-2">{{ $line }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                    <span>Modèle : {{ $summary->model_used ?? 'N/A' }}</span>
                    <span>Généré le {{ $summary->generated_at?->format('d/m/Y à H:i:s') }}</span>
                </div>
            </div>

            <div class="mt-6 flex justify-center">
                <form action="{{ route('summaries.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="period" value="{{ $summary->period_type }}">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Regénérer
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
