<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Category;

use App\Orchid\Filters\CategorySearchFilter;
use App\Orchid\Filters\CategoryTypeFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class CategoryFiltersLayout extends Selection
{
    public $template = self::TEMPLATE_LINE;

    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            CategoryTypeFilter::class,
            CategorySearchFilter::class,
        ];
    }
}
