<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Models\Scopes\EntityRegistrationScope;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Collection;

trait CanBeEntity
{
    public function hasRegistrations(): bool
    {
        return $this->wallet->sentTransactions()->withScope(EntityRegistrationScope::class)->count() > 0;
    }

    public function registrations(): Collection
    {
        return ViewModelFactory::collection($this->wallet->sentTransactions()->withScope(EntityRegistrationScope::class)->get());
    }
}
