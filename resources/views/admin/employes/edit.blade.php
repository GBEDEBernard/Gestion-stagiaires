<x-app-layout>
    @php
        $p = $employe->personnel;
    @endphp

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #15181f !important; }
        header { background-color: transparent !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }
        .dark header { background-color: #131519 !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }

        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.05), 0 8px 24px -16px rgba(36,31,27,.12);
        }
        .dark .ks-sec {
            background: #131519 !important;
            border-color: #222630 !important;
            box-shadow: none;
        }

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            padding: .45rem .85rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover {
            background: #162f73;
            border-color: #162f73;
        }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #dcdad2;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 500;
            padding: .45rem .85rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary {
            background: #131519;
            color: #f8fafc;
            border-color: #222630;
        }
        .ks-btn-secondary:hover {
            border-color: #1c3e96;
            color: #1c3e96;
        }
    </style>

    <div class="w-full max-w-2xl mx-auto px-3 sm:px-6 pt-3 sm:pt-4 pb-8 space-y-4">

        {{-- ── En-tête Compact ── --}}
        <div class="flex items-center gap-2.5 pb-1 border-b border-[#dcdad2] dark:border-[#222630]">
            <a href="{{ route('employes.index') }}" class="ks-btn-secondary !p-1.5" title="Retour à la liste">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-[#1c3e96] dark:bg-[#60a5fa]"></span>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">
                        Gestion du personnel · Modification
                    </p>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc] mt-0.5">
                    Modifier l'employé #{{ $employe->id }}
                </h1>
            </div>
        </div>

        {{-- ── Erreurs de validation ── --}}
        @if($errors->any())
            <div class="p-3 rounded-[10px] bg-[#fee2e2] dark:bg-[rgba(185,28,28,0.22)] border border-[#b91c1c]/30 text-[#b91c1c] dark:text-[#f87171] text-xs">
                <p class="font-bold mb-1">Veuillez corriger les informations suivantes :</p>
                <ul class="list-disc list-inside space-y-0.5 pl-2 opacity-90">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── Avertissement Compte Actif ── --}}
        @if($p?->user)
            <div class="px-3.5 py-2.5 rounded-[10px] bg-[#fef3c7] dark:bg-[rgba(180,83,9,0.22)] border border-[#b45309]/30 text-[#b45309] dark:text-[#fbbf24] text-xs flex items-center gap-2.5">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                <span>Un compte utilisateur est déjà rattaché. La modification de l'adresse email professionnelle impactera les identifiants de ce collaborateur.</span>
            </div>
        @endif

        {{-- ── Formulaire Compact ── --}}
        <div class="ks-sec overflow-hidden">
            <form action="{{ route('employes.update', $employe) }}" method="POST" class="p-4 sm:p-5 space-y-4">
                @csrf
                @method('PUT')

                {{-- Section 1 : Identité & Contact --}}
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[#241f1b] dark:text-[#f8fafc] pb-2 border-b border-[#dcdad2]/60 dark:border-[#222630] mb-3">
                        Identité & Coordonnées
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Nom <span class="text-[#b91c1c]">*</span></label>
                            <input type="text" name="nom" value="{{ old('nom', $p?->nom) }}" required
                                   class="w-full h-8.5 px-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                            @error('nom') <p class="mt-1 text-[10.5px] text-[#b91c1c]">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Prénom <span class="text-[#b91c1c]">*</span></label>
                            <input type="text" name="prenom" value="{{ old('prenom', $p?->prenom) }}" required
                                   class="w-full h-8.5 px-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                            @error('prenom') <p class="mt-1 text-[10.5px] text-[#b91c1c]">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Email professionnel <span class="text-[#b91c1c]">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $p?->email) }}" required
                                   class="w-full h-8.5 px-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                            @error('email') <p class="mt-1 text-[10.5px] text-[#b91c1c]">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone', $p?->telephone) }}"
                                   placeholder="06 12 34 56 78"
                                   class="w-full h-8.5 px-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Genre</label>
                            <select name="genre" class="w-full h-8.5 px-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                                <option value="" {{ old('genre', $p?->genre) ? '' : 'selected' }}>Sélectionner</option>
                                <option value="Masculin" {{ old('genre', $p?->genre) == 'Masculin' ? 'selected' : '' }}>Masculin</option>
                                <option value="Féminin"  {{ old('genre', $p?->genre) == 'Féminin'  ? 'selected' : '' }}>Féminin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Date de naissance</label>
                            <input type="date" name="date_naissance" value="{{ old('date_naissance', $p?->date_naissance) }}"
                                   class="w-full h-8.5 px-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                        </div>
                    </div>
                </div>

                {{-- Section 2 : Affectation Professionnelle --}}
                <div class="pt-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[#241f1b] dark:text-[#f8fafc] pb-2 border-b border-[#dcdad2]/60 dark:border-[#222630] mb-3">
                        Affectation en entreprise
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Domaine <span class="text-[#b91c1c]">*</span></label>
                            <select name="domaine_id" required
                                    class="w-full h-8.5 px-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                                @foreach($domaines as $d)
                                    <option value="{{ $d->id }}" {{ old('domaine_id', $employe->domaine_id) == $d->id ? 'selected' : '' }}>{{ $d->nom }}</option>
                                @endforeach
                            </select>
                            @error('domaine_id') <p class="mt-1 text-[10.5px] text-[#b91c1c]">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Site de travail <span class="text-[#b91c1c]">*</span></label>
                            <select name="site_id" required
                                    class="w-full h-8.5 px-2.5 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                                @foreach($sites as $s)
                                    <option value="{{ $s->id }}" {{ old('site_id', $employe->site_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('site_id') <p class="mt-1 text-[10.5px] text-[#b91c1c]">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] mb-1">Intitulé du poste</label>
                            <input type="text" name="poste" value="{{ old('poste', $employe->poste) }}"
                                   placeholder="Ex : Développeur Web, Responsable Administratif, Technicien..."
                                   class="w-full h-8.5 px-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-xs text-[#241f1b] dark:text-[#f8fafc] focus:outline-none focus:border-[#1c3e96]">
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#dcdad2]/60 dark:border-[#222630]">
                    <a href="{{ route('employes.index') }}" class="ks-btn-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="ks-btn-primary">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span>Mettre à jour</span>
                    </button>
                </div>

            </form>
        </div>

    </div>
</x-app-layout>