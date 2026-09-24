<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalRequest;
use App\Models\Car;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Services\RentalQuote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RentalController extends Controller
{
    public function index(Request $request): Response
    {
        $rentals = $request->user()
            ->rentals()
            ->with(['car.model.make', 'pickupLocation', 'dropoffLocation', 'status', 'user'])
            ->latest()
            ->get()
            ->map(fn (Rental $rental) => $rental->toSummary())
            ->values();

        return Inertia::render('rentals/index', [
            'rentals' => $rentals,
        ]);
    }

    public function store(StoreRentalRequest $request): RedirectResponse
    {
        $car = Car::query()->with('status')->findOrFail($request->integer('car_id'));
        $startsOn = $request->string('starts_on')->toString();
        $endsOn = $request->string('ends_on')->toString();

        if (! $car->isAvailableBetween($startsOn, $endsOn)) {
            return back()->withErrors([
                'starts_on' => 'That car is not available for the selected dates.',
            ]);
        }

        $quote = RentalQuote::fromDates((string) $car->daily_rate, $startsOn, $endsOn);
        $status = RentalStatus::query()->where('code', 'pending')->firstOrFail();

        $rental = Rental::query()->create([
            'user_id' => $request->user()->id,
            'car_id' => $car->id,
            'pickup_location_id' => $request->integer('pickup_location_id'),
            'dropoff_location_id' => $request->integer('dropoff_location_id'),
            'rental_status_id' => $status->id,
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'days' => $quote->days,
            'daily_rate' => $quote->dailyRate,
            'total_amount' => $quote->total,
            'notes' => $request->validated('notes'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Reservation requested. We will confirm it shortly.']);

        return to_route('rentals.index');
    }

    public function cancel(Request $request, Rental $rental): RedirectResponse
    {
        abort_unless($rental->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        abort_unless($rental->isCancellableByClient() || $request->user()->isAdmin(), 403);

        $cancelled = RentalStatus::query()->where('code', 'cancelled')->firstOrFail();
        $rental->update(['rental_status_id' => $cancelled->id]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Reservation cancelled.']);

        return back();
    }
}
