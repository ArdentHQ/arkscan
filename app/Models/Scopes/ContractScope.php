<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Facades\Network;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class ContractScope implements Scope
{
    private ?string $recipient;

    public function __construct(private string $contract, ?string $recipient = null)
    {
        if ($recipient === null) {
            $recipient = Network::knownContract('consensus');
        }

        $this->recipient = $recipient;
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('recipient_address', $this->recipient)
            ->whereRaw('SUBSTRING(encode(data, \'hex\'), 1, 8) = ?', [$this->contract]);
    }
}
