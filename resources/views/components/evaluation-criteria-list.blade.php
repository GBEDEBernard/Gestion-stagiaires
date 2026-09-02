@props(['title', 'subtitle' => null, 'criteria', 'typestages'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $title }}</h3>
        @if($subtitle)
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="p-6">
        @if($criteria->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucun critère pour l'instant.</p>
        @else
            <div class="space-y-3">
                @foreach($criteria as $c)
                    <div x-data="{ editing: false }"
                         class="rounded-xl border border-gray-100 dark:border-gray-700 {{ $c->is_active ? '' : 'opacity-60' }}">

                        {{-- Ligne de lecture --}}
                        <div class="flex flex-wrap items-start justify-between gap-3 p-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $c->label }}</h4>

                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        coef. {{ $c->weight }}
                                    </span>

                                    @if($c->is_auto)
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                                            Calculé
                                        </span>
                                    @endif

                                    @if(!$c->is_active)
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">
                                            Retiré
                                        </span>
                                    @endif
                                </div>

                                @if($c->description)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $c->description }}</p>
                                @endif

                                @if($c->is_auto && $c->autoSourceLabel())
                                    <p class="mt-1 text-xs text-blue-600 dark:text-blue-400">{{ $c->autoSourceLabel() }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="editing = !editing"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                    Modifier
                                </button>

                                <form method="POST" action="{{ route('admin.evaluations.criteres.toggle', $c) }}">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                        {{ $c->is_active ? 'Retirer' : 'Réactiver' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.evaluations.criteres.destroy', $c) }}"
                                      data-swal-title="Supprimer ce critère ?"
                                      data-swal-text="Préférez « Retirer » pour le sortir des futures grilles sans toucher aux évaluations déjà rendues."
                                      data-swal-icon="warning"
                                      data-swal-color="#dc2626"
                                      data-swal-confirm="Oui, supprimer">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800/50 transition">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Formulaire d'édition --}}
                        <div x-show="editing" x-cloak class="border-t border-gray-100 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/40 rounded-b-xl">
                            <form method="POST" action="{{ route('admin.evaluations.criteres.update', $c) }}"
                                  x-data="{ auto: {{ $c->is_auto ? 'true' : 'false' }} }">
                                @csrf @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Intitulé</label>
                                        <input type="text" name="label" value="{{ $c->label }}" required maxlength="120"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">S'applique à</label>
                                        <select name="typestage_id"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                                            <option value="" @selected($c->typestage_id === null)>Toutes les grilles (commun)</option>
                                            @foreach($typestages as $t)
                                                <option value="{{ $t->id }}" @selected($c->typestage_id === $t->id)>{{ $t->libelle }} uniquement</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Description</label>
                                        <textarea name="description" rows="2" maxlength="1000"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">{{ $c->description }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Coefficient</label>
                                        <input type="number" name="weight" value="{{ $c->weight }}" min="1" max="20" required
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Ordre</label>
                                        <input type="number" name="sort_order" value="{{ $c->sort_order }}" min="0" max="999"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="is_auto" value="1" x-model="auto" @checked($c->is_auto)
                                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm">
                                            <span class="text-sm text-gray-700 dark:text-gray-200">Note calculée automatiquement</span>
                                        </label>

                                        <div x-show="auto" x-cloak class="mt-3">
                                            <select name="auto_source"
                                                class="w-full md:w-2/3 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                                                @foreach(\App\Models\EvaluationCriterion::AUTO_SOURCES as $key => $label)
                                                    <option value="{{ $key }}" @selected($c->auto_source === $key)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center gap-2">
                                    <button type="submit"
                                        class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 transition">
                                        Enregistrer
                                    </button>
                                    <button type="button" @click="editing = false"
                                        class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
