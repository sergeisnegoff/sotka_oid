<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Analytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Orchid\Layouts\Dashboard\TopProductsTable;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ProductAnalyticsScreen extends Screen
{
    public function query(Request $request): iterable
    {
        //dd($request->all());
        [$period, $categoryId, $productsSort] = $this->resolveFilters();
        [$from, $to] = $this->resolveDateRange($period);

        return [
            'topProducts' => $this->buildTopProducts($from, $to, $categoryId, $productsSort),
            'filters' => [
                'period' => $period,
                'category_id' => $categoryId,
                'products_sort' => $productsSort,
            ],
            'periodOptions' => [
                '30' => '30 дней',
                '90' => '90 дней',
                '180' => '180 дней',
                '365' => '365 дней',
                'month' => 'Текущий месяц',
                'year' => 'Текущий год',
            ],
            'categoryOptions' => $this->categoryOptions(),
            'productSortOptions' => [
                'qty' => 'По количеству проданного',
                'revenue' => 'По выручке',
            ],
        ];
    }

    public function name(): ?string
    {
        return 'Аналитика товаров';
    }

    public function description(): ?string
    {
        [$period] = $this->resolveFilters();
        [$from, $to] = $this->resolveDateRange($period);

        return 'ТОП-100 товаров за период: '.$from->format('d.m.Y').' - '.$to->format('d.m.Y');
    }

    public function commandBar(): iterable
    {
        [$period] = $this->resolveFilters();
        return [
            DropDown::make('Период')
                ->icon('filter')
                ->list([
                    Link::make($this->periodLabel($period, '30', '30 дней'))->route('platform.analytics.products', ['period' => '30']),
                    Link::make($this->periodLabel($period, '90', '90 дней'))->route('platform.analytics.products', ['period' => '90']),
                    Link::make($this->periodLabel($period, '180', '180 дней'))->route('platform.analytics.products', ['period' => '180']),
                    Link::make($this->periodLabel($period, '365', '365 дней'))->route('platform.analytics.products', ['period' => '365']),
                    Link::make($this->periodLabel($period, 'month', 'месяц'))->route('platform.analytics.products', ['period' => 'month']),
                    Link::make($this->periodLabel($period, 'year', 'год'))->route('platform.analytics.products', ['period' => 'year']),
                ]),

        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::view('orchid.product-analytics.filters'),
            TopProductsTable::class,
        ];
    }

    private function resolveFilters(): array
    {
        $period = (string) request()->get('period', '90');
        if (!in_array($period, ['30', '90', '180', '365', 'month', 'year'], true)) {
            $period = '90';
        }

        $productsSort = (string) request()->get('products_sort', 'qty');
        $productsSort = in_array($productsSort, ['qty', 'revenue'], true) ? $productsSort : 'qty';

        $category = request()->get('category_id');
        $categoryId = is_numeric($category) ? (int) $category : null;
        if ($categoryId !== null && $categoryId <= 0) {
            $categoryId = null;
        }

        return [$period, $categoryId, $productsSort];
    }

    private function resolveDateRange(string $period): array
    {
        $today = now();

        return match ($period) {
            '30' => [$today->copy()->subDays(29)->startOfDay(), $today->copy()->endOfDay()],
            '180' => [$today->copy()->subDays(179)->startOfDay(), $today->copy()->endOfDay()],
            '365' => [$today->copy()->subDays(364)->startOfDay(), $today->copy()->endOfDay()],
            'month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            default => [$today->copy()->subDays(89)->startOfDay(), $today->copy()->endOfDay()],
        };
    }

    private function categoryOptions(): array
    {
        return DB::table('categories')
            ->orderBy('title')
            ->pluck('title', 'id')
            ->prepend('Все категории', '')
            ->toArray();
    }

    private function buildTopProducts(Carbon $from, Carbon $to, ?int $categoryId, string $productsSort): Collection
    {
        $categoryIds = $this->categoryIdsForFilter($categoryId);

        $products = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_products.product_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->when(!empty($categoryIds), fn ($query) => $query->whereIn('products.category_id', $categoryIds))
            ->selectRaw('order_products.product_id as product_id')
            ->selectRaw('COALESCE(products.title, CONCAT("Product #", order_products.product_id)) as title')
            ->selectRaw('COALESCE(SUM(CAST(order_products.qty as DECIMAL(14,2))), 0) as qty_total')
            ->selectRaw('COALESCE(SUM(CAST(order_products.price as DECIMAL(14,2))), 0) as revenue_total')
            ->groupBy('order_products.product_id', 'products.title')
            ->get()
            ->map(static function ($row) {
                $productId = data_get($row, 'product_id') ?? data_get($row, 'order_products.product_id');

                return [
                    'key' => 'order-'.($productId ?? 'unknown'),
                    'title' => $row->title,
                    'qty_total' => (float) $row->qty_total,
                    'revenue_total' => (float) $row->revenue_total,
                ];
            });

        $sortField = $productsSort === 'revenue' ? 'revenue_total' : 'qty_total';

        return $products
            ->sortByDesc($sortField)
            ->take(100)
            ->values()
            ->map(static fn (array $row, int $index) => new Repository($row + ['rank' => $index + 1]));
    }

    private function categoryIdsForFilter(?int $categoryId): array
    {
        if ($categoryId === null) {
            return [];
        }

        $collected = collect([$categoryId]);
        $frontier = collect([$categoryId]);

        while ($frontier->isNotEmpty()) {
            $children = DB::table('categories')
                ->whereIn('parent_id', $frontier->all())
                ->pluck('id');

            $frontier = $children->diff($collected)->values();
            $collected = $collected->merge($children)->unique()->values();
        }

        return $collected->all();
    }

    private function periodLabel(string $currentPeriod, string $period, string $label): string
    {
        return ($currentPeriod === $period ? '[x] ' : '[ ] ').$label;
    }
}
