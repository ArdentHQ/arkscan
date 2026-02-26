<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Statistics\AddressHoldingStatistics;
use App\DTO\Statistics\LowHighValue;
use App\DTO\Statistics\MarketDataPriceStatistics;
use App\DTO\Statistics\MarketDataRecordStatistics;
use App\DTO\Statistics\MarketDataStatistics;
use App\DTO\Statistics\MarketDataVolumeStatistics;
use App\DTO\Statistics\TimestampedValue;
use App\DTO\Statistics\TransactionAveragesStatistics;
use App\DTO\Statistics\UniqueAddressesStatistics;
use App\DTO\Statistics\WalletWithValue;
use App\Enums\StatsPeriods;
use App\Enums\StatsTransactionType;
use App\Facades\Network;
use App\Facades\Services\GasTracker;
use App\Facades\Settings;
use App\Http\Controllers\Concerns\WithStatistics;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\BlockCache;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\FeeCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\TransactionCache;
use App\Services\ExchangeRate;
use App\Services\MainsailApi;
use App\Services\MarketCap;
use App\Services\NumberFormatter;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\WalletViewModel;
use Brick\Math\BigDecimal;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

final class StatisticsController
{
    use WithStatistics;

    /**
     * @var array<int, string>
     */
    private const PERIODS = [
        StatsPeriods::DAY,
        StatsPeriods::WEEK,
        StatsPeriods::MONTH,
        StatsPeriods::QUARTER,
        StatsPeriods::YEAR,
        StatsPeriods::ALL,
    ];

    public function __invoke(): Response
    {
        $refreshInterval             = (int) config('arkscan.statistics.refreshInterval', 60);
        $snapshotLastBlockHeight     = (int) config('arkscan.statistics.snapshotLastBlockHeight', 0);
        $snapshotBlockHeight         = $snapshotLastBlockHeight > 0 ? $snapshotLastBlockHeight + 1 : 0;

        return Inertia::renderWithMeta('Statistics/Index', 'statistics', [
            'refreshInterval'             => $refreshInterval,
            'snapshotBlockHeight'         => $snapshotBlockHeight,
            'gasTracker'                  => $this->gasTracker(),
            'highlights'                  => $this->highlights(),
            'informationCards'            => $this->informationCards($refreshInterval),
            'insights'                    => $this->insights(),
        ]);
    }

    private function gasTracker(): array
    {
        $duration        = MainsailApi::timeToForge();
        $canBeExchanged  = Network::canBeExchanged();

        return [
            'canBeExchanged' => $canBeExchanged,
            'fees'           => [
                'low'     => $this->gasFee(GasTracker::low(), $duration, $canBeExchanged),
                'average' => $this->gasFee(GasTracker::average(), $duration, $canBeExchanged),
                'high'    => $this->gasFee(GasTracker::high(), $duration, $canBeExchanged),
            ],
        ];
    }

    private function gasFee(mixed $amount, int $duration, bool $canBeExchanged): array
    {
        $numericAmount = $amount instanceof \App\Services\BigNumber ? $amount->toFloat() : (float) $amount;

        return [
            'amount'        => (string) $amount,
            'duration'      => $duration,
            'durationLabel' => trans_choice('general.seconds_duration', $duration, ['duration' => $duration]),
            'value'         => $canBeExchanged ? ExchangeRate::convertNumerical($numericAmount) : null,
        ];
    }

    private function highlights(): array
    {
        return [
            'totalSupply' => $this->getTotalSupply(),
            'voting'      => [
                'percentage' => $this->getVotingPercent(),
                'value'      => $this->getVotingValue(),
            ],
            'validators'  => $this->getValidators(),
            'wallets'     => $this->getWallets(),
        ];
    }

    private function informationCards(int $refreshInterval): array
    {
        return [
            'refreshInterval' => $refreshInterval,
            'defaultPeriod'   => StatsPeriods::DAY,
            'periods'         => self::PERIODS,
            'transactions'    => $this->informationCardTransactions(),
            'fees'            => $this->informationCardFees(),
        ];
    }

    private function informationCardTransactions(): array
    {
        $periods = collect(self::PERIODS)
            ->mapWithKeys(function (string $period): array {
                $chartData = $this->chartData(TransactionCache::class, $period);

                return [
                    $period => [
                        'value' => $this->totalFromChart($chartData),
                        'chart' => [
                            ...$chartData,
                            'theme' => $this->chartTheme('black'),
                        ],
                    ],
                ];
            })
            ->toArray();

        return [
            'allTimeValue' => $this->totalFromChart(
                $this->chartData(TransactionCache::class, StatsPeriods::ALL)
            ),
            'periods' => $periods,
        ];
    }

