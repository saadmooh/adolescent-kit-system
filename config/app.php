<?php

return [

    'name' => env('APP_NAME', 'نظام حقيبة الابتكار والتعبير لليافعين'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => env('APP_TIMEZONE', 'Africa/Algiers'),

    'locale' => env('APP_LOCALE', 'ar'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'ar_SA'),

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Secret Unpredictable Login Path
    |--------------------------------------------------------------------------
    | This secret path obfuscates the authentication entry point from scanners
    | and ordinary visitors who should not see the login gate.
    */
    'admin_login_path' => env('ADMIN_LOGIN_PATH', 'sys-access-auth-8492'),

];
