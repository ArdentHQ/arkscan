<?php

declare(strict_types=1);

namespace Tests\FakerProviders;

use App\Facades\Network;
use Faker\Provider\Base;
use Illuminate\Support\Str;

final class Round extends Base
{
    public function validators(): array
    {
        $validators = [];
        foreach (range(0, Network::validatorCount()) as $_) {
            $validators[] = Str::limit(hash('sha512', Str::random(8)), 66);
        }

        return $validators;
    }
}
