@php
    $max      = config('evaluation.max_score', 20);
    $p        = $stage->etudiant?->personnel;
    $nom      = $p ? trim(($p->prenom ?? '') . ' ' . ($p->nom ?? '')) : 'Stagiaire';
    $locked   = $evaluation->isFinalized();
    $composing = !$locked && !$evaluation->gridIsValidated();
@endphp

<x-app-layout>
    {{-- ────────── En-tête ────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ encrypted_route('stages.show', $stage) }}"
                class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $nom }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stage->typestage?->libelle ?? 'Stage' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($locked)
                <span class="px-3 py-2 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-sm font-medium">
                    Finalisée le {{ $evaluation->finalized_at?->format('d/m/Y') }}
                </span>
            @endif
            <a href="{{ encrypted_route('stages.rapport', $stage) }}"
                class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                Rapport
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 text-sm text-emerald-800 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm text-red-800 dark:text-red-300">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if($composing)
        {{-- ══════════ Temps 1 : composer la grille ══════════ --}}
        <form method="POST" action="{{ encrypted_route('stages.evaluation.grid.save', $stage) }}"
              x-data="{
                  rows: {{ Js::from($gridRows->map(fn($r) => $r + ['editing' => false])->values()) }},
                  add() {
                      // Une ligne neuve s'ouvre directement en saisie, inutile
                      // de double-cliquer sur ce qu'on vient de créer.
                      this.rows.push({ id: null, label: '', weight: 1, editing: true });
                      this.$nextTick(() => {
                          const inputs = this.$el.querySelectorAll('input[type=text]');
                          inputs[inputs.length - 1]?.focus();
                      });
                  },
                  remove(i) { this.rows.splice(i, 1); if (!this.rows.length) this.add(); }
              }"
              x-init="if (!rows.length) add()">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                <th class="w-12 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Critère d'évaluation</th>
                                <th class="w-28 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Coef.</th>
                                <th class="w-16 px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, i) in rows" :key="i">
                                {{-- Double-clic pour modifier une ligne existante ;
                                     une ligne neuve s'ouvre directement en saisie. --}}
                                {{-- La ligne ne sort du mode édition que lorsque le
                                     focus quitte la ligne entière : passer du
                                     libellé au coefficient ne la referme pas. --}}
                                <tr class="border-b border-gray-100 dark:border-gray-700/60 transition-colors"
                                    :class="row.editing ? 'bg-blue-50/60 dark:bg-blue-900/15' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30'"
                                    @dblclick="row.editing = true; $nextTick(() => $el.querySelector('input[type=text]')?.focus())"
                                    @focusout="if (!$el.contains($event.relatedTarget)) row.editing = false">

                                    <td class="px-4 py-2 text-gray-400 tabular-nums" x-text="i + 1"></td>

                                    <td class="px-2 py-2">
                                        <input type="hidden" :name="`rows[${i}][id]`" :value="row.id ?? ''">
                                        <input type="text" :name="`rows[${i}][label]`" x-model="row.label"
                                            :readonly="!row.editing"
                                            maxlength="120" placeholder="Saisir un critère…"
                                            @keydown.enter.prevent="i === rows.length - 1 && row.label ? add() : row.editing = false"
                                            @keydown.escape="row.editing = false; $event.target.blur()"
                                            :class="row.editing
                                                ? 'bg-white dark:bg-gray-900 ring-2 ring-blue-500/60 border-blue-500 cursor-text'
                                                : 'bg-transparent ring-0 border-transparent cursor-default'"
                                            class="w-full rounded-lg border px-2.5 py-1.5 text-gray-900 dark:text-white placeholder-gray-400 outline-none transition-all duration-150">
                                    </td>

                                    <td class="px-2 py-2">
                                        <input type="number" :name="`rows[${i}][weight]`" x-model.number="row.weight"
                                            :readonly="!row.editing" min="1" max="20"
                                            @keydown.escape="row.editing = false; $event.target.blur()"
                                            :class="row.editing
                                                ? 'bg-white dark:bg-gray-900 ring-2 ring-blue-500/60 border-blue-500 cursor-text'
                                                : 'bg-transparent ring-0 border-transparent cursor-default'"
                                            class="w-20 rounded-lg border px-2.5 py-1.5 text-gray-900 dark:text-white tabular-nums outline-none transition-all duration-150">
                                    </td>

                                    <td class="px-4 py-2 text-right">
                                        <button type="button" @click="remove(i)" title="Retirer cette ligne"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg
                                                   border border-red-300 dark:border-red-800/70
                                                   text-red-600 dark:text-red-400
                                                   hover:bg-red-50 dark:hover:bg-red-900/25 hover:border-red-400
                                                   active:bg-red-100 dark:active:bg-red-900/45 active:scale-95
                                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50
                                                   transition-all duration-150">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-width="2.5" d="M18 12H6" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            <tr>
                                <td colspan="4" class="px-4 py-3">
                                    <button type="button" @click="add()" title="Ajouter une ligne"
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg
                                               border border-emerald-300 dark:border-emerald-800/70
                                               text-emerald-600 dark:text-emerald-400
                                               hover:bg-emerald-500 hover:border-emerald-500 hover:text-white
                                               active:bg-emerald-600 active:scale-95
                                               focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50
                                               transition-all duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-width="2.5" d="M12 6v12M6 12h12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-4 bg-gray-50 dark:bg-gray-900/40 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 transition shadow-sm">
                        Valider la grille
                    </button>
                </div>
            </div>
        </form>

    @else
        {{-- ══════════ Temps 2 : saisir les notes ══════════ --}}
        @php
            // Notes et coefficients passés en JSON : la moyenne se recalcule à
            // la frappe sans générer de JavaScript en boucle depuis Blade.
            $jsScores  = $evaluation->scores->mapWithKeys(fn($r) => [$r->id => $r->score !== null ? (float) $r->score : null]);
            $jsWeights = $evaluation->scores->mapWithKeys(fn($r) => [$r->id => (int) $r->weight_snapshot]);
        @endphp

        <form method="POST" action="{{ encrypted_route('stages.evaluation.update', $stage) }}"
              x-data="{
                  scores: {{ Js::from($jsScores) }},
                  weights: {{ Js::from($jsWeights) }},
                  total() {
                      let num = 0, den = 0;
                      for (const id in this.weights) {
                          const v = this.scores[id];
                          if (v === null || v === '' || isNaN(Number(v))) continue;
                          num += Number(v) * this.weights[id];
                          den += this.weights[id];
                      }
                      return den ? num / den : null;
                  }
              }">
            @csrf @method('PUT')

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                <th class="w-12 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Critère d'évaluation</th>
                                <th class="w-24 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Coef.</th>
                                <th class="w-40 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-400">Note /{{ $max }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluation->scores as $i => $row)
                                <tr class="border-b border-gray-100 dark:border-gray-700/60">
                                    <td class="px-4 py-3 text-gray-400 tabular-nums">{{ $i + 1 }}</td>

                                    <td class="px-4 py-3">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $row->label_snapshot }}</span>
                                        @if($row->is_auto && $row->computed_score !== null)
                                            <span class="ml-2 text-xs text-blue-600 dark:text-blue-400">
                                                calculé {{ number_format((float) $row->computed_score, 2, ',', ' ') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 tabular-nums">{{ $row->weight_snapshot }}</td>

                                    <td class="px-4 py-3 text-right">
                                        @if($locked)
                                            <span class="font-semibold text-gray-900 dark:text-white tabular-nums">
                                                {{ number_format((float) $row->score, 2, ',', ' ') }}
                                            </span>
                                        @else
                                            <input type="number" name="scores[{{ $row->id }}][score]"
                                                x-model="scores[{{ $row->id }}]"
                                                step="0.25" min="0" max="{{ $max }}"
                                                value="{{ $row->score !== null ? (float) $row->score : '' }}"
                                                placeholder="—"
                                                class="w-24 text-right rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm tabular-nums">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Ligne de total --}}
                            <tr class="bg-gray-50 dark:bg-gray-900/50 font-semibold">
                                <td class="px-4 py-4"></td>
                                <td class="px-4 py-4 text-gray-900 dark:text-white">Total</td>
                                <td class="px-4 py-4 text-gray-500 dark:text-gray-400 tabular-nums">
                                    {{ $evaluation->scores->sum('weight_snapshot') }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    @if($locked)
                                        <span class="text-xl text-gray-900 dark:text-white tabular-nums">
                                            {{ number_format((float) $evaluation->final_score, 2, ',', ' ') }}/{{ $max }}
                                        </span>
                                    @else
                                        <span class="text-xl text-gray-900 dark:text-white tabular-nums"
                                              x-text="total() === null ? '—' : total().toFixed(2).replace('.', ',') + '/{{ $max }}'"></span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if(!$locked)
                    <div class="px-4 py-4 bg-gray-50 dark:bg-gray-900/40 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 transition shadow-sm">
                            Valider les notes
                        </button>
                    </div>
                @endif
            </div>
        </form>

        @if(!$locked)
            {{-- Formulaire distinct : imbriquer ce bouton dans celui des notes
                 le ferait partir en PUT vers la mauvaise action. --}}
            <form method="POST" action="{{ encrypted_route('stages.evaluation.grid.edit', $stage) }}" class="mt-4 inline-block">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Modifier les critères
                </button>
            </form>
        @endif

        @if(!$locked && $evaluation->scores->whereNotNull('score')->count() === $evaluation->scores->count() && $evaluation->scores->isNotEmpty())
            <form method="POST" action="{{ encrypted_route('stages.evaluation.finalize', $stage) }}" class="mt-4"
                  data-swal-title="Finaliser l'évaluation"
                  data-swal-text="La note sera figée, ainsi que les libellés, les coefficients et les chiffres d'assiduité."
                  data-swal-icon="question"
                  data-swal-color="#059669"
                  data-swal-confirm="Oui, finaliser">
                @csrf
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-medium hover:bg-emerald-700 transition shadow-sm">
                    Finaliser l'évaluation
                </button>
            </form>
        @endif

        @if($locked)
            <form method="POST" action="{{ encrypted_route('stages.evaluation.reopen', $stage) }}" class="mt-4"
                  data-swal-title="Rouvrir l'évaluation"
                  data-swal-text="La note redeviendra modifiable et le rapport repassera en document provisoire."
                  data-swal-icon="warning"
                  data-swal-color="#d97706"
                  data-swal-confirm="Oui, rouvrir">
                @csrf
                <button type="submit"
                    class="px-4 py-2.5 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 font-medium hover:bg-amber-200 dark:hover:bg-amber-800/50 transition">
                    Rouvrir pour correction
                </button>
            </form>
        @endif
    @endif
</x-app-layout>
