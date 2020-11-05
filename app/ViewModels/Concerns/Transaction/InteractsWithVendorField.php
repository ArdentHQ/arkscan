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
}
