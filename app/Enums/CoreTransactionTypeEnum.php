<?php

declare(strict_types=1);

namespace App\Enums;

final class CoreTransactionTypeEnum
{
    const TRANSFER = 0;

    const SECOND_SIGNATURE = 1;

    const DELEGATE_REGISTRATION = 2;

    const VOTE = 3;

    const MULTI_SIGNATURE = 4;

    const IPFS = 5;

    const MULTI_PAYMENT = 6;

    const DELEGATE_RESIGNATION = 7;

    const TIMELOCK = 8;

    const TIMELOCK_CLAIM = 9;

    const TIMELOCK_REFUND = 10;
}
