<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals;

use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

final class ExportTransactions extends Component
{
    use HasModal;

    /**
     * @var string[][]
     */
    protected $rules = [
        // 'name'    => ['required', 'string', 'max:50'],
        // 'website' => ['required', 'url'],
        // 'pairs'   => ['required', 'string', 'max:100'],
        // 'message' => ['nullable', 'string', 'max:500'],
    ];

    public function submit(): void
    {
        $this->validate();
    }

    public function render(): View
    {
        return view('livewire.modals.export-transactions');
    }

    public function cancel(): void
    {
        $this->closeModal();
        $this->resetForm();
    }

    public function getCanSubmitProperty(): bool
    {
        $validator = Validator::make($this->data(), $this->rules);

        return $validator->fails();
    }

    private function data(): array
    {
        return [
            // 'name'    => $this->name,
            // 'website' => $this->website,
            // 'pairs'   => $this->pairs,
            // 'message' => $this->message,
        ];
    }

    private function resetForm(): void
    {
        // $this->name     = null;
        // $this->website  = null;
        // $this->pairs    = null;
        // $this->message  = null;

        $this->resetErrorBag();
    }
}
