<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

trait InteractsWithVendorField
{
    /**
     * @codeCoverageIgnore
     */
    public function vendorField(): ?string
    {
        /* @phpstan-ignore-next-line */
        $vendorFieldHex = $this->transaction->vendor_field_hex;

        if (is_null($vendorFieldHex)) {
            return null;
        }

        $vendorFieldStream = stream_get_contents($vendorFieldHex);

        if ($vendorFieldStream === false) {
            return null;
        }

        $vendorField = hex2bin(bin2hex($vendorFieldStream));

        if ($vendorField === false) {
            return null;
        }

        return $vendorField;
    }
}
