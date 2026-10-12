<x-app-layout title="Mon Profil">

    @php
        $personnel = $user->personnel;
        $displayName = trim(($personnel?->nom ?? '') . ' ' . ($personnel?->prenom ?? ''));
        $displayName = $displayName !== '' ? $displayName : ($user->getRawOriginal('email') ?? $user->email ?? 'Utilisateur');
        $displayEmail = $personnel?->email ?? $user->getRawOriginal('email') ?? $user->email ?? 'Email manquant';
        $initials = strtoupper(substr($personnel?->prenom ?? $displayName, 0, 1) . substr($personnel?->nom ?? '', 0, 1));
        if ($initials === '') {
            $initials = strtoupper(substr($displayName, 0, 2));
        }

        $profil = $personnel?->personnable;
        $roleName = $user->getRoleNames()->first();
    @endphp

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }

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
        .ks-btn-primary:active { transform: scale(.98); }

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

    <div class="w-full space-y-3 pb-6">

        {{-- ── NOTIFICATIONS STATUS ── --}}
        @if(session('status') === 'profile-updated')
            <div class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Vos informations de profil ont été enregistrées avec succès.</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold ml-4">✕</button>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Votre mot de passe a été mis à jour avec succès.</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold ml-4">✕</button>
            </div>
        @endif

        {{-- ── EN-TÊTE COMPACT ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-[#241f1b] dark:text-white">
                    Mon Profil
                </h1>
                <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                    Gérez vos informations personnelles, vos paramètres de sécurité et vos autorisations d'appareils.
                </p>
            </div>
        </div>

        {{-- ── HERO CARTE PROFIL COMPACTE ── --}}
        {{-- ── HERO CARTE PROFIL COMPACTE AVEC DÉGRADÉ VIOLET & BLEU ── --}}
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-700 text-white p-3.5 sm:p-4 shadow-sm border border-blue-500/30">
            {{-- Reflet d'ambiance discret --}}
            <div class="absolute -right-10 -top-10 w-44 h-44 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex items-center gap-3.5 sm:gap-4 flex-wrap sm:flex-nowrap">
                {{-- Avatar avec bouton upload rapide --}}
                <div class="relative shrink-0">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                             class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-2 border-white/70 shadow-md">
                    @else
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white/20 border-2 border-white/50 flex items-center justify-center text-white font-extrabold text-xl sm:text-2xl shadow-sm backdrop-blur-sm">
                            {{ $initials }}
                        </div>
                    @endif

                    <button type="button"
                            onclick="document.getElementById('avatar-upload').click()"
                            class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-white text-indigo-700 flex items-center justify-center hover:bg-blue-50 transition shadow-md cursor-pointer"
                            title="Modifier la photo de profil">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>

                {{-- Informations du profil --}}
                <div class="min-w-0 flex-1 space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-lg font-bold text-white leading-tight truncate">
                            {{ $displayName }}
                        </h2>

                        @if($roleName)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-white/20 text-white border border-white/30 uppercase tracking-wider backdrop-blur-sm">
                                {{ $roleName }}
                            </span>
                        @endif

                        @if(Auth::user()->email_verified_at)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/25 text-emerald-100 border border-emerald-400/40 backdrop-blur-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Vérifié
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/25 text-amber-100 border border-amber-400/40 backdrop-blur-sm">
                                Non vérifié
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 text-xs text-white/90 font-mono flex-wrap">
                        <span>{{ $displayEmail }}</span>
                        @if($personnel?->telephone)
                            <span>· {{ $personnel->telephone }}</span>
                        @endif
                    </div>

                    {{-- Détails affectation si présents --}}
                    @if($profil)
                        <div class="pt-1 flex items-center gap-2 text-[11px] text-white/80 flex-wrap">
                            @if(isset($profil->poste))
                                <span class="font-medium text-white">Poste : {{ $profil->poste }}</span>
                            @endif
                            @if(isset($profil->site?->nom))
                                <span>· Site : {{ $profil->site->nom }}</span>
                            @endif
                            @if(isset($profil->domaine?->nom))
                                <span>· Domaine : {{ $profil->domaine->nom }}</span>
                            @endif
                            @if(isset($profil->supervisor?->personnel))
                                <span>· Tuteur : {{ $profil->supervisor->personnel->nom }} {{ $profil->supervisor->personnel->prenom }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── GRILLE 2 COLONNES : FORMULAIRES & SÉCURITÉ ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5">

            {{-- ── COLONNE GAUCHE (7 / 12) : INFOS PERSONNELLES & MOT DE PASSE ── --}}
            <div class="lg:col-span-7 space-y-3.5">

                {{-- 1. INFORMATIONS DU PROFIL --}}
                <div class="ks-sec p-4 sm:p-5 space-y-3.5">
                    <div class="flex items-center gap-2 border-b border-[#ecebe3] dark:border-[#222630] pb-2.5">
                        <span class="w-6 h-6 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-[#241f1b] dark:text-white leading-tight">
                                Informations personnelles
                            </h3>
                            <p class="text-[11px] text-[#6e6862] dark:text-slate-400">
                                Mettez à jour vos coordonnées et votre état civil
                            </p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-update-form" class="space-y-3">
                        @csrf
                        @method('put')
                        <input type="file" id="avatar-upload" name="avatar" accept="image/*" class="hidden" onchange="document.getElementById('profile-update-form').submit()">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Nom --}}
                            <div>
                                <label for="nom" class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                    Nom <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="nom" name="nom" value="{{ old('nom', $user->personnel?->nom ?? '') }}" required class="ks-input">
                                @error('nom')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Prénom --}}
                            <div>
                                <label for="prenom" class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                    Prénom <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $user->personnel?->prenom ?? '') }}" required class="ks-input">
                                @error('prenom')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                    Adresse email <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->personnel?->email ?? $user->email) }}" required class="ks-input">
                                @error('email')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Téléphone --}}
                            <div>
                                <label for="phone" class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                    Téléphone
                                </label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->personnel?->telephone ?? '') }}" class="ks-input" placeholder="+229 ...">
                                @error('phone')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Bio si colonne disponible --}}
                        @if(\Illuminate\Support\Facades\Schema::hasColumn('users', 'bio'))
                            <div>
                                <label for="bio" class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                    Biographie / Notes
                                </label>
                                <textarea id="bio" name="bio" rows="2" class="ks-input !h-auto py-1.5 text-xs" placeholder="Courte description...">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#ecebe3] dark:border-[#222630]">
                            <button type="submit" class="ks-btn-primary ks-btn-xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 2. SÉCURITÉ - MOT DE PASSE --}}
                <div class="ks-sec p-4 sm:p-5 space-y-3.5">
                    <div class="flex items-center gap-2 border-b border-[#ecebe3] dark:border-[#222630] pb-2.5">
                        <span class="w-6 h-6 rounded bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-[#241f1b] dark:text-white leading-tight">
                                Sécurité – Mot de passe
                            </h3>
                            <p class="text-[11px] text-[#6e6862] dark:text-slate-400">
                                Utilisez un mot de passe robuste d'au moins 8 caractères
                            </p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-3">
                        @csrf
                        @method('put')

                        {{-- Mot de passe actuel --}}
                        <div>
                            <label for="update_password_current_password" class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                Mot de passe actuel <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="update_password_current_password" name="current_password" required class="ks-input !pr-8" placeholder="••••••••">
                                <button type="button" onclick="togglePassword('update_password_current_password', this)" class="absolute right-2.5 top-2 text-[#6e6862] dark:text-slate-400 hover:text-[#1c3e96] cursor-pointer" title="Afficher/Masquer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @if($errors->updatePassword->has('current_password'))
                                <p class="mt-1 text-[11px] text-rose-500">{{ $errors->updatePassword->first('current_password') }}</p>
                            @endif
                        </div>

                        {{-- Nouveau mot de passe & Confirmation côte à côte --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="update_password_password" class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                    Nouveau mot de passe <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="update_password_password" name="password" required class="ks-input !pr-8" placeholder="••••••••">
                                    <button type="button" onclick="togglePassword('update_password_password', this)" class="absolute right-2.5 top-2 text-[#6e6862] dark:text-slate-400 hover:text-[#1c3e96] cursor-pointer" title="Afficher/Masquer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                @if($errors->updatePassword->has('password'))
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $errors->updatePassword->first('password') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="update_password_password_confirmation" class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                                    Confirmation <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="update_password_password_confirmation" name="password_confirmation" required class="ks-input !pr-8" placeholder="••••••••">
                                    <button type="button" onclick="togglePassword('update_password_password_confirmation', this)" class="absolute right-2.5 top-2 text-[#6e6862] dark:text-slate-400 hover:text-[#1c3e96] cursor-pointer" title="Afficher/Masquer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                @if($errors->updatePassword->has('password_confirmation'))
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#ecebe3] dark:border-[#222630]">
                            <button type="submit" class="ks-btn-primary ks-btn-xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Mettre à jour le mot de passe
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            {{-- ── COLONNE DROITE (5 / 12) : APPAREILS QR & ZONE SENSIBLE ── --}}
            <div class="lg:col-span-5 space-y-3.5">

                {{-- 3. APPAREILS DE POINTAGE QR --}}
                @include('profile.partials.device-badges')

                {{-- 4. ZONE SENSIBLE - Uniquement si admin --}}
                @if(auth()->user()->hasRole('admin'))
                    <div class="ks-sec p-4 sm:p-5 border-rose-200 dark:border-rose-900/40 space-y-3">
                        <div class="flex items-center gap-2 border-b border-rose-100 dark:border-rose-950/40 pb-2.5">
                            <span class="w-6 h-6 rounded bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-rose-700 dark:text-rose-300 leading-tight">
                                    Zone Sensible
                                </h3>
                                <p class="text-[11px] text-[#6e6862] dark:text-slate-400">
                                    Suppression irréversible du compte
                                </p>
                            </div>
                        </div>

                        <p class="text-[11px] text-[#6e6862] dark:text-slate-400 leading-relaxed">
                            La suppression d'un compte efface définitivement vos informations et vos accès. Cette action ne peut pas être annulée.
                        </p>

                        <div class="pt-1">
                            <button type="button"
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                                    class="ks-btn-secondary ks-btn-xs !w-full justify-center text-rose-600 dark:text-rose-400 hover:border-rose-400 cursor-pointer">
                                Supprimer mon compte
                            </button>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>

    {{-- ── MODALE DE CONFIRMATION DE SUPPRESSION DE COMPTE ── --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-xl overflow-hidden p-5 space-y-4">
            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')

                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#241f1b] dark:text-white">
                            Êtes-vous sûr de vouloir supprimer votre compte ?
                        </h2>
                        <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                            Cette action est irréversible. Saisissez votre mot de passe pour confirmer.
                        </p>
                    </div>
                </div>

                <div>
                    <label for="delete_password" class="block text-[10px] font-semibold text-[#6e6862] dark:text-slate-300 uppercase tracking-wider mb-1">
                        Mot de passe actuel <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" id="delete_password" name="password" required class="ks-input" placeholder="••••••••">
                    @if($errors->userDeletion->has('password'))
                        <p class="mt-1 text-[11px] text-rose-500">{{ $errors->userDeletion->first('password') }}</p>
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-[#ecebe3] dark:border-[#222630]">
                    <button type="button" x-on:click="$dispatch('close')" class="ks-btn-secondary ks-btn-xs cursor-pointer">
                        Annuler
                    </button>
                    <button type="submit" class="ks-btn-primary ks-btn-xs !bg-rose-600 !border-rose-600 hover:!bg-rose-700 cursor-pointer">
                        Supprimer définitivement
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            if (!field) return;
            if (field.type === 'password') {
                field.type = 'text';
            } else {
                field.type = 'password';
            }
        }
    </script>
</x-app-layout>