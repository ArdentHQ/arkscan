<?php

declare(strict_types=1);

namespace App\Http\Livewire\Abstracts;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTablePagination;
use Livewire\Component;

abstract class TabbedTableComponent extends Component
{
    use DeferLoading;
    use HasTablePagination;
}
