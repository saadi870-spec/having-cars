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
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class CatalogRegistry
{
    /**
     * @return array<string, array{
     *     model: class-string<Model>,
     *     label: string,
     *     fields: list<string>
     * }>
     */
    public static function all(): array
    {
        return [
            'makes' => [
                'model' => CarMake::class,
                'label' => 'Makes',
                'fields' => ['code', 'name'],
            ],
            'models' => [
                'model' => CarModel::class,
                'label' => 'Models',
                'fields' => ['code', 'name', 'car_make_id'],
            ],
            'categories' => [
                'model' => CarCategory::class,
                'label' => 'Categories',
                'fields' => ['code', 'name', 'description'],
            ],
            'fuel-types' => [
                'model' => FuelType::class,
                'label' => 'Fuel types',
                'fields' => ['code', 'name'],
            ],
            'transmissions' => [
                'model' => TransmissionType::class,
                'label' => 'Transmissions',
                'fields' => ['code', 'name'],
            ],
            'car-statuses' => [
                'model' => CarStatus::class,
                'label' => 'Car statuses',
                'fields' => ['code', 'name'],
            ],
            'rental-statuses' => [
                'model' => RentalStatus::class,
                'label' => 'Rental statuses',
                'fields' => ['code', 'name'],
            ],
            'locations' => [
                'model' => Location::class,
                'label' => 'Locations',
                'fields' => ['code', 'name', 'address', 'city', 'phone'],
            ],
        ];
    }

    /**
     * @return array{
     *     model: class-string<Model>,
     *     label: string,
     *     fields: list<string>
     * }
     */
    public static function get(string $type): array
    {
        $all = self::all();

        if (! isset($all[$type])) {
            throw new InvalidArgumentException("Unknown catalog type [{$type}].");
        }

        return $all[$type];
    }
}
