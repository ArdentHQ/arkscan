<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class SearchModule extends Component
{
    public ?string $term;

    public ?string $type;

    public ?string $amountRangeFrom;

    public ?string $amountRangeTo;

    public ?string $feeRangeFrom;

    public ?string $feeRangeTo;

    public ?string $dateFrom;

    public ?string $dateTo;

    public function render()
    {
        return view('livewire.search-module');
    }

    public function performSearch()
    {
        return view('livewire.search-module');
    }
}
