<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        throw new RuntimeException('Cannot run seeders this way, please use `artisan playbook:run {playbook} {--clean}`');
    }
}
