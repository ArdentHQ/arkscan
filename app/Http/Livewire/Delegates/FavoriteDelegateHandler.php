<?php

namespace App\Http\Livewire\Delegates;

use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesSettings;
use Livewire\Component;

class FavoriteDelegateHandler extends Component
{
    use HandlesSettings;

    protected $listeners = [
        'setFavoriteDelegate' => 'setDelegate',
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

        dd($delegates);

        $this->saveSetting('favoriteDelegates', $delegates->toArray());
    }
}
