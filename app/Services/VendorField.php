<?php

declare(strict_types=1);

namespace App\Services;

final class VendorField
{
    public static function toHex(mixed $vendorField): ?string
    {
        if (is_null($vendorField)) {
            return null;
        }

        if (is_resource($vendorField)) {
            $vendorField = stream_get_contents($vendorField);
        }

        if (is_bool($vendorField)) {
            return null;
        }

        if (is_string($vendorField) && $vendorField === '') {
            return null;
        }

        return bin2hex((string) $vendorField);
    }

    public static function parse(mixed $vendorField): ?string
    {
        if (is_null($vendorField)) {
            return null;
        }

        if (is_int($vendorField)) {
            return (string) $vendorField;
        }

        if (is_resource($vendorField)) {
            $vendorField = stream_get_contents($vendorField);
        }

        if ($vendorField === '') {
            return null;
        }

        if (! is_string($vendorField)) {
            return null;
        }

        return $vendorField;
    }
}
