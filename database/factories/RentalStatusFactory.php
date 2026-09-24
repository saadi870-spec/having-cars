<?php

namespace Database\Factories;

use App\Models\RentalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RentalStatus>
 */
class RentalStatusFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'code' => Str::slug($name),
            'name' => ucfirst($name),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'code' => 'pending',
            'name' => 'Pending',
        ]);
    }
}
