<x-guest-layout>
    <div class="space-y-6">
        {{-- jb -> Deuxieme etape d'activation:
        le mot de passe temporaire disparait ici pour laisser place au
        premier mot de passe vraiment personnel du proprietaire du compte. --}}
        <div class="text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Activation du compte</p>
            <h1 class="mt-3 text-2xl font-semibold text-slate-900">Choisis ton mot de passe personnel</h1>
            <p class="mt-2 text-sm text-slate-600">
                Ton email est verifie. Il ne reste plus qu'a remplacer le mot de passe temporaire pour finaliser ton acces.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->firstPassword->has('current_password') || $errors->firstPassword->has('password'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                Le mot de passe personnel n'a pas pu etre enregistre. Verifie les informations saisies.
            </div>
        @endif

        <form method="POST" action="{{ route('password.first.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-slate-700 mb-2">Mot de passe temporaire</label>
                <input
                    id="current_password"
                    name="current_password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                    placeholder="Saisis le mot de passe temporaire fourni par l'administration">
                @if ($errors->firstPassword->has('current_password'))
                    <p class="mt-2 text-sm text-rose-600">{{ $errors->firstPassword->first('current_password') }}</p>
                @endif
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Nouveau mot de passe</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                    placeholder="Choisis un mot de passe que toi seul connaitras">
                @if ($errors->firstPassword->has('password'))
                    <p class="mt-2 text-sm text-rose-600">{{ $errors->firstPassword->first('password') }}</p>
                @endif
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirmation du nouveau mot de passe</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                    placeholder="Confirme le nouveau mot de passe">
            </div>

            <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-medium text-white hover:bg-slate-800">
                Finaliser mon compte
            </button>
        </form>
    </div>
</x-guest-layout>
