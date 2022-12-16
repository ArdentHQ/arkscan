<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Str;

trait InteractsWithVendorField
{
    /**
     * @codeCoverageIgnore
     */
    public function vendorField(): ?string
    {
        $vendorField = $this->transaction->vendor_field;

        if (is_null($vendorField)) {
            return null;
        }

        $vendorField = stream_get_contents($vendorField);

        if ($vendorField === '' || $vendorField === false) {
            return null;
        }

        return $vendorField;
    }

    public function migratedAddress(): ?string
    {
        $vendorField = $this->vendorField();

        if ($vendorField === null) {
            return null;
        }

        return Str::length($vendorField) === 42
            && Str::startsWith($vendorField, '0x');
    }
}
