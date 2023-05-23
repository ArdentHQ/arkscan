<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals;

use App\Mail\ExchangeFormSubmitted;
use ARKEcosystem\Foundation\UserInterface\Components\ThrottledComponent;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

final class SubmitExchange extends ThrottledComponent
{
    use HasModal;

    public ?string $name = null;

    public ?string $website = null;

    public ?string $pairs = null;

    public ?string $message = null;

    /**
     * @var string[][]
     */
    protected $rules = [
        'name'    => ['required', 'string', 'max:50'],
        'website' => ['required', 'url'],
        'pairs'   => ['required', 'string', 'max:100'],
        'message' => ['nullable', 'string', 'max:500'],
    ];

    public function submit(): void
    {
        $this->validate();

        if ($this->isThrottled()) {
            return;
        }

        Mail::send(new ExchangeFormSubmitted($this->data()));

        $this->resetForm();
        $this->closeModal();
        $this->toast(trans('pages.exchanges.submit-modal.success_toast'));
    }

    public function render(): View
    {
        return view('livewire.modals.submit-exchange');
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
        return config('explorer.throttle.exchange_submitted.max_attempts', 3);
    }

    protected function getThrottlingTime(): int
    {
        return config('explorer.throttle.exchange_submitted.duration', 3600);
    }

    protected function getThrottlingKey(): string
    {
        return 'exchange-submitted-throttle';
    }

    protected function getThrottlingMessage(string $availableIn): string
    {
        return trans('pages.exchanges.submit-modal.throttle_error', ['time' => $availableIn]);
    }

    private function data(): array
    {
        return [
            'name'    => $this->name,
            'website' => $this->website,
            'pairs'   => $this->pairs,
            'message' => $this->message,
        ];
    }

    private function resetForm(): void
    {
        $this->name    = null;
        $this->website = null;
        $this->pairs    = null;
        $this->message = null;

        $this->resetErrorBag();
    }
}
