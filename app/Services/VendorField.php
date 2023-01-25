<?php

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

        if (! is_string($vendorField) && ! is_int($vendorField)) {
            return null;
        }

        if ($vendorField === '') {
            return null;
        }

        return bin2hex($vendorField);
    }

    public static function parse(mixed $vendorField): ?string
    {
        if (is_null($vendorField)) {
            return null;
        }

        if (is_int($vendorField)) {
            return $vendorField;
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

        if (str_starts_with($vendorField, '0x')) {
            return $vendorField;
        }

        if (! self::isValidHex($vendorField)) {
            return $vendorField;
        }

        return hex2bin($vendorField);
    }

    private static function isValidHex(string $value): bool
    {
        if (strlen($value) & 1) {
            return false;
        }

        if (! ctype_xdigit($value)) {
            return false;
        }

        return preg_match('/^[a-f0-9]{2,}$/i', $value);
    }
}
