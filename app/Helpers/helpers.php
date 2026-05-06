<?php

use Illuminate\Support\Facades\Crypt;

if (!function_exists('encrypted_route')) {
    /**
     * Génère une URL avec paramètre crypté
     * 
     * Utilisation simple:
     * - encrypted_route('stages.show', $stage)
     * - encrypted_route('articles.edit', $article->id)
     * - encrypted_route('users.delete', ['id' => $user->id])
     * 
     * @param string $routeName Nom de la route
     * @param mixed $parameters Modèle, ID ou tableau de paramètres
     * @param bool $absolute URL absolue (par défaut true)
     * @return string URL générée
     */
    function encrypted_route($routeName, $parameters = null, $absolute = true)
    {
        // Extraire l'ID d'un modèle
        if (is_object($parameters) && method_exists($parameters, 'getKey')) {
            $id = $parameters->getKey();
        } elseif (is_numeric($parameters)) {
            $id = $parameters;
        } elseif (is_string($parameters)) {
            $id = $parameters;
        } else {
            // Si parameters est un tableau, essayer d'extraire l'ID
            $id = null;
        }

        // Crypter l'ID si trouvé
        if ($id !== null) {
            $encryptedId = Crypt::encryptString((string)$id);
            return route($routeName, $encryptedId, $absolute);
        }

        // Sinon, passer les paramètres tels quels
        return route($routeName, $parameters, $absolute);
    }
}

if (!function_exists('decrypt_route_param')) {
    /**
     * Déchiffre un paramètre de route
     * Utilisé rarement car le binding le fait automatiquement
     * 
     * @param string $encrypted
     * @return mixed ID décrypté
     */
    function decrypt_route_param($encrypted)
    {
        try {
            return Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('formatMinutes')) {
    /**
     * Format minutes to hours:minutes
     * 125 → "2h 5min" | 90 → "1h 30min" | 45 → "45min"
     */
    function formatMinutes($minutes)
    {
        $minutes = (int) $minutes;
        if ($minutes <= 0) return '0min';

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours == 0) return $mins . 'min';
        if ($mins == 0) return $hours . 'h';

        return $hours . 'h ' . $mins . 'min';
    }
}
