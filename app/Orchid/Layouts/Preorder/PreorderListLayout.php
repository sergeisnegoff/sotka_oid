<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Preorder;

use App\Models\Preorder;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PreorderListLayout extends Table
{
    public $target = 'preorders';

    public function columns(): array
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->width('70px'),

            TD::make('title', 'Название')
                ->sort()
                ->alignLeft()
                ->cantHide()
                ->render(function (Preorder $preorder) {
                    return Link::make($preorder->title)
                        ->style('max-width: 450px; overflow: hidden; text-overflow: ellipsis; white-space: wrap;')
                        ->route('platform.systems.preorders.edit', $preorder->id);
                }),

            TD::make('end_date', 'Дата окончания')
                ->sort()
                ->width('100px')
                ->render(fn (Preorder $preorder) =>
                    date('d.m.Y', strtotime($preorder->end_date)) ?: '—'),

//            TD::make('products_count', 'Товаров')
//                ->align(TD::ALIGN_CENTER)
//                ->width('100px'),

            TD::make('is_internal', 'Внутренний')
                ->align(TD::ALIGN_CENTER)
                ->width('120px')
                ->render(fn (Preorder $preorder) =>
                    $preorder->is_internal
                        ? '<span class="badge bg-info">Да</span>'
                        : '<span class="text-muted">Нет</span>'),

            TD::make('is_finished', 'Статус')
                ->align(TD::ALIGN_CENTER)
                ->width('120px')
                ->render(fn (Preorder $preorder) =>
                    $preorder->is_finished
                        ? '<span class="badge bg-success">Завершён</span>'
                        : '<span class="badge bg-warning text-dark">Активен</span>'),

//            TD::make('file_processed', 'Файл')
//                ->align(TD::ALIGN_CENTER)
//                ->width('100px')
//                ->render(fn (Preorder $preorder) =>
//                    $preorder->file_processed
//                        ? '<i class="text-success bs-check-circle"></i>'
//                        : '<i class="text-muted bs-clock"></i>'),

            TD::make('', 'Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Preorder $preorder) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make('Редактировать')
                            ->route('platform.systems.preorders.edit', $preorder->id)
                            ->icon('bs.pencil'),

                        Button::make('Удалить')
                            ->icon('bs.trash3')
                            ->confirm('Удаление предзаказа приведёт к потере всех связанных данных. Продолжить?')
                            ->method('remove', [
                                'id' => $preorder->id,
                            ]),
                    ])),
        ];
    }
}
