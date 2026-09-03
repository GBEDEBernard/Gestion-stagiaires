<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('sites.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nouveau Site</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">Configure le lieu de presence et sa geofence principale.</p>
        </div>

        @include('admin.sites.partials.form')
    </div>
</x-app-layout>
