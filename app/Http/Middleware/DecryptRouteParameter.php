<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class DecryptRouteParameter
{
    public function handle(Request $request, Closure $next): Response
    {
        $paramsToDecrypt = ['stage', 'etudiant', 'badge', 'service', 'signataire', 'user', 'jour', 'type_stage', 'role'];

        foreach ($paramsToDecrypt as $param) {
            if (!$request->route($param)) {
                continue;
            }

            $value = $request->route($param);

            if (!$this->isEncrypted($value)) {
                continue;
            }

            try {
                $decryptedValue = Crypt::decryptString($value);
                $request->route()->setParameter($param, $decryptedValue);
            } catch (\Throwable $e) {
                \Log::error("Failed to decrypt parameter {$param}: {$e->getMessage()}");
                abort(404, 'URL invalide ou expiree');
            }
        }

        return $next($request);
    }

    private function isEncrypted($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        if (ctype_digit($value)) {
            return false;
        }

        if (str_starts_with($value, 'eyJ')) {
            return true;
        }

        return strlen($value) > 20 && preg_match('/[%+=\/]/', $value) === 1;
    }
}
