<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Str;

trait InteractsWithVendorField
{
    public function vendorField(): ?string
    {
        return $this->transaction->vendorField();
    }

    public function migratedAddress(): ?string
    {
        $vendorField = $this->vendorField();

        if ($vendorField === null) {
            return null;
        }

        if (Str::length($vendorField) === 42 && Str::startsWith($vendorField, '0x')) {
            return $vendorField;
        }

        return null;
    }
}
