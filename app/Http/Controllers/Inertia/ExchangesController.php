<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\IExchange;
use App\Mail\ExchangeFormSubmitted;
use App\Models\Exchange;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

final class ExchangesController
{
    public function __invoke(): Response
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
}
