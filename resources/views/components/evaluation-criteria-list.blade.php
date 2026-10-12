@props(['title', 'subtitle' => null, 'criteria', 'typestages'])

<div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
    <div class="bg-[#f6f5f0] dark:bg-[#131519] px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630]">
        <h3 class="text-sm font-bold text-slate-800 dark:text-[#f8fafc]">{{ $title }}</h3>
        @if($subtitle)
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="p-3.5 sm:p-4">
        @if($criteria->isEmpty())
            <p class="text-xs text-slate-500 dark:text-slate-400">Aucun critère configuré pour le moment.</p>
        @else
            <div class="space-y-2.5">
                @foreach($criteria as $c)
                    <div x-data="{ editing: false }"
                         class="rounded-lg border border-[#dcdad2]/60 dark:border-[#222630] bg-[#fcfbf9] dark:bg-[#181c24] {{ $c->is_active ? '' : 'opacity-60' }}">

                        {{-- Ligne de lecture --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 p-3 sm:p-3.5">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <h4 class="font-semibold text-xs text-slate-900 dark:text-[#f8fafc]">{{ $c->label }}</h4>

                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#f6f5f0] dark:bg-[#1f232d] text-slate-600 dark:text-slate-300 border border-[#dcdad2] dark:border-[#222630]">
                                        coef. {{ $c->weight }}
                                    </span>

                                    @if($c->is_auto)
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            Calculé
                                        </span>
                                    @endif

                                    @if(!$c->is_active)
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            Retiré
                                        </span>
                                    @endif
                                </div>

                                @if($c->description)
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $c->description }}</p>
                                @endif

                                @if($c->is_auto && $c->autoSourceLabel())
                                    <p class="mt-0.5 text-[10px] text-blue-600 dark:text-blue-400 font-mono">{{ $c->autoSourceLabel() }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="editing = !editing"
                                    class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-slate-700 dark:text-slate-300 transition">
                                    Modifier
                                </button>

                                <form method="POST" action="{{ route('admin.evaluations.criteres.toggle', $c) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-slate-700 dark:text-slate-300 transition">
                                        {{ $c->is_active ? 'Retirer' : 'Réactiver' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.evaluations.criteres.destroy', $c) }}"
                                      data-confirm-delete
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800 transition">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Formulaire d'édition --}}
                        <div x-show="editing" x-cloak class="border-t border-[#dcdad2]/60 dark:border-[#222630] p-3.5 bg-[#f6f5f0]/50 dark:bg-[#131519]/50 rounded-b-lg">
                            <form method="POST" action="{{ route('admin.evaluations.criteres.update', $c) }}"
                                  x-data="{ auto: {{ $c->is_auto ? 'true' : 'false' }} }"
                                  class="space-y-3">
                                @csrf @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Intitulé</label>
                                        <input type="text" name="label" value="{{ $c->label }}" required maxlength="120"
                                            class="w-full text-xs px-3 py-1.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fcfbf9] dark:bg-[#181c24] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">S'applique à</label>
                                        <select name="typestage_id"
                                            class="w-full text-xs px-3 py-1.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fcfbf9] dark:bg-[#181c24] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <option value="" @selected($c->typestage_id === null)>Toutes les grilles (commun)</option>
                                            @foreach($typestages as $t)
                                                <option value="{{ $t->id }}" @selected($c->typestage_id === $t->id)>{{ $t->libelle }} uniquement</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Description</label>
                                        <textarea name="description" rows="2" maxlength="1000"
                                            class="w-full text-xs px-3 py-1.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fcfbf9] dark:bg-[#181c24] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $c->description }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Coefficient</label>
                                        <input type="number" name="weight" value="{{ $c->weight }}" min="1" max="20" required
                                            class="w-full text-xs px-3 py-1.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fcfbf9] dark:bg-[#181c24] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Ordre d'affichage</label>
                                        <input type="number" name="sort_order" value="{{ $c->sort_order }}" min="0" max="999"
                                            class="w-full text-xs px-3 py-1.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fcfbf9] dark:bg-[#181c24] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>

                                    <div class="md:col-span-2 border-t border-[#dcdad2]/60 dark:border-[#222630] pt-2.5">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="is_auto" value="1" x-model="auto" @checked($c->is_auto)
                                                class="rounded border-[#dcdad2] dark:border-[#222630] text-blue-600">
                                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                                Note calculée automatiquement
                                            </span>
                                        </label>

                                        <div x-show="auto" x-cloak class="mt-2">
                                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Base de calcul</label>
                                            <select name="auto_source"
                                                class="w-full md:w-2/3 text-xs px-3 py-1.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#fcfbf9] dark:bg-[#181c24] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                @foreach(\App\Models\EvaluationCriterion::AUTO_SOURCES as $key => $label)
                                                    <option value="{{ $key }}" @selected($c->auto_source === $key)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-2">
                                    <button type="submit"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition shadow-xs">
                                        Enregistrer
                                    </button>
                                    <button type="button" @click="editing = false"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] transition">
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