    private function informationCardFees(): array
    {
        $periods = collect(self::PERIODS)
            ->mapWithKeys(function (string $period): array {
                $chartData      = $this->chartData(FeeCache::class, $period);
                $periodTotal    = $this->totalFromChart($chartData);
                $aboveThreshold = $periodTotal > 10000 * 1e18;

                return [
                    $period => [
                        'value'          => $this->weiToArk($periodTotal),
                        'aboveThreshold' => $aboveThreshold,
                        'chart'          => [
                            ...$this->convertFeesChart($chartData),
                            'theme' => $this->chartTheme('yellow'),
                        ],
                    ],
                ];
            })
            ->toArray();

        return [
            'allTimeValue' => $this->weiToArk(
                $this->totalFromChart($this->chartData(FeeCache::class, StatsPeriods::ALL))
            ),
            'periods' => $periods,
        ];
    }

    private function chartData(string $cache, string $period): array
    {
        if ($cache === FeeCache::class) {
            $data = (new FeeCache())->getHistorical($period);
        } elseif ($cache === TransactionCache::class) {
            $data = (new TransactionCache())->getHistorical($period);
        } else {
            throw new InvalidArgumentException("Given cache [{$cache}] is invalid. Use FeeCache or TransactionCache.");
        }

        return [
            'labels'   => array_values($data['labels'] ?? []),
            'datasets' => array_values($data['datasets'] ?? []),
        ];
    }

    private function totalFromChart(array $chartData): float
    {
        return array_sum($chartData['datasets'] ?? []);
    }

    private function convertFeesChart(array $chartData): array
    {
        $datasets = array_map(
            static function ($value): float {
                return BigDecimal::of(NumberFormatter::weiToArk((string) BigDecimal::of($value), false))->toFloat();
            },
            $chartData['datasets'] ?? [],
        );

        return [
            'labels'   => $chartData['labels'] ?? [],
            'datasets' => array_values($datasets),
        ];
    }

    private function weiToArk(float $value): float
    {
        return BigDecimal::of(NumberFormatter::weiToArk((string) BigDecimal::of($value), false))->toFloat();
    }

    private function chartTheme(string $color): array
    {
        return [
            'name' => $color,
            'mode' => Settings::theme(),
        ];
    }

    private function insights(): array
    {
        $statisticsCache  = new StatisticsCache();
        $transactionCache = new TransactionCache();

        return [
            'transactions' => $this->transactionInsights($transactionCache),
            'marketData'   => Network::canBeExchanged() ? $this->marketDataInsights($statisticsCache) : null,
            'validators'   => $this->validatorInsights($statisticsCache),
            'addresses'    => $this->addressInsights($statisticsCache),
            'annual'       => $this->annualInsights($statisticsCache),
        ];
    }

    private function transactionInsights(TransactionCache $cache): array
    {
        $details = StatsTransactionType::all()
            ->mapWithKeys(fn ($type) => [$type => $cache->getHistoricalByType($type)])
            ->toArray();

        $averages = TransactionAveragesStatistics::make($cache->getHistoricalAverages())->toArray();

        return [
            'details'  => $details,
            'averages' => $averages,
            'records'  => $this->transactionRecords($cache),
        ];
    }

    private function transactionRecords(TransactionCache $transactionCache): array
    {
        $blockCache = new BlockCache();

        $largestTransaction = $transactionCache->getLargestIdByAmount();
        $highestFeeBlock    = $blockCache->getLargestIdByFees();
        $mostTransactions   = $blockCache->getLargestIdByTransactionCount();

        return [
            'largest_transaction' => $this->makeTransactionRecord($largestTransaction !== null
                ? Transaction::where('hash', $largestTransaction)->first()
                : null),
            'highest_fee' => $this->makeBlockRecord($highestFeeBlock !== null
                ? Block::where('hash', $highestFeeBlock)->first()
                : null, 'highest_fee'),
            'most_transactions_in_block' => $this->makeBlockRecord($mostTransactions !== null
                ? Block::where('hash', $mostTransactions)->first()
                : null, 'most_transactions_in_block'),
        ];
    }

