<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Dashboard;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class TopUsersTable extends Table
{
    /**
     * @var string
     */
    public $target = 'topUsers';

    /**
     * @var string
     */
    protected $title = 'ТОП-20 покупателей по выручке';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('rank', '#')->width('60px')->align(TD::ALIGN_CENTER),
            TD::make('name', 'Покупатель')->sort(),
            TD::make('email', 'Email'),
            TD::make('orders_count', 'Заказы')->align(TD::ALIGN_RIGHT),
            TD::make('preorders_count', 'Предзаказы')->align(TD::ALIGN_RIGHT),
            TD::make('revenue_total', 'Выручка')->align(TD::ALIGN_RIGHT)
                ->render(fn ($row) => number_format((float) data_get($row, 'revenue_total', 0), 0, '.', ' ').' RUB'),
        ];
    }
}
