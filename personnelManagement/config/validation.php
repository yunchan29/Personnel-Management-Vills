<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Validation Rules
    |--------------------------------------------------------------------------
    |
    | Centralized password validation rules for consistency across
    | registration, password change, and password reset operations.
    |
    */

    'password' => [
        'required',
        'string',
        'min:8',              // Minimum 8 characters
        'confirmed',          // Must match password_confirmation field
        'regex:/[a-z]/',      // At least one lowercase letter
        'regex:/[A-Z]/',      // At least one uppercase letter
        'regex:/[0-9]/',      // At least one digit
        'regex:/[@$!%*#?&]/', // At least one special character
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Error Messages
    |--------------------------------------------------------------------------
    |
    | Custom error messages for password validation failures.
    |
    */

    'password_messages' => [
        'required' => 'Password is required.',
        'min' => 'Password must be at least 8 characters.',
        'confirmed' => 'Password confirmation does not match.',
        'regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
    ],

];
