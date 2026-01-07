<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\PriceChartCache;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController
{
    use WithPagination;

    public function __invoke(Request $request): Response
    {
        return Inertia::render('Home/Index', [
            'transactions' => Inertia::optional(function () {
                $paginator = $this->getTransactions();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getNoResultsMessageProperty($paginator->total()),
                ];
            }),
            'chart'        => fn () => $this->getChartData($request),
            'baseUrl'      => route('home', absolute: false),
        ])
            ->withMeta('home', [
                'name' => Network::currency(),
            ]);
    }

    public function getNoResultsMessageProperty(int $total): ?string
    {
        return $total === 0
            ? (string) trans('tables.transactions.no_results.no_results')
            : null;
    }

    public function getTransactions(): LengthAwarePaginator
    {
        return Transaction::query()
            ->with('votedFor')
            ->withScope(OrderByTimestampScope::class)
            ->paginate($this->perPage('transactions'), page: $this->page())
            ->through(fn (Transaction $transaction) => TransactionDTO::fromModel($transaction));
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
        $chartData    = collect((new PriceChartCache())->getHistoricalRaw($currency, $period));

        $datasets     = collect($chartData->get('datasets', []));
        $initialValue = $datasets->first() ?? $currentPrice;

        if ($datasets->isNotEmpty()) {
            $datasets->pop();
        }

        $datasets->push($currentPrice);

        $labels = collect($chartData->get('labels', []))->values()->all();

        return [
            'datasets'        => $datasets->values()->all(),
            'labels'          => $labels,
            'theme'           => [
                'name' => $initialValue > $currentPrice ? 'red' : 'green',
                'mode' => Settings::theme(),
            ],
            'period'          => $period,
            'refreshInterval' => (int) config('arkscan.statistics.refreshInterval', 60),
        ];
    }
}
