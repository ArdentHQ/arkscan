<?php

declare(strict_types=1);

namespace App\Enums;

enum PayloadSignature: string
{
    case TRANSFER = 'a9059cbb';

    case VALIDATOR_REGISTRATION = '602a9eee';

    case VOTE = '6dd7d8ea';

    case UNVOTE = '3174b689';

    case VALIDATOR_RESIGNATION = 'b85f5da2';
}
