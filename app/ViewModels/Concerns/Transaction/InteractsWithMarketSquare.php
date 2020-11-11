<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Str;
use RuntimeException;

trait InteractsWithMarketSquare
{
    public function marketSquareLink(): string
    {
        if ($this->isBusinessEntityRegistration()) {
            $type = 'businesses';
        } elseif ($this->isProductEntityRegistration()) {
            $type = 'products';
        } elseif ($this->isPluginEntityRegistration()) {
            $type = 'plugins';
        } elseif ($this->isModuleEntityRegistration()) {
            $type = 'modules';
        } else {
            $type = 'delegates';
        }

        return sprintf('https://marketsquare.io/%s/%s', $type, $this->marketSquareSlug());
    }

    private function marketSquareSlug(): string
    {
        $name = $this->isEntityRegistration() ? $this->entityName() : $this->delegateUsername();

        if (is_null($name)) {
            throw new RuntimeException('Transaction has no name but it should.');
        }

        return Str::slug($name);
    }
}
