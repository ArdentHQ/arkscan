<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Icons Sets
    |--------------------------------------------------------------------------
    |
    | With this config option you can define a couple of
    | default icon sets. Provide a key name for your icon
    | set and a combination from the options below.
    |
    */

    'sets' => [

        'default' => [

            /*
            |-----------------------------------------------------------------
            | Icons Path
            |-----------------------------------------------------------------
            |
            | Provide the relative path from your app root to your
            | SVG icons directory. Icons are loaded recursively
            | so there's no need to list every sub-directory.
            |
            */

            'path' => 'vendor/arkecosystem/foundation/resources/assets/icons',

            /*
            |--------------------------------------------------------------------------
            | Default Prefix
            |--------------------------------------------------------------------------
            |
            | This config option allows you to define a default prefix for
            | your icons. The dash separator will be applied automatically
            | to every icon name. It's required and needs to be unique.
            |
            */

            'prefix' => 'icon',

            /*
            |--------------------------------------------------------------------------
            | Default Set Class
            |--------------------------------------------------------------------------
            |
            | This config option allows you to define some classes which
            | will be applied to all icons by default within this set.
            |
            */

            'class' => 'fill-current',

        ],

        'app' => [
            'path'   => 'resources/icons',
            'prefix' => 'app',
            'class'  => 'fill-current',
        ],

        'transactions' => [
            'path'   => 'resources/icons/transactions',
            'prefix' => 'app.transactions',
            'class'  => 'fill-current',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Class
    |--------------------------------------------------------------------------
    |
    | This config option allows you to define some classes which
    | will be applied to all icons by default.
    |
    */

    'class' => '',

];
