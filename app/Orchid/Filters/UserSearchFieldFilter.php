<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class UserSearchFieldFilter extends Filter
{
    public function name(): string { return 'Поля'; }
    public function parameters(): array { return ['field']; }

    public function display(): array
    {
        return [
            Select::make('field')
                ->title('Поля')
                ->options([
                    ''      => 'Все',
                    'name'  => 'Имя',
                    'email' => 'Email',
                    'phon'  => 'Телефон',
                    'city'  => 'Город',
                ])
                ->value((string) $this->request->get('field', '')),
        ];
    }

    public function run(Builder $builder): Builder
    {
        // Этот фильтр сам запрос не меняет, он только задаёт параметр field
        return $builder;
    }

    public function value(): string { return ''; }
}
