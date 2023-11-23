<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

/**
 * @coversNothing
 */
class NetworkStub
{
    public function __construct(public bool $canBeExchanged)
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
}
