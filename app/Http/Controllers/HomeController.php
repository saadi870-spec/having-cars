<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Support\FleetLookups;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $featured = Car::query()
            ->with(['model.make', 'category', 'fuelType', 'transmission', 'status', 'location'])
            ->listed()
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Car $car) => $car->toFleetCard())
            ->values();

        return Inertia::render('welcome', [
            'featured' => $featured,
            'filters' => FleetLookups::searchFilters(),
        ]);
    }
}
