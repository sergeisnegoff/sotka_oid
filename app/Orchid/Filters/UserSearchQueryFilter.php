<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class UserSearchQueryFilter extends Filter
{
    public function name(): string { return 'Запрос'; }
    public function parameters(): array { return ['q']; }

    public function display(): array
    {
        return [
            Input::make('q')
                ->title('Запрос')
                ->placeholder('Поиск...')
                ->value((string) $this->request->get('q', '')),
        ];
    }

    public function run(Builder $builder): Builder
    {
        $q = trim((string) $this->request->get('q', ''));
        if ($q === '') return $builder;

        $field = (string) $this->request->get('field', '');

        $allowed = ['name','email','phon','city'];

        if ($field !== '' && in_array($field, $allowed, true)) {
            return $builder->where($field, 'like', "%{$q}%");
        }

        return $builder->where(function (Builder $b) use ($q) {
            $b->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('phon', 'like', "%{$q}%")
                ->orWhere('city', 'like', "%{$q}%");
        });
    }

    public function value(): string
    {
        $q = trim((string) $this->request->get('q', ''));
        return $q === '' ? '' : 'Запрос: ' . $q;
    }
}
