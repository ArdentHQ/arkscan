<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Str;

trait InteractsWithVendorField
{
    /**
     * This property is false only if the vendor field has not been streamed yet.
     * When computed, it will either be a string (if vendor field is available), or null (if not found).
     */
    protected bool|string|null $vendorField = false;

    /**
     * @codeCoverageIgnore
     */
    public function vendorField(): ?string
    {
        if (! is_bool($this->vendorField)) {
            return $this->vendorField;
        }

        $this->vendorField = null;
        $vendorField = $this->transaction->vendor_field;

        if (is_null($vendorField)) {
            return null;
        }

        $vendorField = stream_get_contents($vendorField);

        if ($vendorField === '' || $vendorField === false) {
            return null;
        }

        $this->vendorField = $vendorField;
        return $vendorField;
    }

    public function migratedAddress(): ?string
    {
        $vendorField = $this->vendorField();

        if ($vendorField === null) {
            return null;
        }

        if(Str::length($vendorField) === 42 && Str::startsWith($vendorField, '0x')) {
            return $vendorField;
        }

        return null;
    }
}
