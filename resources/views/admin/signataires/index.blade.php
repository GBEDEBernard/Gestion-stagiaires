<x-app-layout title="Signataires">

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #15181f !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
        }

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 6px;
            font-size: .85rem;
            font-weight: 600;
            padding: .45rem .9rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #bfbab2;
            border-radius: 6px;
            font-size: .85rem;
            font-weight: 500;
            padding: .45rem .9rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary { background: #131519; color: #fff; border-color: #222630; }
        .ks-btn-secondary:hover { border-color: #1c3e96; color: #1c3e96; }

        .ks-btn-xs {
            min-height: 28px !important;
            padding: .25rem .6rem !important;
            font-size: .75rem !important;
            font-weight: 600;
            border-radius: 6px !important;
            white-space: nowrap;
        }

        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.04);
            transition: border-color .15s ease;
        }
        .dark .ks-sec {
            background: #131519 !important;
            border-color: #222630 !important;
            box-shadow: none;
        }
    </style>

    <div class="w-full max-w-[1500px] mx-auto px-3 sm:px-5 py-3 space-y-3.5">

        {{-- ── EN-TÊTE COMPACT ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-[#241f1b] dark:text-white">
                    Signataires
                </h1>
                <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                    Gérez les signataires habilités pour les permissions et attestations.
                </p>
            </div>
            <a href="{{ route('signataires.create') }}" class="ks-btn-primary ks-btn-xs !text-[13px] !px-3.5 !py-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau Signataire
            </a>
        </div>

        {{-- ── TABLEAU COMPACT ── --}}
        <div class="ks-sec overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#6e6862] dark:text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                            <th class="px-3.5 py-2.5">ID</th>
                            <th class="px-3.5 py-2.5">Nom</th>
                            <th class="px-3 py-2.5">Poste</th>
                            <th class="px-3 py-2.5">Sigle</th>
                            <th class="px-3 py-2.5">Email</th>
                            <th class="px-3 py-2.5">Ordre</th>
                            <th class="px-3.5 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        @forelse($signataires as $signataire)
                        <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                            <td class="px-3.5 py-2.5 font-mono text-[#6e6862] dark:text-slate-400">
                                #{{ $signataire->id }}
                            </td>
                            <td class="px-3.5 py-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 font-bold text-[10px] flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($signataire->nom, 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-[#241f1b] dark:text-white">{{ $signataire->nom }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-2.5 text-[#6e6862] dark:text-slate-300">
                                {{ $signataire->poste }}
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 border border-[#d8e0f5] dark:border-blue-900/50 rounded font-semibold text-[11px]">
                                    {{ $signataire->sigle }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5 font-mono text-[#6e6862] dark:text-slate-400">
                                {{ $signataire->email ?? '—' }}
                            </td>
                            <td class="px-3 py-2.5 font-mono text-[#6e6862] dark:text-slate-400">
                                {{ $signataire->ordre ?? '—' }}
                            </td>
                            <td class="px-3.5 py-2.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ encrypted_route('signataires.edit', $signataire) }}"
                                       class="ks-btn-secondary ks-btn-xs !h-7 !px-2 cursor-pointer" title="Modifier" data-confirm-edit>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ encrypted_route('signataires.destroy', $signataire) }}" method="POST" class="inline" data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="ks-btn-secondary ks-btn-xs !h-7 !px-2 text-rose-600 dark:text-rose-400 hover:border-rose-400 cursor-pointer" title="Supprimer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-xs text-[#6e6862] dark:text-slate-400">
                                Aucun signataire enregistré.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($signataires->hasPages())
            <div class="p-3 border-t border-[#ecebe3] dark:border-[#222630] flex justify-center bg-[#faf9f6]/40 dark:bg-[#181c24]/40">
                {{ $signataires->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>