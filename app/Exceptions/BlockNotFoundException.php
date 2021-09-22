<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\EntityNotFoundInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\HtmlString;
use Konceiver\BladeComponents\View\Components\TruncateMiddle;

final class BlockNotFoundException extends ModelNotFoundException implements EntityNotFoundInterface
{
    public function getCustomMessage(): HtmlString
    {
        $truncateMiddle = new TruncateMiddle();

        [$blockID] = $this->getIds();

        $truncatedBlockID = $truncateMiddle->render()([
            'slot'       => $blockID,
            'attributes' => ['length' => 17],
        ]);

        $message = trans('errors.block_not_found', ['blockID' => $truncatedBlockID]);

        return new HtmlString($message);
    }
}
