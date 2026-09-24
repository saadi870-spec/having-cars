<?php

namespace Database\Factories;

use App\Models\CarMake;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CarModel>
 */
class CarModelFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'car_make_id' => CarMake::factory(),
            'code' => Str::slug($name),
            'name' => ucfirst($name),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
