<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Http\Livewire\Concerns\HasTabs;
use App\Http\Livewire\Concerns\SyncsInput;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * @property int $page
 * @property ?int $perPage
 */
final class Tabs extends Component
{
    use HasTabs;
    use SyncsInput;

    #[Url(history: true, except: 'validators')]
    public string $view = 'validators';

    public array $alreadyLoadedViews = [
        'validators'     => false,
        'missed-blocks'  => false,
        'recent-votes'   => false,
    ];

    public function render(): View
    {
        return view('livewire.validators.tabs');
    }

    #[On('showValidatorsView')]
    public function showValidatorsView(string $view): void
    {
        $this->syncInput('view', $view);
    }
}
