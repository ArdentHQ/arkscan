<?php

declare(strict_types=1);

namespace App\Exceptions\Contracts;

use Illuminate\Support\HtmlString;

interface EntityNotFoundInterface
{
    public function getCustomMessage(): HtmlString;
}
