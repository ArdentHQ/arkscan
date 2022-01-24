<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\EntityNotFoundInterface;
use ARKEcosystem\Foundation\UserInterface\Components\TruncateMiddle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\HtmlString;

final class TransactionNotFoundException extends ModelNotFoundException implements EntityNotFoundInterface
{
    public function getCustomMessage(): HtmlString
    {
        $truncateMiddle = new TruncateMiddle();

        $transactionID = $this->getIds();

        $truncatedTransactionID = $truncateMiddle->render()([
            'slot'       => $transactionID,
            'attributes' => ['length' => 17],
        ]);

        $message = trans('errors.transaction_not_found', ['transactionID' => $truncatedTransactionID]);

        return new HtmlString($message);
    }
}
