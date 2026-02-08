<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Brand;

use App\Models\Brands;
use App\Orchid\Layouts\Brand\BrandEditLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class BrandEditScreen extends Screen
{
    public $brand;

    public function query(Brands $brand): iterable
    {
        return [
            'brand' => $brand,
        ];
    }

    public function name(): ?string
    {
        return $this->brand->exists ? 'Редактирование бренда' : 'Создание бренда';
    }

    public function description(): ?string
    {
        return $this->brand->exists
            ? $this->brand->title
            : 'Заполните данные нового бренда';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.products',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Назад')
                ->icon('bs.arrow-left')
                ->route('platform.systems.brands'),

            Button::make('Удалить')
                ->icon('bs.trash3')
                ->confirm('Удаление бренда может повлиять на привязанные товары. Продолжить?')
                ->method('remove')
                ->canSee($this->brand->exists),

            Button::make('Сохранить')
                ->type(Color::PRIMARY())
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            BrandEditLayout::class,
        ];
    }

    public function save(Brands $brand, Request $request)
    {
        $request->validate([
            'brand.title' => ['required', 'string', 'max:255'],
        ]);

        $brand->fill($request->collect('brand')->toArray())->save();

        Toast::info('Бренд сохранён.');

        return redirect()->route('platform.systems.brands');
    }

    public function remove(Brands $brand)
    {
        $brand->delete();

        Toast::info('Бренд удалён.');

        return redirect()->route('platform.systems.brands');
    }
}
