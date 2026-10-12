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
        'danger'  => [
            'pastille' => 'bg-red-50 dark:bg-red-950/40 border-red-200/60 dark:border-red-900/50 text-red-600 dark:text-red-400',
            'bouton'   => 'bg-[#b91c1c] hover:bg-[#991b1b] text-white',
        ],
        'warning' => [
            'pastille' => 'bg-amber-50 dark:bg-amber-950/40 border-amber-200/60 dark:border-amber-900/50 text-amber-600 dark:text-amber-400',
            'bouton'   => 'bg-[#b45309] hover:bg-[#92400e] text-white',
        ],
        'info'    => [
            'pastille' => 'bg-[#eef1fb] dark:bg-[#1c3e96]/20 border-blue-200/60 dark:border-blue-800/40 text-[#1c3e96] dark:text-blue-300',
            'bouton'   => 'bg-[#1c3e96] hover:bg-[#162f73] text-white',
        ],
    ];

    $tonConfig = $tons[$ton] ?? $tons['info'];
@endphp

<div x-show="verif" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-3 sm:p-4"
     style="background: rgba(15, 18, 22, .6); backdrop-filter: blur(2px);"
     @click.self="verif = false"
     @keydown.escape.window="verif = false"
     @verif-ouvrir.window="verif = true">

    <div class="w-full max-w-sm rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-2xl overflow-hidden"
         role="alertdialog" aria-modal="true"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <div class="p-4 sm:p-5">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-8 h-8 rounded-lg border flex items-center justify-center flex-shrink-0 {{ $tonConfig['pastille'] }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-[#241f1b] dark:text-white leading-tight">{{ $titre }}</h3>
                    <p class="mt-1 text-xs text-[#6e6862] dark:text-slate-400 leading-relaxed">{!! $message !!}</p>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-2">
                @if(!empty($actionUrl) && !empty($actionLabel))
                    <a href="{{ $actionUrl }}"
                       class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-md {{ $tonConfig['bouton'] }} font-semibold text-xs shadow-2xs transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" d="M9 12h6m-3-3v6"/><circle cx="12" cy="12" r="9"/>
                        </svg>
                        <span>{{ $actionLabel }}</span>
                    </a>
                @endif

                <button type="button" @click="verif = false"
                        class="w-full inline-flex items-center justify-center px-3 py-1.5 rounded-md bg-white dark:bg-[#15181f] border border-[#bfbab2] dark:border-[#2b3240] text-[#241f1b] dark:text-slate-300 hover:bg-[#f1f0ed] dark:hover:bg-[#1c212c] font-medium text-xs transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>
