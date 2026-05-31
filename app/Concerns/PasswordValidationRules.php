<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, ValidationRule|array|string>
     */
    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            Password::default()->min(12)->mixedCase()->numbers()->symbols()->uncompromised(),
            'confirmed',
        ];
    }

    /**
     * Get the validation rules used to validate the current password.
     *
     * @return array<int, ValidationRule|array|string>
     */
    protected function currentPasswordRules(): array
    {
        return [
            'required',
            'string',
            'current_password:web',
        ];
    }
}
