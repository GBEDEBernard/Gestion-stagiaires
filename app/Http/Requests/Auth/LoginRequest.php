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
<<<<<<< HEAD
    /**
     * Autoriser la requête
     */
=======
>>>>>>> e9635ab
    public function authorize(): bool
    {
        return true;
    }

<<<<<<< HEAD
    /**
     * Règles de validation
     */
=======
>>>>>>> e9635ab
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

<<<<<<< HEAD
    /**
     * Authentifier l'utilisateur
     *
     * @throws ValidationException
     */
=======
>>>>>>> e9635ab
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

<<<<<<< HEAD
        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'), // message en français défini dans auth.php
=======
        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
>>>>>>> e9635ab
            ]);
        }

        $user = Auth::user();

<<<<<<< HEAD
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
=======
        if ($user->status !== 'actif') {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Votre compte est desactive.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
>>>>>>> e9635ab
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

<<<<<<< HEAD
    /**
     * Clé pour limiter les tentatives
     */
=======
>>>>>>> e9635ab
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
