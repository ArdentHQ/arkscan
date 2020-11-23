<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\BigInteger;
use App\Models\Concerns\SearchesCaseInsensitive;
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
        'businessEntityRegistration'    => Scopes\BusinessEntityRegistrationScope::class,
        'businessEntityResignation'     => Scopes\BusinessEntityResignationScope::class,
        'businessEntityUpdate'          => Scopes\BusinessEntityUpdateScope::class,
        'delegateEntityRegistration'    => Scopes\DelegateEntityRegistrationScope::class,
        'delegateEntityResignation'     => Scopes\DelegateEntityResignationScope::class,
        'delegateEntityUpdate'          => Scopes\DelegateEntityUpdateScope::class,
        'delegateRegistration'          => Scopes\DelegateRegistrationScope::class,
        'delegateResignation'           => Scopes\DelegateResignationScope::class,
        'entityRegistration'            => Scopes\EntityRegistrationScope::class,
        'entityResignation'             => Scopes\EntityResignationScope::class,
        'entityUpdate'                  => Scopes\EntityUpdateScope::class,
        'ipfs'                          => Scopes\IpfsScope::class,
        'legacyBridgechainRegistration' => Scopes\LegacyBridgechainRegistrationScope::class,
        'legacyBridgechainResignation'  => Scopes\LegacyBridgechainResignationScope::class,
        'legacyBridgechainUpdate'       => Scopes\LegacyBridgechainUpdateScope::class,
        'legacyBusinessRegistration'    => Scopes\LegacyBusinessRegistrationScope::class,
        'legacyBusinessResignation'     => Scopes\LegacyBusinessResignationScope::class,
        'legacyBusinessUpdate'          => Scopes\LegacyBusinessUpdateScope::class,
        'moduleEntityRegistration'      => Scopes\ModuleEntityRegistrationScope::class,
        'moduleEntityResignation'       => Scopes\ModuleEntityResignationScope::class,
        'moduleEntityUpdate'            => Scopes\ModuleEntityUpdateScope::class,
        'multiPayment'                  => Scopes\MultiPaymentScope::class,
        'multiSignature'                => Scopes\MultiSignatureScope::class,
        'pluginEntityRegistration'      => Scopes\PluginEntityRegistrationScope::class,
        'pluginEntityResignation'       => Scopes\PluginEntityResignationScope::class,
        'pluginEntityUpdate'            => Scopes\PluginEntityUpdateScope::class,
        'productEntityRegistration'     => Scopes\ProductEntityRegistrationScope::class,
        'productEntityResignation'      => Scopes\ProductEntityResignationScope::class,
        'productEntityUpdate'           => Scopes\ProductEntityUpdateScope::class,
        'secondSignature'               => Scopes\SecondSignatureScope::class,
        'timelockClaim'                 => Scopes\TimelockClaimScope::class,
        'timelockRefund'                => Scopes\TimelockRefundScope::class,
        'timelock'                      => Scopes\TimelockScope::class,
        'transfer'                      => Scopes\TransferScope::class,
        'vote'                          => Scopes\VoteScope::class,
        'voteCombination'               => Scopes\VoteCombinationScope::class,
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    /**
     * A transaction belongs to a sender.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'sender_public_key', 'public_key');
    }

    /**
     * A transaction belongs to a recipient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
