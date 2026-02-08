<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Product;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Orchid\Layouts\Product\ProductListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ProductListScreen extends Screen
{
    public function query(Request $request): iterable
    {
        $q = Product::query()->with(['category', 'brand']);

        // Поиск по названию, штрихкоду, 1С коду
        $search = trim((string) $request->input('filters.q', ''));
        if ($search !== '') {
            $q->where(function ($b) use ($search) {
                $b->where('title', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('oneC_7', 'like', "%{$search}%")
                    ->orWhere('oneC_8', 'like', "%{$search}%");
            });
        }

        // Фильтр по категории
        $categoryId = $request->input('filters.category_id');
        if ($categoryId) {
            // Если выбрана родительская категория, включаем и дочерние
            $childIds = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
            $categoryIds = array_merge([(int) $categoryId], $childIds);
            $q->whereIn('category_id', $categoryIds);
        }

        // Фильтр по бренду
        $brandId = $request->input('filters.brand_id');
        if ($brandId) {
            $q->where('brand_id', $brandId);
        }

        // Сортировка
        $sortParam = $request->input('sort', '-id');
        if (str_starts_with($sortParam, '-')) {
            $sortColumn = substr($sortParam, 1);
            $sortDirection = 'desc';
        } else {
            $sortColumn = $sortParam;
            $sortDirection = 'asc';
        }

        $allowedSorts = ['id', 'title', 'price', 'total', 'created_at'];
        if (in_array($sortColumn, $allowedSorts, true)) {
            $q->orderBy($sortColumn, $sortDirection);
        } else {
            $q->orderBy('id', 'desc');
        }

        return [
            'products' => $q->paginate(20),
            'filters'  => $request->input('filters', []),
            'categories' => Category::where('parent_id', 0)
                ->orWhereNull('parent_id')
                ->orderBy('sorder')
                ->get(),
            'brands' => Brands::orderBy('title')->get(),
        ];
    }

    public function name(): ?string
    {
        return 'Товары';
    }

    public function description(): ?string
    {
        return 'Управление каталогом товаров';
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
                ->route('platform.systems.products.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::view('orchid.products.filters'),
            ProductListLayout::class,
        ];
    }

    public function remove(Request $request): void
    {
        Product::findOrFail($request->get('id'))->delete();

        Toast::info('Товар удалён.');
    }
}
