<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Arr;

trait InteractsWithUsernames
{
    public function username(): ?string
    {
        return Arr::get($this->transaction, 'asset.username');
    }
}
