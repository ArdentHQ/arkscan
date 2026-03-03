<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Services\Cache\WalletCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class OrderByWhitelistedTokensFirstScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $whitelistedTokens = app(WalletCache::class)->getWhitelistedTokens();

        if ($whitelistedTokens === []) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($whitelistedTokens), '?'));

        $builder->orderByRaw(
            "CASE WHEN LOWER(token_address) IN ({$placeholders}) THEN 0 ELSE 1 END",
            $whitelistedTokens,
        );
    }
}
