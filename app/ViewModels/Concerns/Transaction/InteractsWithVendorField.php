<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Str;

trait InteractsWithVendorField
{
    /**
     * This property is false only if the migrated address has never been computed.
     * When computed, it will either be a string (if migrated address found), or null (if not found).
     */
    protected bool|string|null $migratedAddress = false;

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
        if (! is_bool($this->migratedAddress)) {
            return $this->migratedAddress;
        }

        $vendorField = $this->vendorField();

        if ($vendorField === null) {
            $this->migratedAddress = null;

            return null;
        }

        $this->migratedAddress = Str::length($vendorField) === 42 && Str::startsWith($vendorField, '0x')
                    ? $vendorField
                    : null;

        return $this->migratedAddress;
    }
}
