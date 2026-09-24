<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRentalRequest;
use App\Models\Rental;
use App\Support\FleetLookups;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RentalController extends Controller
{
    public function index(): Response
    {
        $rentals = Rental::query()
            ->with(['car.model.make', 'pickupLocation', 'dropoffLocation', 'status', 'user'])
            ->latest()
            ->paginate(15)
            ->through(fn (Rental $rental) => [
                ...$rental->toSummary(),
                'rental_status_id' => $rental->rental_status_id,
            ]);

        return Inertia::render('admin/rentals/index', [
            'rentals' => $rentals,
            'lookups' => FleetLookups::adminForms(),
        ]);
    }

    public function update(UpdateRentalRequest $request, Rental $rental): RedirectResponse
    {
        $rental->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Reservation updated.']);

        return back();
    }
}
