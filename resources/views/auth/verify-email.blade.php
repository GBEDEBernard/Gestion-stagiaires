<x-guest-layout>
    <div class="space-y-6">
        {{-- jb -> Premiere etape d'activation:
        prouver que l'adresse email remise par l'admin est bien controlee
        par la personne qui essaie d'ouvrir le compte. --}}
        <div class="text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Verification email</p>
            <h1 class="mt-3 text-2xl font-semibold text-slate-900">Confirme ton adresse email</h1>
            <p class="mt-2 text-sm text-slate-600">
                Le compte a bien ete cree. Avant d'aller plus loin, ouvre l'email de verification recu puis valide ton adresse.
            </p>
            <p class="mt-2 text-sm text-slate-600">
                Une fois l'email confirme, l'application te demandera de choisir ton mot de passe personnel.
            </p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                Un nouveau lien de verification a ete envoye a ton adresse email.
            </div>
        @endif

        <div class="grid gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button class="w-full justify-center">
                    Renvoyer l'email de verification
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Se deconnecter
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
