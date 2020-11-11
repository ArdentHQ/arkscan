<?php

declare(strict_types=1);

namespace Tests;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $faker = Factory::create();
        $faker->addProvider(new Wallet($faker));

        $app->instance(Generator::class, $faker);

        return $app;
    }
}