    private function makeTransactionRecord(?Transaction $transaction): ?array
    {
        if ($transaction === null) {
            return null;
        }

        $viewModel = new TransactionViewModel($transaction);

        return [
            'type'      => 'transaction',
            'url'       => $transaction->url(),
            'hash'      => $transaction->hash,
            'amount'    => $viewModel->amount(),
            'timestamp' => $transaction->timestamp,
        ];
    }

    private function makeBlockRecord(?Block $block, string $key): ?array
    {
        if ($block === null) {
            return null;
        }

        $record = [
            'type'      => 'block',
            'url'       => $block->url(),
            'height'    => $block->number->toNumber(),
            'timestamp' => $block->timestamp,
        ];

        if ($key === 'most_transactions_in_block') {
            $record['transactionCount'] = $block->transactions_count;
        } elseif ($key === 'highest_fee') {
            $record['fee'] = $block->fee->toFloat();
        }

        return $record;
    }

    private function marketDataInsights(StatisticsCache $cache): array
    {
        $marketData = MarketDataStatistics::make(
            $this->getMarketDataPrice($cache),
            $this->getMarketDataVolume($cache),
            $this->getMarketDataCap($cache),
        );

        return [
            'prices' => [
                'daily' => [
                    'low'  => $marketData->prices->dailyLow(),
                    'high' => $marketData->prices->dailyHigh(),
                ],
                'year' => [
                    'low'  => $marketData->prices->yearLow(),
                    'high' => $marketData->prices->yearHigh(),
                ],
                'atl' => [
                    'value'     => $marketData->prices->atlValue(),
                    'timestamp' => $marketData->prices->atlTimestamp(),
                ],
                'ath' => [
                    'value'     => $marketData->prices->athValue(),
                    'timestamp' => $marketData->prices->athTimestamp(),
                ],
            ],
            'volume' => [
                'today' => $marketData->volume->todayVolumeValue(),
                'atl'   => [
                    'value'     => $marketData->volume->atlValue(),
                    'timestamp' => $marketData->volume->atlTimestamp(),
                ],
                'ath'   => [
                    'value'     => $marketData->volume->athValue(),
                    'timestamp' => $marketData->volume->athTimestamp(),
                ],
            ],
            'caps' => [
                'today' => $marketData->caps->todayValueValue(),
                'atl'   => [
                    'value'     => $marketData->caps->atlValue(),
                    'timestamp' => $marketData->caps->atlTimestamp(),
                ],
                'ath'   => [
                    'value'     => $marketData->caps->athValue(),
                    'timestamp' => $marketData->caps->athTimestamp(),
                ],
            ],
        ];
    }

    private function validatorInsights(StatisticsCache $cache): array
    {
        $mostUniqueVoters  = Wallet::firstWhere('address', $cache->getMostUniqueVoters());
        $leastUniqueVoters = Wallet::firstWhere('address', $cache->getLeastUniqueVoters());
        $mostBlocksForged  = Wallet::firstWhere('address', $cache->getMostBlocksForged());

        $rows = [];

        $rows[] = $this->validatorRow('most_unique_voters', $mostUniqueVoters, function (WalletViewModel $viewModel): int {
            return $viewModel->voterCount();
        });

        $rows[] = $this->validatorRow('least_unique_voters', $leastUniqueVoters, function (WalletViewModel $viewModel): int {
            return $viewModel->voterCount();
        });

        $oldestActiveValidator = $this->walletWithValue('oldest_active_validator', $cache->getOldestActiveValidator());
        if ($oldestActiveValidator !== null) {
            $rows[] = $oldestActiveValidator;
        }

        $newestActiveValidator = $this->walletWithValue('newest_active_validator', $cache->getNewestActiveValidator());
        if ($newestActiveValidator !== null) {
            $rows[] = $newestActiveValidator;
        }

        $rows[] = $this->validatorRow('most_blocks_forged', $mostBlocksForged, function (WalletViewModel $viewModel): int {
            return $viewModel->forgedBlocks();
        });

        return array_values($rows);
    }

    private function validatorRow(string $key, ?Wallet $wallet, callable $valueResolver): array
    {
        if ($wallet === null) {
            return [
                'key'    => $key,
                'wallet' => null,
                'value'  => null,
            ];
        }

        $viewModel = new WalletViewModel($wallet);

        return [
            'key'    => $key,
            'wallet' => $this->walletData($viewModel),
            'value'  => $valueResolver($viewModel),
        ];
    }

