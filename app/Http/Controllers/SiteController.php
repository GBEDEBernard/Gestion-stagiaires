<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::with(['geofences'])->orderBy('name')->paginate(10);

        return view('admin.sites.index', compact('sites'));
    }

    public function create()
    {
        return view('admin.sites.create');
    }

    public function store(Request $request)
    {
        $payload = $this->validatePayload($request);

        $site = Site::create([
            'code' => $payload['code'],
            'name' => $payload['name'],
            'contact_person' => $payload['contact_person'] ?? null,
            'contact_phone' => $payload['contact_phone'] ?? null,
            'address' => $payload['address'] ?? null,
            'city' => $payload['city'] ?? null,
            'country' => $payload['country'] ?? 'Benin',
            'latitude' => $payload['latitude'] ?? null,
            'longitude' => $payload['longitude'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $payload['notes'] ?? null,
        ]);

        $this->syncPrimaryGeofence($site, $payload);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Creation site',
            'description' => "Site {$site->name} cree",
        ]);

        return redirect()->route('sites.index')->with('success', 'Site cree avec succes.');
    }

    public function edit(Site $site)
    {
        $site->load(['geofences']);

        return view('admin.sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $payload = $this->validatePayload($request, $site->id);

        $site->update([
            'code' => $payload['code'],
            'name' => $payload['name'],
            'contact_person' => $payload['contact_person'] ?? null,
            'contact_phone' => $payload['contact_phone'] ?? null,
            'address' => $payload['address'] ?? null,
            'city' => $payload['city'] ?? null,
            'country' => $payload['country'] ?? 'Benin',
            'latitude' => $payload['latitude'] ?? null,
            'longitude' => $payload['longitude'] ?? null,
            'is_active' => $request->boolean('is_active', false),
            'notes' => $payload['notes'] ?? null,
        ]);

        $this->syncPrimaryGeofence($site, $payload);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Mise a jour site',
            'description' => "Site {$site->name} modifie",
        ]);

        return redirect()->route('sites.index')->with('success', 'Site mis a jour.');
    }

    public function destroy(Site $site)
    {
        $name = $site->name;
        $site->delete();

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Suppression site',
            'description' => "Site {$name} supprime",
        ]);

        return redirect()->route('sites.index')->with('success', 'Site supprime.');
    }

    protected function validatePayload(Request $request, ?int $siteId = null): array
    {
        return $request->validate([
            'code' => 'required|string|max:50|unique:sites,code,' . $siteId,
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string|max:5000',
            'geofence_name' => 'required|string|max:255',
            'geofence_latitude' => 'required|numeric|between:-90,90',
            'geofence_longitude' => 'required|numeric|between:-180,180',
            'geofence_radius_meters' => 'required|integer|min:10|max:5000',
            'geofence_allowed_accuracy_meters' => 'required|integer|min:5|max:500',
            'geofence_notes' => 'nullable|string|max:5000',
        ]);
    }

    protected function syncPrimaryGeofence(Site $site, array $payload): void
    {
        // jb -> On garde une geofence principale tres simple au depart:
        // un centre, un rayon, une precision acceptable.
        $site->geofences()->update(['is_primary' => false]);

        $site->geofences()->updateOrCreate(
            ['is_primary' => true],
            [
                'name' => $payload['geofence_name'],
                'center_latitude' => $payload['geofence_latitude'],
                'center_longitude' => $payload['geofence_longitude'],
                'radius_meters' => $payload['geofence_radius_meters'],
                'allowed_accuracy_meters' => $payload['geofence_allowed_accuracy_meters'],
                'is_primary' => true,
                'is_active' => true,
                'notes' => $payload['geofence_notes'] ?? null,
            ]
        );
    }
}
