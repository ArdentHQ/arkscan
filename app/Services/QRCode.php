<?php

declare(strict_types=1);

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

final class QRCode
{
    public static function generate(string $value): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(160),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString($value);
    }
}
