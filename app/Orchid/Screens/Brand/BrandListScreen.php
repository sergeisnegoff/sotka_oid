<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Brand;

use App\Models\Brands;
use App\Orchid\Layouts\Brand\BrandListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class BrandListScreen extends Screen
{
    public function query(Request $request): iterable
    {
        $q = Brands::query()->withCount('products');

        // Поиск по названию
        $search = trim((string) $request->input('filters.q', ''));
        if ($search !== '') {
            $q->where('title', 'like', "%{$search}%");
        }

        // Сортировка
        $sortParam = $request->input('sort', 'title');
        if (str_starts_with($sortParam, '-')) {
            $sortColumn = substr($sortParam, 1);
            $sortDirection = 'desc';
        } else {
            $sortColumn = $sortParam;
            $sortDirection = 'asc';
        }

        $allowedSorts = ['id', 'title'];
        if (in_array($sortColumn, $allowedSorts, true)) {
            $q->orderBy($sortColumn, $sortDirection);
        } else {
            $q->orderBy('title');
        }

        return [
            'brands' => $q->paginate(30),
        ];
    }

    public function name(): ?string
    {
        return 'Бренды';
    }

    public function description(): ?string
    {
        return 'Управление брендами';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.products',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить')
                ->icon('bs.plus-circle')
                ->route('platform.systems.brands.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            BrandListLayout::class,
        ];
    }

    public function remove(Request $request): void
    {
        Brands::findOrFail($request->get('id'))->delete();

        Toast::info('Бренд удалён.');
    }
}
