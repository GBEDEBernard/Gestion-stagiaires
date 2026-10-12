<x-app-layout>
<div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]" x-data="urgentNotificationManager()">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">Communication & Alertes</p>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">Notifications Urgentes & Ciblage</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-[#94a3b8] mt-0.5">Diffusez des alertes prioritaires avec affichage persistant et suivi des accusés de réception en temps réel.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] px-3.5 py-2 flex items-center gap-2.5 shadow-xs">
                <div class="w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xs">
                    👥
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-[#94a3b8]">Comptes actifs</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-[#f8fafc] leading-tight">{{ $totalActiveUsers }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages Flash --}}
    @if(session('success'))
    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center gap-2 text-xs font-semibold shadow-xs">
        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center gap-2 text-xs font-semibold shadow-xs">
        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    {{-- Formulaire de composition --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-[#222630] bg-slate-50/70 dark:bg-[#131519] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xs">
                    ⚡
                </div>
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-[#f8fafc]">Composer une alerte urgente</h2>
                    <p class="text-[11px] text-slate-500 dark:text-[#94a3b8]">Définissez les détails du message et la cible de diffusion.</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-900/50">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                    Priorité maximale
                </span>
            </div>
        </div>

        <form action="{{ route('admin.notifications.urgent.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-4 text-xs" @submit.prevent="confirmSubmit($event)">
            @csrf

            {{-- 1. Titre & Modèles rapides --}}
            <div class="space-y-1.5">
                <label class="block font-semibold text-slate-800 dark:text-[#f8fafc]">
                    Titre de l'alerte <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       x-model="form.title"
                       required
                       placeholder="Ex: Arrêt d'urgence de la plateforme pour maintenance" 
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-slate-900 dark:text-[#f8fafc] text-xs focus:ring-1 focus:ring-rose-500 focus:border-rose-500 transition">
                
                {{-- Suggestions de titres rapides --}}
                <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                    <span class="text-[10px] text-slate-400">Modèles rapides :</span>
                    <button type="button" @click="form.title = 'Arrêt d\'urgence de la plateforme'" class="px-2 py-0.5 rounded-md text-[11px] bg-slate-100 dark:bg-[#131519] text-slate-600 dark:text-[#94a3b8] hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/30 transition border border-slate-200 dark:border-[#222630]">
                        Arrêt plateforme
                    </button>
                    <button type="button" @click="form.title = 'Réunion générale obligatoire à la Direction'" class="px-2 py-0.5 rounded-md text-[11px] bg-slate-100 dark:bg-[#131519] text-slate-600 dark:text-[#94a3b8] hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/30 transition border border-slate-200 dark:border-[#222630]">
                        Réunion obligatoire
                    </button>
                    <button type="button" @click="form.title = 'Consigne de sécurité importante'" class="px-2 py-0.5 rounded-md text-[11px] bg-slate-100 dark:bg-[#131519] text-slate-600 dark:text-[#94a3b8] hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/30 transition border border-slate-200 dark:border-[#222630]">
                        Consigne sécurité
                    </button>
                    <button type="button" @click="form.title = 'Date limite impérative de soumission des rapports'" class="px-2 py-0.5 rounded-md text-[11px] bg-slate-100 dark:bg-[#131519] text-slate-600 dark:text-[#94a3b8] hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/30 transition border border-slate-200 dark:border-[#222630]">
                        Rapports requis
                    </button>
                </div>
            </div>

            {{-- 2. Message détaillé --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block font-semibold text-slate-800 dark:text-[#f8fafc]">
                        Message détaillé <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[10px] text-slate-400" x-text="form.message.length + '/2000 caractères'"></span>
                </div>
                <textarea name="message" 
                          x-model="form.message"
                          rows="3" 
                          required
                          maxlength="2000"
                          placeholder="Ex: Merci de bien vouloir enregistrer tous vos travaux en cours. Une intervention critique aura lieu..."
                          class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-slate-900 dark:text-[#f8fafc] text-xs focus:ring-1 focus:ring-rose-500 focus:border-rose-500 transition"></textarea>
            </div>

            {{-- 3. Lien d'action optionnel & Pièce jointe en 2 colonnes --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-semibold text-slate-800 dark:text-[#f8fafc]">
                        Lien d'action optionnel (URL)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400 text-xs">
                            🔗
                        </span>
                        <input type="url" 
                               name="url" 
                               x-model="form.url"
                               placeholder="https://… (lien vers document ou formulaire)" 
                               class="w-full pl-8 pr-3 py-2 rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] text-slate-900 dark:text-[#f8fafc] text-xs focus:ring-1 focus:ring-rose-500 focus:border-rose-500 transition">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-semibold text-slate-800 dark:text-[#f8fafc]">
                        Pièce jointe PDF (optionnel)
                    </label>
                    <div class="relative">
                        <div class="flex items-center gap-2">
                            <label class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-dashed border-slate-200 dark:border-[#222630] bg-slate-50/50 dark:bg-[#131519] hover:border-rose-400 transition cursor-pointer group text-xs text-slate-600 dark:text-[#94a3b8]"
                                   :class="form.attachment ? 'border-emerald-400 dark:border-emerald-600 bg-emerald-50/30 dark:bg-emerald-950/20' : ''">
                                <input type="file" 
                                       name="attachment" 
                                       accept=".pdf"
                                       @change="handleAttachment($event)"
                                       class="sr-only">
                                <span>📎</span>
                                <span class="truncate max-w-[180px]" x-text="form.attachment ? form.attachment.name : 'Choisir un fichier PDF'"></span>
                            </label>
                            <button type="button" 
                                    x-show="form.attachment" 
                                    @click="removeAttachment()"
                                    class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition"
                                    title="Supprimer la pièce jointe">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Ciblage des destinataires --}}
            <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-[#222630]">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-[#f8fafc]">Ciblage des destinataires</h3>
                        <p class="text-[11px] text-slate-400">Sélectionnez la portée de transmission de cette alerte.</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-[#131519] text-slate-700 dark:text-[#f8fafc] border border-slate-200 dark:border-[#222630]">
                            <span x-text="recipientCount"></span> destinataire(s)
                        </span>
                    </div>
                </div>

                {{-- Tuiles de sélection de cible --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    {{-- Cible 1: Tous --}}
                    <label @click="selectTarget('all')" :class="form.target_type === 'all' ? 'ring-2 ring-rose-500 bg-rose-50/50 dark:bg-rose-950/30 border-rose-300' : 'bg-slate-50/50 dark:bg-[#131519] border-slate-200 dark:border-[#222630]'" class="p-2.5 rounded-lg border cursor-pointer hover:border-rose-300 transition flex items-center gap-2">
                        <input type="radio" name="target_type" value="all" x-model="form.target_type" class="sr-only">
                        <span class="text-lg">📢</span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">Tous les comptes</p>
                            <p class="text-[10px] text-slate-400 truncate">Organisation entière</p>
                        </div>
                    </label>

                    {{-- Cible 2: Employés --}}
                    <label @click="selectTarget('employes')" :class="form.target_type === 'employes' ? 'ring-2 ring-rose-500 bg-rose-50/50 dark:bg-rose-950/30 border-rose-300' : 'bg-slate-50/50 dark:bg-[#131519] border-slate-200 dark:border-[#222630]'" class="p-2.5 rounded-lg border cursor-pointer hover:border-rose-300 transition flex items-center gap-2">
                        <input type="radio" name="target_type" value="employes" x-model="form.target_type" class="sr-only">
                        <span class="text-lg">👔</span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">Tous les employés</p>
                            <p class="text-[10px] text-slate-400 truncate">Personnel & cadres</p>
                        </div>
                    </label>

                    {{-- Cible 3: Poste --}}
                    <label @click="selectTarget('poste')" :class="form.target_type === 'poste' ? 'ring-2 ring-rose-500 bg-rose-50/50 dark:bg-rose-950/30 border-rose-300' : 'bg-slate-50/50 dark:bg-[#131519] border-slate-200 dark:border-[#222630]'" class="p-2.5 rounded-lg border cursor-pointer hover:border-rose-300 transition flex items-center gap-2">
                        <input type="radio" name="target_type" value="poste" x-model="form.target_type" class="sr-only">
                        <span class="text-lg">🎯</span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">Par poste</p>
                            <p class="text-[10px] text-slate-400 truncate">DT, DTA, etc.</p>
                        </div>
                    </label>

                    {{-- Cible 4: Stagiaires --}}
                    <label @click="selectTarget('stagiaires')" :class="form.target_type === 'stagiaires' ? 'ring-2 ring-rose-500 bg-rose-50/50 dark:bg-rose-950/30 border-rose-300' : 'bg-slate-50/50 dark:bg-[#131519] border-slate-200 dark:border-[#222630]'" class="p-2.5 rounded-lg border cursor-pointer hover:border-rose-300 transition flex items-center gap-2">
                        <input type="radio" name="target_type" value="stagiaires" x-model="form.target_type" class="sr-only">
                        <span class="text-lg">🎓</span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">Tous les stagiaires</p>
                            <p class="text-[10px] text-slate-400 truncate">Tous types confondus</p>
                        </div>
                    </label>

                    {{-- Cible 5: Type de stage --}}
                    <label @click="selectTarget('typestage')" :class="form.target_type === 'typestage' ? 'ring-2 ring-rose-500 bg-rose-50/50 dark:bg-rose-950/30 border-rose-300' : 'bg-slate-50/50 dark:bg-[#131519] border-slate-200 dark:border-[#222630]'" class="p-2.5 rounded-lg border cursor-pointer hover:border-rose-300 transition flex items-center gap-2">
                        <input type="radio" name="target_type" value="typestage" x-model="form.target_type" class="sr-only">
                        <span class="text-lg">📑</span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">Type de stage</p>
                            <p class="text-[10px] text-slate-400 truncate">Académique / pro</p>
                        </div>
                    </label>

                    {{-- Cible 6: Domaine --}}
                    <label @click="selectTarget('domaine')" :class="form.target_type === 'domaine' ? 'ring-2 ring-rose-500 bg-rose-50/50 dark:bg-rose-950/30 border-rose-300' : 'bg-slate-50/50 dark:bg-[#131519] border-slate-200 dark:border-[#222630]'" class="p-2.5 rounded-lg border cursor-pointer hover:border-rose-300 transition flex items-center gap-2">
                        <input type="radio" name="target_type" value="domaine" x-model="form.target_type" class="sr-only">
                        <span class="text-lg">🏢</span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">Par domaine</p>
                            <p class="text-[10px] text-slate-400 truncate">Direction / service</p>
                        </div>
                    </label>

                    {{-- Cible 7: Individuelle --}}
                    <label @click="selectTarget('individual')" :class="form.target_type === 'individual' ? 'ring-2 ring-rose-500 bg-rose-50/50 dark:bg-rose-950/30 border-rose-300' : 'bg-slate-50/50 dark:bg-[#131519] border-slate-200 dark:border-[#222630]'" class="p-2.5 rounded-lg border cursor-pointer hover:border-rose-300 transition flex items-center gap-2 sm:col-span-2">
                        <input type="radio" name="target_type" value="individual" x-model="form.target_type" class="sr-only">
                        <span class="text-lg">👤</span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">Sélection individuelle</p>
                            <p class="text-[10px] text-slate-400 truncate">Choisir un ou plusieurs comptes spécifiques</p>
                        </div>
                    </label>
                </div>

                {{-- Options secondaires conditionnelles --}}
                <div x-show="form.target_type === 'poste'" x-cloak class="p-3 rounded-lg bg-slate-50/70 dark:bg-[#131519] border border-slate-200 dark:border-[#222630] space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase">
                        Choisir le poste concerné
                    </label>
                    <select name="target_value" x-model="form.target_value" @change="fetchRecipientCount()" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] text-xs text-slate-900 dark:text-[#f8fafc]">
                        <option value="">-- Sélectionnez un poste --</option>
                        @foreach($targetOptions['postes'] as $poste)
                        <option value="{{ $poste }}">{{ $poste }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="form.target_type === 'typestage'" x-cloak class="p-3 rounded-lg bg-slate-50/70 dark:bg-[#131519] border border-slate-200 dark:border-[#222630] space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase">
                        Choisir le type de stage
                    </label>
                    <select name="target_value" x-model="form.target_value" @change="fetchRecipientCount()" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] text-xs text-slate-900 dark:text-[#f8fafc]">
                        <option value="">-- Sélectionnez un type de stage --</option>
                        @foreach($targetOptions['typeStages'] as $typeStage)
                        <option value="{{ $typeStage->id }}">{{ $typeStage->libelle }} ({{ $typeStage->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="form.target_type === 'domaine'" x-cloak class="p-3 rounded-lg bg-slate-50/70 dark:bg-[#131519] border border-slate-200 dark:border-[#222630] space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase">
                        Choisir la direction / domaine
                    </label>
                    <select name="target_value" x-model="form.target_value" @change="fetchRecipientCount()" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] text-xs text-slate-900 dark:text-[#f8fafc]">
                        <option value="">-- Sélectionnez un domaine --</option>
                        @foreach($targetOptions['domaines'] as $domaine)
                        <option value="{{ $domaine->id }}">{{ $domaine->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="form.target_type === 'individual'" x-cloak class="p-3 rounded-lg bg-slate-50/70 dark:bg-[#131519] border border-slate-200 dark:border-[#222630] space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase">
                            Rechercher des utilisateurs (<span x-text="form.individual_ids.length"></span> sélectionnés)
                        </label>
                    </div>
                    
                    <input type="text" 
                           x-model="userSearchQuery" 
                           placeholder="Rechercher par nom ou email…"
                           class="w-full px-3 py-1.5 rounded-lg border border-slate-200 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] text-xs text-slate-900 dark:text-[#f8fafc]">

                    <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-1 bg-[#fffefa] dark:bg-[#161a22] p-2 rounded-lg border border-slate-200 dark:border-[#222630]">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <label class="flex items-center justify-between p-1.5 rounded hover:bg-slate-100 dark:hover:bg-[#1f232d] cursor-pointer text-xs">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" 
                                           name="individual_ids[]"
                                           :value="user.id" 
                                           x-model="form.individual_ids" 
                                           @change="fetchRecipientCount()"
                                           class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-[#f8fafc]" x-text="user.name"></p>
                                        <p class="text-slate-400 text-[10px]" x-text="user.email"></p>
                                    </div>
                                </div>
                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-slate-100 dark:bg-[#131519] text-slate-500" x-text="user.role"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Bouton d'action soumission --}}
            <div class="pt-3 border-t border-slate-200 dark:border-[#222630] flex items-center justify-end">
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg font-semibold text-xs text-white bg-rose-600 hover:bg-rose-700 transition shadow-xs">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Diffuser l'alerte urgente</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Historique & Accusés de Réception --}}
    <div class="rounded-xl border border-slate-200/80 dark:border-[#222630] bg-[#fffefa] dark:bg-[#161a22] shadow-xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-[#222630] bg-slate-50/70 dark:bg-[#131519] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs">
                    📋
                </div>
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-[#f8fafc]">Historique & Accusés de réception</h2>
                    <p class="text-[11px] text-slate-500 dark:text-[#94a3b8]">Suivez la prise de connaissance de chaque alerte en temps réel.</p>
                </div>
            </div>
        </div>

        <div class="p-4">
            @if($recentBatches->isEmpty())
            <div class="py-10 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-[#131519] flex items-center justify-center text-slate-400 text-xl mb-2">
                    📭
                </div>
                <p class="font-semibold text-slate-700 dark:text-[#f8fafc] text-xs">Aucune alerte urgente diffusée pour le moment.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Les alertes envoyées apparaîtront ici avec leur taux d'acquittement.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($recentBatches as $batch)
                <div class="p-3.5 rounded-lg border border-slate-200 dark:border-[#222630] bg-slate-50/50 dark:bg-[#131519] flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    
                    {{-- Infos alerte --}}
                    <div class="space-y-1 flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                {{ $batch->target_label }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-medium">
                                Le {{ \Carbon\Carbon::parse($batch->created_at)->format('d/m/Y à H:i') }}
                            </span>
                            @if($batch->sender)
                            <span class="text-[11px] text-slate-500 dark:text-[#94a3b8]">
                                par <strong>{{ $batch->sender->name }}</strong>
                            </span>
                            @endif
                        </div>

                        <h3 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc] truncate">
                            {{ $batch->title }}
                        </h3>

                        <p class="text-xs text-slate-600 dark:text-[#94a3b8] line-clamp-2">
                            {{ $batch->message }}
                        </p>

                        @if($batch->attachment_path)
                        <a href="{{ $batch->attachment_url }}" target="_blank" class="inline-flex items-center gap-1.5 mt-1 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            📎 {{ $batch->attachment_name }}
                        </a>
                        @endif
                    </div>

                    {{-- Taux de lecture & Barre de progression --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 shrink-0">
                        <div class="w-44 space-y-1">
                            <div class="flex justify-between text-[11px] font-bold">
                                <span class="text-slate-600 dark:text-[#94a3b8]">Accusés</span>
                                <span class="{{ $batch->read_percentage >= 80 ? 'text-emerald-600' : ($batch->read_percentage >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $batch->read_count }} / {{ $batch->total_recipients }} ({{ $batch->read_percentage }}%)
                                </span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $batch->read_percentage >= 80 ? 'bg-emerald-500' : ($batch->read_percentage >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                     style="width: {{ $batch->read_percentage }}%"></div>
                            </div>
                        </div>

                        <button type="button" 
                                @click="openDetailsModal('{{ $batch->batch_id }}')"
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 dark:text-[#f8fafc] bg-[#fffefa] dark:bg-[#161a22] border border-slate-200 dark:border-[#222630] hover:bg-slate-50 dark:hover:bg-[#1f232d] transition shadow-xs">
                            <span>Détails</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Modale Détails du lot & Destinataires --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="w-full max-w-2xl bg-[#fffefa] dark:bg-[#161a22] border border-slate-200 dark:border-[#222630] rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
            
            {{-- Header modal --}}
            <div class="p-4 border-b border-slate-200 dark:border-[#222630] bg-slate-50/70 dark:bg-[#131519] flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]" x-text="activeBatch?.title || 'Détails de l\'alerte'"></h3>
                    <p class="text-xs text-slate-400" x-text="'Cible : ' + (activeBatch?.target_label || '')"></p>
                </div>
                <button @click="modalOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-[#f8fafc] transition">
                    ✕
                </button>
            </div>

            {{-- Corps modal --}}
            <div class="p-4 overflow-y-auto custom-scrollbar space-y-3">
                <template x-if="loadingDetails">
                    <div class="py-10 text-center text-slate-400">
                        <svg class="w-6 h-6 mx-auto animate-spin text-rose-500 mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs font-semibold">Chargement des destinataires…</p>
                    </div>
                </template>

                <template x-if="!loadingDetails && activeBatch">
                    <div class="space-y-3">
                        {{-- Synthèse --}}
                        <div class="grid grid-cols-3 gap-2 p-3 rounded-lg bg-slate-50/70 dark:bg-[#131519] border border-slate-200 dark:border-[#222630] text-center text-xs">
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase">Envoyés</p>
                                <p class="text-base font-bold text-slate-900 dark:text-[#f8fafc]" x-text="activeBatch.total_recipients"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-emerald-600 uppercase">Reçus</p>
                                <p class="text-base font-bold text-emerald-600" x-text="activeBatch.read_count"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-rose-600 uppercase">En attente</p>
                                <p class="text-base font-bold text-rose-600" x-text="activeBatch.unread_count"></p>
                            </div>
                        </div>

                        {{-- Liste des destinataires --}}
                        <div class="space-y-1">
                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">État individuel des destinataires</p>
                            
                            <div class="divide-y divide-slate-100 dark:divide-[#222630] rounded-lg border border-slate-200 dark:border-[#222630] overflow-hidden">
                                <template x-for="rec in activeBatch.recipients" :key="rec.id">
                                    <div class="p-2.5 flex items-center justify-between text-xs hover:bg-slate-50/70 dark:hover:bg-[#1f232d]/40">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs"
                                                 :class="rec.is_read ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-[#131519] dark:text-slate-400'">
                                                <span x-text="rec.name.substring(0, 2).toUpperCase()"></span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-[#f8fafc]" x-text="rec.name"></p>
                                                <p class="text-slate-400 text-[10px]" x-text="rec.email + ' • ' + rec.role"></p>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <template x-if="rec.is_read">
                                                <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-[11px]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span x-text="'Lu le ' + rec.read_at"></span>
                                                </span>
                                            </template>
                                            <template x-if="!rec.is_read">
                                                <span class="inline-flex items-center gap-1 text-rose-500 font-semibold text-[11px]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    <span>Non acquitté</span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function urgentNotificationManager() {
        return {
            form: {
                title: '',
                message: '',
                url: '',
                target_type: 'all',
                target_value: '',
                individual_ids: [],
                attachment: null
            },
            recipientCount: {{ $totalActiveUsers }},
            loadingCount: false,
            userSearchQuery: '',
            allUsers: @json($targetOptions['users'] ?? []),
            modalOpen: false,
            loadingDetails: false,
            activeBatch: null,

            get filteredUsers() {
                if (!this.userSearchQuery) return this.allUsers;
                const q = this.userSearchQuery.toLowerCase();
                return this.allUsers.filter(u => 
                    u.name.toLowerCase().includes(q) || 
                    u.email.toLowerCase().includes(q)
                );
            },

            selectTarget(type) {
                this.form.target_type = type;
                this.form.target_value = '';
                this.form.individual_ids = [];
                this.fetchRecipientCount();
            },

            handleAttachment(event) {
                const file = event.target.files[0];
                if (file && file.type === 'application/pdf') {
                    this.form.attachment = file;
                } else if (file) {
                    Swal.fire({
                        title: 'Format invalide',
                        text: 'Seuls les fichiers PDF sont acceptés.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                    event.target.value = '';
                }
            },

            removeAttachment() {
                this.form.attachment = null;
                const input = this.$el.querySelector('input[type="file"]');
                if (input) input.value = '';
            },

            async fetchRecipientCount() {
                this.loadingCount = true;
                try {
                    const params = new URLSearchParams({
                        target_type: this.form.target_type,
                        target_value: this.form.target_value || '',
                        individual_ids: this.form.individual_ids.join(',')
                    });

                    const res = await fetch(`/admin/notifications/urgent/count-recipients?${params.toString()}`);
                    const data = await res.json();
                    this.recipientCount = data.count;
                } catch (e) {
                    console.error('Erreur comptage destinataires:', e);
                } finally {
                    this.loadingCount = false;
                }
            },

            async openDetailsModal(batchId) {
                this.modalOpen = true;
                this.loadingDetails = true;
                try {
                    const res = await fetch(`/admin/notifications/urgent/batch/${batchId}`);
                    this.activeBatch = await res.json();
                } catch (e) {
                    console.error('Erreur chargement détails lot:', e);
                } finally {
                    this.loadingDetails = false;
                }
            },

            confirmSubmit(event) {
                if (this.recipientCount === 0) {
                    Swal.fire({
                        title: 'Aucun destinataire',
                        text: 'Votre ciblage ne correspond à aucun compte utilisateur actif.',
                        icon: 'warning',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Diffuser cette alerte urgente ?',
                    html: `Cette alerte sera affichée avec la priorité maximale à <strong>${this.recipientCount} destinataire(s)</strong> et restera affichée jusqu'à validation de leur accusé de réception.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Oui, diffuser immédiatement !',
                    cancelButtonText: 'Annuler'
                }).then(r => {
                    if (r.isConfirmed) {
                        const formEl = event.target;
                        const formData = new FormData(formEl);

                        if (this.form.attachment) {
                            formData.set('attachment', this.form.attachment);
                        } else {
                            formData.delete('attachment');
                        }

                        fetch(formEl.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        }).then(res => {
                            if (res.redirected) {
                                window.location.href = res.url;
                            } else {
                                window.location.reload();
                            }
                        }).catch(() => {
                            window.location.reload();
                        });
                    }
                });
            }
        };
    }
</script>
@endpush
</x-app-layout>
