<?php

declare(strict_types=1);

namespace App\Services;

final class MigrationAddress
{
    public function isDelegate(): bool
    {
        return true;
    }

    public function address(): string
    {
        return config('explorer.migration_address');
    }

    public function username(): string
    {
        return config('explorer.migration_wallet');
    }
}
