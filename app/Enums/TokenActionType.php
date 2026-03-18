<?php

declare(strict_types=1);

namespace App\Enums;

enum TokenActionType: string
{
    case Transfer = 'Transfer';
    case Approval = 'Approval';
}
