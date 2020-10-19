<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

function configureExplorerDatabase()
{
    $database = database_path('explorer.sqlite');

    File::delete($database);

    touch($database);

    Config::set('database.connections.explorer', [
        'driver'                  => 'sqlite',
        'url'                     => '',
        'database'                => $database,
        'prefix'                  => '',
        'foreign_key_constraints' => true,
    ]);

    Artisan::call('migrate', [
        '--database' => 'explorer',
        '--path'     => 'tests/migrations',
    ]);
}
