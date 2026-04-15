<x-app-layout>
    <div class="max-w-5xl mx-auto">
@php
        $isEmployeeCreation = isset($selectedRoles[0]) && $selectedRoles[0] === 'employe';
    @endphp
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('admin.users.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $isEmployeeCreation ? 'Nouvel Employé' : 'Nouvel Utilisateur' }}</h1>
        </div>
        <p class="text-gray-500 dark:text-gray-400 ml-14">{{ $isEmployeeCreation ? 'Création rapide d’un employé du domaine TFG.' : 'Formulaire unique pour creer un admin, un superviseur ou un etudiant.' }}</p>
        </div>

        @include('admin.users.partials.form', array_merge($formData, [
        'formAction' => route('admin.users.store'),
        'submitLabel' => 'Creer l\'utilisateur',
        ]))
    </div>
</x-app-layout>