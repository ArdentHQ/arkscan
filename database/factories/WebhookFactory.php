<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

final class WebhookFactory extends Factory
{
    protected $model = Webhook::class;

    public function definition()
    {
        return [
            'id'    => $this->faker->uuid(),
            'token' => $this->faker->text(),
            'event' => $this->faker->text(64),
            'host'  => $this->faker->ipv4(),
            'port'  => $this->faker->numberBetween(1, 65535),
        ];
    }
}
