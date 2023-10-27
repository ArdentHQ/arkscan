<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Facades\Rounds;
use App\Facades\Settings;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\DelegateData;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;
use Throwable;

/**
 * @property bool $hasDelegates
 */
final class Monitor extends Component
{
    use DeferLoading;
    use DelegateData;

    /** @var mixed */
    protected $listeners = [
        'monitorIsReady',
    ];

    private array $delegates = [];

    public function render(): View
    {
        return view('livewire.delegates.monitor', [
            'delegates'  => collect($this->delegates)
                ->each(fn ($slot) => $slot->setFavorite(Settings::hasFavoriteDelegate($slot->publicKey())))
                ->sortBy(fn ($slot) => ! $slot->isFavorite())
                ->values(),

            'round'      => Rounds::current(),
        ]);
    }

    public function monitorIsReady(): void
    {
        $this->setIsReady();

        $this->pollDelegates();
    }

    public function getHasDelegatesProperty(): bool
    {
        return count($this->delegates) > 0;
    }

    public function pollDelegates(): void
    {
        if (! $this->isReady) {
            return;
        }

        try {
            $this->delegates = $this->fetchDelegates();

            Cache::forget('poll-delegates-exception-occurrence');
        } catch (Throwable $e) {
            $occurrences = Cache::increment('poll-delegates-exception-occurrence');

            if ($occurrences >= 3) {
                throw $e;
            }

            // @README: If any errors occur we want to keep polling until we have a list of delegates
            $this->pollDelegates();
        }
    }
}
