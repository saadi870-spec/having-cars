<?php

namespace App\Http\Requests\Admin;

use App\Models\Car;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $car = $this->route('car');
        $carId = $car instanceof Car ? $car->id : null;

        return [
            'license_plate' => ['required', 'string', 'max:20', 'unique:cars,license_plate,'.($carId ?? 'NULL')],
            'car_model_id' => ['required', 'integer', 'exists:car_models,id'],
            'car_category_id' => ['required', 'integer', 'exists:car_categories,id'],
            'fuel_type_id' => ['required', 'integer', 'exists:fuel_types,id'],
            'transmission_type_id' => ['required', 'integer', 'exists:transmission_types,id'],
            'car_status_id' => ['required', 'integer', 'exists:car_statuses,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'year' => ['required', 'integer', 'min:1990', 'max:'.(now()->year + 1)],
            'color' => ['required', 'string', 'max:40'],
            'seats' => ['required', 'integer', 'min:2', 'max:15'],
            'doors' => ['required', 'integer', 'min:2', 'max:6'],
            'daily_rate' => ['required', 'numeric', 'min:1'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
