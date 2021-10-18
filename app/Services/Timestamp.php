<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

final class Timestamp
{
    public static function fromGenesis(int $seconds): Carbon
    {
        return Network::epoch()->addSeconds($seconds);
    }

    public static function fromGenesisHuman(int $seconds, bool $short = false): string
    {
        return static::fromGenesis($seconds)->format(
            $short ? DateFormat::TIME_SHORT : DateFormat::TIME
        );
    }

    public static function fromUnix(int $seconds): Carbon
    {
        return Carbon::createFromTimestamp($seconds)->subSeconds(Network::epoch()->unix());
    }

    public static function fromUnixHuman(int $seconds): string
    {
        return Carbon::createFromTimestamp($seconds)->format(DateFormat::TIME);
    }

    public static function now(): Carbon
    {
        return static::fromUnix(Carbon::now()->unix());
    }
}
