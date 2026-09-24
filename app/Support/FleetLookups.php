<?php

namespace App\Support;

use App\Models\CarCategory;
use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\CarStatus;
use App\Models\FuelType;
use App\Models\Location;
use App\Models\RentalStatus;
use App\Models\TransmissionType;

final class FleetLookups
{
    /**
     * @return array<string, mixed>
     */
    public static function searchFilters(): array
    {
        return [
            'locations' => Location::query()->active()->get(['id', 'name', 'city']),
            'categories' => CarCategory::query()->active()->get(['id', 'name', 'code']),
            'makes' => CarMake::query()->active()->get(['id', 'name']),
            'transmissions' => TransmissionType::query()->active()->get(['id', 'name']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function adminForms(): array
    {
        return [
            'makes' => CarMake::query()->active()->get(['id', 'name']),
            'models' => CarModel::query()->with('make:id,name')->active()->get(['id', 'name', 'car_make_id']),
            'categories' => CarCategory::query()->active()->get(['id', 'name']),
            'fuelTypes' => FuelType::query()->active()->get(['id', 'name']),
            'transmissions' => TransmissionType::query()->active()->get(['id', 'name']),
            'statuses' => CarStatus::query()->active()->get(['id', 'name', 'code']),
            'locations' => Location::query()->active()->get(['id', 'name', 'city']),
            'rentalStatuses' => RentalStatus::query()->active()->get(['id', 'name', 'code']),
        ];
    }
}
