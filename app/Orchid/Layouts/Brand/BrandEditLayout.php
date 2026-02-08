<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Brand;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class BrandEditLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Input::make('brand.title')
                ->title('Название')
                ->required()
                ->placeholder('Название бренда'),
        ];
    }
}
