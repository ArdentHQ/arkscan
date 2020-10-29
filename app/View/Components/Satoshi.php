<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Services\NumberFormatter;
use Closure;
use Illuminate\View\Component;

final class Satoshi extends Component
{
    public function render(): Closure
    {
        return function (array $data): string {
            return NumberFormatter::satoshi(trim((string) $data['slot']));
        };
    }
}
