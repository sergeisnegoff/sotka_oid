<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Category;

use App\Models\Category;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CategoryListLayout extends Table
{
    public $target = 'categories';

    public function columns(): array
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->width('70px'),

            TD::make('title', 'Название')
                ->sort()
                ->cantHide()
                ->render(function (Category $category) {
                    $prefix = $category->parent_id ? '— ' : '';
                    return Link::make($prefix . $category->title)
                        ->route('platform.systems.categories.edit', $category->id);
                }),

            TD::make('parent_id', 'Родительская')
                ->render(fn (Category $category) =>
                    $category->parent ? $category->parent->title : '—'),

            TD::make('sorder', 'Сортировка')
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->width('100px'),

            TD::make('products_count', 'Товаров')
                ->align(TD::ALIGN_CENTER)
                ->width('100px'),

            TD::make('', 'Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Category $category) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make('Редактировать')
                            ->route('platform.systems.categories.edit', $category->id)
                            ->icon('bs.pencil'),

                        Button::make('Удалить')
                            ->icon('bs.trash3')
                            ->confirm('Удаление категории может повлиять на привязанные товары. Продолжить?')
                            ->method('remove', [
                                'id' => $category->id,
                            ]),
                    ])),
        ];
    }
}
