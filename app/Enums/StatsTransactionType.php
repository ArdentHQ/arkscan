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

    public const SWITCH_VOTE = 'switch_vote';

    public const DELEGATE_REGISTRATION = 'delegate_registration';

    public const DELEGATE_RESIGNATION = 'delegate_resignation';

    public static function all(): Collection
    {
        return new Collection([
            self::TRANSFER,
            self::MULTIPAYMENT,
            self::VOTE,
            self::UNVOTE,
            self::SWITCH_VOTE,
            self::DELEGATE_REGISTRATION,
            self::DELEGATE_RESIGNATION,
        ]);
    }
}
