<?php

namespace App\Http\Controllers;

use App\Exceptions\PresenceRefusedException;
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

        // Le badge actif porté par ce téléphone, s'il y en a un.
        $badge = $this->badgeActif($rawTokens);

        // Une session ouverte ne doit pas détourner le scan vers la page de
        // l'application. C'est le même geste au second passage qu'au premier,
        // et le pointage automatique n'existe qu'ici : renvoyer dans
        // l'application supprimait de fait l'automatisation pour la moitié des
        // personnes, celles qui laissent leur session ouverte.
        if (Auth::check()) {
            $user = Auth::user();

            // Le badge n'automatise que le compte qu'il autorise. Un téléphone
            // dont le badge appartient à un autre compte, ou sans badge du tout,
            // reste un pointage volontaire : le bouton reste, rien ne part seul.
            if ($badge === null || $badge->user_id !== $user->id) {
                return $this->cartePointage($site, $user, null, false);
            }

            return $this->pointagePorte($site, $user, $badge, $this->tokenDuBadge($rawTokens, $badge));
        }

        if ($badge === null) {
            // Aucun badge utilisable : mémorisation et redirection vers le login
            session(['pending_qr_site' => $site->qr_token]);

            return redirect()->route('login')->with(
                'info',
                empty($rawTokens)
                    ? "Bienvenue sur le site « {$site->name} ». Veuillez vous connecter pour valider votre pointage et configurer votre appareil."
                    : "Votre badge sur cet appareil n'est plus actif ou a expiré. Veuillez vous connecter pour le renouveler."
            );
        }

        // Un téléphone porte un seul badge : on prend le premier actif. Le choix
        // entre plusieurs profils n'existe plus, l'enrôlement l'interdit en amont.
        return $this->pointagePorte($site, $badge->user, $badge, $this->tokenDuBadge($rawTokens, $badge));
    }

    /**
     * Le badge actif porté par ce téléphone, déduit des tokens du cookie.
     *
     * @param  array<int, string>  $rawTokens
     */
    protected function badgeActif(array $rawTokens): ?TrustedDevice
    {
        if ($rawTokens === []) {
            return null;
        }

        $tokenHashes = array_map(fn($t) => hash('sha256', $t), $rawTokens);

        return TrustedDevice::whereIn('pointage_token_hash', $tokenHashes)
            ->where('is_qr_badge', true)
            ->whereNull('revoked_at')
            ->with(['user.etudiant.stages', 'user.domaine'])
            ->get()
            ->first(fn($device) => $device->isBadgeActive());
    }

    /**
     * Le token brut correspondant au badge retenu. Il repart dans la carte pour
     * que le POST puisse réidentifier l'appareil sans refaire la résolution.
     *
     * @param  array<int, string>  $rawTokens
     */
    protected function tokenDuBadge(array $rawTokens, TrustedDevice $badge): ?string
    {
        foreach ($rawTokens as $raw) {
            if (hash('sha256', $raw) === $badge->pointage_token_hash) {
                return $raw;
            }
        }

        return null;
    }

    /**
     * Contrôles d'accès de la porte, puis carte automatique.
     *
     * Un compte désactivé ne pointe pas, même badge encore actif : la
     * désactivation doit se voir tout de suite, pas au prochain login. Un
     * compte de gestion n'est pas habilité au pointage : le badge reste
     * valide, mais ce qu'il ouvre, c'est le message qui suit.
     */
    protected function pointagePorte(Site $site, User $user, TrustedDevice $badge, ?string $deviceToken)
    {
        if ($user->status !== 'actif') {
            return view('presence.qr.result', [
                'status'     => 'rejected',
                'reasonCode' => 'account_inactive',
                'title'      => 'Compte désactivé',
                'message'    => 'Votre compte est désactivé. Contactez votre administrateur pour être réactivé.',
                'user'       => $user,
                'site'       => $site,
                'eventType'  => 'unknown',
                'time'       => now()->format('H:i'),
            ]);
        }

        if (!$user->peutPointer()) {
            return view('presence.qr.result', [
                'status'     => 'rejected',
                'reasonCode' => 'not_pointable',
                'title'      => 'Compte non habilité au pointage',
                'message'    => 'Votre compte ne permet pas le pointage.',
                'user'       => $user,
                'site'       => $site,
                'eventType'  => 'unknown',
                'time'       => now()->format('H:i'),
            ]);
        }

        return $this->cartePointage($site, $user, $deviceToken, true);
    }

    /**
     * La carte de pointage servie à la porte : même écran que dans
     * l'application, mais hors session — le badge autorise à pointer, pas à
     * entrer dans le compte.
     *
     * Elle pointe toute seule. Sur une porte, un clic de plus n'est pas une
     * confirmation : c'est une seconde à perdre, devant la file, et rien à
     * décider. Les conditions sont déjà vérifiées côté serveur ; le scan les
     * déclenche et le résultat s'affiche.
     *
     * @param  bool  $autoPointage  False quand aucun badge n'autorise ce
     *                             téléphone : le pointage reste alors un geste
     *                             volontaire, bouton conservé.
     */
    protected function cartePointage(Site $site, User $user, ?string $deviceToken, bool $autoPointage = true)
    {
        $etat = $this->pointageState->forUser($user, $site);

        $hasCheckedIn = (bool) ($etat['day']?->first_check_in_at);

        return view('presence.qr.pointage', array_merge($etat, [
            'site'        => $site,
            'user'        => $user,
            'deviceToken' => $deviceToken,
            'prenom'      => $user->personnel?->prenom ?: ($user->prenom ?: $user->name),
            'autoPointage' => $autoPointage,
            'journeeOubliee' => app(\App\Services\PointageState::class)->journeeOubliee($user, $etat['stage']),
            'declarationUrl' => route('presence.depart-oublie'),
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

        // Un compte désactivé ne pointe pas, même par badge de porte : la
        // règle vaut pour toutes les entrées, pas seulement le navigateur.
        if ($user->status !== 'actif') {
            return view('presence.qr.result', [
                'status'     => 'rejected',
                'reasonCode' => 'account_inactive',
                'title'      => 'Compte désactivé',
                'message'    => 'Votre compte est désactivé. Contactez votre administrateur pour être réactivé.',
                'user'       => $user,
                'site'       => $site,
                'eventType'  => 'unknown',
                'time'       => now()->format('H:i'),
            ]);
        }

        // La route est publique : le refus du scan doit être rejoué ici, sinon
        // un POST direct sur /p/{token}/process enregistrerait la journée d'un
        // compte de gestion.
        if (!$user->peutPointer()) {
            return view('presence.qr.result', [
                'status'     => 'rejected',
                'reasonCode' => 'not_pointable',
                'title'      => 'Compte non habilité au pointage',
                'message'    => 'Votre compte ne permet pas le pointage.',
                'user'       => $user,
                'site'       => $site,
                'eventType'  => 'unknown',
                'time'       => now()->format('H:i'),
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
                'forgotten_departure' => $result['forgotten_departure'] ?? null,
            ]);
        } catch (ValidationException $e) {
            $errorMessage = collect($e->errors())->flatten()->first() ?? "Pointage non autorisé.";
            return view('presence.qr.result', [
                'status'     => 'rejected',
                'eventType'  => 'rejected',
                'message'    => $errorMessage,
                'user'       => $user,
                'site'       => $site,
                'time'       => now()->format('H:i'),
                // Un refus d'horaire porte son code : sans lui, l'écran de
                // résultat retomberait sur « Pointage non enregistré » et la
                // personne ne saurait pas à quelle heure revenir.
                'reasonCode' => $e instanceof PresenceRefusedException
                    ? $e->reasonCode
                    : null,
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

        // Un badge qui n'a pas le droit de pointer n'a rien à faire à la porte :
        // sans ce refus, un compte d'administration promène des jetons valides,
        // sans date d'expiration, sur une route publique.
        if (!$user->peutPointer()) {
            return response()->json([
                'success' => false,
                'message' => "Ce compte n'est pas habilité au pointage. Demandez le rôle « Employé » à votre administrateur si vous devez enregistrer vos présences.",
            ], 422);
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
