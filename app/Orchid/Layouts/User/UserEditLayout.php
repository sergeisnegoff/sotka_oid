<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\ContactsManagersModel;
use App\Models\User;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Repository;

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

            Input::make('user.phon')
                ->type('text')
                ->max(50)
                ->title('Телефон')
                ->placeholder('+7 ...'),

            Input::make('user.city')
                ->type('text')
                ->max(255)
                ->title('Город')
                ->placeholder('Например, Москва'),

            Input::make('user.personal_sale')
                ->type('number')
                ->title('Персональная скидка (%)')
                ->placeholder('0–100')
                ->min(0)
                ->max(100)
                ->step(1),


            CheckBox::make('user.active')
                ->title('Аккаунт активен')
                ->placeholder('Включить доступ')
                ->sendTrueOrFalse()
                ->help('Если выключено — пользователь не должен иметь доступ к системе.'),

            Relation::make('user.manager_id')
                ->title('Менеджер')
                ->fromModel(ContactsManagersModel::class, 'name', 'id')
                ->searchColumns('name', 'email', 'phone', 'uuid')
                ->help('Начните вводить имя, email или телефон.'),



            Picture::make('user.img_url')
                ->title('Фото')
                ->storage('public')
                ->path('users/' . date('FY')),


        ];
    }
}
