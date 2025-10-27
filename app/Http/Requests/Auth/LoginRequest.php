<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Autoriser la requête
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Authentifier l'utilisateur
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'), // message en français défini dans auth.php
            ]);
        }

        $user = Auth::user();

        // Admin ne peut jamais être bloqué
        if ($user->role !== 'admin') {
            // Vérifier si le compte est actif
            if ($user->status !== 'actif') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Votre compte est désactivé.',
                ]);
            }

            // Vérifier si l’email est validé
            if (method_exists($user, 'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Vous devez vérifier votre email avant de vous connecter.',
                ]);
            }
        }

        // Tout est OK → réinitialiser le compteur de tentatives
        RateLimiter::clear($this->throttleKey());

        // Régénérer la session pour la sécurité
        $this->session()->regenerate();
    }

    /**
     * Vérifier la limitation des tentatives
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Clé pour limiter les tentatives
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
