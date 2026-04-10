<x-guest-layout>
<<<<<<< HEAD
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
=======
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
>>>>>>> e9635ab
    </div>
</x-guest-layout>
