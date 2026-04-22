<x-app-layout title="Rapports de travail">

    <div class="max-w-5xl mx-auto px-4 py-8 space-y-8">

        {{-- HEADER --}}
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Rapports de travail</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Crée et gère tes rapports quotidiens
                </p>
            </div>

            <a href="{{ route('presence.pointage') }}"
                class="inline-flex items-center rounded-xl border px-4 py-2 text-sm hover:bg-slate-50">
                Retour au pointage
            </a>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
        @endif

        {{-- ===================== --}}
        {{-- FORM CREATE / EDIT --}}
        {{-- ===================== --}}
        <div class="bg-white border rounded-2xl p-6 space-y-5">

            <h2 class="font-semibold text-slate-900">
                {{ isset($editReport) ? "Modifier le rapport" : "Nouveau rapport" }}
            </h2>

            <form method="POST"
                action="{{ isset($editReport)
                        ? route('reports.update', $editReport->id)
                        : route('reports.store') }}">

                @csrf
                @isset($editReport)
                @method('PUT')
                @endisset

                {{-- résumé --}}
                <div>
                    <label class="text-sm font-medium">Résumé</label>
                    <textarea name="summary" rows="4"
                        class="w-full mt-2 rounded-xl border"
                        required>{{ old('summary', $editReport->summary ?? '') }}</textarea>
                </div>

                {{-- blocages --}}
                <div>
                    <label class="text-sm font-medium">Blocages</label>
                    <textarea name="blockers" rows="3"
                        class="w-full mt-2 rounded-xl border">{{ old('blockers', $editReport->blockers ?? '') }}</textarea>
                </div>

                {{-- actions --}}
                <div>
                    <label class="text-sm font-medium">Prochaines actions</label>
                    <textarea name="next_steps" rows="3"
                        class="w-full mt-2 rounded-xl border">{{ old('next_steps', $editReport->next_steps ?? '') }}</textarea>
                </div>

                {{-- heures --}}
                <div>
                    <label class="text-sm font-medium">Heures travaillées</label>
                    <input type="number" name="hours_declared"
                        class="w-full mt-2 rounded-xl border"
                        value="{{ old('hours_declared', $editReport->hours_declared ?? 0) }}">
                </div>

                {{-- actions --}}
                <div class="flex justify-end gap-3 mt-4">

                    <button type="submit" name="status_action" value="draft"
                        class="px-4 py-2 rounded-xl border hover:bg-slate-50">
                        Brouillon
                    </button>

                    <button type="submit" name="status_action" value="submit"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white">
                        Enregistrer
                    </button>

                </div>

            </form>

        </div>

        {{-- ===================== --}}
        {{-- LISTE DES RAPPORTS --}}
        {{-- ===================== --}}
        <div class="bg-white border rounded-2xl overflow-hidden">

            <div class="p-4 border-b bg-slate-50 font-semibold">
                Historique des rapports
            </div>

            <div class="divide-y">

                @forelse($reports as $report)

                <div class="p-4 flex justify-between items-center">

                    <div>
                        <p class="font-medium">
                            {{ $report->report_date->format('d M Y') }}
                        </p>

                        <p class="text-sm text-slate-500">
                            {{ \Illuminate\Support\Str::limit($report->summary, 60) }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">

                        <span class="text-sm text-slate-600">
                            {{ $report->hours_declared }}h
                        </span>

                        {{-- bouton EDIT --}}
                        <a href="{{ route('reports.index', [
                            'period' => 'daily',
                            'edit' => $report->id
                        ]) }}"
                            class="text-sm px-3 py-1 rounded-lg border hover:bg-slate-50">
                            Modifier
                        </a>

                    </div>

                </div>

                @empty
                <div class="p-6 text-center text-slate-500">
                    Aucun rapport
                </div>
                @endforelse

            </div>

        </div>

    </div>

</x-app-layout>