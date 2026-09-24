<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FleetSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'starts_on' => ['nullable', 'date', 'after_or_equal:today'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'category_id' => ['nullable', 'integer', 'exists:car_categories,id'],
            'make_id' => ['nullable', 'integer', 'exists:car_makes,id'],
            'transmission_id' => ['nullable', 'integer', 'exists:transmission_types,id'],
            'q' => ['nullable', 'string', 'max:80'],
        ];
    }

    public function hasDateRange(): bool
    {
        return filled($this->input('starts_on')) && filled($this->input('ends_on'));
    }
}
