<?php

namespace Database\Factories;

use App\Models\CarMake;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CarMake>
 */
class CarMakeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'code' => Str::slug($name),
            'name' => $name,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
