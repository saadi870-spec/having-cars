<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalRequest extends FormRequest
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
        return [
            'rental_status_id' => ['required', 'integer', 'exists:rental_statuses,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
