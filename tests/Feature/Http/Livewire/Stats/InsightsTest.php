<?php

declare(strict_types=1);

use App\Contracts\Network as NetworkContract;
use App\Http\Livewire\Stats\Insights;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\Feature\Http\Livewire\__stubs\NetworkStub;

it('should render transaction details', function (): void {
    Transaction::factory(12)->delegateRegistration()->create();
    Transaction::factory(13)->delegateResignation()->create();
    Transaction::factory(14)->transfer()->create();
    Transaction::factory(15)->vote()->create();
    Transaction::factory(16)->unvote()->create();
    Transaction::factory(17)->voteCombination()->create();
    Transaction::factory(18)->multipayment()->create();

    Artisan::call('explorer:cache-transactions');

    Livewire::test(Insights::class)
        ->assertViewHas('transactionDetails', [
            'transfer'              => 14,
            'multipayment'          => 18,
            'vote'                  => 15,
            'unvote'                => 16,
            'switch_vote'           => 17,
            'delegate_registration' => 12,
            'delegate_resignation'  => 13,
        ])
        ->assertSeeInOrder([
            trans('pages.statistics.insights.transactions.header.transfer'),
            '14',
            trans('pages.statistics.insights.transactions.header.multipayment'),
            '18',
            trans('pages.statistics.insights.transactions.header.vote'),
            '15',
            trans('pages.statistics.insights.transactions.header.unvote'),
            '16',
            trans('pages.statistics.insights.transactions.header.switch_vote'),
            '17',
            trans('pages.statistics.insights.transactions.header.delegate_registration'),
            '12',
            trans('pages.statistics.insights.transactions.header.delegate_resignation'),
            '13',
        ]);
});

it('should render daily average', function (): void {
    $networkStub = new NetworkStub(true, Carbon::now()->subDay(2));
    app()->singleton(NetworkContract::class, fn () => $networkStub);

    Transaction::factory(2)->delegateRegistration()->create([
        'amount' => 0,
        'fee' => 9 * 1e8,
    ]);
    Transaction::factory(3)->transfer()->create([
        'amount' => 2000 * 1e8,
        'fee' => 10 * 1e8,
    ]);
    Transaction::factory(4)->multipayment()->create([
        'amount' => 0,
        'fee' => 11 * 1e8,
        'asset' => [
            'payments' => [
                [
                    'amount' => 3000 * 1e8,
                ],
            ],
        ],
    ]);

    expect(Transaction::count())->toBe(9);

    $transactionCount = (int) round(9 / 2);
    $totalAmount = (int) round(((4 * 3000) + (3 * 2000)) / 2);
    $totalFees = (int) round(((9 * 2) + (10 * 3) + (11 * 4)) / 2);

    Artisan::call('explorer:cache-transactions');

    Livewire::test(Insights::class)
        ->assertViewHas('transactionAverages', [
            'transactions'       => $transactionCount,
            'transaction_volume' => number_format($totalAmount).' DARK',
            'transaction_fees'   => number_format($totalFees).' DARK',
        ])
        ->assertSeeInOrder([
            trans('pages.statistics.insights.transactions.header.transfer'),
            trans('pages.statistics.insights.transactions.header.multipayment'),
            trans('pages.statistics.insights.transactions.header.vote'),
            trans('pages.statistics.insights.transactions.header.unvote'),
            trans('pages.statistics.insights.transactions.header.switch_vote'),
            trans('pages.statistics.insights.transactions.header.delegate_registration'),
            trans('pages.statistics.insights.transactions.header.delegate_resignation'),
            trans('pages.statistics.insights.transactions.header.transactions'),
            $transactionCount,
            trans('pages.statistics.insights.transactions.header.transaction_volume'),
            number_format($totalAmount).' DARK',
            trans('pages.statistics.insights.transactions.header.transaction_fees'),
            number_format($totalFees).' DARK',
        ]);
});
