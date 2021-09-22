<?php

declare(strict_types=1);

namespace Tests\FakerProviders;

use Faker\Provider\Base;
use Illuminate\Support\Str;

final class Transaction extends Base
{
    public function transactionId(): string
    {
        return hash('sha256', Str::random(8));
    }
}
