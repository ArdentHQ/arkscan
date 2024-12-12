<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * @coversNothing
 */
class NetworkStub
{
    public function __construct(public bool $canBeExchanged, public ?Carbon $epoch = null)
    {
        //
    }

    public function canBeExchanged(): bool
    {
        return $this->canBeExchanged;
    }

    public function currency(): string
    {
        return 'DARK';
    }

    public function epoch(): ?Carbon
    {
        return $this->epoch;
    }

    public function contractMethod(string $name, string $default): string
    {
        $method = Arr::get(config('arkscan.networks.development.contract_methods'), $name);
        if ($method === null) {
            return $default;
        }

        return $method;
    }

    public function knownContracts(): array
    {
        return config('arkscan.networks.development.contract_addresses');
    }

    public function knownContract(string $name): ?string
    {
        return Arr::get($this->knownContracts(), $name);
    }
}
