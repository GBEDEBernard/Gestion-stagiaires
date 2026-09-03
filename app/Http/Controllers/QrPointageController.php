<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Stage;
use App\Models\TrustedDevice;
use App\Models\User;
use App\Models\Activity;
use App\Services\NotificationService;
use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QrPointageController extends Controller
{
    public function __construct(
        protected PresenceService $presenceService,
        protected NotificationService $notificationService,
        protected \App\Services\PointageState $pointageState
    ) {}

    /**
     * Endpoint public appelé lors du scan du QR Code de porte.
     * GET /p/{site_token}
     */
    public function scan(Request $request, string $site_token)
    {
        $site = Site::where('qr_token', $site_token)->where('is_active', true)->firstOrFail();

        // 1. Récupération des tokens de badge dans le cookie
        $cookieData = $request->cookie('pointage_device_tokens');
        $rawTokens = [];
        if ($cookieData) {
            $decoded = json_decode($cookieData, true);
            $rawTokens = is_array($decoded) ? $decoded : [$cookieData];
        }

        // Déjà connecté : la page de pointage de l'application est exactement
        // cet écran. Inutile d'en servir une copie hors session.
        if (Auth::check()) {
            return redirect()->route('presence.pointage')->with(
                'info',
                "Site « {$site->name} » reconnu. Vous pouvez pointer ci-dessous."
            );
        }

        if (empty($rawTokens)) {
            // Aucun appareil enrôlé : mémorisation et redirection vers le login
            session(['pending_qr_site' => $site->qr_token]);
            return redirect()->route('login')->with(
                'info',
                "Bienvenue sur le site « {$site->name} ». Veuillez vous connecter pour valider votre pointage et configurer votre appareil."
            );
        }

        // Résolution des badges actifs liés aux tokens du cookie
        $tokenHashes = array_map(fn($t) => hash('sha256', $t), $rawTokens);
        $activeDevices = TrustedDevice::whereIn('pointage_token_hash', $tokenHashes)
            ->where('is_qr_badge', true)
            ->whereNull('revoked_at')
            ->with(['user.etudiant.stages', 'user.domaine'])
            ->get()
            ->filter(fn($device) => $device->isBadgeActive());

        if ($activeDevices->isEmpty()) {
            session(['pending_qr_site' => $site->qr_token]);
            return redirect()->route('login')->with(
                'info',
                "Votre badge sur cet appareil n'est plus actif ou a expiré. Veuillez vous connecter pour le renouveler."
            );
        }

        // Un téléphone porte un seul badge : on prend le premier actif. Le choix
        // entre plusieurs profils n'existe plus, l'enrôlement l'interdit en amont.
        $device = $activeDevices->first();
        $user   = $device->user;

        $matchingRawToken = null;
        foreach ($rawTokens as $raw) {
            if (hash('sha256', $raw) === $device->pointage_token_hash) {
                $matchingRawToken = $raw;
                break;
            }
        }

        return $this->cartePointage($site, $user, $matchingRawToken);
    }

    /**
     * La carte de pointage servie à la porte : même écran que dans
     * l'application, mais hors session — le badge autorise à pointer, pas à
     * entrer dans le compte.
     */
    protected function cartePointage(Site $site, User $user, ?string $deviceToken)
    {
        $etat = $this->pointageState->forUser($user, $site);

        return view('presence.qr.pointage', array_merge($etat, [
            'site'        => $site,
            'user'        => $user,
            'deviceToken' => $deviceToken,
            'prenom'      => $user->personnel?->prenom ?: ($user->prenom ?: $user->name),
        ]));
    }

    /**
     * Traitement effectif du pointage QR avec capture GPS.
     * POST /p/{site_token}/process
     */
    public function processPointage(Request $request, string $site_token)
    {
        $site = Site::where('qr_token', $site_token)->where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'latitude'           => 'required|numeric',
            'longitude'          => 'required|numeric',
            'accuracy_meters'    => 'nullable|numeric',
            'device_fingerprint' => 'nullable|string',
            'device_uuid'        => 'nullable|string',
            'device_label'       => 'nullable|string',
            'platform'           => 'nullable|string',
            'browser'            => 'nullable|string',
            'user_id'            => 'nullable|integer',
            'device_token'       => 'nullable|string',
            'observation_message'=> 'nullable|string|max:500',
        ]);

        $user = null;
        $device = null;

        // 1. Résolution de l'utilisateur par session ou par jeton d'appareil
        if (Auth::check()) {
            $user = Auth::user();
        } elseif (!empty($validated['device_token'])) {
            $hash = hash('sha256', $validated['device_token']);
            $device = TrustedDevice::where('pointage_token_hash', $hash)
                ->where('is_qr_badge', true)
                ->whereNull('revoked_at')
                ->first();

            if ($device && $device->isBadgeActive()) {
                $user = $device->user;
            }
        }

        if (!$user && !empty($validated['user_id']) && !empty($validated['device_token'])) {
            $hash = hash('sha256', $validated['device_token']);
            $device = TrustedDevice::where('user_id', $validated['user_id'])
                ->where('pointage_token_hash', $hash)
                ->where('is_qr_badge', true)
                ->whereNull('revoked_at')
                ->first();

            if ($device && $device->isBadgeActive()) {
                $user = $device->user;
            }
        }

        if (!$user) {
            return view('presence.qr.result', [
                'status'    => 'rejected',
                'title'     => 'Authentification requise',
                'message'   => "Impossible d'identifier votre compte. Veuillez vous connecter pour pointer.",
                'user'      => null,
                'site'      => $site,
                'eventType' => 'unknown',
                'time'      => now()->format('H:i'),
            ]);
        }

        // 2. Un retard exige une observation, comme dans le pointage classique.
        //    On la demande AVANT d'écrire la journée : constater le retard après
        //    coup laisserait la personne sans moyen de s'expliquer.
        $observation = trim((string) ($validated['observation_message'] ?? ''));

        try {
            $preflight = $this->presenceService->qrPreflight($user, $site);
        } catch (\Exception $e) {
            // Le pointage doit rester possible même si le préflight échoue,
            // mais l'échec doit se voir : avalé en silence, c'est ainsi qu'une
            // erreur SQL sur le chemin stagiaire est restée invisible.
            Log::warning('Préflight QR indisponible : ' . $e->getMessage(), [
                'user_id' => $user->id,
                'site_id' => $site->id,
            ]);

            $preflight = ['is_late' => false, 'event_type' => 'check_in', 'expected' => null];
        }

        if ($preflight['is_late'] && mb_strlen($observation) < 10) {
            return view('presence.qr.observation', [
                'site'      => $site,
                'user'      => $user,
                'expected'  => $preflight['expected'],
                'payload'   => $validated,
                'tooShort'  => $observation !== '',
            ]);
        }

        // 3. Exécution du pointage via PresenceService
        try {
            $result = $this->presenceService->registerFromQrScan(
                $user,
                $site,
                $validated,
                $device,
                $observation !== '' ? $observation : null
            );

            return view('presence.qr.result', [
                'status'    => $result['status'],
                'eventType' => $result['event_type'],
                'message'   => $result['message'],
                'user'      => $user,
                'site'      => $site,
                'time'      => now()->format('H:i'),
                'event'     => $result['event'] ?? null,
            ]);
        } catch (ValidationException $e) {
            $errorMessage = collect($e->errors())->flatten()->first() ?? "Pointage non autorisé.";
            return view('presence.qr.result', [
                'status'    => 'rejected',
                'eventType' => 'rejected',
                'message'   => $errorMessage,
                'user'      => $user,
                'site'      => $site,
                'time'      => now()->format('H:i'),
            ]);
        } catch (\Exception $e) {
            // Un identifiant court est affiché à l'utilisateur pour que l'admin
            // puisse retrouver la trace complète dans les logs sans exposer le
            // détail technique de l'erreur (SQL, chemins serveur) à la porte.
            $incidentId = strtoupper(Str::random(8));

            Log::error('Erreur lors du pointage QR: ' . $e->getMessage(), [
                'incident_id' => $incidentId,
                'user_id'     => $user->id,
                'site_id'     => $site->id,
                'trace'       => $e->getTraceAsString(),
            ]);

            return view('presence.qr.result', [
                'status'    => 'rejected',
                'eventType' => 'rejected',
                'message'   => "Une erreur est survenue lors de l'enregistrement de votre pointage. Veuillez réessayer ou signaler la référence {$incidentId} à un administrateur.",
                'user'      => $user,
                'site'      => $site,
                'time'      => now()->format('H:i'),
            ]);
        }
    }

    /**
     * Enrôler l'appareil courant comme badge : un utilisateur, un téléphone.
     * POST /presence/devices/enroll
     */
    public function enrollCurrentDevice(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non authentifié.'], 401);
        }

        $validated = $request->validate([
            'device_fingerprint' => 'required|string',
            'device_uuid'        => 'nullable|string',
            'device_label'       => 'nullable|string',
            'device_name'        => 'nullable|string|max:100',
            'platform'           => 'nullable|string',
            'browser'            => 'nullable|string',
        ]);

        // L'appareil est résolu avant de compter, car le plafond ne doit
        // s'appliquer qu'à un téléphone réellement nouveau. Quelqu'un qui vide
        // ses cookies et réenrôle le même appareil ne consomme pas de place :
        // le compter reviendrait à lui refuser son propre téléphone.
        $device = TrustedDevice::firstOrNew([
            'user_id'            => $user->id,
            'device_fingerprint' => $validated['device_fingerprint'],
        ]);

        $otherActiveBadges = $user->trustedDevices()->activeQrBadges()
            ->when($device->exists, fn($q) => $q->whereKeyNot($device->getKey()))
            ->count();

        // Un utilisateur, un seul téléphone badge.
        if ($otherActiveBadges >= 1) {
            return response()->json([
                'success' => false,
                'message' => "Un seul téléphone peut servir de badge. Révoquez l'appareil actuel depuis votre profil avant d'en enregistrer un autre.",
            ], 422);
        }

        // Et un téléphone ne peut porter qu'un seul compte : sans cela, deux
        // personnes partageant un appareil pointeraient chacune depuis le même,
        // ce qui vide la notion de badge personnel de son sens.
        $claimedByOther = TrustedDevice::where('device_fingerprint', $validated['device_fingerprint'])
            ->where('user_id', '!=', $user->id)
            ->activeQrBadges()
            ->exists();

        if ($claimedByOther) {
            return response()->json([
                'success' => false,
                'message' => "Ce téléphone sert déjà de badge à un autre compte. Son propriétaire doit le révoquer depuis son profil.",
            ], 422);
        }

        // Génération d'un jeton aléatoire sécurisé
        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        // Date d'expiration
        $expiresAt = null;
        if ($user->etudiant) {
            $latestStage = $user->etudiant->stages()->orderBy('date_fin', 'desc')->first();
            if ($latestStage && $latestStage->date_fin) {
                $expiresAt = $latestStage->date_fin->endOfDay();
            }
        }

        // L'appareil a été résolu plus haut, pour le contrôle du plafond.
        $device->pointage_token_hash = $tokenHash;
        $device->is_qr_badge         = true;
        $device->device_uuid         = $validated['device_uuid'] ?? $device->device_uuid ?? Str::uuid()->toString();
        $device->device_label        = $validated['device_label'] ?? $device->device_label ?? 'Smartphone';
        $device->device_name         = $validated['device_name'] ?? 'Mon Smartphone';
        $device->platform            = $validated['platform'] ?? $device->platform;
        $device->browser             = $validated['browser'] ?? $device->browser;
        $device->last_ip_address     = $request->ip();
        $device->token_expires_at    = $expiresAt;
        $device->revoked_at          = null;
        $device->is_trusted          = true;
        if (!$device->exists) {
            $device->first_seen_at = now();
        }
        $device->last_seen_at = now();
        $device->save();

        // Un téléphone, un compte, un badge : le cookie ne porte que le jeton
        // courant. L'empiler laissait des jetons révoqués vivre indéfiniment
        // dans le navigateur.
        $cookie = Cookie::make('pointage_device_tokens', json_encode([$rawToken]), 525600, '/', null, false, false);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Votre appareil a été configuré avec succès comme badge de pointage !",
                'device'  => $device,
            ])->withCookie($cookie);
        }

        return redirect()->back()->with('status', 'Appareil configuré comme badge avec succès !')->withCookie($cookie);
    }

    /**
     * Révoquer un appareil badge depuis le profil utilisateur.
     * DELETE /presence/devices/{device}
     */
    public function revokeDevice(TrustedDevice $device)
    {
        $user = Auth::user();
        if ($device->user_id !== $user->id) {
            abort(403, "Action non autorisée.");
        }

        $deviceName = $device->device_name ?: ($device->device_label ?: 'Smartphone');

        if ($device->revoked_at !== null) {
            return redirect()->back()->with('status', "L'appareil « {$deviceName} » était déjà révoqué.");
        }

        $device->update([
            'is_qr_badge' => false,
            'revoked_at'  => now(),
        ]);

        // Une révocation signale souvent un téléphone perdu ou volé : les
        // administrateurs doivent pouvoir la relier à un incident.
        $this->notificationService->notifyAdminsOfDeviceRevocation($user, $device);

        // Le jeton ne vaut plus rien : il n'a plus à rester sur l'appareil.
        return redirect()->back()
            ->with('status', "L'appareil « {$deviceName} » a été révoqué avec succès.")
            ->withCookie(Cookie::forget('pointage_device_tokens'));
    }

    /**
     * Révocation d'un badge par un administrateur, depuis la fiche utilisateur.
     * Complète la révocation par l'intéressé lui-même : en cas de téléphone perdu,
     * l'utilisateur n'a pas toujours le moyen de se connecter pour le couper,
     * et désactiver tout le compte serait disproportionné.
     * DELETE /admin/users/{user}/devices/{device}
     */
    public function adminRevokeDevice(Request $request, User $user, TrustedDevice $device)
    {
        // L'appareil doit bien appartenir à l'utilisateur de l'URL, sinon un
        // identifiant d'appareil deviné permettrait de révoquer n'importe qui.
        if ($device->user_id !== $user->id) {
            abort(404);
        }

        $admin      = Auth::user();
        $deviceName = $device->device_name ?: ($device->device_label ?: 'Smartphone');

        if ($device->revoked_at !== null) {
            return redirect()->back()->with('info', "L'appareil « {$deviceName} » était déjà révoqué.");
        }

        $device->update([
            'is_qr_badge' => false,
            'revoked_at'  => now(),
        ]);

        $this->notificationService->notifyUserOfDeviceRevokedByAdmin($user, $device, $admin);

        Activity::create([
            'user_id'     => $admin->id,
            'action'      => 'Revocation badge de pointage',
            'description' => "Appareil « {$deviceName} » révoqué pour {$user->name}.",
        ]);

        return redirect()->back()->with('success', "L'appareil « {$deviceName} » de {$user->name} a été révoqué.");
    }
}
