<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class UserEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('user.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title('Имя')
                ->placeholder('Имя'),

            Input::make('user.email')
                ->type('email')
                ->required()
                ->title('Email')
                ->placeholder('Email'),

            CheckBox::make('user.active')
                ->title('Аккаунт активен')
                ->placeholder('Включить доступ')
                ->sendTrueOrFalse()
                ->help('Если выключено — пользователь не должен иметь доступ к системе.'),

        ];
    }
}
