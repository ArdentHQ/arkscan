<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Livewire\Component;

final class SearchModule extends Component
{
    public array $state = [
        // Generic
        'term'        => null,
        'type'        => null,
        'dateFrom'    => null,
        'dateTo'      => null,
        // Blocks
        'totalAmountFrom'    => null,
        'totalAmountTo'      => null,
        'totalFeeFrom'       => null,
        'totalFeeTo'         => null,
        'generatorPublicKey' => null,
        // Transactions
        'amountFrom'  => null,
        'amountTo'    => null,
        'feeFrom'     => null,
        'feeTo'       => null,
        'smartBridge' => null,
        // Wallets
        'username'    => null,
        'vote'        => null,
        'balanceFrom' => null,
        'balanceTo'   => null,
    ];

    public bool $isSlim = false;

    public function mount(bool $isSlim = false)
    {
        $this->isSlim = $isSlim;
    }

    public function performSearch()
    {
        $data = $this->validate([
            // Generic
            'state'             => 'array',
            'state.term'        => ['nullable', 'string', 'max:255'],
            'state.type'        => ['nullable', 'string'], // @TODO: validate based on an enum
            'state.dateFrom'    => ['nullable', 'date'],
            'state.dateTo'      => ['nullable', 'date'],
            // Blocks
            'state.totalAmountFrom'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.totalAmountTo'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.totalFeeFrom'       => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.totalFeeTo'         => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.generatorPublicKey' => ['nullable', 'string', 'max:255'],
            // Transactions
            'state.amountFrom'  => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.amountTo'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.feeFrom'     => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.feeTo'       => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.smartBridge' => ['nullable', 'string', 'max:255'],
            // Wallets
            'state.username'    => ['nullable', 'string', 'max:255'],
            'state.vote'        => ['nullable', 'string', 'max:255'],
            'state.balanceFrom' => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.balanceTo'   => ['nullable', 'integer', 'min:0', 'max:100'],
        ])['state'];

        $this->searchBlocks($data);

        $this->searchTransactions($data);

        $this->searchWallets($data);
    }

    private function searchBlocks(array $parameters)
    {
        $query = Block::query();

        if ($parameters['totalAmountFrom'] && $parameters['totalAmountTo']) {
            $query->whereBetween('total_amount', [$parameters['totalAmountFrom'], $parameters['totalAmountTo']]);
        } elseif ($parameters['totalAmountFrom']) {
            $query->where('total_amount', '>=', $parameters['totalAmountFrom']);
        } elseif ($parameters['totalAmountTo']) {
            $query->where('total_amount', '<=', $parameters['totalAmountTo']);
        }

        if ($parameters['totalFeeFrom'] && $parameters['totalFeeTo']) {
            $query->whereBetween('total_fee', [$parameters['totalFeeFrom'], $parameters['totalFeeTo']]);
        } elseif ($parameters['totalFeeFrom']) {
            $query->where('total_fee', '>=', $parameters['totalFeeFrom']);
        } elseif ($parameters['totalFeeTo']) {
            $query->where('total_fee', '<=', $parameters['totalFeeTo']);
        }

        // @TODO: take the genesis timestamp into account
        if ($parameters['dateFrom'] && $parameters['dateTo']) {
            $query->whereBetween('timestamp', [$parameters['dateFrom'], $parameters['dateTo']]);
        } elseif ($parameters['dateFrom']) {
            $query->where('timestamp', '>=', $parameters['dateFrom']);
        } elseif ($parameters['dateTo']) {
            $query->where('timestamp', '<=', $parameters['dateTo']);
        }

        if ($parameters['generatorPublicKey']) {
            $query->where('generator_public_key', $parameters['generatorPublicKey']);
        }

        return $query->paginate();
    }

    private function searchTransactions(array $parameters)
    {
        $query = Transaction::query();

        if ($parameters['amountFrom'] && $parameters['amountTo']) {
            $query->whereBetween('amount', [$parameters['amountFrom'], $parameters['amountTo']]);
        } elseif ($parameters['amountFrom']) {
            $query->where('amount', '>=', $parameters['amountFrom']);
        } elseif ($parameters['amountTo']) {
            $query->where('amount', '<=', $parameters['amountTo']);
        }

        if ($parameters['feeFrom'] && $parameters['feeTo']) {
            $query->whereBetween('fee', [$parameters['feeFrom'], $parameters['feeTo']]);
        } elseif ($parameters['feeFrom']) {
            $query->where('fee', '>=', $parameters['feeFrom']);
        } elseif ($parameters['feeTo']) {
            $query->where('fee', '<=', $parameters['feeTo']);
        }

        // @TODO: take the genesis timestamp into account
        if ($parameters['dateFrom'] && $parameters['dateTo']) {
            $query->whereBetween('timestamp', [$parameters['dateFrom'], $parameters['dateTo']]);
        } elseif ($parameters['dateFrom']) {
            $query->where('timestamp', '>=', $parameters['dateFrom']);
        } elseif ($parameters['dateTo']) {
            $query->where('timestamp', '<=', $parameters['dateTo']);
        }

        if ($parameters['smartBridge']) {
            $query->where('vendor_field_hex', $parameters['smartBridge']);
        }

        return $query->paginate();
    }

    private function searchWallets(array $parameters)
    {
        $query = Wallet::query();

        if ($parameters['balanceFrom'] && $parameters['balanceTo']) {
            $query->whereBetween('balance', [$parameters['balanceFrom'], $parameters['balanceTo']]);
        } elseif ($parameters['balanceFrom']) {
            $query->where('balance', '>=', $parameters['balanceFrom']);
        } elseif ($parameters['balanceTo']) {
            $query->where('balance', '<=', $parameters['balanceTo']);
        }

        if ($parameters['term']) {
            $query->where('address', $parameters['term']);
            $query->orWhere('public_key', $parameters['term']);
        }

        if ($parameters['username']) {
            $query->where('username', $parameters['username']);
        }

        if ($parameters['vote']) {
            $query->where('vote', $parameters['vote']);
        }

        return $query->paginate();
    }
}
