<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Product;

use App\Models\Product;
use App\Orchid\Layouts\Product\ProductEditLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class ProductEditScreen extends Screen
{
    public $product;

    public function query(Product $product): iterable
    {
        $img = trim((string) ($product->images ?? ''));
        $imgUrl = '';

        if ($img !== '') {
            $imgUrl = preg_match('~^https?://~i', $img) === 1
                ? $img
                : asset('storage/' . ltrim($img, '/'));
        }

        return [
            'product'        => $product,
            'product.img_url' => $imgUrl,
        ];
    }

    public function name(): ?string
    {
        return $this->product->exists ? 'Редактирование товара' : 'Создание товара';
    }

    public function description(): ?string
    {
        return $this->product->exists
            ? $this->product->title
            : 'Заполните данные нового товара';
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
                ->route('platform.systems.products'),

            Button::make('Удалить')
                ->icon('bs.trash3')
                ->confirm('Вы уверены, что хотите удалить этот товар?')
                ->method('remove')
                ->canSee($this->product->exists),

            Button::make('Сохранить')
                ->type(Color::PRIMARY())
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            ProductEditLayout::class,
        ];
    }

    public function save(Product $product, Request $request)
    {
        $request->validate([
            'product.title'       => ['required', 'string', 'max:255'],
            'product.description' => ['nullable', 'string'],
            'product.category_id' => ['required', 'integer', 'exists:categories,id'],
            'product.brand_id'    => ['nullable', 'integer', 'exists:brands,id'],
            'product.price'       => ['nullable', 'numeric', 'min:0'],
            'product.quantity'    => ['nullable', 'integer', 'min:0'],
            'product.multiplicity'=> ['nullable', 'integer', 'min:0'],
            'product.total'       => ['nullable', 'integer', 'min:0'],
            'product.barcode'     => ['nullable', 'string', 'max:255'],
            'product.video_link'  => ['nullable', 'string', 'max:255'],
            'product.main_page'   => ['nullable'],
            'product.img_url'     => ['nullable', 'string'],
        ]);

        $data = $request->collect('product')->toArray();

        // Обработка изображения: URL → относительный путь
        if (isset($data['img_url']) && is_string($data['img_url'])) {
            $img = trim($data['img_url']);

            if ($img === '') {
                $data['images'] = null;
            } else {
                $storagePrefix = rtrim(asset('storage'), '/') . '/';
                if (str_starts_with($img, $storagePrefix)) {
                    $data['images'] = ltrim(substr($img, strlen($storagePrefix)), '/');
                } else {
                    $data['images'] = $img;
                }
            }
        }
        unset($data['img_url']);

        // main_page: привести к 0/1
        if (array_key_exists('main_page', $data)) {
            $data['main_page'] = filter_var($data['main_page'], FILTER_VALIDATE_BOOL) ? 1 : 0;
        }

        $product->fill($data)->save();

        Toast::info('Товар сохранён.');

        return redirect()->route('platform.systems.products');
    }

    public function remove(Product $product)
    {
        $product->delete();

        Toast::info('Товар удалён.');

        return redirect()->route('platform.systems.products');
    }
}
