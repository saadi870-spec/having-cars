<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarModel;
use App\Models\CarStatus;
use App\Models\FuelType;
use App\Models\Location;
use App\Models\TransmissionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'license_plate' => strtoupper(fake()->unique()->bothify('??####')),
            'car_model_id' => CarModel::factory(),
            'car_category_id' => CarCategory::factory(),
            'fuel_type_id' => FuelType::factory(),
            'transmission_type_id' => TransmissionType::factory(),
            'car_status_id' => CarStatus::factory()->available(),
            'location_id' => Location::factory(),
            'year' => fake()->numberBetween(2019, 2026),
            'color' => fake()->safeColorName(),
            'seats' => 5,
            'doors' => 4,
            'daily_rate' => fake()->randomFloat(2, 39, 189),
            'image_url' => 'https://images.unsplash.com/photo-1494905998402-395d579af36f?auto=format&fit=crop&w=1200&q=80',
            'description' => fake()->sentence(12),
        ];
    }
}
