<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Http\Livewire\Concerns\HasTabs;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Tabs extends Component
{
    use HasTabs;

    public string $view = 'delegates';

    public ?string $previousView = 'delegates';

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public array $alreadyLoadedViews = [
        'delegates' => false,
    ];

    public function queryString(): array
    {
        $perPage = intval(config('arkscan.pagination.per_page'));
        if ($this->view === 'delegates') {
            $perPage = Delegates::defaultPerPage();
        }

        // TODO: Handle filters - https://app.clickup.com/t/861n4ydmh - see WalletTables

        return [
            'view'    => ['except' => 'delegates'],
            'page'    => ['except' => 1],
            'perPage' => ['except' => $perPage],
        ];
    }

    public function boot(): void
    {
        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'delegates' => [
                    'page'    => 1,
                    'perPage' => Delegates::defaultPerPage(),

                    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTables
                ],
            ];
        }
    }

    public function render(): View
    {
        return view('livewire.delegates.tabs');
    }
}
