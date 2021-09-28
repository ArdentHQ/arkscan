<?php

declare(strict_types=1);

namespace App\Enums;

final class CoreTransactionTypeEnum
{
    public const TRANSFER = 0;

    public const SECOND_SIGNATURE = 1;

    public const DELEGATE_REGISTRATION = 2;

    public const VOTE = 3;

    public const MULTI_SIGNATURE = 4;

    public const IPFS = 5;

    public const MULTI_PAYMENT = 6;

    public const DELEGATE_RESIGNATION = 7;

    public const TIMELOCK = 8;

    public const TIMELOCK_CLAIM = 9;

    public const TIMELOCK_REFUND = 10;
}
