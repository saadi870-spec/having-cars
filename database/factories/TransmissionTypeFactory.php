<?php

namespace Database\Factories;

use App\Models\TransmissionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TransmissionType>
 */
class TransmissionTypeFactory extends Factory
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
