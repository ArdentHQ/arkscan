<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Livewire\Wireable;

final class ValidatorStatistics implements Wireable
{
    public ?WalletViewModel $mostUniqueVoters = null;

    public ?WalletViewModel $leastUniqueVoters = null;

    public ?WalletViewModel $mostBlocksForged = null;

    public function __construct(
        ?Wallet $mostUniqueVoters = null,
        ?Wallet $leastUniqueVoters = null,
        ?Wallet $mostBlocksForged = null,
        public ?WalletWithValue $oldestActiveValidator = null,
        public ?WalletWithValue $newestActiveValidator = null,
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
        ?WalletWithValue $oldestActiveValidator = null,
        ?WalletWithValue $newestActiveValidator = null,
    ): self {
        return new self(
            $mostUniqueVoters,
            $leastUniqueVoters,
            $mostBlocksForged,
            $oldestActiveValidator,
            $newestActiveValidator,
        );
    }

    public function toLivewire(): array
    {
        return [
            'mostUniqueVoters'  => $this->mostUniqueVoters?->address(),
            'leastUniqueVoters' => $this->leastUniqueVoters?->address(),
            'mostBlocksForged'  => $this->mostBlocksForged?->address(),

            'oldestActiveValidator' => [
                'wallet'    => $this->oldestActiveValidator?->wallet->address,
                'timestamp' => $this->oldestActiveValidator?->timestamp->toISOString(),
            ],
            'newestActiveValidator' => [
                'wallet'    => $this->newestActiveValidator?->wallet->address,
                'timestamp' => $this->newestActiveValidator?->timestamp->toISOString(),
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
        $oldestActiveValidator = null;
        if ($value['oldestActiveValidator']['wallet'] !== null) {
            $oldestActiveValidator = Wallet::firstWhere('address', $value['oldestActiveValidator']['wallet']);
        }

        $newestActiveValidator = null;
        if ($value['newestActiveValidator']['wallet'] !== null) {
            $newestActiveValidator = Wallet::firstWhere('address', $value['newestActiveValidator']['wallet']);
        }

        return new self(
            $value['mostUniqueVoters'] !== null ? Wallet::firstWhere('address', $value['mostUniqueVoters']) : null,
            $value['leastUniqueVoters'] !== null ? Wallet::firstWhere('address', $value['leastUniqueVoters']) : null,
            $value['mostBlocksForged'] !== null ? Wallet::firstWhere('address', $value['mostBlocksForged']) : null,
            $oldestActiveValidator !== null ? WalletWithValue::make(
                $oldestActiveValidator,
                Carbon::parse($value['oldestActiveValidator']['timestamp']),
            ) : null,
            $newestActiveValidator !== null ? WalletWithValue::make(
                $newestActiveValidator,
                Carbon::parse($value['newestActiveValidator']['timestamp']),
            ) : null,
        );
    }
}
