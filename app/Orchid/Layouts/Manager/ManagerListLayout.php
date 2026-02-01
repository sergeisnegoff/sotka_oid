<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Manager;

use App\Models\ContactsManagersModel;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ManagerListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'managers';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', 'Имя')
                ->sort()
                ->cantHide()
                ->render(function (ContactsManagersModel $manager) {
                    $avatar = $this->avatarUrl((string) ($manager->img ?? ''));
                    $name = e((string) ($manager->name ?? ''));

                    $imgHtml = $avatar !== ''
                        ? '<img src="' . e($avatar) . '" alt="" style="width:70px;height:70px;border-radius:50%;object-fit:cover;margin-right:10px;">'
                        : '<span style="display:inline-block;width:70px;height:70px;border-radius:50%;background:#e9ecef;margin-right:10px;"></span>';

                    return '<div style="display:flex;align-items:center;">' . $imgHtml . '<strong>' . $name . '</strong></div>';
                }),

            TD::make('email', 'Email')
                ->sort()
                ->cantHide()
                ->render(fn (ContactsManagersModel $manager) => e((string) ($manager->email ?? ''))),

            TD::make('phone', 'Телефон')
                ->sort()
                ->width('160px')
                ->render(fn (ContactsManagersModel $manager) => e((string) ($manager->phone ?? ''))),

            TD::make('position', 'Должность')
                ->sort()
                ->render(fn (ContactsManagersModel $manager) => e((string) ($manager->position ?? ''))),

            TD::make('img', 'Фото')
                ->defaultHidden()
                ->render(fn (ContactsManagersModel $manager) => e((string) ($manager->img ?? ''))),

            TD::make('uuid', 'UUID')
                ->defaultHidden()
                ->sort()
                ->render(fn (ContactsManagersModel $manager) => e((string) ($manager->uuid ?? ''))),

            TD::make('visible', 'Видимость')
                ->sort()
                ->width('110px')
                ->render(fn (ContactsManagersModel $manager) => (int) $manager->visible === 1 ? 'Да' : 'Нет'),

            TD::make('user_id', 'User ID')
                ->defaultHidden()
                ->sort()
                ->width('110px')
                ->render(fn (ContactsManagersModel $manager) => (string) ($manager->user_id ?? '')),

            TD::make('created_at', 'Создан')
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', 'Обновлён')
                ->defaultHidden()
                ->sort(),

            TD::make('actions', 'Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (ContactsManagersModel $manager) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make('Редактировать')
                            ->route('platform.systems.managers.edit', $manager->id)
                            ->icon('bs.pencil'),
                    ])),
        ];
    }

    private function avatarUrl(string $img): string
    {
        $img = trim($img);

        if ($img === '') {
            return '';
        }

        if (preg_match('~^https?://~i', $img) === 1) {
            return $img;
        }

        $img = ltrim($img, '/');

        return asset('storage/' . $img);
    }
}
