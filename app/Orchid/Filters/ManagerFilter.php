<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use App\Models\ContactsManagersModel;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class ManagerFilter extends Filter
{
    public function name(): string
    {
        return 'Менеджер';
    }

    public function parameters(): array
    {
        return ['manager_id'];
    }

    public function display(): array
    {
        $options = ContactsManagersModel::query()
            ->visible()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($m) => [
                $m->id => trim($m->name . ($m->position ? ' — ' . $m->position : '')),
            ])
            ->toArray();

        return [
            Select::make('manager_id')
                ->title('Менеджер')
                ->options($options)
                ->empty('Все')
                ->set('width', 'col-md-3')
                ->value($this->request->get('manager_id')),
        ];
    }

    public function run(Builder $builder): Builder
    {
        $managerId = $this->request->get('manager_id');

        if (!$managerId) {
            return $builder;
        }

        return $builder->where('manager_id', $managerId);
    }

    public function value(): string
    {
        $managerId = $this->request->get('manager_id');

        if (!$managerId) {
            return '';
        }

        $manager = ContactsManagersModel::find($managerId);

        return $manager
            ? 'Менеджер: ' . $manager->name
            : '';
    }
}
