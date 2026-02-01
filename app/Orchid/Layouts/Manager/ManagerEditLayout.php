<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Manager;

use App\Models\User;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;

class ManagerEditLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Input::make('manager.name')
                ->title('Имя')
                ->required()
                ->placeholder('Например, Иван Иванов'),

            Input::make('manager.email')
                ->title('Email')
                ->type('email')
                ->placeholder('name@example.com'),

            Input::make('manager.phone')
                ->title('Телефон')
                ->placeholder('+7 ...'),

            Input::make('manager.position')
                ->title('Должность')
                ->placeholder('Менеджер по продажам'),

            Switcher::make('manager.visible')
                ->title('Показывать на сайте')
                ->sendTrueOrFalse(),

//            Input::make('manager.user_id')
//                ->title('User ID (если требуется)')
//                ->type('number')
//                ->readonly()
//                ->placeholder(''),

            Relation::make('manager.user_id')
                ->title('Связанный пользователь')
                ->fromModel(User::class, 'name', 'id')
                ->searchColumns('name', 'email')
                ->help('Начните вводить имя или email, чтобы найти пользователя.'),



            Picture::make('manager.img_url')
                ->title('Фото')
                ->storage('public')
                ->path('contacts-managers/' . date('FY')),

        ];
    }
}
