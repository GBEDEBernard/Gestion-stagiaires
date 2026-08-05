<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Journaux d’erreurs et activité
            </h2>
            <span class="text-sm text-gray-500">
                {{ $logFileExists ? 'Fichier de logs disponible' : 'Aucun fichier de logs trouvé' }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Affichage des dernières lignes de logs applicatifs. Utilisez cette page pour vérifier les erreurs SMTP, les échecs d’envoi d’email et les problèmes de génération de compte.
                    </p>
                </div>

                @if($entries)
                    <pre class="whitespace-pre-wrap text-xs bg-gray-900 text-green-300 p-4 rounded overflow-auto max-h-[70vh]">{{ implode(PHP_EOL, $entries) }}</pre>
                @else
                    <div class="rounded border border-dashed border-gray-300 p-6 text-center text-gray-500">
                        Aucun log récent n’a été trouvé.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
