<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\IExchange;
use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Facades\Settings;
use App\Mail\ExchangeFormSubmitted;
use App\Models\Exchange;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\PriceChartCache;
use App\Services\MarketCap;
use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\NumberFormatter\NumberFormatter as BetterNumberFormatter;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

final class ExchangesController
{
    public function __invoke(Request $request): Response
    {
        return Inertia::renderWithMeta('Resources/Exchanges', 'exchanges', [
            'typeOptions' => [
                [
                    'title' => trans('general.all'),
                    'value' => 'all',
                ],
                [
                    'title' => trans('pages.exchanges.type.exchanges'),
                    'value' => 'exchanges',
                ],
                [
                    'title' => trans('pages.exchanges.type.aggregators'),
                    'value' => 'aggregators',
                ],
            ],

            'pairOptions' => [
                [
                    'title' => trans('general.all'),
                    'value' => 'all',
                ],
                [
                    'title' => trans('pages.exchanges.pair.btc'),
                    'value' => 'btc',
                ],
                [
                    'title' => trans('pages.exchanges.pair.eth'),
                    'value' => 'eth',
                ],
                [
                    'title' => trans('pages.exchanges.pair.stablecoins'),
                    'value' => 'stablecoins',
                ],
                [
                    'title' => trans('pages.exchanges.pair.other'),
                    'value' => 'other',
                ],
            ],

            'exchanges' => Inertia::optional(function () {
                $paginator = $this->getExchanges();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noExchangesResultsMessage($paginator->total()),
                ];
            }),

            'chart' => fn () => $this->getChartData($request),
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        /** @phpstan-ignore-next-line */
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:50'],
            'website' => ['required', 'url'],
            'pairs'   => ['required', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        Mail::send(new ExchangeFormSubmitted([
            'name'    => $data['name'],
            'website' => $data['website'],
            'pairs'   => $data['pairs'],
            'message' => $data['message'] ?? null,
        ]));

        /* @phpstan-ignore-next-line */
        flash()->success(trans('pages.exchanges.submit-modal.success_toast'));

        return response()->json();
    }

    private function getChartData(Request $request): array
    {
        $period = $this->resolveChartPeriod($request->query('chartPeriod'));

        $currency = Settings::currency();

        /** @var array{labels?: array<int|string, int|float|string>, datasets?: array<int, int|float>} $chartData */
        $chartData = (new PriceChartCache())->getHistoricalRaw($currency, $period);

        /** @var array<float> $datasets */
        $datasets = $chartData['datasets'] ?? [];
        /** @var array<int> $labels */
        $labels = $chartData['labels'] ?? [];

        $variation = $this->mainValueVariation($datasets, $currency);

        return [
            'datasets'           => collect($datasets)->values()->all(),
            'labels'             => collect($labels)->values()->all(),
            'theme'              => [
                'name' => $variation,
                'mode' => Settings::theme(),
            ],
            'period'              => $period,
            'options'             => $this->periodOptions(),
            'refreshInterval'     => (int) config('arkscan.statistics.refreshInterval', 60),
            'mainValueFiat'       => $this->mainValueFiat($currency),
            'mainValuePercentage' => $this->mainValuePercentage($datasets, $currency),
            'mainValueVariation'  => $variation,
            'marketCapValue'      => MarketCap::getFormatted(Network::currency(), $currency),
            'minPriceValue'       => $this->minPrice($datasets, $currency),
            'maxPriceValue'       => $this->maxPrice($datasets, $currency),
            'dateUnitOverride'    => $period === StatsPeriods::WEEK ? 'day' : null,
        ];
    }

    private function noExchangesResultsMessage(int $total): ?string
    {
        return $total === 0
            ? (string) trans('tables.exchanges.no_results')
            : null;
    }

    private function getExchanges(): LengthAwarePaginator
    {
        $type = request()->query('type');
        $pair = request()->query('pair');

        $sort          = request()->query('sort', 'volume');
        $sortDirection = request()->query('sort-direction', 'desc');

        if (! in_array($sort, ['name', 'top_pairs', 'volume', 'price'], true)) {
            $sort          = 'volume';
            $sortDirection = 'desc';
        }

        $exchanges = Exchange::filterByType($type)
            ->filterByPair($pair)
            ->get()
            ->sort(function ($a, $b) use ($sort, $sortDirection) {
                $volumeSort = 0;
                if ($a->volume === null) {
                    $volumeSort = 1;
                }

                if ($b->volume === null) {
                    $volumeSort = -1;
                }

                if ($volumeSort === 0) {
                    $volumeSort = ($sortDirection === 'asc' ? 1 : -1) * (intval($a->volume ?? 0) - intval($b->volume ?? 0));
                }

                if ($sort === 'volume') {
                    return $volumeSort;
                }

                if ($sort === 'price') {
                    if ($a->price === null) {
                        return 1;
                    }

                    if ($b->price === null) {
                        return -1;
                    }

                    if (floatval($a->price) === floatval($b->price)) {
                        return $volumeSort;
                    }

                    return ($sortDirection === 'asc' ? 1 : -1) * (floatval($a->price) <=> floatval($b->price));
                }

                if ($sort === 'top_pairs') {
                    $aPairsTypes = [];
                    $bPairsTypes = [];

                    foreach (['btc', 'eth', 'stablecoins', 'other'] as $pairType) {
                        if ($a->getAttributes()[$pairType] === true) {
                            $aPairsTypes[] = $pairType;
                        }

                        if ($b->getAttributes()[$pairType] === true) {
                            $bPairsTypes[] = $pairType;
                        }
                    }

                    $aPairs = implode(', ', $aPairsTypes);
                    $bPairs = implode(', ', $bPairsTypes);

                    if (strcmp($aPairs, $bPairs) === 0) {
                        return $volumeSort;
                    }

                    return ($sortDirection === 'asc' ? 1 : -1) * strcmp($aPairs, $bPairs);
                }

                return ($sortDirection === 'asc' ? 1 : -1) * strcmp($a->name, $b->name);
            })
            ->values();

        if ($exchanges->isEmpty()) {
            return new LengthAwarePaginator([], 0, 15, 1, [
                'path'     => route('exchanges'),
                'pageName' => 'page',
            ]);
        }

        return (new LengthAwarePaginator($exchanges, $exchanges->count(), $exchanges->count(), 1, [
            'path'     => route('exchanges'),
            'pageName' => 'page',
        ]))->through(fn ($exchange) => IExchange::fromModel($exchange));
    }

    private function resolveChartPeriod(?string $period): string
    {
        $availablePeriods = [
            StatsPeriods::DAY,
            StatsPeriods::WEEK,
            StatsPeriods::MONTH,
            StatsPeriods::QUARTER,
            StatsPeriods::YEAR,
            StatsPeriods::ALL,
        ];

        if (! in_array($period, $availablePeriods, true)) {
            return StatsPeriods::DAY;
        }

        return $period;
    }

    private function periodOptions(): array
    {
        return [
            [
                'value' => StatsPeriods::DAY,
                'label' => trans('forms.statistics.periods.day'),
            ],
            [
                'value' => StatsPeriods::WEEK,
                'label' => trans('forms.statistics.periods.week'),
            ],
            [
                'value' => StatsPeriods::MONTH,
                'label' => trans('forms.statistics.periods.month'),
            ],
            [
                'value' => StatsPeriods::QUARTER,
                'label' => trans('forms.statistics.periods.quarter'),
            ],
            [
                'value' => StatsPeriods::YEAR,
                'label' => trans('forms.statistics.periods.year'),
            ],
            [
                'value' => StatsPeriods::ALL,
                'label' => trans('forms.statistics.periods.all'),
            ],
        ];
    }

    private function mainValueFiat(string $currency): string
    {
        $price = $this->getPrice($currency);

        if (NumberFormatter::isFiat($currency)) {
            return BetterNumberFormatter::new()
                ->withLocale(Settings::locale())
                ->withFractionDigits(2)
                ->formatWithCurrencyAccounting($price);
        }

        return BetterNumberFormatter::new()
            ->formatWithCurrencyCustom($price, $currency, NumberFormatter::CRYPTO_DECIMALS);
    }

    private function mainValuePercentage(array $dataset, string $currency): float
    {
        $initialValue = collect($dataset)->first();
        $currentValue = $this->getPrice($currency);

        if ($currentValue === 0.0) {
            return 0.0;
        }

        return (1 - ($initialValue / $currentValue)) * 100;
    }

    private function mainValueVariation(array $dataset, string $currency): string
    {
        $initialValue = collect($dataset)->first();
        $currentValue = $this->getPrice($currency);

        return $initialValue > $currentValue ? 'red' : 'green';
    }

    private function getPrice(string $currency): float
    {
        return (new NetworkStatusBlockCache())->getPrice(Network::currency(), $currency) ?? 0.0;
    }

    private function minPrice(array $dataset, string $currency): string
    {
        return NumberFormatter::currency((float) collect($dataset)->min(), $currency);
    }

    private function maxPrice(array $dataset, string $currency): string
    {
        return NumberFormatter::currency((float) collect($dataset)->max(), $currency);
    }
}
