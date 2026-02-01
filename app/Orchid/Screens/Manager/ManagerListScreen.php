<?php

namespace App\Orchid\Screens\Manager;

use App\Models\ContactsManagersModel;
use App\Orchid\Layouts\Manager\ManagerListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class ManagerListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'managers' => ContactsManagersModel::query()
                ->filters()
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Менеджеры';
    }

    public function description(): ?string
    {
        return 'Список менеджеров.';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.systems.managers.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            ManagerListLayout::class,
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.managers',
        ];
    }
}
