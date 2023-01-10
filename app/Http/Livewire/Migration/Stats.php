<?php

declare(strict_types=1);

namespace App\Http\Livewire\Migration;

use App\Http\Livewire\Migration\Concerns\HandlesStats;
use Livewire\Component;

final class Stats extends Component
{
    use HandlesStats;
}
