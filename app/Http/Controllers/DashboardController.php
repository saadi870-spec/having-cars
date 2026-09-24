<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Rental;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $upcoming = $user->rentals()
            ->with(['car.model.make', 'pickupLocation', 'dropoffLocation', 'status', 'user'])
            ->whereHas('status', fn ($query) => $query->whereIn('code', ['pending', 'confirmed', 'active']))
            ->orderBy('starts_on')
            ->limit(5)
            ->get()
            ->map(fn (Rental $rental) => $rental->toSummary())
            ->values();

        $stats = [
            'upcoming' => $user->rentals()->whereHas('status', fn ($query) => $query->whereIn('code', ['pending', 'confirmed', 'active']))->count(),
            'completed' => $user->rentals()->whereHas('status', fn ($query) => $query->where('code', 'completed'))->count(),
            'fleet' => Car::query()->listed()->count(),
        ];

        if ($user->isAdmin()) {
            $stats['open_rentals'] = Rental::query()
                ->whereHas('status', fn ($query) => $query->whereIn('code', ['pending', 'confirmed', 'active']))
                ->count();
        }

        return Inertia::render('dashboard', [
            'upcoming' => $upcoming,
            'stats' => $stats,
        ]);
    }
}
