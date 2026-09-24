<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Location;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\User;
use App\Services\RentalQuote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rental>
 */
class RentalFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsOn = now()->addDays(3)->toDateString();
        $endsOn = now()->addDays(6)->toDateString();
        $dailyRate = '89.00';
        $quote = RentalQuote::fromDates($dailyRate, $startsOn, $endsOn);
        $location = Location::factory();

        return [
            'user_id' => User::factory(),
            'car_id' => Car::factory(),
            'pickup_location_id' => $location,
            'dropoff_location_id' => $location,
            'rental_status_id' => RentalStatus::factory()->pending(),
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'days' => $quote->days,
            'daily_rate' => $quote->dailyRate,
            'total_amount' => $quote->total,
            'notes' => null,
        ];
    }
}
