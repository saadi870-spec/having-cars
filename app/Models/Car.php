<?php

namespace App\Models;

use App\Services\RentalQuote;
use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $license_plate
 * @property int $car_model_id
 * @property int $car_category_id
 * @property int $fuel_type_id
 * @property int $transmission_type_id
 * @property int $car_status_id
 * @property int $location_id
 * @property int $year
 * @property string $color
 * @property int $seats
 * @property int $doors
 * @property string $daily_rate
 * @property string|null $image_url
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CarModel $model
 * @property-read CarCategory $category
 * @property-read FuelType $fuelType
 * @property-read TransmissionType $transmission
 * @property-read CarStatus $status
 * @property-read Location $location
 */
#[Fillable([
    'license_plate',
    'car_model_id',
    'car_category_id',
    'fuel_type_id',
    'transmission_type_id',
    'car_status_id',
    'location_id',
    'year',
    'color',
    'seats',
    'doors',
    'daily_rate',
    'image_url',
    'description',
])]
class Car extends Model
{
    /** @use HasFactory<CarFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'seats' => 'integer',
            'doors' => 'integer',
            'daily_rate' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<CarModel, $this>
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    /**
     * @return BelongsTo<CarCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class, 'car_category_id');
    }

    /**
     * @return BelongsTo<FuelType, $this>
     */
    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    /**
     * @return BelongsTo<TransmissionType, $this>
     */
    public function transmission(): BelongsTo
    {
        return $this->belongsTo(TransmissionType::class, 'transmission_type_id');
    }

    /**
     * @return BelongsTo<CarStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(CarStatus::class, 'car_status_id');
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return HasMany<Rental, $this>
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function displayName(): string
    {
        $this->loadMissing('model.make');

        return trim(($this->model->make->name ?? '').' '.($this->model->name ?? ''));
    }

    public function isListed(): bool
    {
        $this->loadMissing('status');

        return $this->status->code === 'available';
    }

    public function isAvailableBetween(string $startsOn, string $endsOn, ?int $ignoreRentalId = null): bool
    {
        if (! $this->isListed()) {
            return false;
        }

        return ! $this->rentals()
            ->blockingDates()
            ->when($ignoreRentalId, fn (Builder $query) => $query->where('id', '!=', $ignoreRentalId))
            ->overlapping($startsOn, $endsOn)
            ->exists();
    }

    /**
     * @param  Builder<Car>  $query
     * @return Builder<Car>
     */
    public function scopeListed(Builder $query): Builder
    {
        return $query->whereHas('status', fn (Builder $status) => $status->where('code', 'available'));
    }

    /**
     * @param  Builder<Car>  $query
     * @return Builder<Car>
     */
    public function scopeAvailableBetween(Builder $query, string $startsOn, string $endsOn): Builder
    {
        return $query->listed()
            ->whereDoesntHave('rentals', function (Builder $rentals) use ($startsOn, $endsOn): void {
                /** @var Builder<Rental> $rentals */
                $rentals->blockingDates()->overlapping($startsOn, $endsOn);
            });
    }

    /**
     * @return array<string, mixed>
     */
    public function toFleetCard(?string $startsOn = null, ?string $endsOn = null): array
    {
        $this->loadMissing(['model.make', 'category', 'fuelType', 'transmission', 'status', 'location']);

        $quote = null;

        if ($startsOn && $endsOn) {
            $quote = RentalQuote::fromDates((string) $this->daily_rate, $startsOn, $endsOn);
        }

        return [
            'id' => $this->id,
            'name' => $this->displayName(),
            'year' => $this->year,
            'color' => $this->color,
            'seats' => $this->seats,
            'doors' => $this->doors,
            'daily_rate' => (string) $this->daily_rate,
            'image_url' => $this->image_url,
            'description' => $this->description,
            'license_plate' => $this->license_plate,
            'make' => $this->model->make->name,
            'model' => $this->model->name,
            'category' => $this->category->name,
            'fuel' => $this->fuelType->name,
            'transmission' => $this->transmission->name,
            'status' => $this->status->name,
            'status_code' => $this->status->code,
            'location' => [
                'id' => $this->location->id,
                'name' => $this->location->name,
                'city' => $this->location->city,
            ],
            'quote' => $quote ? [
                'days' => $quote->days,
                'total' => $quote->total,
            ] : null,
        ];
    }
}
