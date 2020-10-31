<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Models\Transaction;
use Illuminate\Support\Facades\View;

final class TransactionTypeComponent
{
    private TransactionTypeSlug $slug;

    public function __construct(Transaction $transaction)
    {
        $this->slug = new TransactionTypeSlug($transaction);
    }

    public function header(): string
    {
        return $this->getView('page-headers', 'transaction');
    }

    public function details(): string
    {
        return $this->getView('transaction', 'details');
    }

    public function extension(): string
    {
        return $this->getView('transaction', 'extension');
    }

    private function getView(string $group, string $type): string
    {
        $views = [
            sprintf("$group.$type.".$this->slug->exact()),
            sprintf("$group.$type.".$this->slug->generic()),
        ];

        foreach ($views as $view) {
            if (View::exists("components.$view")) {
                return $view;
            }
        }

        return "$group.$type.fallback";
    }
}
