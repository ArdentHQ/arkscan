<?php

declare(strict_types=1);

namespace App\Enums;

final class TransactionTypeEnum
{
    public const TRANSFER = 0;

    public const VALIDATOR_REGISTRATION = 2;

    public const VOTE = 3;

    public const VALIDATOR_RESIGNATION = 7;
}
