<?php

namespace App\Models;

use Database\Factories\RentalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $car_id
 * @property int $pickup_location_id
 * @property int $dropoff_location_id
 * @property int $rental_status_id
 * @property Carbon $starts_on
 * @property Carbon $ends_on
 * @property int $days
 * @property string $daily_rate
 * @property string $total_amount
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Car $car
 * @property-read Location $pickupLocation
 * @property-read Location $dropoffLocation
 * @property-read RentalStatus $status
 */
#[Fillable([
    'user_id',
    'car_id',
    'pickup_location_id',
    'dropoff_location_id',
    'rental_status_id',
    'starts_on',
    'ends_on',
    'days',
    'daily_rate',
    'total_amount',
    'notes',
])]
class Rental extends Model
{
    /** @use HasFactory<RentalFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'days' => 'integer',
            'daily_rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }

    /**
     * @return BelongsTo<RentalStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(RentalStatus::class, 'rental_status_id');
    }

    public function isCancellableByClient(): bool
    {
        $this->loadMissing('status');

        return in_array($this->status->code, ['pending', 'confirmed'], true);
    }

    /**
     * @param  Builder<Rental>  $query
     * @return Builder<Rental>
     */
    public function scopeBlockingDates(Builder $query): Builder
    {
        return $query->whereHas(
            'status',
            fn (Builder $status) => $status->whereIn('code', ['pending', 'confirmed', 'active']),
        );
    }

    /**
     * @param  Builder<Rental>  $query
     * @return Builder<Rental>
     */
    public function scopeOverlapping(Builder $query, string $startsOn, string $endsOn): Builder
    {
        return $query
            ->whereDate('starts_on', '<=', $endsOn)
            ->whereDate('ends_on', '>=', $startsOn);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSummary(): array
    {
        $this->loadMissing(['car.model.make', 'pickupLocation', 'dropoffLocation', 'status', 'user']);

        return [
            'id' => $this->id,
            'starts_on' => $this->starts_on->toDateString(),
            'ends_on' => $this->ends_on->toDateString(),
            'days' => $this->days,
            'daily_rate' => (string) $this->daily_rate,
            'total_amount' => (string) $this->total_amount,
            'notes' => $this->notes,
            'status' => $this->status->name,
            'status_code' => $this->status->code,
            'cancellable' => $this->isCancellableByClient(),
            'car' => [
                'id' => $this->car->id,
                'name' => $this->car->displayName(),
                'image_url' => $this->car->image_url,
                'license_plate' => $this->car->license_plate,
            ],
            'pickup' => [
                'id' => $this->pickupLocation->id,
                'name' => $this->pickupLocation->name,
                'city' => $this->pickupLocation->city,
            ],
            'dropoff' => [
                'id' => $this->dropoffLocation->id,
                'name' => $this->dropoffLocation->name,
                'city' => $this->dropoffLocation->city,
            ],
            'customer' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
        ];
    }
}
