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

            {{-- Actions --}}
            <div class="mt-6 flex items-center justify-center gap-3">
                <form action="{{ route('summaries.generate') }}" method="POST" class="generate-form">
                    @csrf
                    <input type="hidden" name="period" value="{{ $summary->period_type }}">
                    <button type="submit"
                        class="generate-btn inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="generate-icon w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="generate-text">Regénérer</span>
                        <span class="generate-spinner hidden">
                            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </span>
                    </button>
                </form>

                <form action="{{ route('summaries.destroy', $summary) }}" method="POST"
                      onsubmit="return confirm('Supprimer ce résumé ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.generate-form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('.generate-btn');
                btn.disabled = true;
                btn.querySelector('.generate-icon').classList.add('hidden');
                btn.querySelector('.generate-text').classList.add('hidden');
                btn.querySelector('.generate-spinner').classList.remove('hidden');
            });
        });
    </script>
</x-app-layout>
