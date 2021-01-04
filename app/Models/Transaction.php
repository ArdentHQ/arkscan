<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Concerns\SearchesCaseInsensitive;
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
use App\Models\Scopes\VoteCombinationScope;
use App\Models\Scopes\VoteScope;
use App\Services\BigNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property array|null $asset
 * @property BigNumber $amount
 * @property BigNumber $fee
 * @property int $timestamp
 * @property int $type
 * @property int $type_group
 * @property string $block_id
 * @property string|null $recipient_id
 * @property string $sender_public_key
 * @property int $block_height
 * @property resource|null $vendor_field
 */
final class Transaction extends Model
{
    use HasFactory;
    use SearchesCaseInsensitive;

    /**
     * A list of transaction scopes used for filtering based on type.
     *
     * Exposed through the model to keep its usage consistent across
     * all places that need to filter transactions by their type.
     */
    const TYPE_SCOPES = [
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
        'voteCombination'               => VoteCombinationScope::class,
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['serialized'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount'       => BigInteger::class,
        'asset'        => 'array',
        'fee'          => BigInteger::class,
        'timestamp'    => 'int',
        'type_group'   => 'int',
        'type'         => 'int',
        'block_height' => 'int',
    ];

    /**
     * A transaction belongs to a block.
     *
     * @return BelongsTo
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    /**
     * A transaction belongs to a sender.
     *
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'sender_public_key', 'public_key');
    }

    /**
     * A transaction belongs to a recipient.
     *
     * @return BelongsTo
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'recipient_id', 'address');
    }

    /**
     * Get the current connection name for the model.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return 'explorer';
    }
}
