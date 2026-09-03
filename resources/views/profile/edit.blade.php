<x-app-layout>
    @php
        $user = Auth::user();
        $personnel = $user->personnel;
        $profil = $personnel?->personnable;

        $displayName = trim(($personnel?->nom ?? '') . ' ' . ($personnel?->prenom ?? ''));
        $displayName = $displayName !== '' ? $displayName : ($user->email ?? 'Utilisateur');
        $displayEmail = $personnel?->email ?? $user->email ?? 'Email manquant';

        $prenom = $personnel?->prenom ?? '';
        $nom = $personnel?->nom ?? '';
        $initials = mb_strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1)) ?: 'U';
        $isFemme = in_array($personnel?->genre ?? '', ['Femme', 'Féminin', 'F']);

        $userAvatar = $user->avatar;
        $hasAvatar = $userAvatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($userAvatar);

        $roleLabels = $user->getRoleNames()?->implode(', ') ?? '';
        $statusColor = ($user->status ?? null) === 'actif' ? 'bg-green-500' : 'bg-red-500';
        $statusText = ($user->status ?? null) === 'actif' ? 'Actif' : 'Inactif';

        $typeLabel = $personnel?->type_label ?? 'Utilisateur';
        $typeColor = match($typeLabel) {
            'Stagiaire' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'Employé'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'Admin'     => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            default     => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        };

        $dateNaissanceFormatted = '-';
        if ($personnel?->date_naissance) {
            try {
                $dateNaissanceFormatted = \Illuminate\Support\Carbon::parse($personnel->date_naissance)->format('d/m/Y');
            } catch (\Exception $e) {
                $dateNaissanceFormatted = (string)$personnel->date_naissance;
            }
        }

        $dateInscriptionFormatted = $personnel?->date_inscription ? \Illuminate\Support\Carbon::parse($personnel->date_inscription)->format('d/m/Y') : '-';
        $dateDebutPointageFormatted = $personnel?->date_debut_pointage ? \Illuminate\Support\Carbon::parse($personnel->date_debut_pointage)->format('d/m/Y') : '-';

        $supervisorName = '-';
        $supervisorEmail = null;
        $supervisorRole = null;
        $siteName = '-';
        $siteAddress = null;
        $siteCity = null;

        if ($profil instanceof \App\Models\Etudiant) {
            if ($profil->supervisor) {
                $supervisorName = $profil->supervisor->personnel?->full_name ?? $profil->supervisor->name;
                $supervisorEmail = $profil->supervisor->email;
                $supervisorRole = $profil->supervisor->getRoleNames()->implode(', ');
            }
            $lastStage = $profil->stages()->latest()->first();
            if ($lastStage && $lastStage->site) {
                $siteName = $lastStage->site->name;
                $siteAddress = $lastStage->site->address;
                $siteCity = $lastStage->site->city;
            }
        } elseif ($profil instanceof \App\Models\Employe) {
            if ($profil->supervisor) {
                $supervisorName = $profil->supervisor->personnel?->full_name ?? $profil->supervisor->name;
                $supervisorEmail = $profil->supervisor->email;
                $supervisorRole = $profil->supervisor->getRoleNames()->implode(', ');
            }
            if ($profil->site) {
                $siteName = $profil->site->name;
                $siteAddress = $profil->site->address;
                $siteCity = $profil->site->city;
            }
        }

        $stages = ($profil instanceof \App\Models\Etudiant)
            ? $profil->stages()->with(['typestage', 'domaine', 'site', 'badge'])->orderBy('date_debut', 'desc')->get()
            : collect();

        $dureeTotaleJours = ($profil instanceof \App\Models\Etudiant)
            ? $stages->filter(fn($s) => $s->date_debut && $s->date_fin)->sum(fn($s) => $s->date_debut->diffInDays($s->date_fin))
            : 0;

        $tasksQuery = \App\Models\Task::query();
        $tasksQuery->where(function ($q) use ($user, $profil) {
            $q->where('owner_id', $user->id)
              ->orWhereHas('assignees', function ($aq) use ($user) {
                  $aq->where('users.id', $user->id);
              });
            if ($profil instanceof \App\Models\Etudiant) {
                $q->orWhere('etudiant_id', $profil->id);
            }
        });

        $userTasks = $tasksQuery->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
    @endphp

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}"
                class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Mon Profil
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Paramètres / <span class="text-gray-700 dark:text-gray-200 font-medium">{{ $displayName }}</span>
                </p>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl mb-6 shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="relative h-28 bg-gradient-to-r from-violet-400 to-blue-600 dark:from-sky-600 dark:to-blue-800">
                    <div class="absolute -bottom-12 left-6">
                        <div class="relative">
                            <div class="h-24 w-24 rounded-2xl overflow-hidden ring-4 ring-white dark:ring-gray-800 shadow-xl
                                        {{ $hasAvatar ? '' : 'bg-white dark:bg-gray-800' }} flex items-center justify-center">
                                @if($hasAvatar)
                                    <img src="{{ asset('storage/' . $userAvatar) }}" alt="{{ $displayName }}" class="w-full h-full object-cover object-center">
                                @else
                                    <div class="w-full h-full flex items-center justify-center
                                                {{ $isFemme ? 'bg-gradient-to-br from-pink-400 to-rose-500' : 'bg-gradient-to-br from-blue-400 to-indigo-500' }}">
                                        <span class="text-white font-bold text-2xl tracking-wide">{{ $initials }}</span>
                                    </div>
                                @endif
                            </div>
                            <label for="avatar-upload"
                                class="absolute -bottom-1 -right-1 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 p-1.5 rounded-full cursor-pointer shadow-lg hover:bg-blue-50 dark:hover:bg-gray-600 transition border-2 border-white dark:border-gray-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </label>
                        </div>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-form">
                    @csrf
                    @method('put')
                    <input type="file" id="avatar-upload" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">

                    <div class="pt-16 pb-6 px-6">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $displayName }}</h2>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">
                                    @if($profil instanceof \App\Models\Etudiant)
                                        Étudiant • {{ $profil->ecole ?? 'École non renseignée' }}
                                    @elseif($profil instanceof \App\Models\Employe)
                                        Employé • {{ $profil->poste ?? 'Poste non renseigné' }}
                                    @else
                                        {{ $typeLabel }}
                                    @endif
                                </p>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $typeColor }}">
                                        {{ $typeLabel }}
                                    </span>
                                    @if($personnel?->genre)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ $isFemme ? 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        {{ $personnel->genre }}
                                    </span>
                                    @endif
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg text-white {{ $statusColor }}">
                                        ● {{ $statusText }}
                                    </span>
                                    @if($user->hasVerifiedEmail())
                                        <span class="inline-flex items-center gap-1 text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-2.5 py-1 rounded-lg">
                                            Email vérifié
                                        </span>
                                    @endif
                                    @if($roleLabels)
                                        <span class="inline-flex items-center text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 px-2.5 py-1 rounded-lg">
                                            {{ $roleLabels }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @if($displayEmail)
                                <a href="mailto:{{ $displayEmail }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Envoyer un email">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </a>
                                @endif
                                @if($personnel?->telephone)
                                <a href="tel:{{ $personnel->telephone }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Appeler">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50/80 dark:bg-gray-900/30">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">Site de rattachement</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $siteName }}</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $siteAddress ?? 'Adresse non renseignée' }}</p>
                                @if($siteCity)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $siteCity }}</p>
                                @endif
                            </div>

                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50/80 dark:bg-gray-900/30">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="h-10 w-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A8.966 8.966 0 0112 15c2.5 0 4.847 1.023 6.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0zM19 21H5a2 2 0 01-2-2v-1a9 9 0 0118 0v1a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">Superviseur référent</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $supervisorName }}</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $supervisorEmail ?? 'Aucun email associé' }}</p>
                                @if($supervisorRole)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $supervisorRole }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informations détaillées
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-xl p-4 border border-violet-100 dark:border-violet-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-violet-100 dark:bg-violet-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wide">Identité</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ $displayName }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $personnel?->genre ?? 'Genre non renseigné' }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Email</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold truncate">{{ $displayEmail }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $personnel?->telephone ?? 'Sans téléphone' }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-4 border border-emerald-100 dark:border-emerald-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Date de naissance</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ $dateNaissanceFormatted }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-4 border border-amber-100 dark:border-amber-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-amber-100 dark:bg-amber-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide">Dates clés</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold text-sm">
                                Inscrit : {{ $dateInscriptionFormatted }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Pointage : {{ $dateDebutPointageFormatted }}
                            </p>
                        </div>

                        <div class="bg-gradient-to-br from-cyan-50 to-sky-50 dark:from-cyan-900/20 dark:to-sky-900/20 rounded-xl p-4 border border-cyan-100 dark:border-cyan-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-cyan-100 dark:bg-cyan-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 uppercase tracking-wide">Profil métier</span>
                            </div>
                            @if($profil instanceof \App\Models\Etudiant)
                                <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ $profil->ecole ?? 'École non renseignée' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Niveau : {{ $profil->niveau ?? '-' }}</p>
                            @elseif($profil instanceof \App\Models\Employe)
                                <p class="text-gray-800 dark:text-gray-200 font-semibold">{{ optional($profil->domaine)->nom ?? 'Domaine non renseigné' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Poste : {{ $profil->poste ?? '-' }}</p>
                            @else
                                <p class="text-gray-800 dark:text-gray-200 font-semibold">—</p>
                            @endif
                        </div>

                        <div class="bg-gradient-to-br from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-rose-100 dark:border-rose-800/30">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1.5 bg-rose-100 dark:bg-rose-900/50 rounded-lg">
                                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wide">Adresse</span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold text-sm">{{ $personnel?->adresse ?? 'Non renseignée' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($profil instanceof \App\Models\Etudiant && $stages->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Historique des stages ({{ $stages->count() }})
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($stages as $stageItem)
                    @php
                        $stStatut = $stageItem->date_debut && $stageItem->date_fin
                            ? (today()->lt($stageItem->date_debut) ? 'À venir' : (today()->gt($stageItem->date_fin) ? 'Terminé' : 'En cours'))
                            : 'À venir';
                    @endphp
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <a href="{{ encrypted_route('stages.show', $stageItem) }}" class="font-semibold text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                        {{ $stageItem->theme ?? 'Stage sans thème' }}
                                    </a>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $stageItem->typestage->libelle ?? 'Type non défini' }} • {{ $stageItem->domaine->nom ?? 'Domaine non défini' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $stageItem->date_debut?->format('d/m/Y') ?? '—' }} - {{ $stageItem->date_fin?->format('d/m/Y') ?? '—' }}
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($stStatut === 'En cours') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                    @elseif($stStatut === 'À venir') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 @endif">
                                    {{ $stStatut }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-violet-600 to-indigo-600 rounded-2xl shadow-xl overflow-hidden text-white">
                <div class="p-4 sm:p-5">
                    <h3 class="text-base sm:text-lg font-bold mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Statistiques
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        @if($profil instanceof \App\Models\Etudiant)
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 min-w-0">
                            <p class="text-xl sm:text-2xl font-bold tracking-tight truncate">{{ $stages->count() }}</p>
                            <p class="text-xs text-white/80 truncate">Stage(s)</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 min-w-0">
                            <p class="text-xl sm:text-2xl font-bold tracking-tight truncate">{{ $dureeTotaleJours }}</p>
                            <p class="text-xs text-white/80 truncate">Jour(s) cumulé(s)</p>
                        </div>
                        @else
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 min-w-0">
                            <p class="text-xl sm:text-2xl font-bold tracking-tight truncate">1</p>
                            <p class="text-xs text-white/80 truncate">Compte actif</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 min-w-0">
                            <p class="text-base sm:text-lg font-bold tracking-tight truncate" title="{{ $user->created_at?->format('d/m/Y') }}">
                                {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                            </p>
                            <p class="text-xs text-white/80 truncate">Date de création</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Activité du compte
                    </h3>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div class="flex justify-between items-center"><span class="text-gray-500 dark:text-gray-400">Créé le</span><span class="font-medium text-gray-800 dark:text-white">{{ $user->created_at->format('d/m/Y') }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-500 dark:text-gray-400">Mise à jour</span><span class="font-medium text-gray-800 dark:text-white">{{ $user->updated_at->format('d/m/Y à H:i') }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="font-medium {{ $user->status === 'actif' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $statusText }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-500 dark:text-gray-400">Rôle(s)</span><div class="flex flex-wrap gap-1">@foreach($user->getRoleNames() as $role)<span class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs px-2 py-0.5 rounded">{{ $role }}</span>@endforeach</div></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Contact
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200 truncate">{{ $displayEmail }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Téléphone</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $personnel?->telephone ?? 'Non défini' }}</p>
                        </div>
                    </div>
                    @if($personnel?->adresse)
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Adresse</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $personnel->adresse }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-violet-50 to-indigo-50 dark:from-gray-800 dark:to-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Projets / Tâches
                    </h3>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">
                        {{ $userTasks->count() }}
                    </span>
                </div>
                @if($userTasks->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($userTasks as $taskItem)
                    @php
                        $statusBadge = match($taskItem->status) {
                            'in_progress' => ['bg' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'label' => 'En cours'],
                            'pending' => ['bg' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', 'label' => 'En attente'],
                            'blocked' => ['bg' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'label' => 'Bloqué'],
                            'awaiting_validation' => ['bg' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300', 'label' => 'À valider'],
                            'completed' => ['bg' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'label' => 'Terminé'],
                            default => ['bg' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', 'label' => ucfirst($taskItem->status)],
                        };
                        $progressPercent = $taskItem->last_progress_percent ?? $taskItem->base_progress_percent ?? 0;
                    @endphp
                    <a href="{{ encrypted_route('tasks.show', $taskItem) }}"
                       class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition group">
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition line-clamp-1">
                                {{ $taskItem->title }}
                            </h4>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium shrink-0 {{ $statusBadge['bg'] }}">
                                {{ $statusBadge['label'] }}
                            </span>
                        </div>
                        @if($taskItem->description)
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mb-2">
                            {{ $taskItem->description }}
                        </p>
                        @endif
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden flex items-center">
                            <div class="bg-gradient-to-r from-violet-500 to-blue-600 h-full rounded-full transition-all duration-300"
                                 style="width: {{ min(100, max(0, $progressPercent)) }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                            <span>Progression : {{ $progressPercent }}%</span>
                            @if($taskItem->due_date)
                            <span>Échéance : {{ $taskItem->due_date->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Aucun projet ou tâche en cours
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
