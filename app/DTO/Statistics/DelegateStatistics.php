<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Livewire\Wireable;

final class DelegateStatistics implements Wireable
{
    public ?WalletViewModel $mostUniqueVoters = null;

    public ?WalletViewModel $leastUniqueVoters = null;

    public ?WalletViewModel $mostBlocksForged = null;

    public function __construct(
        ?Wallet $mostUniqueVoters = null,
        ?Wallet $leastUniqueVoters = null,
        ?Wallet $mostBlocksForged = null,
        public ?WalletWithValue $oldestActiveDelegate = null,
        public ?WalletWithValue $newestActiveDelegate = null,
    ) {
        if ($mostUniqueVoters !== null) {
            $this->mostUniqueVoters = new WalletViewModel($mostUniqueVoters);
        }

        if ($leastUniqueVoters !== null) {
            $this->leastUniqueVoters = new WalletViewModel($leastUniqueVoters);
        }

        if ($mostBlocksForged !== null) {
            $this->mostBlocksForged = new WalletViewModel($mostBlocksForged);
        }
    }

    public static function make(
        ?Wallet $mostUniqueVoters = null,
        ?Wallet $leastUniqueVoters = null,
        ?Wallet $mostBlocksForged = null,
        ?WalletWithValue $oldestActiveDelegate = null,
        ?WalletWithValue $newestActiveDelegate = null,
    ): self {
        return new self(
            $mostUniqueVoters,
            $leastUniqueVoters,
            $mostBlocksForged,
            $oldestActiveDelegate,
            $newestActiveDelegate,
        );
    }

    public function toLivewire(): array
    {
        return [
            'mostUniqueVoters'  => $this->mostUniqueVoters?->address(),
            'leastUniqueVoters' => $this->leastUniqueVoters?->address(),
            'mostBlocksForged'  => $this->mostBlocksForged?->address(),

            'oldestActiveDelegate' => [
                'wallet'    => $this->oldestActiveDelegate?->wallet->address,
                'timestamp' => $this->oldestActiveDelegate?->timestamp->toISOString(),
            ],
            'newestActiveDelegate' => [
                'wallet'    => $this->newestActiveDelegate?->wallet->address,
                'timestamp' => $this->newestActiveDelegate?->timestamp->toISOString(),
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public static function fromLivewire($value)
    {
        $oldestActiveDelegate = null;
        if ($value['oldestActiveDelegate']['wallet'] !== null) {
            $oldestActiveDelegate = Wallet::firstWhere('address', $value['oldestActiveDelegate']['wallet']);
        }

        $newestActiveDelegate = null;
        if ($value['newestActiveDelegate']['wallet'] !== null) {
            $newestActiveDelegate = Wallet::firstWhere('address', $value['newestActiveDelegate']['wallet']);
        }

        return new self(
            $value['mostUniqueVoters'] !== null ? Wallet::firstWhere('address', $value['mostUniqueVoters']) : null,
            $value['leastUniqueVoters'] !== null ? Wallet::firstWhere('address', $value['leastUniqueVoters']) : null,
            $value['mostBlocksForged'] !== null ? Wallet::firstWhere('address', $value['mostBlocksForged']) : null,
            $oldestActiveDelegate !== null ? WalletWithValue::make(
                $oldestActiveDelegate,
                Carbon::parse($value['oldestActiveDelegate']['timestamp']),
            ) : null,
            $newestActiveDelegate !== null ? WalletWithValue::make(
                $newestActiveDelegate,
                Carbon::parse($value['newestActiveDelegate']['timestamp']),
            ) : null,
        );
    }
}
