<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->unique()->city();

        return [
            'code' => Str::slug($city),
            'name' => $city.' Airport',
            'address' => fake()->streetAddress(),
            'city' => $city,
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
