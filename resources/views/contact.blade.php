@extends('bloglayouts')

@section('contenu')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nous contacter') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-indigo-100 via-white to-blue-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Message de succès -->
            @if (session('status') === 'contact-sent')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                     class="mb-6 p-4 text-green-800 bg-green-100 dark:bg-green-900 dark:text-green-200 border border-green-300 dark:border-green-700 rounded-lg shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Message envoyé avec succès !') }}
                </div>
            @endif

            <!-- Section Contact -->
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-8 transform transition-all duration-300 hover:shadow-2xl">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Nous contacter') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    {{ __('Remplissez le formulaire ci-dessous pour nous envoyer un message. Nous vous répondrons dans les plus brefs délais.') }}
                </p>

                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                    @csrf

                    <!-- Champ Nom -->
                    <div>
                        <x-input-label for="name" :value="__('Nom')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <x-text-input id="name" name="name" type="text"
                                      class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                                      :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Champ Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <x-text-input id="email" name="email" type="email"
                                      class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                                      :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Champ Sujet -->
                    <div>
                        <x-input-label for="subject" :value="__('Sujet')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <x-text-input id="subject" name="subject" type="text"
                                      class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                                      :value="old('subject')" required />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Champ Message -->
                    <div>
                        <x-input-label for="message" :value="__('Message')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <textarea id="message" name="message"
                                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm resize-y transition duration-200"
                                  rows="6" required>{{ old('message') }}</textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Bouton Envoyer -->
                    <div class="flex items-center gap-4">
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                            {{ __('Envoyer') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Informations de contact supplémentaires -->
            <div class="mt-8 text-center text-gray-600 dark:text-gray-400">
                <p class="text-sm">
                    {{ __('Vous pouvez également nous contacter directement :') }}
                </p>
                <p class="mt-2">
                    <a href="mailto:gbedebernard60@gmail.com" class="text-indigo-600 dark:text-indigo-400 hover:underline">gbedebernard60@gmail.com</a>
                    | <span>+229 0166439030 /+229 0169580603</span>
                </p>
            </div>
        </div>
    </div>
@endsection