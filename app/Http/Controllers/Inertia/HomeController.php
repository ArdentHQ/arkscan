<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\Actions\CacheNetworkSupply;
use App\DTO\Inertia\Block as BlockDTO;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Facades\Network;
use App\Facades\Services\GasTracker;
use App\Http\Controllers\Concerns\WithStatistics;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController
{
    use WithStatistics;

    public function __invoke(): Response
    {
        return Inertia::render('Home/Index', [
            'statistics' => $this->statistics(),

            'transactions' => Inertia::optional(function () {
                $paginator = $this->getTransactions();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noTransactionsResultsMessage($paginator->total()),
                ];
            }),

            'blocks' => Inertia::optional(function () {
                $paginator = $this->getBlocks();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noBlocksResultsMessage($paginator->total()),
                ];
            }),

            'baseUrl' => route('home', absolute: false),
        ])->withMeta('home', [
            'name' => Network::currency(),
        ]);
    }

    public function noTransactionsResultsMessage(int $total): ?string
    {
        return $total === 0
            ? (string) trans('tables.transactions.no_results.no_results')
            : null;
    }

    public function noBlocksResultsMessage(int $total): ?string
    {
        return $total === 0
            ? (string) trans('tables.blocks.no_results')
            : null;
    }

    public function getTransactions(): LengthAwarePaginator
    {
        return Transaction::query()
            ->with('votedFor')
            ->withScope(OrderByTimestampScope::class)
            ->paginate((int) config('arkscan.pagination.per_page'))
            ->through(fn (Transaction $transaction) => TransactionDTO::fromModel($transaction));
    }

    public function getBlocks(): LengthAwarePaginator
    {
        return Block::withScope(OrderByHeightScope::class)
            ->paginate((int) config('arkscan.pagination.per_page'))
            ->through(fn (Block $block) => BlockDTO::fromModel($block));
    }

    protected function getTotalSupply(): string
    {
        $supply = CacheNetworkSupply::execute() / config('currencies.notation.crypto', 1e18);

        return NumberFormatter::currencyShortNotation($supply);
    }

    private function statistics()
    {
        $gasLow     = (string) GasTracker::low();
        $gasAverage = (string) GasTracker::average();
        $gasHigh    = (string) GasTracker::high();

        $gasLowValue     = null;
        $gasAverageValue = null;
        $gasHighValue    = null;

        if (Network::canBeExchanged()) {
            $gasLowValue     = ExchangeRate::convert(BigNumber::new(UnitConverter::parseUnits($gasLow, 'gwei')), null, true);
            $gasAverageValue = ExchangeRate::convert(BigNumber::new(UnitConverter::parseUnits($gasAverage, 'gwei')), null, true);
            $gasHighValue    = ExchangeRate::convert(BigNumber::new(UnitConverter::parseUnits($gasHigh, 'gwei')), null, true);
        }

        return [
            'addresses'   => $this->getWallets(),
            'totalSupply' => $this->getTotalSupply(),
            'voting'      => [
                'percentage' => $this->getVotingPercent(),
                'amount'     => NumberFormatter::currencyShortNotation($this->getVotingValue()),
            ],

            'gas' => [
                'low' => [
                    'amount' => $gasLow,
                    'value'  => $gasLowValue,
                ],
                'average' => [
                    'amount' => $gasAverage,
                    'value'  => $gasAverageValue,
                ],
                'high' => [
                    'amount' => $gasHigh,
                    'value'  => $gasHighValue,
                ],
            ],
        ];
    }
}
