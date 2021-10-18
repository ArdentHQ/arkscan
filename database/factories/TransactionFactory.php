<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\MagistrateTransactionEntityActionEnum;
use App\Enums\MagistrateTransactionEntitySubTypeEnum;
use App\Enums\MagistrateTransactionEntityTypeEnum;
use App\Enums\MagistrateTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
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
            'timestamp'         => 112982056,
            'fee'               => $this->faker->numberBetween(1, 100) * 1e8,
            'amount'            => $this->faker->numberBetween(1, 100) * 1e8,
            'nonce'             => 1,
            'asset'             => [
                'ipfs' => 'QmXrvSZaDr8vjLUB9b7xz26S3kpk3S3bSc8SUyZmNPvmVo',
            ],
        ];
    }

    public function transfer(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::TRANSFER,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [],
        ]);
    }

    public function secondSignature(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::SECOND_SIGNATURE,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [],
        ]);
    }

    public function delegateRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::DELEGATE_REGISTRATION,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [],
        ]);
    }

    public function vote(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::VOTE,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [
                'votes' => ['+publicKey'],
            ],
        ]);
    }

    public function unvote(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::VOTE,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [
                'votes' => ['-publicKey'],
            ],
        ]);
    }

    public function voteCombination(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::VOTE,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [
                'votes' => ['+publicKey', '-publicKey'],
            ],
        ]);
    }

    public function multiSignature(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::MULTI_SIGNATURE,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [],
        ]);
    }

    public function ipfs(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::IPFS,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [
                'ipfs' => 'QmXrvSZaDr8vjLUB9b7xz26S3kpk3S3bSc8SUyZmNPvmVo',
            ],
        ]);
    }

    public function delegateResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::DELEGATE_RESIGNATION,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [],
        ]);
    }

    public function multiPayment(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::MULTI_PAYMENT,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [],
        ]);
    }

    public function timelock(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::TIMELOCK,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [],
        ]);
    }

    public function timelockClaim(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::TIMELOCK_CLAIM,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [],
        ]);
    }

    public function timelockRefund(): Factory
    {
        return $this->state(fn () => [
            'type'       => CoreTransactionTypeEnum::TIMELOCK_REFUND,
            'type_group' => TransactionTypeGroupEnum::CORE,
            'asset'      => [],
        ]);
    }

    public function entityRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
            ],
        ]);
    }

    public function entityResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
            ],
        ]);
    }

    public function entityUpdate(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
            ],
        ]);
    }

    public function businessEntityRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
            ],
        ]);
    }

    public function businessEntityResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
            ],
        ]);
    }

    public function businessEntityUpdate(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::BUSINESS,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
            ],
        ]);
    }

    public function productEntityRegistration(array $data = []): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
                'data'    => $data,
            ],
        ]);
    }

    public function productEntityResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::PRODUCT,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
            ],
        ]);
    }

    public function productEntityUpdate(?string $registrationId = null): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'           => MagistrateTransactionEntityTypeEnum::PRODUCT,
                'subtype'        => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'         => MagistrateTransactionEntityActionEnum::UPDATE,
                'registrationId' => $registrationId,
            ],
        ]);
    }

    public function pluginEntityRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
            ],
        ]);
    }

    public function pluginEntityResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
            ],
        ]);
    }

    public function pluginEntityUpdate(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::PLUGIN,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
            ],
        ]);
    }

    public function moduleEntityRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
            ],
        ]);
    }

    public function moduleEntityResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
            ],
        ]);
    }

    public function moduleEntityUpdate(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::MODULE,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
            ],
        ]);
    }

    public function delegateEntityRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::REGISTER,
            ],
        ]);
    }

    public function delegateEntityResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::RESIGN,
            ],
        ]);
    }

    public function delegateEntityUpdate(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::ENTITY,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [
                'type'    => MagistrateTransactionEntityTypeEnum::DELEGATE,
                'subtype' => MagistrateTransactionEntitySubTypeEnum::NONE,
                'action'  => MagistrateTransactionEntityActionEnum::UPDATE,
            ],
        ]);
    }

    public function legacyBusinessRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::BUSINESS_REGISTRATION,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [],
        ]);
    }

    public function legacyBusinessResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::BUSINESS_RESIGNATION,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [],
        ]);
    }

    public function legacyBusinessUpdate(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::BUSINESS_UPDATE,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [],
        ]);
    }

    public function legacyBridgechainRegistration(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::BRIDGECHAIN_REGISTRATION,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [],
        ]);
    }

    public function legacyBridgechainResignation(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::BRIDGECHAIN_RESIGNATION,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [],
        ]);
    }

    public function legacyBridgechainUpdate(): Factory
    {
        return $this->state(fn () => [
            'type'       => MagistrateTransactionTypeEnum::BRIDGECHAIN_UPDATE,
            'type_group' => TransactionTypeGroupEnum::MAGISTRATE,
            'asset'      => [],
        ]);
    }
}
