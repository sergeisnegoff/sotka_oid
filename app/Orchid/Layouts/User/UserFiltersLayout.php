<?php

namespace App\Orchid\Layouts\User;

use App\Orchid\Filters\ManagerFilter;
use App\Orchid\Filters\RoleFilter;
use App\Orchid\Filters\UserSearchFieldFilter;
use App\Orchid\Filters\UserSearchFilter;
use App\Orchid\Filters\UserSearchQueryFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class UserFiltersLayout extends Selection
{

    public $template = self::TEMPLATE_LINE;
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [

            RoleFilter::class,
            ManagerFilter::class,
            UserSearchFieldFilter::class,
            UserSearchQueryFilter::class,

        ];
    }
}
