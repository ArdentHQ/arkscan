<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Illuminate\Support\Str;

trait HasTabs
{
    use SyncsInput;

    public function triggerViewIsReady(?string $view = null): void
    {
        $this->dispatch('changedTabTo'.Str::studly($view));

        if ($view === null) {
            $view = $this->view;
        }

        if (! array_key_exists($view, $this->alreadyLoadedViews)) {
            return;
        }

        if ($this->alreadyLoadedViews[$view] === true) {
            return;
        }

        $this->dispatch('set'.Str::studly($view).'Ready');

        $this->alreadyLoadedViews[$view] = true;
    }

    public function updatingView(string $newView): void
    {
        if ($newView === $this->view) {
            return;
        }

        $this->triggerViewIsReady($newView);
    }
}
