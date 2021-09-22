<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Cache\TaggedCache;

interface Cache
{
    public function getCache(): TaggedCache;
}
