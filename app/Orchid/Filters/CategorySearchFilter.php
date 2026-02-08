<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class CategorySearchFilter extends Filter
{
    public function name(): string
    {
        return 'Поиск';
    }

    public function parameters(): array
    {
        return ['q'];
    }

    public function display(): array
    {
        return [
            Input::make('q')
                ->title('Поиск')
                ->placeholder('Название категории...')
                ->value((string) $this->request->get('q', '')),
        ];
    }

    public function run(Builder $builder): Builder
    {
        $q = trim((string) $this->request->get('q', ''));

        if ($q === '') {
            return $builder;
        }

        return $builder->where('title', 'like', "%{$q}%");
    }

    public function value(): string
    {
        $q = trim((string) $this->request->get('q', ''));

        return $q === '' ? '' : 'Поиск: ' . $q;
    }
}
