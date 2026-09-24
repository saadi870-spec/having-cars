<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\CarStatus;
use App\Models\FuelType;
use App\Models\Location;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\TransmissionType;
use App\Models\User;
use App\Services\RentalQuote;
use Illuminate\Database\Seeder;

class RentalCatalogSeeder extends Seeder
{
    /**
     * Seed catalog tables, fleet inventory, and demo rentals.
     */
    public function run(): void
    {
        $makes = collect([
            ['code' => 'toyota', 'name' => 'Toyota', 'sort_order' => 1],
            ['code' => 'honda', 'name' => 'Honda', 'sort_order' => 2],
            ['code' => 'ford', 'name' => 'Ford', 'sort_order' => 3],
            ['code' => 'bmw', 'name' => 'BMW', 'sort_order' => 4],
            ['code' => 'tesla', 'name' => 'Tesla', 'sort_order' => 5],
            ['code' => 'mercedes', 'name' => 'Mercedes-Benz', 'sort_order' => 6],
        ])->mapWithKeys(fn (array $row) => [$row['code'] => CarMake::query()->create($row)]);

        $models = [
            ['make' => 'toyota', 'code' => 'camry', 'name' => 'Camry'],
            ['make' => 'toyota', 'code' => 'rav4', 'name' => 'RAV4'],
            ['make' => 'honda', 'code' => 'civic', 'name' => 'Civic'],
            ['make' => 'honda', 'code' => 'crv', 'name' => 'CR-V'],
            ['make' => 'ford', 'code' => 'explorer', 'name' => 'Explorer'],
            ['make' => 'ford', 'code' => 'mustang', 'name' => 'Mustang'],
            ['make' => 'bmw', 'code' => '3-series', 'name' => '3 Series'],
            ['make' => 'bmw', 'code' => 'x5', 'name' => 'X5'],
            ['make' => 'tesla', 'code' => 'model-3', 'name' => 'Model 3'],
            ['make' => 'tesla', 'code' => 'model-y', 'name' => 'Model Y'],
            ['make' => 'mercedes', 'code' => 'c-class', 'name' => 'C-Class'],
            ['make' => 'mercedes', 'code' => 'glc', 'name' => 'GLC'],
        ];

        $modelRecords = collect($models)->mapWithKeys(function (array $row) use ($makes) {
            $record = CarModel::query()->create([
                'car_make_id' => $makes[$row['make']]->id,
                'code' => $row['code'],
                'name' => $row['name'],
                'sort_order' => 0,
            ]);

            return [$row['make'].'.'.$row['code'] => $record];
        });

        $categories = collect([
            ['code' => 'economy', 'name' => 'Economy', 'description' => 'Efficient city cars', 'sort_order' => 1],
            ['code' => 'compact', 'name' => 'Compact', 'description' => 'Easy to park, still roomy', 'sort_order' => 2],
            ['code' => 'suv', 'name' => 'SUV', 'description' => 'Space for people and bags', 'sort_order' => 3],
            ['code' => 'luxury', 'name' => 'Luxury', 'description' => 'Premium ride and finish', 'sort_order' => 4],
            ['code' => 'electric', 'name' => 'Electric', 'description' => 'Zero-emission driving', 'sort_order' => 5],
            ['code' => 'sport', 'name' => 'Sport', 'description' => 'Weekend machines', 'sort_order' => 6],
        ])->mapWithKeys(fn (array $row) => [$row['code'] => CarCategory::query()->create($row)]);

        $fuels = collect([
            ['code' => 'gasoline', 'name' => 'Gasoline', 'sort_order' => 1],
            ['code' => 'hybrid', 'name' => 'Hybrid', 'sort_order' => 2],
            ['code' => 'electric', 'name' => 'Electric', 'sort_order' => 3],
            ['code' => 'diesel', 'name' => 'Diesel', 'sort_order' => 4],
        ])->mapWithKeys(fn (array $row) => [$row['code'] => FuelType::query()->create($row)]);

        $transmissions = collect([
            ['code' => 'automatic', 'name' => 'Automatic', 'sort_order' => 1],
            ['code' => 'manual', 'name' => 'Manual', 'sort_order' => 2],
        ])->mapWithKeys(fn (array $row) => [$row['code'] => TransmissionType::query()->create($row)]);

        $carStatuses = collect([
            ['code' => 'available', 'name' => 'Available', 'sort_order' => 1],
            ['code' => 'maintenance', 'name' => 'Maintenance', 'sort_order' => 2],
            ['code' => 'retired', 'name' => 'Retired', 'sort_order' => 3],
        ])->mapWithKeys(fn (array $row) => [$row['code'] => CarStatus::query()->create($row)]);

        $rentalStatuses = collect([
            ['code' => 'pending', 'name' => 'Pending', 'sort_order' => 1],
            ['code' => 'confirmed', 'name' => 'Confirmed', 'sort_order' => 2],
            ['code' => 'active', 'name' => 'Active', 'sort_order' => 3],
            ['code' => 'completed', 'name' => 'Completed', 'sort_order' => 4],
            ['code' => 'cancelled', 'name' => 'Cancelled', 'sort_order' => 5],
        ])->mapWithKeys(fn (array $row) => [$row['code'] => RentalStatus::query()->create($row)]);

        $locations = collect([
            ['code' => 'sfo', 'name' => 'San Francisco Airport', 'address' => '780 McDonnell Rd', 'city' => 'San Francisco', 'phone' => '415-555-0101', 'sort_order' => 1],
            ['code' => 'oak', 'name' => 'Oakland Downtown', 'address' => '1200 Broadway', 'city' => 'Oakland', 'phone' => '510-555-0144', 'sort_order' => 2],
            ['code' => 'sjc', 'name' => 'San Jose Airport', 'address' => '1701 Airport Blvd', 'city' => 'San Jose', 'phone' => '408-555-0199', 'sort_order' => 3],
        ])->mapWithKeys(fn (array $row) => [$row['code'] => Location::query()->create($row)]);

        $fleet = [
            ['plate' => 'APX-102', 'model' => 'toyota.camry', 'category' => 'compact', 'fuel' => 'hybrid', 'trans' => 'automatic', 'loc' => 'sfo', 'year' => 2024, 'color' => 'Pearl White', 'seats' => 5, 'rate' => 62, 'image' => 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Quiet hybrid sedan for airport runs and weekday errands.'],
            ['plate' => 'APX-118', 'model' => 'honda.civic', 'category' => 'economy', 'fuel' => 'gasoline', 'trans' => 'automatic', 'loc' => 'oak', 'year' => 2023, 'color' => 'Aegean Blue', 'seats' => 5, 'rate' => 48, 'image' => 'https://images.unsplash.com/photo-1606661953050-3d3890d55d0d?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Light on fuel, easy in city traffic.'],
            ['plate' => 'APX-204', 'model' => 'toyota.rav4', 'category' => 'suv', 'fuel' => 'hybrid', 'trans' => 'automatic', 'loc' => 'sjc', 'year' => 2025, 'color' => 'Midnight Black', 'seats' => 5, 'rate' => 89, 'image' => 'https://images.unsplash.com/photo-1606661956850-76c1c3b5b2d4?auto=format&fit=crop&w=1400&q=80', 'desc' => 'All-weather SUV with room for weekend bags.'],
            ['plate' => 'APX-221', 'model' => 'honda.crv', 'category' => 'suv', 'fuel' => 'gasoline', 'trans' => 'automatic', 'loc' => 'sfo', 'year' => 2024, 'color' => 'Silver', 'seats' => 5, 'rate' => 84, 'image' => 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Family-friendly crossover with a high seating position.'],
            ['plate' => 'APX-310', 'model' => 'ford.explorer', 'category' => 'suv', 'fuel' => 'gasoline', 'trans' => 'automatic', 'loc' => 'oak', 'year' => 2023, 'color' => 'Oxford White', 'seats' => 7, 'rate' => 109, 'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Seven seats for airport groups and road trips.'],
            ['plate' => 'APX-333', 'model' => 'ford.mustang', 'category' => 'sport', 'fuel' => 'gasoline', 'trans' => 'automatic', 'loc' => 'sfo', 'year' => 2024, 'color' => 'Race Red', 'seats' => 4, 'rate' => 139, 'image' => 'https://images.unsplash.com/photo-1584345604476-8d5f13b7d1b5?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Convertible-ready weekend car for the coast.'],
            ['plate' => 'APX-401', 'model' => 'bmw.3-series', 'category' => 'luxury', 'fuel' => 'gasoline', 'trans' => 'automatic', 'loc' => 'sjc', 'year' => 2025, 'color' => 'Mineral Grey', 'seats' => 5, 'rate' => 129, 'image' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Sharp handling and a quiet cabin for client meetings.'],
            ['plate' => 'APX-418', 'model' => 'bmw.x5', 'category' => 'luxury', 'fuel' => 'gasoline', 'trans' => 'automatic', 'loc' => 'sfo', 'year' => 2024, 'color' => 'Alpine White', 'seats' => 5, 'rate' => 169, 'image' => 'https://images.unsplash.com/photo-1617531653332-bd46c24f2068?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Flagship SUV with executive-level comfort.'],
            ['plate' => 'APX-501', 'model' => 'tesla.model-3', 'category' => 'electric', 'fuel' => 'electric', 'trans' => 'automatic', 'loc' => 'oak', 'year' => 2025, 'color' => 'Stealth Grey', 'seats' => 5, 'rate' => 99, 'image' => 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Instant torque, Supercharger-friendly around the Bay.'],
            ['plate' => 'APX-522', 'model' => 'tesla.model-y', 'category' => 'electric', 'fuel' => 'electric', 'trans' => 'automatic', 'loc' => 'sjc', 'year' => 2025, 'color' => 'Pearl White', 'seats' => 5, 'rate' => 119, 'image' => 'https://images.unsplash.com/photo-1617704548623-340376564e68?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Electric SUV with a glass roof and cargo space to spare.'],
            ['plate' => 'APX-610', 'model' => 'mercedes.c-class', 'category' => 'luxury', 'fuel' => 'gasoline', 'trans' => 'automatic', 'loc' => 'sfo', 'year' => 2024, 'color' => 'Obsidian Black', 'seats' => 5, 'rate' => 149, 'image' => 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Quiet luxury sedan for dinners and downtown stays.'],
            ['plate' => 'APX-640', 'model' => 'mercedes.glc', 'category' => 'suv', 'fuel' => 'hybrid', 'trans' => 'automatic', 'loc' => 'oak', 'year' => 2025, 'color' => 'Polar White', 'seats' => 5, 'rate' => 159, 'image' => 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=1400&q=80', 'desc' => 'Polished crossover with a hybrid powertrain.'],
        ];

        $cars = collect($fleet)->mapWithKeys(function (array $row) use ($modelRecords, $categories, $fuels, $transmissions, $carStatuses, $locations) {
            $car = Car::query()->create([
                'license_plate' => $row['plate'],
                'car_model_id' => $modelRecords[$row['model']]->id,
                'car_category_id' => $categories[$row['category']]->id,
                'fuel_type_id' => $fuels[$row['fuel']]->id,
                'transmission_type_id' => $transmissions[$row['trans']]->id,
                'car_status_id' => $carStatuses['available']->id,
                'location_id' => $locations[$row['loc']]->id,
                'year' => $row['year'],
                'color' => $row['color'],
                'seats' => $row['seats'],
                'doors' => $row['seats'] >= 7 ? 4 : 4,
                'daily_rate' => $row['rate'],
                'image_url' => $row['image'],
                'description' => $row['desc'],
            ]);

            return [$row['plate'] => $car];
        });

        $client = User::query()->where('email', 'test@example.com')->first();

        if ($client) {
            $startsOn = now()->addDays(10)->toDateString();
            $endsOn = now()->addDays(14)->toDateString();
            $car = $cars['APX-102'];
            $quote = RentalQuote::fromDates((string) $car->daily_rate, $startsOn, $endsOn);

            Rental::query()->create([
                'user_id' => $client->id,
                'car_id' => $car->id,
                'pickup_location_id' => $car->location_id,
                'dropoff_location_id' => $car->location_id,
                'rental_status_id' => $rentalStatuses['confirmed']->id,
                'starts_on' => $startsOn,
                'ends_on' => $endsOn,
                'days' => $quote->days,
                'daily_rate' => $quote->dailyRate,
                'total_amount' => $quote->total,
                'notes' => 'Airport pickup, Terminal 2.',
            ]);
        }

        User::query()->where('email', 'admin@apexdrive.test')->update([
            'role' => UserRole::Admin,
        ]);
    }
}
