<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\EntityNotFoundInterface;
use ARKEcosystem\Foundation\UserInterface\Components\TruncateMiddle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\HtmlString;

final class WalletNotFoundException extends ModelNotFoundException implements EntityNotFoundInterface
{
    public function getCustomMessage(): HtmlString
    {
        $truncateMiddle = new TruncateMiddle();

        $walletID = collect($this->getIds());

        /** @var string $truncatedWalletID */
        $truncatedWalletID = $truncateMiddle->render()([
            'slot'       => $walletID->first(),
            'attributes' => ['length' => 17],
        ]);

        $message = trans('errors.wallet_not_found', [
            'truncatedWalletID' => $truncatedWalletID,
            'walletID'          => $walletID->first(),
        ]);

        return new HtmlString($message);
    }
}
