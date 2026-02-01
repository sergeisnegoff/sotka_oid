<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Manager;

use App\Models\ContactsManagersModel;
use App\Orchid\Layouts\Manager\ManagerEditLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ManagerEditScreen extends Screen
{
    public ?ContactsManagersModel $manager = null;

    public function query(ContactsManagersModel $manager): iterable
    {
        $this->manager = $manager;

        $img = trim((string) ($manager->img ?? ''));
        $imgUrl = '';

        if ($img !== '') {
            $imgUrl = preg_match('~^https?://~i', $img) === 1
                ? $img
                : asset('storage/' . ltrim($img, '/'));
        }

        return [
            'manager' => $manager,
            'manager.img_url' => $imgUrl,
        ];
    }

    private function isEdit(): bool
    {
        return $this->manager !== null && $this->manager->exists;
    }

    public function name(): ?string
    {
        return $this->isEdit() ? 'Редактирование менеджера' : 'Создание менеджера';
    }

    public function description(): ?string
    {
        return $this->isEdit() ? 'Изменение данных менеджера.' : 'Создание нового менеджера.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.managers',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Назад')
                ->icon('bs.arrow-left')
                ->route('platform.systems.managers'),

            Button::make('Сохранить')
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make('Удалить')
                ->icon('bs.trash3')
                ->confirm('Удалить менеджера без возможности восстановления?')
                ->canSee($this->isEdit())
                ->method('remove'),
        ];
    }

    public function layout(): iterable
    {
        return [
            ManagerEditLayout::class,
        ];
    }

    public function save(Request $request, ContactsManagersModel $manager)
    {

        $data = $request->validate([
            'manager.name' => ['required', 'string', 'max:255'],
            'manager.email' => ['nullable', 'email', 'max:255'],
            'manager.phone' => ['nullable', 'string', 'max:255'],
            'manager.position' => ['nullable', 'string', 'max:255'],
            'manager.visible' => ['nullable'],
            'manager.user_id' => ['nullable', 'integer', 'exists:users,id'],
            'manager.img_url' => ['nullable', 'string'],
        ]);

        $managerData = $data['manager'] ?? [];
        //dd($managerData);
        $managerData['visible'] = isset($managerData['visible']) ? 1 : 0;

        // Нормализуем: если пришёл полный URL вида https://<host>/storage/<path>,
        // сохраняем только <path> (как обычно ожидает Voyager).
        if (isset($managerData['img_url']) && is_string($managerData['img_url'])) {
            $img = trim($managerData['img_url']);

            if ($img === '') {
                $managerData['img'] = null;
            } else {
                $storagePrefix = rtrim(asset('storage'), '/') . '/';
                if (str_starts_with($img, $storagePrefix)) {
                    $managerData['img'] = ltrim(substr($img, strlen($storagePrefix)), '/');
                }
            }
        }

        if (!$manager->exists) {
            $managerData['uuid'] = (string) Str::uuid();
        }



        $manager->fill($managerData)->save();

        Toast::info('Менеджер сохранён.');

        return redirect()->route('platform.systems.managers.edit', $manager->id);
    }


    public function remove(ContactsManagersModel $manager)
    {
        $manager->delete();

        Toast::info('Менеджер удалён.');

        return redirect()->route('platform.systems.managers');
    }
}
