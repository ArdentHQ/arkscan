<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns;

use ArkEcosystem\Crypto\Configuration\Network;
use ARKEcosystem\UserInterface\Support\DateFormat;
use Illuminate\Support\Carbon;

trait HasTimestamp
{
    /**
     * Get the human readable representation of the timestamp.
     *
     * @return string
     */
    public function timestamp(): string
    {
        return Carbon::parse(Network::get()->epoch())
            ->addSeconds($this->model->timestamp)
            ->format(DateFormat::TIME);
    }
}
