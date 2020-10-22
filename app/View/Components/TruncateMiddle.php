<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;

final class TruncateMiddle extends Component
{
    private string $value;

    private int $length;

    public function __construct(string $value, int $length = 7)
    {
        $this->value  = $value;
        $this->length = $length;
    }

    public function render(): string
    {
        $maxLength = $this->length + 3;

        if (strlen($this->value) <= $maxLength) {
            return $this->value;
        }

        return substr_replace($this->value, '...', ceil($maxLength / 2), strlen($this->value) - $maxLength);
    }
}
