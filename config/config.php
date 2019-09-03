<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    |
    | The broker option controls the default email verification broker for
    | your application. You may change this default as required,
    | but they're a perfect start for most applications.
    |
    | You may need to specify multiple email verification configurations if you have
    | more than one user table or model in the application and you want to have
    | separate email verification settings based on the specific user types.
    |
    | The expire time is the number of minutes that the email verification token
    | should be considered valid. This security feature keeps tokens short-lived
    | so they have less time to be guessed. You may change this as needed.
    |
    */

    'emailverification' => [

        'broker' => 'member',

        'admin' => [
            'provider' => 'admins',
            'expire' => 60,
        ],

        'member' => [
            'provider' => 'members',
            'expire' => 60,
        ],

        'manager' => [
            'provider' => 'managers',
            'expire' => 60,
        ],

    ],

];
