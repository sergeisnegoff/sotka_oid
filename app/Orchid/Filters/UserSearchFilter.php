<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class UserSearchFilter extends Filter
{
    public function name(): string
    {
        return __('Search');
    }

    public function parameters(): array
    {
        return ['field', 'q'];
    }

    public function display(): array
    {
        return [
            Select::make('field')
                ->title('Поля')
                ->options([
                    'name'  => 'Имя',
                    'email' => 'Email',
                    'phon'  => 'Телефон',
                    'city'  => 'Город',
                ])
                ->empty('Все')
                ->set('width', 'col-md-3'),

            Input::make('q')
                ->title('Запрос')
                ->placeholder('Поиск...')
                ->set('width', '300'),
        ];
    }

    public function run(Builder $builder): Builder
    {
        $q = trim((string) $this->request->get('q', ''));
        if ($q === '') {
            return $builder;
        }

        $field = $this->request->get('field');

        // Если поле не выбрано — ищем по нескольким
        if (!$field) {
            return $builder->where(function (Builder $b) use ($q) {
                $b->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phon', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%");
            });
        }

        // Если выбрано поле — фильтруем по нему
        $allowed = ['name', 'email', 'phon', 'city'];
        if (!in_array($field, $allowed, true)) {
            return $builder;
        }

        return $builder->where($field, 'like', "%{$q}%");
    }

    public function value(): string
    {
        $q = trim((string) $this->request->get('q', ''));
        $field = (string) $this->request->get('field', '');

        if ($q === '') {
            return '';
        }

        return $field ? "{$this->name()}: {$field} contains '{$q}'" : "{$this->name()}: '{$q}'";
    }
}
