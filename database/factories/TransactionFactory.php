<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PayloadSignature;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

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
            'sender_public_key' => fn () => $wallet->public_key,
            'recipient_address' => fn () => $wallet->address,
            'timestamp'         => 1603083256000,
            'gas_price'         => $this->faker->numberBetween(1, 100),
            'amount'            => $this->faker->numberBetween(1, 100) * 1e18,
            'nonce'             => 1,
        ];
    }

    public function withReceipt(int $gasUsed = 21000): Factory
    {
        return $this->has(Receipt::factory()->state(fn () => ['gas_used' => $gasUsed]));
    }

    public function transfer(): Factory
    {
        return $this->state(fn () => [
            'recipient_address' => Network::knownContract('consensus'),
        ]);
    }

    public function vote(string $address): Factory
    {
        $method = PayloadSignature::VOTE->value;

        return $this->state(fn () => [
            'recipient_address' => Network::knownContract('consensus'),
            // TODO: don't use a query for the encoding - https://app.clickup.com/t/86dv9e9nf
            'data'              => DB::raw("decode('".$method.str_pad(preg_replace('/^0x/', '', $address), 64, '0', STR_PAD_LEFT)."', 'hex')"),
        ]);
    }

    public function unvote(): Factory
    {
        $method = PayloadSignature::UNVOTE->value;

        return $this->state(fn () => [
            'recipient_address' => Network::knownContract('consensus'),
            'data'              => DB::raw("decode('".$method."', 'hex')"),
        ]);
    }

    public function validatorRegistration(): Factory
    {
        $method = PayloadSignature::VALIDATOR_REGISTRATION->value;

        return $this->state(fn () => [
            'recipient_address' => Network::knownContract('consensus'),
            'data'              => DB::raw("decode('".$method."', 'hex')"),
        ]);
    }

    public function validatorResignation(): Factory
    {
        $method = PayloadSignature::VALIDATOR_RESIGNATION->value;

        return $this->state(fn () => [
            'recipient_address' => Network::knownContract('consensus'),
            'data'              => DB::raw("decode('".$method."', 'hex')"),
        ]);
    }
}
