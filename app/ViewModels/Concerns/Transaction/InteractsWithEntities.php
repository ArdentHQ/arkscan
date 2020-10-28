<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Arr;

trait InteractsWithEntities
{
    public function entityName(): ?string
    {
        return Arr::get($this->transaction, 'asset.data.name');
    }
}
