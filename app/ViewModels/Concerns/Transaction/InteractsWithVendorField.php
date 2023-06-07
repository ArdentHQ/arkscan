<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

trait InteractsWithVendorField
{
    public function vendorField(): ?string
    {
        return $this->transaction->vendorField();
    }
}
