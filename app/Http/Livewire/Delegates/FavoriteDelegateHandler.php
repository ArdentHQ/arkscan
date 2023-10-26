<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesSettings;
use Livewire\Component;

final class FavoriteDelegateHandler extends Component
{
    use HandlesSettings;

    /** @var mixed */
    protected $listeners = [
        'setFavoriteDelegate'    => 'setDelegate',
        'removeFavoriteDelegate' => 'removeDelegate',
    ];

    public function render(): string
    {
        return '<div></div>';
    }

    public function setDelegate(string $publicKey): void
    {
        $delegates = Settings::favoriteDelegates();

        if ($delegates->has($publicKey)) {
            return;
        }

        $delegates->add($publicKey);

        $this->saveSetting('favoriteDelegates', $delegates->toArray());
    }

    public function removeDelegate(string $publicKey): void
    {
        $delegates = Settings::favoriteDelegates();

        if (! $delegates->has($publicKey)) {
            return;
        }

        $delegates->filter(fn ($delegatePublicKey) => $delegatePublicKey !== $publicKey);

        $this->saveSetting('favoriteDelegates', $delegates->toArray());
    }
}
