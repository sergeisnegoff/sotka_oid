<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Brand;

use App\Models\Brands;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class BrandListLayout extends Table
{
    public $target = 'brands';

    public function columns(): array
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->width('70px'),

            TD::make('title', 'Название')
                ->sort()
                ->cantHide()
                ->render(fn (Brands $brand) =>
                    Link::make($brand->title)
                        ->route('platform.systems.brands.edit', $brand->id)),

            TD::make('products_count', 'Товаров')
                ->align(TD::ALIGN_CENTER)
                ->width('100px'),

            TD::make('', 'Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Brands $brand) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make('Редактировать')
                            ->route('platform.systems.brands.edit', $brand->id)
                            ->icon('bs.pencil'),

                        Button::make('Удалить')
                            ->icon('bs.trash3')
                            ->confirm('Удаление бренда может повлиять на привязанные товары. Продолжить?')
                            ->method('remove', [
                                'id' => $brand->id,
                            ]),
                    ])),
        ];
    }
}
