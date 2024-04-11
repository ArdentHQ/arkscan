<?php

declare(strict_types=1);

namespace App\Enums;

final class TransactionTypeEnum
{
    public const TRANSFER = 0;

    public const VALIDATOR_REGISTRATION = 2;

    public const VOTE = 3;

    public const MULTI_SIGNATURE = 4;

    public const MULTI_PAYMENT = 6;

    public const VALIDATOR_RESIGNATION = 7;

    public const USERNAME_REGISTRATION = 8;

    public const USERNAME_RESIGNATION = 9;
}