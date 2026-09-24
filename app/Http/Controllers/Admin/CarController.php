<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarRequest;
use App\Models\Car;
use App\Support\FleetLookups;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CarController extends Controller
{
    public function index(): Response
    {
        $cars = Car::query()
            ->with(['model.make', 'category', 'fuelType', 'transmission', 'status', 'location'])
            ->latest()
            ->paginate(12)
            ->through(fn (Car $car) => [
                ...$car->toFleetCard(),
                'car_model_id' => $car->car_model_id,
                'car_category_id' => $car->car_category_id,
                'fuel_type_id' => $car->fuel_type_id,
                'transmission_type_id' => $car->transmission_type_id,
                'car_status_id' => $car->car_status_id,
                'location_id' => $car->location_id,
            ]);

        return Inertia::render('admin/cars/index', [
            'cars' => $cars,
            'lookups' => FleetLookups::adminForms(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/cars/form', [
            'car' => null,
            'lookups' => FleetLookups::adminForms(),
        ]);
    }

    public function store(StoreCarRequest $request): RedirectResponse
    {
        Car::query()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Car added to the fleet.']);

        return to_route('admin.cars.index');
    }

    public function edit(Car $car): Response
    {
        return Inertia::render('admin/cars/form', [
            'car' => [
                ...$car->toFleetCard(),
                'car_model_id' => $car->car_model_id,
                'car_category_id' => $car->car_category_id,
                'fuel_type_id' => $car->fuel_type_id,
                'transmission_type_id' => $car->transmission_type_id,
                'car_status_id' => $car->car_status_id,
                'location_id' => $car->location_id,
            ],
            'lookups' => FleetLookups::adminForms(),
        ]);
    }

    public function update(StoreCarRequest $request, Car $car): RedirectResponse
    {
        $car->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Car updated.']);

        return to_route('admin.cars.index');
    }

    public function destroy(Car $car): RedirectResponse
    {
        $car->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Car removed.']);

        return to_route('admin.cars.index');
    }
}
