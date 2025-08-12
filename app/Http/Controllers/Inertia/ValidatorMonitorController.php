<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\Actions\CacheNetworkHeight;
use App\DTO\Slot;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Http\Livewire\Concerns\ValidatorData;
use App\Http\Livewire\Validators\Concerns\HandlesMonitorDataBoxes;
use App\Models\Block;
use App\Services\Monitor\Monitor as MonitorService;
use App\Services\Timestamp;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

// Heavily duplicated from app/Http/Livewire/Validators/Monitor.php for a Proof-of-Concept.
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
            'height'   => Inertia::optional(fn () => CacheNetworkHeight::execute()),
            'rowCount' => Inertia::optional(fn () => Network::validatorCount()),

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
