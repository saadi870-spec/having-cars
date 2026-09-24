<?php

namespace Database\Factories;

use App\Models\FuelType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FuelType>
 */
class FuelTypeFactory extends Factory
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
}
