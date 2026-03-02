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
                $value = $request->route($param);

                // Vérifier si la valeur est réellement chiffrée (commence par eyJ pour base64)
                // ou contient des caractères spéciaux de chiffrement
                if ($this->isEncrypted($value)) {
                    try {
                        // Déchiffrer
                        $decryptedValue = Crypt::decryptString($value);

                        // Remplacer dans les paramètres de route
                        $request->route()->setParameter($param, $decryptedValue);

                        // Log pour debug
                        \Log::debug("Decrypted parameter $param: $value -> $decryptedValue");
                    } catch (\Exception $e) {
                        // Si le déchiffrement échoue, c'est une URL invalide
                        \Log::error("Failed to decrypt parameter $param: " . $e->getMessage());
                        abort(404, 'URL invalide ou expirée');
                    }
                }
                // Si ce n'est pas chiffré, on ne fait rien - Laravel gérera normalement
            }
        }

        return $next($request);
    }

    /**
     * Vérifie si une valeur est chiffrée
     */
    private function isEncrypted($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        // Les valeurs chiffrées commencent généralement par "eyJ" (JSON base64)
        // ou contiennent des caractères spéciaux comme ":" "/" "+" "="
        return str_starts_with($value, 'eyJ') ||
            preg_match('/^[A-Za-z0-9+\/=]+$/', $value) === 1;
    }
}
