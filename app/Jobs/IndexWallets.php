<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;

final class IndexWallets extends IndexModel
{
    public function handle(): void
    {
        $this->execute(indexName: 'wallets');
    }

    protected function elementsToIndexQuery(int $latestIndexedTimestamp): Builder
    {
        return Wallet::getSearchableQuery()->where('wallets.updated_at', '>', $latestIndexedTimestamp);
    }
}
