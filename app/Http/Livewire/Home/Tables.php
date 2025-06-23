<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Http\Livewire\Concerns\HasTabs;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

final class Tables extends Component
{
    use HasTabs;

    #[Url(history: true, except: 'validators')]
    public string $view = 'transactions';

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
}
