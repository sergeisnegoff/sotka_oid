<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Dashboard;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

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
            TD::make('rank', '#')->width('60px')->align(TD::ALIGN_CENTER),
            TD::make('title', 'Товар'),
            TD::make('qty_total', 'Количество')->align(TD::ALIGN_RIGHT)
                ->render(fn ($row) => number_format((float) data_get($row, 'qty_total', 0), 0, '.', ' ')),
            TD::make('revenue_total', 'Выручка')->align(TD::ALIGN_RIGHT)
                ->render(fn ($row) => number_format((float) data_get($row, 'revenue_total', 0), 0, '.', ' ').' RUB'),
        ];
    }
}
