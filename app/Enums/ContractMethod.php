<?php

declare(strict_types=1);

namespace App\Enums;

use App\Facades\Network;

final class ContractMethod
{
    public static function transfer(): ?string
    {
        return Network::contractMethod('transfer', 'a9059cbb');
    }

    public static function validatorRegistration(): ?string
    {
        return Network::contractMethod('validator_registration', '602a9eee');
    }

    public static function vote(): ?string
    {
        return Network::contractMethod('vote', '6dd7d8ea');
    }

    public static function unvote(): ?string
    {
        return Network::contractMethod('unvote', '3174b689');
    }

    public static function validatorResignation(): ?string
    {
        return Network::contractMethod('validator_resignation', 'b85f5da2');
    }
}
