<?php

declare(strict_types=1);

namespace App\Enums;

final class MagistrateTransactionTypeEnum
{
    public const BUSINESS_REGISTRATION = 0;

    public const BUSINESS_RESIGNATION = 1;

    public const BUSINESS_UPDATE = 2;

    public const BRIDGECHAIN_REGISTRATION = 3;

    public const BRIDGECHAIN_RESIGNATION = 4;

    public const BRIDGECHAIN_UPDATE = 5;

    public const ENTITY = 6;
}
