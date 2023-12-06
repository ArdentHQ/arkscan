<?php

declare(strict_types=1);

use App\Http\Livewire\Stats\Insights;
use App\Models\Transaction;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

it('should render the component', function (): void {
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
