<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Category;

use App\Models\Category;
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
            ->withCount('product');

        // Поиск по названию
        $search = trim((string) $request->input('filters.q', ''));
        if ($search !== '') {
            $q->where('title', 'like', "%{$search}%");
        }

        // Фильтр: только корневые / только дочерние
        $type = $request->input('filters.type');
        if ($type === 'root') {
            $q->where(function ($query) {
                $query->where('parent_id', 0)->orWhereNull('parent_id');
            });
        } elseif ($type === 'child') {
            $q->where('parent_id', '>', 0);
        }

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
            CategoryListLayout::class,
        ];
    }

    public function remove(Request $request): void
    {
        Category::findOrFail($request->get('id'))->delete();

        Toast::info('Категория удалена.');
    }
}
