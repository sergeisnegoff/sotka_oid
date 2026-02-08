<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use App\Orchid\Layouts\Category\CategoryEditLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class CategoryEditScreen extends Screen
{
    public $category;

    public function query(Category $category): iterable
    {
        return [
            'category' => $category,
        ];
    }

    public function name(): ?string
    {
        return $this->category->exists ? 'Редактирование категории' : 'Создание категории';
    }

    public function description(): ?string
    {
        return $this->category->exists
            ? $this->category->title
            : 'Заполните данные новой категории';
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
                ->route('platform.systems.categories'),

            Button::make('Удалить')
                ->icon('bs.trash3')
                ->confirm('Удаление категории может повлиять на привязанные товары. Продолжить?')
                ->method('remove')
                ->canSee($this->category->exists),

            Button::make('Сохранить')
                ->type(Color::PRIMARY())
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            CategoryEditLayout::class,
        ];
    }

    public function save(Category $category, Request $request)
    {
        $request->validate([
            'category.title'     => ['required', 'string', 'max:255'],
            'category.parent_id' => ['nullable', 'integer'],
            'category.sorder'    => ['nullable', 'integer', 'min:0'],
        ]);

        $data = $request->collect('category')->toArray();

        // parent_id: 0 или null = корневая
        if (empty($data['parent_id'])) {
            $data['parent_id'] = 0;
        }

        $category->fill($data)->save();

        Toast::info('Категория сохранена.');

        return redirect()->route('platform.systems.categories');
    }

    public function remove(Category $category)
    {
        $category->delete();

        Toast::info('Категория удалена.');

        return redirect()->route('platform.systems.categories');
    }
}
