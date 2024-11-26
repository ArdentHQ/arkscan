<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Collection;

enum StatsTransactionType
{
    public const TRANSFER = 'transfer';

    public const BATCH_TRANSFER = 'batch_transfer';

    public const VOTE = 'vote';

    public const UNVOTE = 'unvote';

    public const VALIDATOR_REGISTRATION = 'validator_registration';

    public const VALIDATOR_RESIGNATION = 'validator_resignation';

    public static function all(): Collection
    {
        return new Collection([
            self::TRANSFER,
            self::BATCH_TRANSFER,
            self::VOTE,
            self::UNVOTE,
            self::VALIDATOR_REGISTRATION,
            self::VALIDATOR_RESIGNATION,
        ]);
    }
}
