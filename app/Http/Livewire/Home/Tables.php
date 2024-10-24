<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Tables extends Component
{
    public string $view = 'transactions';

    public ?string $previousView = 'transactions';

    public array $alreadyLoadedViews = [
        'transactions' => false,
        'blocks'       => false,
    ];

    /**
     * @var mixed
     */
    protected $queryString = [
        'view' => ['except' => 'transactions'],
    ];

    public function render(): View
    {
        return view('livewire.home.tables');
    }

    public function triggerViewIsReady(?string $view = null): void
    {
        if ($view === null) {
            $view = $this->view;
        }

        if (! array_key_exists($view, $this->alreadyLoadedViews)) {
            return;
        }

        if ($this->alreadyLoadedViews[$view] === true) {
            return;
        }

        $this->dispatch('set'.ucfirst($view).'Ready');

        $this->alreadyLoadedViews[$view] = true;
    }

    public function updatingView(string $newView): void
    {
        if ($newView === $this->view) {
            return;
        }

        $this->previousView = $this->view;

        $this->triggerViewIsReady($newView);
    }
}
