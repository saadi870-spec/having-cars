<?php

namespace App\Http\Controllers;

use App\Http\Requests\FleetSearchRequest;
use App\Models\Car;
use App\Support\FleetLookups;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CarController extends Controller
{
    public function index(FleetSearchRequest $request): Response
    {
        $startsOn = $request->validated('starts_on');
        $endsOn = $request->validated('ends_on');

        $cars = Car::query()
            ->with(['model.make', 'category', 'fuelType', 'transmission', 'status', 'location'])
            ->listed()
            ->when(
                $request->hasDateRange(),
                fn ($query) => $query->availableBetween($startsOn, $endsOn),
            )
            ->when($request->validated('location_id'), fn ($query, $id) => $query->where('location_id', $id))
            ->when($request->validated('category_id'), fn ($query, $id) => $query->where('car_category_id', $id))
            ->when(
                $request->validated('make_id'),
                fn ($query, $id) => $query->whereHas('model', fn ($models) => $models->where('car_make_id', $id)),
            )
            ->when($request->validated('transmission_id'), fn ($query, $id) => $query->where('transmission_type_id', $id))
            ->when($request->validated('q'), function ($query, string $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('license_plate', 'like', "%{$search}%")
                        ->orWhere('color', 'like', "%{$search}%")
                        ->orWhereHas('model', function ($models) use ($search) {
                            $models->where('name', 'like', "%{$search}%")
                                ->orWhereHas('make', fn ($makes) => $makes->where('name', 'like', "%{$search}%"));
                        });
                });
            })
            ->orderBy('daily_rate')
            ->paginate(9)
            ->withQueryString()
            ->through(fn (Car $car) => $car->toFleetCard($startsOn, $endsOn));

        return Inertia::render('cars/index', [
            'cars' => $cars,
            'filters' => FleetLookups::searchFilters(),
            'query' => $request->validated(),
        ]);
    }

    public function show(Request $request, Car $car): Response
    {
        $car->load(['model.make', 'category', 'fuelType', 'transmission', 'status', 'location']);

        abort_unless($car->isListed() || $request->user()?->isAdmin(), 404);

        $startsOn = $request->query('starts_on');
        $endsOn = $request->query('ends_on');

        $available = true;
        $quote = $car->toFleetCard(
            is_string($startsOn) ? $startsOn : null,
            is_string($endsOn) ? $endsOn : null,
        );

        if (is_string($startsOn) && is_string($endsOn)) {
            $available = $car->isAvailableBetween($startsOn, $endsOn);
        }

        return Inertia::render('cars/show', [
            'car' => $quote,
            'available' => $available,
            'filters' => FleetLookups::searchFilters(),
            'query' => [
                'starts_on' => is_string($startsOn) ? $startsOn : '',
                'ends_on' => is_string($endsOn) ? $endsOn : '',
                'pickup_location_id' => $request->integer('pickup_location_id') ?: $car->location_id,
                'dropoff_location_id' => $request->integer('dropoff_location_id') ?: $car->location_id,
            ],
        ]);
    }
}
