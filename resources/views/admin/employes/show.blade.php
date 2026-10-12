<x-app-layout>
    @php
        $p = $employe->personnel;
        $initiales = $p ? strtoupper(mb_substr($p->prenom, 0, 1) . mb_substr($p->nom, 0, 1)) : 'EM';
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

        .ks-sec-head {
            padding: .75rem 1rem;
            border-bottom: 1px solid #dcdad2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }
        .dark .ks-sec-head {
            border-bottom-color: #222630 !important;
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

    <div class="w-full max-w-4xl mx-auto px-3 sm:px-6 pt-3 sm:pt-4 pb-8 space-y-4">

        {{-- ── En-tête Compact ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-[#dcdad2] dark:border-[#222630]">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('employes.index') }}" class="ks-btn-secondary !p-1.5" title="Retour à la liste">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-[#1c3e96] dark:bg-[#60a5fa]"></span>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">
                            Fiche Collaborateur · #{{ $employe->id }}
                        </p>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc] mt-0.5">
                        {{ $p ? ($p->prenom . ' ' . $p->nom) : 'Employé' }}
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('employes.edit', $employe) }}" class="ks-btn-secondary">
                    <svg class="w-3.5 h-3.5 text-[#b45309]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                    <span>Modifier</span>
                </a>
                <form action="{{ route('employes.destroy', $employe) }}" method="POST" class="inline" onsubmit="return confirm('Mettre cet employé à la corbeille ?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="ks-btn-secondary !text-[#b91c1c] hover:!border-[#b91c1c]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        <span>Supprimer</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Alertes Flash ── --}}
        @if(session('success'))
            <div class="px-3.5 py-2 rounded-[10px] bg-[#dcfce7] dark:bg-[rgba(22,128,74,0.22)] border border-[#15803d]/30 text-[#15803d] dark:text-[#4ade80] text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="px-3.5 py-2 rounded-[10px] bg-[#fee2e2] dark:bg-[rgba(185,28,28,0.22)] border border-[#b91c1c]/30 text-[#b91c1c] dark:text-[#f87171] text-xs font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- ── Bannière Identité avec Dégradé Harmonisé Violet/Bleu ── --}}
        <div class="rounded-[12px] bg-gradient-to-r from-violet-600/20 via-indigo-600/15 to-blue-600/20 border border-[#dcdad2] dark:border-[#222630] p-4 flex flex-col sm:flex-row items-center sm:items-start gap-4">
            <div class="w-16 h-16 rounded-full bg-[#fffefa] dark:bg-[#131519] border-2 border-[#1c3e96] text-[#1c3e96] dark:text-[#60a5fa] font-bold text-xl flex items-center justify-center shrink-0 shadow-sm">
                {{ $initiales }}
            </div>
            <div class="flex-1 text-center sm:text-left min-w-0">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                    <h2 class="text-lg font-bold text-[#241f1b] dark:text-[#f8fafc] truncate">
                        {{ $p ? ($p->prenom . ' ' . $p->nom) : 'Employé sans fiche' }}
                    </h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#eef1fb] text-[#1c3e96] dark:bg-[#1c3e96]/30 dark:text-[#60a5fa]">
                        Collaborateur
                    </span>
                    @if($p?->user)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-[#15803d]/30 dark:text-[#4ade80]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#15803d]"></span>
                            Compte utilisateur actif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-[#fee2e2] text-[#b91c1c] dark:bg-[#b91c1c]/30 dark:text-[#f87171]">
                            Aucun compte actif
                        </span>
                    @endif
                </div>

                <div class="mt-2 flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-1 text-xs text-[#6e6862] dark:text-[#a89f93]">
                    <span class="inline-flex items-center gap-1 font-mono">
                        <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $p?->email ?: 'Email non renseigné' }}
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $p?->telephone ?: 'Sans téléphone' }}
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $employe->domaine->nom ?? 'Domaine non défini' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Grille 2 Colonnes : Coordonnées & Cadre Professionnel ── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">

            {{-- 1. Informations Personnelles --}}
            <div class="ks-sec">
                <div class="ks-sec-head">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#241f1b] dark:text-[#f8fafc]">Identité & Coordonnées</span>
                </div>
                <div class="p-3 space-y-2 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-[#dcdad2]/60 dark:border-[#222630]">
                        <span class="text-[#6e6862] dark:text-[#a89f93]">Genre</span>
                        <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $p?->genre ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-[#dcdad2]/60 dark:border-[#222630]">
                        <span class="text-[#6e6862] dark:text-[#a89f93]">Date de naissance</span>
                        <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $p?->date_naissance ?? 'Non renseignée' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-[#dcdad2]/60 dark:border-[#222630]">
                        <span class="text-[#6e6862] dark:text-[#a89f93]">Adresse</span>
                        <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $p?->adresse ?? 'Non renseignée' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-[#6e6862] dark:text-[#a89f93]">Date de création</span>
                        <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $employe->created_at?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. Affectation Professionnelle --}}
            <div class="ks-sec">
                <div class="ks-sec-head">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#241f1b] dark:text-[#f8fafc]">Affectation Entreprise</span>
                </div>
                <div class="p-3 space-y-2 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-[#dcdad2]/60 dark:border-[#222630]">
                        <span class="text-[#6e6862] dark:text-[#a89f93]">Domaine</span>
                        <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $employe->domaine->nom ?? 'Non défini' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-[#dcdad2]/60 dark:border-[#222630]">
                        <span class="text-[#6e6862] dark:text-[#a89f93]">Site de travail</span>
                        <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $employe->site->name ?? 'Non défini' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-[#dcdad2]/60 dark:border-[#222630]">
                        <span class="text-[#6e6862] dark:text-[#a89f93]">Intitulé du poste</span>
                        <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $employe->poste ?? 'Collaborateur' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-[#6e6862] dark:text-[#a89f93]">Télétravail</span>
                        <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $p?->user?->canWorkRemotely() ? 'Autorisé' : 'Non activé' }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── 3. Compte Utilisateur & Actions Rapides ── --}}
        <div class="ks-sec">
            <div class="ks-sec-head">
                <span class="text-xs font-bold uppercase tracking-wider text-[#241f1b] dark:text-[#f8fafc]">Compte de connexion & Accès</span>
                @if($p?->user)
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-[#dcfce7] text-[#15803d] dark:bg-[#15803d]/20 dark:text-[#4ade80]">
                        ID Utilisateur : #{{ $p->user->id }}
                    </span>
                @endif
            </div>

            <div class="p-4">
                @if($p?->user)
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pb-3 border-b border-[#dcdad2]/60 dark:border-[#222630] text-xs">
                        <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2]/60 dark:border-[#222630]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] block">Statut du compte</span>
                            <span class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc] capitalize">{{ $p->user->status ?? 'Actif' }}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2]/60 dark:border-[#222630]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] block">Email vérifié</span>
                            <span class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc]">{{ $p->user->email_verified_at ? 'Oui (' . $p->user->email_verified_at->format('d/m/Y') . ')' : 'Non' }}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2]/60 dark:border-[#222630]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93] block">Changement mot de passe</span>
                            <span class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc]">{{ $p->user->must_change_password ? 'Requis à la connexion' : 'Non requis' }}</span>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between flex-wrap gap-2">
                        <div class="text-xs text-[#6e6862] dark:text-[#a89f93]">
                            Dernière activité : <span class="font-medium text-[#241f1b] dark:text-[#f8fafc]">{{ $p->user->updated_at?->diffForHumans() ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('attendance.tracking.user.historique', $p->user) }}" class="ks-btn-secondary !text-xs !py-1.5">
                                Consulter historique pointages
                            </a>
                            <form action="{{ route('employes.generate-account', $employe) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="ks-btn-primary !text-xs !py-1.5">
                                    Renvoyer email d'accès
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                        <div>
                            <p class="font-bold text-[#241f1b] dark:text-[#f8fafc]">Aucun compte utilisateur n'est associé à ce collaborateur</p>
                            <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93] mt-0.5">Générez un accès pour lui permettre de se connecter et de pointer sa présence.</p>
                        </div>
                        <form action="{{ route('employes.generate-account', $employe) }}" method="POST">
                            @csrf
                            <button type="submit" class="ks-btn-primary !py-2 !px-4">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                <span>Générer le compte</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>