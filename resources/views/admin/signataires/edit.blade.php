<x-app-layout title="Modifier le Signataire">

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
            font-size: .8rem;
            font-weight: 600;
            padding: .4rem .85rem;
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
            font-size: .8rem;
            font-weight: 500;
            padding: .4rem .85rem;
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

        .ks-input {
            width: 100%;
            height: 32px;
            padding: 0 .6rem;
            font-size: .75rem;
            background: #faf9f6;
            border: 1px solid #dcdad2;
            border-radius: 6px;
            color: #241f1b;
            transition: all .15s ease;
        }
        .dark .ks-input {
            background: #181c24 !important;
            border-color: #2b3240 !important;
            color: #f1f5f9 !important;
        }
        .ks-input:focus {
            outline: none;
            border-color: #1c3e96;
            box-shadow: 0 0 0 1px #1c3e96;
        }
    </style>

    <div class="w-full max-w-xl mx-auto px-3 sm:px-5 py-4 space-y-3.5">
        <!-- En-tête -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('signataires.index') }}" class="ks-btn-secondary ks-btn-xs !px-2" title="Retour">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-[#241f1b] dark:text-white leading-tight">Modifier le Signataire</h1>
                    <p class="text-[11px] text-[#6e6862] dark:text-slate-400">Modifiez les informations de {{ $signataire->nom }}</p>
                </div>
            </div>
        </div>

        <!-- Formulaire compact -->
        <div class="ks-sec p-4 sm:p-5">
            <form action="{{ encrypted_route('signataires.update', $signataire) }}" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="nom" class="block text-[11px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                            Nom <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $signataire->nom) }}" required
                            class="ks-input" placeholder="Ex: Jean Dupont">
                        @error('nom')
                        <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-[11px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $signataire->email) }}"
                            class="ks-input" placeholder="Ex: jean.dupont@entreprise.com" {{ $signataire->user ? 'readonly' : '' }}>
                        @error('email')
                        <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="poste" class="block text-[11px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                            Poste <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="poste" id="poste" value="{{ old('poste', $signataire->poste) }}" required
                            class="ks-input" placeholder="Ex: Directeur Général">
                        @error('poste')
                        <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="sigle" class="block text-[11px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                Sigle <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="sigle" id="sigle" value="{{ old('sigle', $signataire->sigle) }}" required
                                class="ks-input" placeholder="Ex: DG">
                            @error('sigle')
                            <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ordre" class="block text-[11px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                Ordre
                            </label>
                            <input type="number" name="ordre" id="ordre" value="{{ old('ordre', $signataire->ordre) }}" min="1"
                                class="ks-input" placeholder="1">
                            @error('ordre')
                            <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#ecebe3] dark:border-[#222630]">
                    <a href="{{ route('signataires.index') }}" class="ks-btn-secondary ks-btn-xs">
                        Annuler
                    </a>
                    <button type="submit" class="ks-btn-primary ks-btn-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>