<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Analytics;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Filters\Types\Like;

class TopProductsTable extends Table
{
    /**
     * @var string
     */
    public $target = 'topProducts';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('rank', '#')
                ->width('60px')
                ->align(TD::ALIGN_CENTER),

            TD::make('title', 'Товар')
                ->sort()
                ->filter(Like::class)
                ->width('400px')
                ->render(function ($row) {
                    $title = $row->get('title');
                    $productId = $row->get('product_id');

                    if ($productId && $productId !== 'unknown' && is_numeric($productId)) {
                        return Link::make($title)
                            ->route('platform.products.edit', (int) $productId);
                    }

                    return $title;
                }),

            TD::make('qty_total', 'Количество')
                ->align(TD::ALIGN_RIGHT)
                ->sort()
                ->render(fn ($row) => number_format(
                    (float) $row->get('qty_total', 0),
                    0,
                    '.',
                    ' '
                )),

            TD::make('revenue_total', 'Выручка')
                ->align(TD::ALIGN_RIGHT)
                ->sort()
                ->render(fn ($row) => number_format(
                        (float) $row->get('revenue_total', 0),
                        0,
                        '.',
                        ' '
                    ) . ' ₽'),
        ];
    }

    /**
     * @return string
     */
    protected function iconNotFound(): string
    {
        return 'table';
    }

    /**
     * @return string
     */
    protected function textNotFound(): string
    {
        return 'За выбранный период нет данных о продажах';
    }

    /**
     * @return string
     */
    protected function subNotFound(): string
    {
        return 'Попробуйте изменить период или сбросить фильтры';
    }
}
