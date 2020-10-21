<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Scopes\BusinessEntityRegistrationScope;
use App\Models\Scopes\BusinessEntityResignationScope;
use App\Models\Scopes\BusinessEntityUpdateScope;
use App\Models\Scopes\DelegateEntityRegistrationScope;
use App\Models\Scopes\DelegateEntityResignationScope;
use App\Models\Scopes\DelegateEntityUpdateScope;
use App\Models\Scopes\DelegateRegistrationScope;
use App\Models\Scopes\DelegateResignationScope;
use App\Models\Scopes\EntityRegistrationScope;
use App\Models\Scopes\EntityResignationScope;
use App\Models\Scopes\EntityUpdateScope;
use App\Models\Scopes\IpfsScope;
use App\Models\Scopes\LegacyBridgechainRegistrationScope;
use App\Models\Scopes\LegacyBridgechainResignationScope;
use App\Models\Scopes\LegacyBridgechainUpdateScope;
use App\Models\Scopes\LegacyBusinessRegistrationScope;
use App\Models\Scopes\LegacyBusinessResignationScope;
use App\Models\Scopes\LegacyBusinessUpdateScope;
use App\Models\Scopes\ModuleEntityRegistrationScope;
use App\Models\Scopes\ModuleEntityResignationScope;
use App\Models\Scopes\ModuleEntityUpdateScope;
use App\Models\Scopes\MultiPaymentScope;
use App\Models\Scopes\MultiSignatureScope;
use App\Models\Scopes\PluginEntityRegistrationScope;
use App\Models\Scopes\PluginEntityResignationScope;
use App\Models\Scopes\PluginEntityUpdateScope;
use App\Models\Scopes\ProductEntityRegistrationScope;
use App\Models\Scopes\ProductEntityResignationScope;
use App\Models\Scopes\ProductEntityUpdateScope;
use App\Models\Scopes\SecondSignatureScope;
use App\Models\Scopes\TimelockClaimScope;
use App\Models\Scopes\TimelockRefundScope;
use App\Models\Scopes\TimelockScope;
use App\Models\Scopes\TransferScope;
use App\Models\Scopes\VoteScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\View\View;
use Livewire\Component;

final class TransactionTable extends Component
{
    use HasPagination;

    public bool $viewMore = false;

    public function mount(bool $viewMore = false): void
    {
        $this->viewMore = $viewMore;
    }

    public array $state = [
        'type' => 'all',
    ];

    private array $scopes = [
        'businessEntityRegistration'    => BusinessEntityRegistrationScope::class,
        'businessEntityResignation'     => BusinessEntityResignationScope::class,
        'businessEntityUpdate'          => BusinessEntityUpdateScope::class,
        'delegateEntityRegistration'    => DelegateEntityRegistrationScope::class,
        'delegateEntityResignation'     => DelegateEntityResignationScope::class,
        'delegateEntityUpdate'          => DelegateEntityUpdateScope::class,
        'delegateRegistration'          => DelegateRegistrationScope::class,
        'delegateResignation'           => DelegateResignationScope::class,
        'entityRegistration'            => EntityRegistrationScope::class,
        'entityResignation'             => EntityResignationScope::class,
        'entityUpdate'                  => EntityUpdateScope::class,
        'ipfs'                          => IpfsScope::class,
        'legacyBridgechainRegistration' => LegacyBridgechainRegistrationScope::class,
        'legacyBridgechainResignation'  => LegacyBridgechainResignationScope::class,
        'legacyBridgechainUpdate'       => LegacyBridgechainUpdateScope::class,
        'legacyBusinessRegistration'    => LegacyBusinessRegistrationScope::class,
        'legacyBusinessResignation'     => LegacyBusinessResignationScope::class,
        'legacyBusinessUpdate'          => LegacyBusinessUpdateScope::class,
        'moduleEntityRegistration'      => ModuleEntityRegistrationScope::class,
        'moduleEntityResignation'       => ModuleEntityResignationScope::class,
        'moduleEntityUpdate'            => ModuleEntityUpdateScope::class,
        'multiPayment'                  => MultiPaymentScope::class,
        'multiSignature'                => MultiSignatureScope::class,
        'pluginEntityRegistration'      => PluginEntityRegistrationScope::class,
        'pluginEntityResignation'       => PluginEntityResignationScope::class,
        'pluginEntityUpdate'            => PluginEntityUpdateScope::class,
        'productEntityRegistration'     => ProductEntityRegistrationScope::class,
        'productEntityResignation'      => ProductEntityResignationScope::class,
        'productEntityUpdate'           => ProductEntityUpdateScope::class,
        'secondSignature'               => SecondSignatureScope::class,
        'timelockClaim'                 => TimelockClaimScope::class,
        'timelockRefund'                => TimelockRefundScope::class,
        'timelock'                      => TimelockScope::class,
        'transfer'                      => TransferScope::class,
        'vote'                          => VoteScope::class,
    ];

    public function render(): View
    {
        if ($this->state['type'] !== 'all') {
            $scopeClass = $this->scopes[$this->state['type']];

            /* @var \Illuminate\Database\Eloquent\Model */
            Transaction::addGlobalScope(new $scopeClass());
        }

        return view('livewire.transaction-table', [
            'transactions' => ViewModelFactory::paginate(Transaction::latestByTimestamp()->paginate()),
        ]);
    }
}
