<?php

declare(strict_types=1);

namespace App\Services;

final class MigrationAddress
{
    /**
     * Added so it's shown with both the username and the avatar in the UI.
     */
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
