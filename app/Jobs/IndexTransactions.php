<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;

class IndexTransactions extends IndexModel
{
    public function handle(): void
    {
        $this->execute(indexName: 'transactions');
    }

    protected function elementsToIndexQuery(int $latestIndexedTimestamp): Builder
    {
        return Transaction::where('timestamp', '>', $latestIndexedTimestamp);
    }
}
