<?php

namespace App\Http\Requests\Admin;

use App\Support\CatalogRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCatalogItemRequest extends FormRequest
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
        $type = (string) $this->route('type');
        $definition = CatalogRegistry::get($type);
        $table = (new $definition['model'])->getTable();
        $itemId = $this->route('item');

        $rules = [
            'code' => ['required', 'string', 'max:40', Rule::unique($table, 'code')->ignore($itemId)],
            'name' => ['required', 'string', 'max:80'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        if (in_array('description', $definition['fields'], true)) {
            $rules['description'] = ['nullable', 'string', 'max:255'];
        }

        if (in_array('car_make_id', $definition['fields'], true)) {
            $rules['car_make_id'] = ['required', 'integer', 'exists:car_makes,id'];
            $rules['code'] = [
                'required',
                'string',
                'max:40',
                Rule::unique($table, 'code')
                    ->ignore($itemId)
                    ->where('car_make_id', $this->input('car_make_id')),
            ];
        }

        if (in_array('address', $definition['fields'], true)) {
            $rules['address'] = ['required', 'string', 'max:160'];
            $rules['city'] = ['required', 'string', 'max:80'];
            $rules['phone'] = ['nullable', 'string', 'max:40'];
        }

        return $rules;
    }
}
