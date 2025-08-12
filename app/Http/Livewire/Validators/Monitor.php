<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Actions\CacheNetworkHeight;
use App\Facades\Rounds;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\ValidatorData;
use App\Http\Livewire\Validators\Concerns\HandlesMonitorDataBoxes;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;
use Throwable;

/**
 * @property bool $hasValidators
 * @property array $overflowValidators
 */
final class Monitor extends Component
{
    use DeferLoading;
    use HandlesMonitorDataBoxes;
    use ValidatorData;

    /** @var mixed */
    protected $listeners = [
        'monitorIsReady',
        'echo:blocks,NewBlock' => 'pollData',
    ];

    private array $validators = [];

    public function render(): View
    {
        $height = 0;
        if ($this->isReady) {
            $height = CacheNetworkHeight::execute();
        }

        return view('livewire.validators.monitor', [
            'round'              => Rounds::current()->round,
            'validators'         => $this->validators,
            'overflowValidators' => $this->overflowValidators,
            'height'             => $height,
            'statistics'         => $this->statistics,
        ]);
    }

    public function monitorIsReady(): void
    {
        $this->setIsReady();

        $this->pollValidators();
        $this->pollStatistics();
    }

    public function getHasValidatorsProperty(): bool
    {
        return count($this->validators) > 0;
    }

    public function pollData(): void
    {
        $this->pollValidators();
        $this->pollStatistics();
    }

    public function pollValidators(): void
    {
        if (! $this->isReady) {
            return;
        }

        try {
            $this->validators = $this->fetchValidators();

            Cache::forget('poll-validators-exception-occurrence');
        } catch (Throwable $e) {
            $occurrences = Cache::increment('poll-validators-exception-occurrence');

            if ($occurrences >= 3) {
                throw $e;
            }

            // @README: If any errors occur we want to keep polling until we have a list of validators
            $this->pollValidators();
        }
    }
}
