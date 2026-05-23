<x-app-layout>
<div class="mb-8 ml-4">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Gestion du personnel</p>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Employés</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestion des employés et génération de leurs comptes</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('employes.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouvel employé
            </a>
            <a href="{{ route('employes.trash') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Corbeille
            </a>
        </div>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl text-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Tableau ── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">N°</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Matricule</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Nom complet</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Email</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Domaine</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Site</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Poste</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Compte</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($employes as $emp)
                    @php $p = $emp->personnel; @endphp

                    {{-- ── Ligne orpheline (pas de personnel lié) ── --}}
                    @if(!$p)
                    <tr class="bg-red-50 dark:bg-red-900/10">
                        <td class="px-5 py-3 text-xs text-red-500 dark:text-red-400" colspan="8">
                            ⚠ Employé #{{ $emp->id }} (matricule : {{ $emp->matricule }}) — aucun personnel associé.
                            <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline ml-3">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs underline text-red-600 dark:text-red-400 hover:text-red-800">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @continue
                    @endif

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-5 py-3.5 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $emp->matricule }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40 text-xs font-semibold text-amber-700 dark:text-amber-300">
                                    {{ mb_strtoupper(mb_substr($p->prenom, 0, 1) . mb_substr($p->nom, 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $p->prenom }} {{ $p->nom }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400">{{ $p->email }}</td>
                        <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">{{ $emp->domaine->nom ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">{{ $emp->site->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ $emp->poste ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @if($p->user)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-medium">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>Compte actif
                                </span>
                            @else
                                <button type="button"
                                        onclick="openPasswordModal({{ $emp->id }}, '{{ route('employes.generate-account', $emp) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 text-xs font-medium hover:bg-sky-100 dark:hover:bg-sky-900/50 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Générer compte
                                </button>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('employes.show', $emp) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('employes.edit', $emp) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-800/50 transition" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('employes.destroy', $emp) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-800/50 transition" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-medium text-gray-500">Aucun employé trouvé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $employes->links() }}
        </div>
    </div>
</div>

{{-- ── Modal mot de passe ── --}}
<div id="passwordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Générer un compte</h2>
            <button type="button" onclick="closePasswordModal()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Entrez un mot de passe temporaire pour cet employé. Si laissé vide, un mot de passe aléatoire sera généré et envoyé par email.</p>
            <form id="passwordForm" method="POST" action="">
                @csrf
                <div class="mb-5">
                    <label for="customPassword" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Mot de passe temporaire <span class="text-gray-400 font-normal">(optionnel)</span></label>
                    <input type="password" id="customPassword" name="custom_password"
                           placeholder="Laisser vide pour générer automatiquement"
                           class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent text-sm">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closePasswordModal()"
                            class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">Annuler</button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-sky-600 rounded-xl hover:bg-sky-700 transition">Générer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPasswordModal(empId, actionUrl) {
    document.getElementById('passwordModal').classList.remove('hidden');
    document.getElementById('passwordForm').action = actionUrl;
    document.getElementById('customPassword').value = '';
}
function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closePasswordModal(); });
document.getElementById('passwordModal')?.addEventListener('click', function(e) { if (e.target === this) closePasswordModal(); });
</script>
</x-app-layout>