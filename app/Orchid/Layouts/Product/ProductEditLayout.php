<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Product;

use App\Models\Brands;
use App\Models\Category;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class ProductEditLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Input::make('product.title')
                ->title('Название')
                ->required()
                ->placeholder('Название товара'),

            TextArea::make('product.description')
                ->title('Описание')
                ->rows(5),

            Picture::make('product.img_url')
                ->title('Изображение')
                ->storage('public')
                ->path('products/' . date('FY')),

            Group::make([
                Select::make('product.category_id')
                    ->title('Категория')
                    ->options($this->buildCategoryOptions())
                    ->empty('Выберите категорию', '')
                    ->required(),

                Select::make('product.brand_id')
                    ->title('Бренд')
                    ->fromModel(Brands::class, 'title')
                    ->empty('Без бренда', ''),
            ]),

            Group::make([
                Input::make('product.price')
                    ->title('Цена')
                    ->type('number')
                    ->step('0.01'),

                Input::make('product.multiplicity')
                    ->title('Кратность')
                    ->type('number')
                    ->help('Минимум для заказа'),
            ]),

            Group::make([
                Input::make('product.qty')
                    ->title('Количество')
                    ->type('number'),

                Input::make('product.total')
                    ->title('Остаток')
                    ->type('number'),
            ]),

            Input::make('product.barcode')
                ->title('Штрихкод'),

            Input::make('product.video_link')
                ->title('Ссылка на видео')
                ->placeholder('https://...'),

            Group::make([
                Input::make('product.oneC_7')
                    ->title('1С (7)')
                    ->readonly()
                    ->help('Идентификатор из 1С'),

                Input::make('product.oneC_8')
                    ->title('1С (8)')
                    ->readonly()
                    ->help('Идентификатор из 1С'),
            ]),

            CheckBox::make('product.main_page')
                ->title('Показывать на главной')
                ->sendTrueOrFalse(),
        ];
    }

    private function buildCategoryOptions(): array
    {
        $all = Category::orderBy('sorder')->get()->groupBy('parent_id');

        $options = [];
        $this->addChildren($all, $options, 0, 0);

        return $options;
    }

    private function addChildren($grouped, array &$options, int $parentId, int $depth): void
    {
        $children = $grouped->get($parentId, collect())
            ->merge($parentId === 0 ? $grouped->get(null, collect()) : collect());

        foreach ($children as $cat) {
            $prefix = str_repeat('· ', $depth);
            $options[$cat->id] = $prefix . $cat->title;
            $this->addChildren($grouped, $options, (int) $cat->id, $depth + 1);
        }
    }
}
