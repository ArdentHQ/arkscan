<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\Actions\CacheNetworkHeight;
use App\Actions\CacheNetworkSupply;
use App\DTO\Inertia\Block as BlockDTO;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Facades\Services\GasTracker;
use App\Facades\Settings;
use App\Http\Controllers\Concerns\WithStatistics;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\PriceChartCache;
use App\Services\ExchangeRate;
use App\Services\MarketCap;
use App\Services\NumberFormatter;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController
{
    use WithStatistics;

    public function __invoke(Request $request): Response
    {
        return Inertia::renderWithMeta('Home/Index', 'home', [
            'statistics' => fn () => $this->statistics(),

            'transactions' => Inertia::optional(function () {
                $paginator = $this->getTransactions();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noTransactionsResultsMessage(isEmpty: $paginator->isEmpty()),
                ];
            }),

            'chart'   => fn () => $this->getChartData($request),

            'blocks' => Inertia::optional(function () {
                $paginator = $this->getBlocks();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noBlocksResultsMessage($paginator->isEmpty()),
                ];
            }),

            'baseUrl' => route('home', absolute: false),
        ]);
    }

    public function noTransactionsResultsMessage(bool $isEmpty): ?string
    {
        return $isEmpty
            ? (string) trans('tables.transactions.no_results.no_results')
            : null;
    }

    public function noBlocksResultsMessage(bool $isEmpty): ?string
    {
        return $isEmpty
            ? (string) trans('tables.blocks.no_results')
            : null;
    }

    public function getTransactions(): LengthAwarePaginator
    {
        return Transaction::query()
            ->with(['votedFor', 'sender', 'senderWallet', 'recipientWallet', 'multiPaymentRecipients'])
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

    protected function getBlockHeight(): int
    {
        return CacheNetworkHeight::execute();
    }

    private function statistics(): array
    {
        $gasLow     = (string) GasTracker::low();
        $gasAverage = (string) GasTracker::average();
        $gasHigh    = (string) GasTracker::high();

        $gasLowValue     = null;
        $gasAverageValue = null;
        $gasHighValue    = null;

        if (Network::canBeExchanged()) {
            $gasLowValue     = $this->formatGasValue($gasLow);
            $gasAverageValue = $this->formatGasValue($gasAverage);
            $gasHighValue    = $this->formatGasValue($gasHigh);
        }

        return [
            'blockHeight' => $this->getBlockHeight(),
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

    private function formatGasValue(string $gas): string
    {
        $amount    = BigNumber::new((string) UnitConverter::parseUnits($gas, 'gwei'));
        $currency  = Settings::currency();
        $converted = ExchangeRate::convertNumerical($amount->toFloat());

        if (! NumberFormatter::isFiat($currency)) {
            return NumberFormatter::currency($converted, $currency, true);
        }

        if ($converted > 0 && $converted < 0.01) {
            return sprintf('< %s', NumberFormatter::currency(0.01, $currency));
        }

        return NumberFormatter::currency($converted, $currency);
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
