<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class CategoryTypeFilter extends Filter
{
    public function name(): string
    {
        return 'Тип';
    }

    public function parameters(): array
    {
        return ['type'];
    }

    public function display(): array
    {
        return [
            Select::make('type')
                ->title('Тип категории')
                ->options([
                    'root'  => 'Корневые',
                    'child' => 'Дочерние',
                ])
                ->empty('Все')
                ->value($this->request->get('type')),
        ];
    }

    public function run(Builder $builder): Builder
    {
        $type = $this->request->get('type');

        if ($type === 'root') {
            return $builder->where(function ($query) {
                $query->where('parent_id', 0)->orWhereNull('parent_id');
            });
        }

        if ($type === 'child') {
            return $builder->where('parent_id', '>', 0);
        }

        return $builder;
    }

    public function value(): string
    {
        $type = $this->request->get('type');

        $labels = [
            'root'  => 'Корневые',
            'child' => 'Дочерние',
        ];

        return isset($labels[$type]) ? 'Тип: ' . $labels[$type] : '';
    }
}
