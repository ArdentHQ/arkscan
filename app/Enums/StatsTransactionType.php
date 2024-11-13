<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Collection;

enum StatsTransactionType
{
    public const TRANSFER = 'transfer';

    public const MULTIPAYMENT = 'multipayment';

    public const VOTE = 'vote';

    public const UNVOTE = 'unvote';

    public const VALIDATOR_REGISTRATION = 'validator_registration';

    public const VALIDATOR_RESIGNATION = 'validator_resignation';

    public static function all(): Collection
    {
        return new Collection([
            self::TRANSFER,
            self::MULTIPAYMENT,
            self::VOTE,
            self::UNVOTE,
            self::VALIDATOR_REGISTRATION,
            self::VALIDATOR_RESIGNATION,
        ]);
    }
}
