<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Preorder;

use App\Models\Preorder;
use App\Orchid\Layouts\Preorder\PreorderListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class PreorderListScreen extends Screen
{
    public function query(Request $request): iterable
    {
        $q = Preorder::query()
            ->withCount('products');

        // Поиск по названию
        $search = trim((string) $request->input('filter.search', ''));
        if ($search !== '') {
            $q->where('title', 'like', "%{$search}%");
        }

        // Фильтр по статусу
        $status = $request->input('filter.status');
        if ($status === 'active') {
            $q->where('is_finished', false);
        } elseif ($status === 'finished') {
            $q->where('is_finished', true);
        }

        // Фильтр по типу
        $type = $request->input('filter.type');
        if ($type === 'internal') {
            $q->where('is_internal', true);
        } elseif ($type === 'external') {
            $q->where('is_internal', false);
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

        $allowedSorts = ['id', 'title', 'end_date'];
        if (in_array($sortColumn, $allowedSorts, true)) {
            $q->orderBy($sortColumn, $sortDirection);
        } else {
            $q->orderBy('id', 'desc');
        }

        return [
            'preorders' => $q->paginate(20),
        ];
    }

    public function name(): ?string
    {
        return 'Предзаказы';
    }

    public function description(): ?string
    {
        return 'Управление предзаказными кампаниями';
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
                ->route('platform.systems.preorders.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            PreorderListLayout::class,
        ];
    }

    public function remove(Request $request): void
    {
        Preorder::findOrFail($request->get('id'))->delete();

        Toast::info('Предзаказ удалён.');
    }
}
