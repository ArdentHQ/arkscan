<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TransactionTypeEnum;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        $wallet = Wallet::factory()->create();

        return [
            'id'                => $this->faker->transactionId,
            'block_id'          => fn () => Block::factory(),
            'block_height'      => $this->faker->numberBetween(1, 10000),
            'type'              => $this->faker->numberBetween(1, 100),
            'type_group'        => $this->faker->numberBetween(1, 100),
            'sender_public_key' => fn () => $wallet->public_key,
            'recipient_id'      => fn () => $wallet->address,
            'timestamp'         => 1603083256000,
            'fee'               => $this->faker->numberBetween(1, 100) * 1e18,
            'amount'            => $this->faker->numberBetween(1, 100) * 1e18,
            'nonce'             => 1,
        ];
    }

    public function transfer(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::TRANSFER,
            'type_group' => 1,
            'asset'      => [],
        ]);
    }

    public function validatorRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::VALIDATOR_REGISTRATION,
            'type_group' => 1,
            'asset'      => [],
        ]);
    }

    public function vote(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::VOTE,
            'type_group' => 1,
            'asset'      => [
                'votes'   => ['address'],
                'unvotes' => [],
            ],
        ]);
    }

    public function unvote(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::VOTE,
            'type_group' => 1,
            'asset'      => [
                'votes'   => [],
                'unvotes' => ['address'],
            ],
        ]);
    }

    public function voteCombination(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::VOTE,
            'type_group' => 1,
            'asset'      => [
                'votes'   => ['address'],
                'unvotes' => ['address'],
            ],
        ]);
    }

    public function multiSignature(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::MULTI_SIGNATURE,
            'type_group' => 1,
            'asset'      => [],
        ]);
    }

    public function validatorResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::VALIDATOR_RESIGNATION,
            'type_group' => 1,
            'asset'      => [],
        ]);
    }

    public function multiPayment(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::MULTI_PAYMENT,
            'type_group' => 1,
            'asset'      => [],
        ]);
    }

    public function usernameRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::USERNAME_REGISTRATION,
            'type_group' => 1,
            'asset'      => [
                'username' => 'bob',
            ],
        ]);
    }

    public function usernameResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => TransactionTypeEnum::USERNAME_RESIGNATION,
            'type_group' => 1,
            'asset'      => [],
        ]);
    }
}
