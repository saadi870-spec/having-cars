<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCatalogItemRequest;
use App\Models\CarMake;
use App\Models\CarModel;
use App\Support\CatalogRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

class CatalogController extends Controller
{
    public function index(string $type): Response
    {
        $definition = $this->definition($type);
        $query = $definition['model']::query()->orderBy('sort_order')->orderBy('name');

        if ($type === 'models') {
            $query->with('make:id,name');
        }

        $items = $query->get()->map(function (Model $item) use ($type) {
            $row = $item->only(array_merge(['id', 'is_active', 'sort_order'], CatalogRegistry::get($type)['fields']));

            if ($item instanceof CarModel && $item->relationLoaded('make')) {
                $row['make_name'] = $item->make->name;
            }

            return $row;
        });

        return Inertia::render('admin/catalog/index', [
            'type' => $type,
            'label' => $definition['label'],
            'fields' => $definition['fields'],
            'items' => $items,
            'makes' => $type === 'models' ? CarMake::query()->active()->get(['id', 'name']) : [],
            'catalogTypes' => collect(CatalogRegistry::all())->map(fn (array $item, string $key) => [
                'type' => $key,
                'label' => $item['label'],
            ])->values(),
        ]);
    }

    public function store(StoreCatalogItemRequest $request, string $type): RedirectResponse
    {
        $definition = $this->definition($type);
        $definition['model']::query()->create($this->payload($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Catalog item created.']);

        return to_route('admin.catalog.index', $type);
    }

    public function update(StoreCatalogItemRequest $request, string $type, int $item): RedirectResponse
    {
        $definition = $this->definition($type);
        $record = $definition['model']::query()->findOrFail($item);
        $record->update($this->payload($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Catalog item updated.']);

        return to_route('admin.catalog.index', $type);
    }

    public function destroy(Request $request, string $type, int $item): RedirectResponse
    {
        $definition = $this->definition($type);
        $record = $definition['model']::query()->findOrFail($item);
        $record->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Catalog item deleted.']);

        return to_route('admin.catalog.index', $type);
    }

    /**
     * @return array{
     *     model: class-string<Model>,
     *     label: string,
     *     fields: list<string>
     * }
     */
    private function definition(string $type): array
    {
        try {
            return CatalogRegistry::get($type);
        } catch (InvalidArgumentException) {
            abort(404);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(StoreCatalogItemRequest $request): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
