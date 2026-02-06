<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Facades\Network;
use App\Models\Token;
use App\Models\TokenTransfer;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TokenTransferFactory extends Factory
{
    protected $model = TokenTransfer::class;

    public function definition()
    {
        Wallet::factory()->create([
            'address' => Network::knownContract('consensus'),
        ]);

        $transaction = Transaction::factory()
            ->tokenTransfer(
                Wallet::factory()->create()->address,
                BigNumber::new($this->faker->numberBetween(1, 1000))->multipliedBy(1e18),
            )
            ->create([
                'status' => true,
            ]);

        return [
            'address'          => fn () => Token::factory()->create()->address,
            'block_number'     => $transaction->block_number,
            'index'            => $this->faker->numberBetween(0, 100),
            'transaction_hash' => $transaction->hash,
            'from'             => fn () => Wallet::factory()->create()->address,
            'to'               => fn () => Wallet::factory()->create()->address,
            'value'            => (string) BigNumber::new($this->faker->numberBetween(1, 1000))->multipliedBy(1e18),
        ];
    }
}
