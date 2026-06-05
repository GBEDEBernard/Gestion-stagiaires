<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Str;

final class NotificationGreeting
{
    public static function greetingForNow(?\DateTimeInterface $now = null): string
    {
        $now ??= now();

        // Africa/Lagos est déjà dans config('app.timezone'), donc now() est cohérent.
        $hour = (int) $now->format('H');

        // Matin : 06:00 - 11:59
        // Soir : 18:00 - 21:59
        if ($hour >= 6 && $hour <= 11) {
            return 'Bonjour';
        }

        if ($hour >= 18 && $hour <= 21) {
            return 'Bonsoir';
        }

        // Par défaut : on reste prudent (Bonjour pour 00-05 et 12-17)
        // et on peut ajuster plus tard si besoin.
        return 'Bonjour';
    }

    public static function civilityFromGenre(?string $genre): string
    {
        $genre = Str::of((string) $genre)->trim()->lower();

        if ($genre === 'masculin' || $genre === 'homme') {
            return 'Monsieur';
        }

        if ($genre === 'feminin' || $genre === 'femme') {
            return 'Madame';
        }

        return 'Bonjour'; // sera corrigé par le template si nécessaire (fallback)
    }

    public static function civilityForRecipient(?User $user): string
    {
        // Le champ `genre` est dans personnels (via polymorphisme).
        $genre = $user?->personnel?->genre;
        $civility = self::civilityFromGenre($genre);

        // Fallback : si le genre est inconnu
        if ($civility === 'Bonjour') {
            return 'Cher/Chère';
        }

        return $civility;
    }
}

