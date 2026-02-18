<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class DecryptRouteParameter
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Liste des paramètres à déchiffrer
        $paramsToDecrypt = ['stage', 'etudiant', 'badge', 'service', 'signataire', 'user', 'jour', 'type_stage'];

        foreach ($paramsToDecrypt as $param) {
            if ($request->route($param)) {
                try {
                    // Récupérer la valeur cryptée
                    $encryptedValue = $request->route($param);

                    // Déchiffrer
                    $decryptedValue = Crypt::decryptString($encryptedValue);

                    // Remplacer dans les paramètres de route
                    $request->route()->setParameter($param, $decryptedValue);

                    // Log pour debug
                    \Log::debug("Decrypted parameter $param: $encryptedValue -> $decryptedValue");
                } catch (\Exception $e) {
                    // Si le déchiffrement échoue, retourner 404
                    \Log::error("Failed to decrypt parameter $param: " . $e->getMessage());
                    abort(404, 'URL invalide ou expirée');
                }
            }
        }

        return $next($request);
    }
}