    private function walletWithValue(string $key, ?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $wallet = Wallet::firstWhere('address', Arr::get($data, 'address'));
        if ($wallet === null) {
            return null;
        }

        $timestamp = Arr::get($data, 'timestamp');
        if ($timestamp === null) {
            return null;
        }

        $walletWithValue = WalletWithValue::make($wallet, Carbon::createFromTimestamp($timestamp));

        return [
            'key'    => $key,
            'wallet' => $this->walletData($walletWithValue->wallet()),
            'value'  => $walletWithValue->value(),
        ];
    }

    private function walletData(WalletViewModel $viewModel): array
    {
        return [
            'address'     => $viewModel->address(),
            'username'    => $viewModel->username(),
            'hasUsername' => $viewModel->hasUsername(),
            'url'         => $viewModel->model()->url(),
        ];
    }

    private function addressInsights(StatisticsCache $cache): array
    {
        $holdings = $this->getAddressHoldings($cache);

        $uniqueAddresses = $this->getUniqueAddresses($cache);

        return [
            'holdings' => collect($holdings->toArray())
                ->map(fn (int $count, int $grouped): array => [
                    'grouped' => $grouped,
                    'count'   => $count,
                ])
                ->values()
                ->all(),
            'unique' => $this->formatUniqueAddresses($uniqueAddresses),
        ];
    }

    private function formatUniqueAddresses(UniqueAddressesStatistics $uniqueAddresses): array
    {
        $largest = $uniqueAddresses->largest;

        return [
            'genesis'           => $uniqueAddresses->genesis,
            'newest'            => $uniqueAddresses->newest,
            'most_transactions' => $uniqueAddresses->mostTransactions,
            'largest'           => $largest,
        ];
    }

    private function annualInsights(StatisticsCache $cache): array
    {
        $startYear   = Carbon::parse(Network::epoch())->year;
        $currentYear = Carbon::now()->year;
        $yearData    = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $data = $cache->getAnnualData($year);
            if ($data === null) {
                continue;
            }

            $fees = $data['fees'];

            $yearData[] = [
                'year'         => $data['year'],
                'transactions' => $data['transactions'],
                'volume'       => $data['volume'],
                'fees'         => $fees,
                'blocks'       => $data['blocks'],
            ];
        }

        return $yearData;
    }

    private function getMarketDataPrice(StatisticsCache $cache): MarketDataPriceStatistics
    {
        $currency = Settings::currency();

        return MarketDataPriceStatistics::make(
            TimestampedValue::fromArray($cache->getPriceAtl($currency)),
            TimestampedValue::fromArray($cache->getPriceAth($currency)),
            LowHighValue::fromArray($cache->getPriceRangeDaily($currency)),
            LowHighValue::fromArray($cache->getPriceRange52($currency)),
        );
    }

    private function getMarketDataVolume(StatisticsCache $cache): MarketDataVolumeStatistics
    {
        $currency = Settings::currency();

        return MarketDataVolumeStatistics::make(
            (new CryptoDataCache())->getVolume($currency),
            TimestampedValue::fromArray($cache->getVolumeAtl($currency)),
            TimestampedValue::fromArray($cache->getVolumeAth($currency)),
        );
    }

    private function getMarketDataCap(StatisticsCache $cache): MarketDataRecordStatistics
    {
        $currency = Settings::currency();

        return MarketDataRecordStatistics::make(
            MarketCap::get(Network::currency(), $currency),
            TimestampedValue::fromArray($cache->getMarketCapAtl($currency)),
            TimestampedValue::fromArray($cache->getMarketCapAth($currency)),
        );
    }

    private function getAddressHoldings(StatisticsCache $cache): AddressHoldingStatistics
    {
        $holdings = $cache->getAddressHoldings();

        unset($holdings['0']);

        $previousValue = 0;
        $summedValues  = [];
        foreach (array_reverse($holdings) as $values) {
            array_unshift($summedValues, [
                'grouped' => $values['grouped'],
                'count'   => $values['count'] + $previousValue,
            ]);
            $previousValue = $values['count'] + $previousValue;
        }

        return AddressHoldingStatistics::make($summedValues);
    }

    private function getUniqueAddresses(StatisticsCache $cache): UniqueAddressesStatistics
    {
        return new UniqueAddressesStatistics(
            $cache->getGenesisAddress(),
            $cache->getNewestAddress(),
            $cache->getMostTransactions(),
            $cache->getLargestAddress(),
        );
    }

    private function getValidators(): int
    {
        return (new NetworkCache())->getValidatorRegistrationCount();
    }
}
