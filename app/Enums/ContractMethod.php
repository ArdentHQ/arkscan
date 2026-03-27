<?php

declare(strict_types=1);

namespace App\Enums;

use App\Facades\Network;
use App\Services\ContractAbiService;

final class ContractMethod
{
    public static function transfer(): string
    {
        return self::resolve('transfer');
    }

    public static function multiPayment(): string
    {
        return self::resolve('multipayment');
    }

    public static function vote(): string
    {
        return self::resolve('vote');
    }

    public static function unvote(): string
    {
        return self::resolve('unvote');
    }

    public static function validatorRegistration(): string
    {
        return self::resolve('validator_registration');
    }

    public static function validatorResignation(): string
    {
        return self::resolve('validator_resignation');
    }

    public static function validatorUpdate(): string
    {
        return self::resolve('validator_update');
    }

    public static function usernameRegistration(): string
    {
        return self::resolve('username_registration');
    }

    public static function usernameResignation(): string
    {
        return self::resolve('username_resignation');
    }

    public static function approve(): string
    {
        return self::resolve('approve');
    }

    public static function batchTransfer(): string
    {
        return self::resolve('batch_transfer');
    }

    /**
     * Resolve a contract method hash: config (env override) → ABI-derived fallback.
     */
    private static function resolve(string $name): string
    {
        $abiDefault = app(ContractAbiService::class)->getKnownMethodHash($name) ?? '';

        return Network::contractMethod($name, $abiDefault);
    }
}
