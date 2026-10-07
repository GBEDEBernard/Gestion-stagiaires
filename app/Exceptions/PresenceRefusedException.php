<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

/**
 * Refus de pointage identifié.
 *
 * Les contrôles horaires de PresenceService bloquent avec une
 * ValidationException : le message suffit pour un formulaire, mais à la porte
 * l'écran de résultat ne peut pas distinguer « trop tôt pour pointer » d'un
 * stage introuvable. Cette sous-classe transporte le code du contrôle qui a
 * bloqué, sans rien changer pour les blocs catch existants, qui continuent de
 * lire les messages comme avant.
 */
class PresenceRefusedException extends ValidationException
{
    /** Code du contrôle bloquant, lu par l'écran de résultat. */
    public string $reasonCode;

    public static function refuse(string $reasonCode, string $message): self
    {
        $exception = static::withMessages(['presence' => $message]);
        $exception->reasonCode = $reasonCode;

        return $exception;
    }
}
