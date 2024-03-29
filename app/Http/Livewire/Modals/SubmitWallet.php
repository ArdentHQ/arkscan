<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals;

use App\Mail\WalletFormSubmitted;
use ARKEcosystem\Foundation\UserInterface\Components\ThrottledComponent;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

final class SubmitWallet extends ThrottledComponent
{
    use HasModal;

    public ?string $name = null;

    public ?string $website = null;

    public ?string $message = null;

    /**
     * @var string[][]
     */
    protected $rules = [
        'name'    => ['required', 'string', 'max:50'],
        'website' => ['required', 'url'],
        'message' => ['nullable', 'string', 'max:500'],
    ];

    public function submit(): void
    {
        $this->validate();

        if ($this->isThrottled()) {
            return;
        }

        Mail::send(new WalletFormSubmitted($this->data()));

        $this->resetForm();
        $this->closeModal();
        $this->toast(trans('pages.compatible-wallets.submit-modal.success_toast'));
    }

    public function render(): View
    {
        return view('livewire.modals.submit-wallet');
    }

    public function cancel(): void
    {
        $this->closeModal();
        $this->resetForm();
    }

    public function updated(string $key): void
    {
        $this->validateOnly($key);
    }

    public function getCanSubmitProperty(): bool
    {
        $validator = Validator::make($this->data(), $this->rules);

        return $validator->fails();
    }

    protected function getThrottlingMaxAttempts(): int
    {
        return config('arkscan.throttle.wallet_submitted.max_attempts', 3);
    }

    protected function getThrottlingTime(): int
    {
        return config('arkscan.throttle.wallet_submitted.duration', 3600);
    }

    protected function getThrottlingKey(): string
    {
        return 'wallet-submitted-throttle';
    }

    protected function getThrottlingMessage(string $availableIn): string
    {
        return trans('pages.compatible-wallets.submit-modal.throttle_error', ['time' => $availableIn]);
    }

    private function data(): array
    {
        return [
            'name'    => $this->name,
            'website' => $this->website,
            'message' => $this->message,
        ];
    }

    private function resetForm(): void
    {
        $this->name    = null;
        $this->website = null;
        $this->message = null;

        $this->resetErrorBag();
    }
}
