<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Block as BlockDTO;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Facades\Settings;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\PriceChartCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Home/Index', [
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
            'chart'   => fn () => $this->getChartData($request),
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

    private function getChartData(Request $request): array
    {
        $period           = $request->query('chartPeriod', StatsPeriods::DAY);
        $availablePeriods = [
            StatsPeriods::ALL,
            StatsPeriods::DAY,
            StatsPeriods::WEEK,
            StatsPeriods::MONTH,
            StatsPeriods::YEAR,
        ];

        if (! in_array($period, $availablePeriods, true)) {
            $period = StatsPeriods::DAY;
        }

        $currency     = Settings::currency();
        $currentPrice = (new NetworkStatusBlockCache())->getPrice(Network::currency(), $currency) ?? 0.0;
        /** @var array{labels?: array<int|string, int|float|string>, datasets?: array<int, int|float>} $chartData */
        $chartData = (new PriceChartCache())->getHistoricalRaw($currency, $period);

        $datasets     = collect($chartData['datasets'] ?? []);
        $initialValue = $datasets->first() ?? $currentPrice;

        if ($datasets->isNotEmpty()) {
            $datasets->pop();
        }

        $datasets->push($currentPrice);

        $labels    = collect($chartData['labels'] ?? [])->values()->all();
        $volume    = (new CryptoDataCache())->getVolume($currency);
        $marketCap = MarketCap::getFormatted(Network::currency(), $currency);

        return [
            'datasets'        => $datasets->values()->all(),
            'labels'          => $labels,
            'theme'           => [
                'name' => $initialValue > $currentPrice ? 'red' : 'green',
                'mode' => Settings::theme(),
            ],
            'market'          => [
                'volume'    => $volume !== null ? NumberFormatter::currencyForViews($volume, $currency) : null,
                'marketCap' => $marketCap,
            ],
            'period'          => $period,
            'refreshInterval' => (int) config('arkscan.statistics.refreshInterval', 60),
        ];
    }
}
