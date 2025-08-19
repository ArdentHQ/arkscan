<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\Actions\CacheNetworkHeight;
use App\Facades\Network;
use App\Http\Livewire\Concerns\ValidatorData;
use App\Http\Livewire\Validators\Concerns\HandlesMonitorDataBoxes;
use Inertia\Inertia;
use Inertia\Response;

final class ValidatorMonitorController
{
    use HandlesMonitorDataBoxes;
    use ValidatorData;

    public bool $isReady = true;

    private array $validators = [];

    private array $overflowValidators = [];

    public function __invoke(): Response
    {
        return Inertia::render('Validators/Monitor', [
            'rowCount' => Network::validatorCount(),

            // Deferred properties
            'height'        => Inertia::optional(fn () => CacheNetworkHeight::execute()),
            'validatorData' => Inertia::optional(function () {
                $this->pollValidators();
                $this->overflowValidators = $this->getOverflowValidatorsProperty();
                $this->pollStatistics();

                if ($this->statistics['nextValidator'] !== null) {
                    $this->statistics['nextValidator'] = $this->statistics['nextValidator']->model()->toArray();
                }

                return [
                    'validators'         => array_map(fn ($v) => $v->toArray(), $this->validators),
                    'overflowValidators' => array_map(fn ($v) => $v->toArray(), $this->overflowValidators),
                    'statistics'         => $this->statistics,
                ];
            }),
        ]);
    }
}
