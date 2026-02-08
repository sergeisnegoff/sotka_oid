<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Product;

use App\Models\Product;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductListLayout extends Table
{
    public $target = 'products';

    public function columns(): array
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->width('70px'),

            TD::make('title', 'Название')
                ->sort()
                ->cantHide()
                ->render(fn (Product $product) =>
                    Link::make($product->title)
                        ->route('platform.systems.products.edit', $product->id)),

            TD::make('category_id', 'Категория')
                ->render(fn (Product $product) => $product->category->title ?? '—'),

            TD::make('brand_id', 'Бренд')
                ->render(fn (Product $product) =>
                    $product->brand->title ?? '—'),

            TD::make('price', 'Цена')
                ->sort()
                ->align(TD::ALIGN_RIGHT),

            TD::make('total', 'Остаток')
                ->sort()
                ->align(TD::ALIGN_RIGHT),

            TD::make('multiplicity', 'Кратность')
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden(),

            TD::make('main_page', 'Главная')
                ->render(fn (Product $product) => $product->main_page ? '✓' : '—')
                ->align(TD::ALIGN_CENTER)
                ->width('80px'),

            TD::make('created_at', 'Создан')
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('', 'Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Product $product) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make('Редактировать')
                            ->route('platform.systems.products.edit', $product->id)
                            ->icon('bs.pencil'),

                        Button::make('Удалить')
                            ->icon('bs.trash3')
                            ->confirm('Вы уверены, что хотите удалить этот товар?')
                            ->method('remove', [
                                'id' => $product->id,
                            ]),
                    ])),
        ];
    }
}
