{{--
    Alerte de vérification de la porte : ce que la porte a constaté, et la
    seule sortie possible. Un constat par écran plutôt qu'un bandeau par cas —
    empilés, ils finissent par se concurrencer et l'un d'eux annonce une
    action qui n'est pas à l'écran.

    Attendus :
      $titre, $message, $ton       'danger' | 'warning' | 'info'
      $icone                        'interdit' | 'document' | 'horloge' | 'calendrier'
      $actionUrl, $actionLabel      optionnels : le geste à faire
      $bouton                       libellé du bouton dans la carte

    Le modal s'ouvre au chargement. Le bouton de la carte le rouvre s'il a été
    fermé, d'où l'événement `verif-ouvrir` plutôt qu'un simple x-show local :
    les deux sont éloignés dans le DOM.
--}}
@php
    $tons = [
        'danger'  => ['pastille' => 'bg-red-50 dark:bg-red-900/30',      'icone' => 'text-red-600 dark:text-red-400',    'bouton' => 'bg-red-600 hover:bg-red-700'],
        'warning' => ['pastille' => 'bg-amber-50 dark:bg-amber-900/30',  'icone' => 'text-amber-600 dark:text-amber-400', 'bouton' => 'bg-amber-500 hover:bg-amber-600'],
        'info'    => ['pastille' => 'bg-slate-100 dark:bg-slate-700',   'icone' => 'text-slate-600 dark:text-slate-300', 'bouton' => 'bg-gray-900 hover:bg-gray-800 dark:bg-white dark:text-gray-900'],
    ];

    $ton = $tons[$ton] ?? $tons['info'];
@endphp

<div x-show="verif" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
     style="background: rgba(15, 18, 22, .5)"
     @click.self="verif = false"
     @keydown.escape.window="verif = false"
     @verif-ouvrir.window="verif = true">

    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 shadow-xl overflow-hidden"
         role="alertdialog" aria-modal="true"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <div class="p-6">
            <div class="w-10 h-10 rounded-full {{ $ton['pastille'] }} flex items-center justify-center mb-4">
                <svg class="w-5 h-5 {{ $ton['icone'] }}" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    @if($icone === 'document')
                        <path d="M8 3h8l4 4v14a1 1 0 01-1 1H5a1 1 0 01-1-1V4a1 1 0 011-1z" stroke-linejoin="round"/><path d="M16 3v4h4" stroke-linejoin="round"/><path stroke-linecap="round" d="M8 13h8M8 17h5"/>
                    @elseif($icone === 'horloge')
                        <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/>
                    @elseif($icone === 'calendrier')
                        <rect x="3.5" y="5" width="17" height="16" rx="2"/><path stroke-linecap="round" d="M3.5 10h17M8 3v4M16 3v4"/>
                    @else
                        <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/>
                    @endif
                </svg>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $titre }}</h3>
            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{!! $message !!}</p>

            <div class="mt-5 flex flex-col gap-2">
                @if(!empty($actionUrl) && !empty($actionLabel))
                    <a href="{{ $actionUrl }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                              {{ $ton['bouton'] }} text-white dark:text-gray-900 font-medium text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" d="M9 12h6m-3-3v6"/><circle cx="12" cy="12" r="9"/>
                        </svg>
                        {{ $actionLabel }}
                    </a>
                @endif

                <button type="button" @click="verif = false"
                        class="w-full px-4 py-2.5 rounded-xl text-gray-600 dark:text-gray-300 font-medium text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>
