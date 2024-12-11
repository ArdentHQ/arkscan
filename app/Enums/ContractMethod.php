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

    public static function multiPayment(): ?string
    {
        return Network::contractMethod('multipayment', '084ce708');
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

    public static function usernameRegistration(): ?string
    {
        return Network::contractMethod('username_registration', '36a94134');
    }

    public static function usernameResignation(): ?string
    {
        return Network::contractMethod('username_resignation', 'ebed6dab');
    }

    public static function contractDeployment(): ?string
    {
        return Network::contractMethod('contract_deployment', '60806040');
    }
}
