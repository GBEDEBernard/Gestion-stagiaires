<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',               // au moins 8 caractères
                'regex:/[A-Z]/',       // au moins une majuscule
                'regex:/[a-z]/',       // au moins une minuscule
                'regex:/[0-9]/',       // au moins un chiffre
                'regex:/[@$!%*?&]/',   // au moins un caractère spécial
                'confirmed'            // doit correspondre à password_confirmation
            ],
        ];
    }
}
