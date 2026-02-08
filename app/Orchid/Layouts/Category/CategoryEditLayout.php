<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Category;

use App\Models\Category;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

class CategoryEditLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Input::make('category.title')
                ->title('Название')
                ->required()
                ->placeholder('Название категории'),

            Relation::make('category.parent_id')
                ->title('Родительская категория')
                ->fromModel(Category::class, 'title')
                ->applyScope('rootCategories')
                ->searchColumns('title')
                ->empty('Нет (корневая)', 0)
                ->help('Оставьте пустым для корневой категории'),

            Input::make('category.sorder')
                ->title('Сортировка')
                ->type('number')
                ->value(0)
                ->help('Чем меньше число, тем выше в списке'),
        ];
    }
}
