<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoForbiddenChars implements Rule
{
    protected array $forbidden = ['&', '"', "'", '<', '>'];

    public function passes($attribute, $value)
    {
        foreach ($this->forbidden as $char) {
            if (strpos($value, $char) !== false) {
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return 'Le mot de passe ne doit pas contenir les caractères : & " \' < >';
    }
}