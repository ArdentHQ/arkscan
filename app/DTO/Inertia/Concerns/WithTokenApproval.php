<?php

declare(strict_types=1);

namespace App\DTO\Inertia\Concerns;

use App\DTO\Inertia\Wallet as WalletDTO;
use App\Enums\ApproveArgument;
use App\Models\Wallet;
use App\ViewModels\TransactionViewModel;
use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;

trait WithTokenApproval
{
    protected const UNLIMITED_APPROVAL_AMOUNT = '115792089237316195423570985008687907853269984665640564039457584007913129639935';

    protected static function tokenApprovalDetails(TransactionViewModel $transaction): ?array
    {
        if (! $transaction->isApprove()) {
            return null;
        }

        $arguments = $transaction->methodArguments();
        if (count($arguments) === 0 || ! array_key_exists(ApproveArgument::SPENDER, $arguments)) {
            return null;
        }

        $spender = (new ArgumentDecoder($arguments[ApproveArgument::SPENDER]))->decodeAddress();

        $amount      = null;
        $isUnlimited = false;
        $isRevoke    = false;
        if (array_key_exists(ApproveArgument::VALUE, $arguments)) {
            $amount = (new ArgumentDecoder($arguments[ApproveArgument::VALUE]))->decodeUnsignedInt();

            // Max uint256 (2^256 - 1). ERC20 approve uses this value to represent unlimited allowance.
            if ($amount === static::UNLIMITED_APPROVAL_AMOUNT) {
                $isUnlimited = true;
            } elseif ($amount === '0') {
                $isRevoke = true;
            }
        }

        $spenderWallet     = Wallet::where('address', $spender)->first();
        $spenderWalletData = $spenderWallet !== null
            ? WalletDTO::fromModelSimple($spenderWallet)
            : WalletDTO::stub($spender);

        return [
            'spender'     => $spenderWalletData,
            'amount'      => $amount,
            'isUnlimited' => $isUnlimited,
            'isRevoke'    => $isRevoke,
        ];
    }
}
