<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [];
    }
}
