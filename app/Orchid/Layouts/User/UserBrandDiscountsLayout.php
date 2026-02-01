<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Layouts\Rows;

class UserBrandDiscountsLayout extends Rows
{
    public function fields(): array
    {
        return [
            Matrix::make('discounts.brands')
                ->title('Скидки по брендам')
                ->columns([
                    'ID'         => 'id',
                    'Бренд'      => 'title',
                    'Включено'   => 'enabled',
                    'Скидка (%)' => 'sale',
                ])
                ->fields([
                    'id' => Input::make()
                        ->readonly(),
                    'title' => Input::make()
                        ->readonly(),

                    'enabled' => CheckBox::make()
                        ->sendTrueOrFalse(),

                    'sale' => Input::make()
                        ->type('number')
                        ->min(0)
                        ->max(100)
                        ->step(1),
                ])
                ->help('Отметьте бренды и укажите % скидки.'),
        ];
    }
}
