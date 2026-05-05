@php
    $primaryGeofence = isset($site) ? ($site->geofences->firstWhere('is_primary', true) ?? $site->geofences->first()) : null;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <form action="{{ isset($site) ? encrypted_route('sites.update', $site) : route('sites.store') }}" method="POST" class="p-6 space-y-8">
        @csrf
        @if(isset($site))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" id="code" value="{{ old('code', $site->code ?? '') }}" required
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom du site <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $site->name ?? '') }}" required
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
            </div>

            <div>
                <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact sur place</label>
                <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $site->contact_person ?? '') }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
            </div>

            <div>
                <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telephone</label>
                <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $site->contact_phone ?? '') }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adresse</label>
                <input type="text" name="address" id="address" value="{{ old('address', $site->address ?? '') }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
            </div>

            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville</label>
                <input type="text" name="city" id="city" value="{{ old('city', $site->city ?? '') }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
            </div>

            <div>
                <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pays</label>
                <input type="text" name="country" id="country" value="{{ old('country', $site->country ?? 'Benin') }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
            </div>

            <div>
                <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Latitude du site</label>
                <input type="number" step="0.0000001" name="latitude" id="latitude" value="{{ old('latitude', $site->latitude ?? '') }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
            </div>

            <div>
                <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Longitude du site</label>
                <input type="number" step="0.0000001" name="longitude" id="longitude" value="{{ old('longitude', $site->longitude ?? '') }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
            </div>

            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', isset($site) ? $site->is_active : true) ? 'checked' : '' }}
                        class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500 border-gray-300">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Site actif</span>
                </label>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Geofence principale</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="geofence_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom de la zone <span class="text-red-500">*</span></label>
                    <input type="text" name="geofence_name" id="geofence_name" value="{{ old('geofence_name', $primaryGeofence->name ?? 'Zone principale') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
                </div>

                <div>
                    <label for="geofence_radius_meters" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rayon autorise (metres) <span class="text-red-500">*</span></label>
                    <input type="number" name="geofence_radius_meters" id="geofence_radius_meters" value="{{ old('geofence_radius_meters', $primaryGeofence->radius_meters ?? 100) }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
                </div>

                <div>
                    <label for="geofence_latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Latitude centre <span class="text-red-500">*</span></label>
                    <input type="number" step="0.0000001" name="geofence_latitude" id="geofence_latitude" value="{{ old('geofence_latitude', $primaryGeofence->center_latitude ?? $site->latitude ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
                </div>

                <div>
                    <label for="geofence_longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Longitude centre <span class="text-red-500">*</span></label>
                    <input type="number" step="0.0000001" name="geofence_longitude" id="geofence_longitude" value="{{ old('geofence_longitude', $primaryGeofence->center_longitude ?? $site->longitude ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
                </div>

                <div>
                    <label for="geofence_allowed_accuracy_meters" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Precision GPS max (metres) <span class="text-red-500">*</span></label>
                    <input type="number" name="geofence_allowed_accuracy_meters" id="geofence_allowed_accuracy_meters" value="{{ old('geofence_allowed_accuracy_meters', $primaryGeofence->allowed_accuracy_meters ?? 50) }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes site</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">{{ old('notes', $site->notes ?? '') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="geofence_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes geofence</label>
                    <textarea name="geofence_notes" id="geofence_notes" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-gray-900 dark:text-white">{{ old('geofence_notes', $primaryGeofence->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700">
                <ul class="space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('sites.index') }}"
                class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                Annuler
            </a>
            <button type="submit"
                class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition font-medium shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ isset($site) ? 'Mettre a jour' : 'Enregistrer le site' }}
            </button>
        </div>
    </form>
</div>
