<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Layouts\Rows;

class UserSidebarLayout extends Rows
{
    public function fields(): array
    {
        return [
            CheckBox::make('user.active')
                ->title('Аккаунт активен')
                ->placeholder('Включить доступ')
                ->sendTrueOrFalse()
                ->help('Если выключено — пользователь не должен иметь доступ к системе.'),

            Cropper::make('user.avatar')
                ->title('Аватар')
                ->width(300)
                ->height(300)
                ->help('Загрузите изображение профиля.'),
        ];
    }
}
