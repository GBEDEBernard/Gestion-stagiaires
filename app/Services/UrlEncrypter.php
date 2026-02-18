<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UrlEncrypter
{
    /**
     * Encrypte un ID pour l'URL
     * 
     * @param int|string $id
     * @return string
     */
    public static function encrypt($id): string
    {
        try {
            return Crypt::encryptString((string)$id);
        } catch (\Exception $e) {
            return (string)$id;
        }
    }

    /**
     * Déchiffre un ID venant de l'URL
     * 
     * @param string $encrypted
     * @return int|null
     */
    public static function decrypt($encrypted): ?int
    {
        try {
            $id = Crypt::decryptString($encrypted);
            return (int)$id;
        } catch (DecryptException $e) {
            return null;
        }
    }

    /**
     * Encrypte plusieurs IDs
     * 
     * @param array $ids
     * @return array
     */
    public static function encryptMultiple(array $ids): array
    {
        return array_map([self::class, 'encrypt'], $ids);
    }
}
