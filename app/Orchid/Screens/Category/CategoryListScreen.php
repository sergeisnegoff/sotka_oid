<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use App\Orchid\Filters\CategorySearchFilter;
use App\Orchid\Filters\CategoryTypeFilter;
use App\Orchid\Layouts\Category\CategoryFiltersLayout;
use App\Orchid\Layouts\Category\CategoryListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class CategoryListScreen extends Screen
{
    public function query(Request $request): iterable
    {
        $q = Category::query()
            ->with('parent')
            ->withCount('product')
            ->filtersApply([
                CategorySearchFilter::class,
                CategoryTypeFilter::class,
            ]);

        // Сортировка
        $sortParam = $request->input('sort', 'sorder');
        if (str_starts_with($sortParam, '-')) {
            $sortColumn = substr($sortParam, 1);
            $sortDirection = 'desc';
        } else {
            $sortColumn = $sortParam;
            $sortDirection = 'asc';
        }

        $allowedSorts = ['id', 'title', 'sorder'];
        if (in_array($sortColumn, $allowedSorts, true)) {
            // Убираем дефолтный orderBy из newQuery(), добавляем свой
            $q->reorder($sortColumn, $sortDirection);
        }

        return [
            'categories' => $q->paginate(30),
        ];
    }

    public function name(): ?string
    {
        return 'Категории';
    }

    public function description(): ?string
    {
        return 'Управление категориями товаров';
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
                ->route('platform.systems.categories.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            CategoryFiltersLayout::class,
            CategoryListLayout::class,
        ];
    }

    public function remove(Request $request): void
    {
        Category::findOrFail($request->get('id'))->delete();

        Toast::info('Категория удалена.');
    }
}
