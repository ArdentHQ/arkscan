<?php

declare(strict_types=1);

use Laravel\Fortify\Features;

return [

    /*
    |--------------------------------------------------------------------------
    | Fortify Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify which authentication guard Fortify will use while
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

    'guard'                   => 'web',

    /*
    |--------------------------------------------------------------------------
    | Fortify Password Broker
    |--------------------------------------------------------------------------
    |
    | Here you may specify which password broker Fortify can use when a user
    | is resetting their password. This configured value should match one
    | of your password brokers setup in your "auth" configuration file.
    |
    */

    'passwords'               => 'users',

    /*
    |--------------------------------------------------------------------------
    | Username / Email
    |--------------------------------------------------------------------------
    |
    | This value defines which model attribute should be considered as your
    | application's "username" field. Typically, this might be the email
    | address of the users but you are free to change this value here.
    |
    | Out of the box, Fortify expects forgot password and reset password
    | requests to have a field named 'email'. If the application uses
    | another name for the field you may define it below as needed.
    |
    */

    'username'                => 'email',

    'username_alt'            => 'username',

    'email'                   => 'email',

    /*
    |--------------------------------------------------------------------------
    | Home Path
    |--------------------------------------------------------------------------
    |
    | Here you may configure the path where users will get redirected during
    | authentication or password reset when the operations are successful
    | and the user is authenticated. You are free to change this value.
    |
    */

    'home'                    => config('home'),

    /*
    |--------------------------------------------------------------------------
    | Accept invitation route name
    |--------------------------------------------------------------------------
    |
    | If there is a route for accept an user invitation it should be defined
    | here. Note that the route should accept the `$invitation` object
    |
    */
    'accept_invitation_route' => 'invitations.accept',

    /*
    |--------------------------------------------------------------------------
    | Fortify Routes Middleware
    |--------------------------------------------------------------------------
    |
    | Here you may specify which middleware Fortify will assign to the routes
    | that it registers with the application. If necessary, you may change
    | these middleware but typically this provided default is preferred.
    |
    */

    'middleware'              => ['web'],

    'middlewares'             => [
        'account_settings' => [
            'update_profile'  => ['web', 'auth'],
            'update_password' => ['web', 'auth'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | By default, Fortify will throttle logins to five requests per minute for
    | every email and IP address combination. However, if you would like to
    | specify a custom rate limiter to call then you may specify it here.
    |
    */

    'limiters'                => [
        'login' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Some of the Fortify features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features or you can even remove all of these if you need to.
    |
    */

    'features'                => [
        // Features::registration(),
        // Features::resetPasswords(),
        // Features::emailVerification(),
        // Features::updateProfileInformation(),
        // Features::updatePasswords(),
        // Features::twoFactorAuthentication([
        //     'confirmPassword' => true,
        // ]),
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Model mapping which allows overriding of models on a per-project basis.
    |
    */

    'models'                  => [
        'user'       => '',
        'invitation' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mails
    |--------------------------------------------------------------------------
    */

    'mail'                    => [
        'default'  => [
            'name'    => env('MAIL_DEFAULT_NAME', 'ARK Ecosystem'),
            'address' => env('MAIL_DEFAULT_ADDRESS', 'noreply@ark.io'),
        ],

        'feedback' => [
            'name'    => env('MAIL_FEEDBACK_NAME', 'ARK Ecosystem'),
            'address' => env('MAIL_FEEDBACK_ADDRESS', 'feedback@marketsquare.io'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */

    'routes'                  => [
        'feedback_thank_you'        => env('ROUTE_FEEDBACK_THANK_YOU', '/feedback/thank-you'),
        'two_factor_reset_password' => env('ROUTE_TWO_RESET_PASSWORD', '/two-factor/reset-password/{token}'),
        'account_settings_account'  => env('ROUTE_ACCOUNT_SETTINGS_ACCOUNT', '/account/settings/account'),
        'account_settings_password' => env('ROUTE_ACCOUNT_SETTINGS_PASSWORD', '/account/settings/password'),
    ],

];
