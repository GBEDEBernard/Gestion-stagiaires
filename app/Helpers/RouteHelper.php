<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route as LaravelRoute;

class RouteHelper
{
    /**
     * Génère une URL avec paramètre crypté
     * 
     * @param string $name Nom de la route
     * @param mixed $parameters Paramètres (peut être un modèle ou un ID)
     * @param array $additionalParams Paramètres additionnels
     * @param bool $absolute
     * @return string
     */
    public static function encryptedRoute($name, $parameters = [], $additionalParams = [], $absolute = true)
    {
        // Gérer le cas où $additionalParams est en réalité un booléen (pour $absolute)
        if (is_bool($additionalParams)) {
            $absolute = $additionalParams;
            $additionalParams = [];
        }

        $routeParams = [];

        // Si c'est un objet Model, récupérer son ID
        if (is_object($parameters) && method_exists($parameters, 'getKey')) {
            $routeParams[] = Crypt::encryptString((string)$parameters->getKey());
        } elseif (is_numeric($parameters) || is_string($parameters)) {
            // Si c'est un ID simple
            $routeParams[] = Crypt::encryptString((string)$parameters);
        } elseif (is_array($parameters)) {
            // Si c'est un tableau
            foreach ($parameters as $key => $value) {
                if (is_object($value) && method_exists($value, 'getKey')) {
                    $routeParams[$key] = Crypt::encryptString((string)$value->getKey());
                } elseif (is_numeric($value) || is_string($value)) {
                    $routeParams[$key] = Crypt::encryptString((string)$value);
                } else {
                    $routeParams[$key] = $value;
                }
            }
        }

        // Fusionner avec les paramètres additionnels
        $finalParams = array_merge($routeParams, $additionalParams);

        return route($name, $finalParams, $absolute);
    }

    /**
     * Déchiffre un paramètre crypté
     * 
     * @param string $encryptedValue
     * @return mixed
     */
    public static function decryptParam($encryptedValue)
    {
        try {
            return Crypt::decryptString($encryptedValue);
        } catch (\Exception $e) {
            abort(404, 'URL invalide ou expirée');
        }
    }
}
