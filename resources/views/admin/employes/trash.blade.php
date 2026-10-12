<x-app-layout>
    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #15181f !important; }
        header { background-color: transparent !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }
        .dark header { background-color: #131519 !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #dcdad2;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 500;
            padding: .45rem .85rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary {
            background: #131519;
            color: #f8fafc;
            border-color: #222630;
        }
        .ks-btn-secondary:hover {
            border-color: #1c3e96;
            color: #1c3e96;
        }
    </style>

    <div class="w-full max-w-6xl mx-auto px-3 sm:px-6 pt-3 sm:pt-4 pb-8 space-y-4">

        {{-- ── En-tête Compact ── --}}
        <div class="flex items-center justify-between pb-1 border-b border-[#dcdad2] dark:border-[#222630]">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('employes.index') }}" class="ks-btn-secondary !p-1.5" title="Retour aux employés">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-[#b45309] dark:bg-[#fbbf24]"></span>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">
                            Administration RH · Éléments supprimés
                        </p>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#241f1b] dark:text-[#f8fafc] mt-0.5">
                        Corbeille — Employés
                    </h1>
                </div>
            </div>

            <a href="{{ route('employes.index') }}" class="ks-btn-secondary">
                <span>Tous les employés</span>
            </a>
        </div>

        <div class="space-y-4">
            <x-trash-table
                :items="$employes"
                title="Employés supprimés"
                :columns="['Employé', 'Email', 'Supprimé']"
                :restoreRoute="'employes.restore'"
                :restoreMethod="'PUT'"
                :forceDeleteRoute="'employes.force-delete'"
                :fields="['personnel.full_name','personnel.email']"
                icon="👔"
                color="amber" />
        </div>

        @if($employes->hasPages())
            <div class="mt-4">
                {{ $employes->links() }}
            </div>
        @endif

    </div>
</x-app-layout>