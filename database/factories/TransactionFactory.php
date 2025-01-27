<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ContractMethod;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Utils\AbiEncoder;
use Illuminate\Database\Eloquent\Factories\Factory;
use function Tests\faker;

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
            'sender_address'    => fn () => $wallet->address,
            'recipient_address' => fn () => $wallet->address,
            'timestamp'         => 1603083256000,
            'gas_price'         => $this->faker->numberBetween(1, 100),
            'amount'            => $this->faker->numberBetween(1, 100) * 1e18,
            'nonce'             => 1,
            'data'              => function () {
                // In-memory stream
                $stream = fopen('php://temp', 'r+');
                fwrite($stream, '');
                rewind($stream);

                return $stream;
            },
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

    public function tokenTransfer(string $address, int $amount): Factory
    {
        $payload  = ContractMethod::transfer();
        $payload .= str_pad(preg_replace('/^0x/', '', $address), 64, '0', STR_PAD_LEFT);
        $payload .= str_pad(dechex($amount), 64, '0', STR_PAD_LEFT);

        return $this->withPayload($payload)
            ->state(fn () => [
                // TODO: update recipient - https://app.clickup.com/t/86dvdegme
                'recipient_address' => Network::knownContract('consensus'),
            ]);
    }

    /**
     * @param array{string} $recipients
     * @param array{BigNumber} $amounts
     * @return Factory
     */
    public function multiPayment(array $recipients, array $amounts): Factory
    {
        $pay = [];

        foreach ($recipients as $index => $recipient) {
            $pay[0][] = $recipient;
            $pay[1][] = $amounts[$index]->__toString();
        }

        $payload = (new AbiEncoder(ContractAbiType::MULTIPAYMENT))
            ->encodeFunctionCall(AbiFunction::MULTIPAYMENT->value, $pay);

        $payload = preg_replace('/^0x/', '', $payload);

        return $this->withPayload($payload)
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

    public function validatorRegistration(?string $address = null): Factory
    {
        $method = ContractMethod::validatorRegistration();

        if ($address === null) {
            $address = faker()->wallet['address'];
        }

        return $this->withPayload($method.str_pad(preg_replace('/^0x/', '', $address), 64, '0', STR_PAD_LEFT))
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
        return $this->state(fn () => [
            'recipient_address' => null,
        ]);
    }

    public function withPayload(string $payload): Factory
    {
        $binaryData = hex2bin($payload);

        return $this->state(fn () => [
            'data' => function () use ($binaryData) {
                // In-memory stream
                $stream = fopen('php://temp', 'r+');
                fwrite($stream, $binaryData);
                rewind($stream);

                return $stream;
            },
        ]);
    }
}
