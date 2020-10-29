<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Facades\Network;
use App\Services\NumberFormatter;
use Closure;
use Illuminate\View\Component;

final class Currency extends Component
{
    public function render(): Closure
    {
        return function (array $data): string {
            return NumberFormatter::currency(trim((string) $data['slot']), Network::currency());
        };
    }
}
