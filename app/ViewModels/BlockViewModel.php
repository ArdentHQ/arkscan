<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Block;
use App\Services\NumberFormatter;
use ARKEcosystem\UserInterface\Support\DateFormat;
use Illuminate\Support\Carbon;
use Spatie\ViewModels\ViewModel;

final class BlockViewModel extends ViewModel
{
    private Block $model;

    public function __construct(Block $block)
    {
        $this->model = $block;
    }

    public function id(): string
    {
        return $this->model->id;
    }

    public function timestamp(): string
    {
        return Carbon::parse(\ArkEcosystem\Crypto\Configuration\Network::get()->epoch())
            ->addSeconds($this->model->timestamp)
            ->format(DateFormat::TIME);
    }

    public function delegate(): string
    {
        return $this->model->delegate->username;
    }

    public function height(): string
    {
        return NumberFormatter::number($this->model->height);
    }

    public function transactionCount(): string
    {
        return $this->model->number_of_transactions;
    }

    public function amount(): string
    {
        return NumberFormatter::currency($this->model->total_amount / 1e8, Network::currency());
    }

    public function fee(): string
    {
        return NumberFormatter::currency($this->model->total_fee / 1e8, Network::currency());
    }

    public function reward(): string
    {
        return NumberFormatter::currency($this->model->reward / 1e8, Network::currency());
    }
}
