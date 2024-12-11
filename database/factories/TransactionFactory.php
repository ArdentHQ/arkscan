<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ContractMethod;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Receipt;
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
            'sender_public_key' => fn () => $wallet->public_key,
            'recipient_address' => fn () => $wallet->address,
            'timestamp'         => 1603083256000,
            'gas_price'         => $this->faker->numberBetween(1, 100),
            'amount'            => $this->faker->numberBetween(1, 100) * 1e18,
            'nonce'             => 1,
            'sequence'          => 1,
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

    public function multiPayment(): Factory
    {
        $method = ContractMethod::multiPayment();

        return $this->withPayload($method)
            ->state(fn () => [
                'recipient_address' => Network::knownContract('multipayment'),
            ]);
    }

    public function vote(string $address): Factory
    {
        $method = ContractMethod::vote();

        return $this->withPayload($method.str_pad(preg_replace('/^0x/', '', $address), 64, '0', STR_PAD_LEFT))
            ->state(fn () => [
                'recipient_address' => Network::knownContract('consensus'),
            ]);
    }

    public function unvote(): Factory
    {
        $method = ContractMethod::unvote();

        return $this->withPayload($method)
            ->state(fn () => [
                'recipient_address' => Network::knownContract('consensus'),
            ]);
    }

    public function validatorRegistration(): Factory
    {
        $method = ContractMethod::validatorRegistration();

        return $this->withPayload($method)
            ->state(fn () => [
                'recipient_address' => Network::knownContract('consensus'),
            ]);
    }

    public function validatorResignation(): Factory
    {
        $method = ContractMethod::validatorResignation();

        return $this->withPayload($method)
            ->state(fn () => [
                'recipient_address' => Network::knownContract('consensus'),
            ]);
    }

    public function usernameRegistration(): Factory
    {
        $method = ContractMethod::usernameRegistration();

        return $this->withPayload($method)
            ->state(fn () => [
                'recipient_address' => Network::knownContract('username'),
            ]);
    }

    public function usernameResignation(): Factory
    {
        $method = ContractMethod::usernameResignation();

        return $this->withPayload($method)
            ->state(fn () => [
                'recipient_address' => Network::knownContract('username'),
            ]);
    }

    public function contractDeployment(): Factory
    {
        $method = ContractMethod::contractDeployment();

        return $this->withPayload($method)
            ->state(fn () => [
                'recipient_address' => null,
            ]);
    }

    public function withPayload(string $payload): Factory
    {
        $binaryData = hex2bin($payload);

        // In-memory stream
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $binaryData);
        rewind($stream);

        return $this->state(fn () => [
            'data' => $stream,
        ]);
    }
}
