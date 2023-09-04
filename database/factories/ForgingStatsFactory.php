<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ForgingStats;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ForgingStatsFactory extends Factory
{
    protected $model = ForgingStats::class;

    public function definition()
    {
        return [
            'timestamp'     => $this->faker->dateTimeBetween('-1 year', 'now')->getTimestamp(),
            'public_key'    => fn () => Wallet::factory()->create()->public_key,
            'forged'        => $this->faker->boolean(),
            'missed_height' => $this->faker->numberBetween(1, 10000),
        ];
    }
}
