<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Models\User;
use App\Orchid\Layouts\Dashboard\ActivityBarChart;
use App\Orchid\Layouts\Dashboard\RevenueLineChart;
use App\Orchid\Layouts\Dashboard\RevenueSplitPieChart;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    public function query(): iterable
    {
        [$period] = $this->resolveFilters();
        [$from, $to] = $this->resolveDateRange($period);
        [$previousFrom, $previousTo] = $this->resolvePreviousDateRange($from, $to);

        $months = $this->monthsForRange($from, $to);
        $monthKeys = $months->map(static fn (Carbon $month) => $month->format('Y-m'));
        $labels = $months->map(static fn (Carbon $month) => $month->format('M Y'))->values()->all();

        $ordersRevenueByMonth = $this->ordersRevenueByMonth($from, $to);
        $preordersRevenueByMonth = $this->preordersRevenueByMonth($from, $to);
        $buyersByMonth = $this->countByMonth('users', $from, $to);
        $productsByMonth = $this->countByMonth('products', $from, $to);
        $ordersByMonth = $this->countByMonth('orders', $from, $to);
        $preordersByMonth = $this->countByMonth('preorder_checkouts', $from, $to);

        $buyersTotal = User::count();
        $buyersCurrentPeriod = User::whereBetween('created_at', [$from, $to])->count();
        $buyersPreviousPeriod = User::whereBetween('created_at', [$previousFrom, $previousTo])->count();

        $productsInStock = Product::query()->where('total', '>', 0)->count();
        $productsCurrentPeriod = Product::whereBetween('created_at', [$from, $to])->count();
        $productsPreviousPeriod = Product::whereBetween('created_at', [$previousFrom, $previousTo])->count();

        $ordersCurrentPeriodRevenue = (float) $ordersRevenueByMonth->sum();
        $ordersPreviousPeriodRevenue = (float) $this->ordersRevenueByMonth($previousFrom, $previousTo)->sum();

        $preordersCurrentPeriodRevenue = (float) $preordersRevenueByMonth->sum();
        $preordersPreviousPeriodRevenue = (float) $this->preordersRevenueByMonth($previousFrom, $previousTo)->sum();

        return [
            'metrics' => [
                'buyers' => [
                    'value' => number_format($buyersTotal, 0, '.', ' '),
                    'diff'  => $this->calculateDiff($buyersCurrentPeriod, $buyersPreviousPeriod),
                ],
                'products' => [
                    'value' => number_format($productsInStock, 0, '.', ' '),
                    'diff'  => $this->calculateDiff($productsCurrentPeriod, $productsPreviousPeriod),
                ],
                'orders' => [
                    'value' => $this->formatMoney($ordersCurrentPeriodRevenue),
                    'diff'  => $this->calculateDiff($ordersCurrentPeriodRevenue, $ordersPreviousPeriodRevenue),
                ],
                'preorders' => [
                    'value' => $this->formatMoney($preordersCurrentPeriodRevenue),
                    'diff'  => $this->calculateDiff($preordersCurrentPeriodRevenue, $preordersPreviousPeriodRevenue),
                ],
            ],
            'revenueTrend' => [
                [
                    'name'   => 'Заказы',
                    'values' => $monthKeys->map(static fn (string $key) => round((float) ($ordersRevenueByMonth[$key] ?? 0), 2))->all(),
                    'labels' => $labels,
                ],
                [
                    'name'   => 'Предзаказы',
                    'values' => $monthKeys->map(static fn (string $key) => round((float) ($preordersRevenueByMonth[$key] ?? 0), 2))->all(),
                    'labels' => $labels,
                ],
            ],
            'activityTrend' => [
                [
                    'name'   => 'Новые покупатели',
                    'values' => $monthKeys->map(static fn (string $key) => (int) ($buyersByMonth[$key] ?? 0))->all(),
                    'labels' => $labels,
                ],
                [
                    'name'   => 'Новые товары',
                    'values' => $monthKeys->map(static fn (string $key) => (int) ($productsByMonth[$key] ?? 0))->all(),
                    'labels' => $labels,
                ],
                [
                    'name'   => 'Заказов',
                    'values' => $monthKeys->map(static fn (string $key) => (int) ($ordersByMonth[$key] ?? 0))->all(),
                    'labels' => $labels,
                ],
                [
                    'name'   => 'Предзаказов',
                    'values' => $monthKeys->map(static fn (string $key) => (int) ($preordersByMonth[$key] ?? 0))->all(),
                    'labels' => $labels,
                ],
            ],
            'revenueSplit' => [
                [
                    'name'   => 'Revenue split',
                    'values' => [round($ordersCurrentPeriodRevenue, 2), round($preordersCurrentPeriodRevenue, 2)],
                    'labels' => ['Заказы', 'Предзаказы'],
                ],
            ],
        ];
    }

    public function name(): ?string
    {
        return 'Достижения компании';
    }

    public function description(): ?string
    {
        [$period] = $this->resolveFilters();
        [$from, $to] = $this->resolveDateRange($period);

        return 'Период: '.$from->format('d.m.Y').' - '.$to->format('d.m.Y');
    }

    public function commandBar(): iterable
    {
        [$period] = $this->resolveFilters();

        return [
            DropDown::make('Период')
                ->icon('filter')
                ->list([
                    Link::make($this->periodLabel($period, '30', '30 дней'))->route('platform.main', ['period' => '30']),
                    Link::make($this->periodLabel($period, '90', '90 дней'))->route('platform.main', ['period' => '90']),
                    Link::make($this->periodLabel($period, '180', '180 дней'))->route('platform.main', ['period' => '180']),
                    Link::make($this->periodLabel($period, '365', '365 дней'))->route('platform.main', ['period' => '365']),
                    Link::make($this->periodLabel($period, 'month', 'месяц'))->route('platform.main', ['period' => 'month']),
                    Link::make($this->periodLabel($period, 'year', 'год'))->route('platform.main', ['period' => 'year']),
                ]),

        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Всего покупателей' => 'metrics.buyers',
                'Товаров в наличии' => 'metrics.products',
                'Выручка заказы' => 'metrics.orders',
                'Выручка предзаказы' => 'metrics.preorders',
            ]),
            Layout::columns([
                RevenueLineChart::make('revenueTrend', 'Выручка по месяцам')
                    ->description('Выручка от заказов и предзаказов за выбранный период'),
                RevenueSplitPieChart::make('revenueSplit', 'Выручка по типам заказов')
                    ->description('Заказы и предзаказы.'),
            ]),
            ActivityBarChart::make('activityTrend', 'Активность')
                ->description('Покупатели, товары, заказы и предзаказы за период'),
        ];
    }

    public function applyFilter($period = 90)
    {
        return redirect()->route('platform.main', ['period' => $period]);
    }

    private function resolveFilters(): array
    {
        $period = (string) request()->get('period', '90');
        if (!in_array($period, ['30', '90', '180', '365', 'month', 'year'], true)) {
            $period = '90';
        }

        return [$period];
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

    private function resolvePreviousDateRange(Carbon $from, Carbon $to): array
    {
        $daysInRange = $from->diffInDays($to) + 1;
        $previousTo = $from->copy()->subSecond();
        $previousFrom = $previousTo->copy()->subDays($daysInRange - 1)->startOfDay();

        return [$previousFrom, $previousTo];
    }

    private function monthsForRange(Carbon $from, Carbon $to): Collection
    {
        $cursor = $from->copy()->startOfMonth();
        $end = $to->copy()->startOfMonth();
        $months = collect();

        while ($cursor->lte($end)) {
            $months->push($cursor->copy());
            $cursor->addMonthNoOverflow();
        }

        return $months;
    }

    private function ordersRevenueByMonth(Carbon $from, Carbon $to): Collection
    {
        return DB::table('orders')
            ->leftJoin('order_products', 'orders.id', '=', 'order_products.order_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') as month_key")
            ->selectRaw('COALESCE(SUM(CAST(order_products.price as DECIMAL(14,2))), 0) as total_amount')
            ->groupBy('month_key')
            ->pluck('total_amount', 'month_key');
    }

    private function preordersRevenueByMonth(Carbon $from, Carbon $to): Collection
    {
        return DB::table('preorder_checkouts')
            ->leftJoin('preorder_checkout_products', 'preorder_checkouts.id', '=', 'preorder_checkout_products.preorder_checkout_id')
            ->leftJoin('preorder_products', 'preorder_products.id', '=', 'preorder_checkout_products.preorder_product_id')
            ->whereBetween('preorder_checkouts.created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(preorder_checkouts.created_at, '%Y-%m') as month_key")
            ->selectRaw("COALESCE(SUM(preorder_checkout_products.qty * CAST(REPLACE(preorder_products.price, ',', '.') as DECIMAL(14,2))), 0) as total_amount")
            ->groupBy('month_key')
            ->pluck('total_amount', 'month_key');
    }

    private function countByMonth(string $table, Carbon $from, Carbon $to): Collection
    {
        return DB::table($table)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key")
            ->selectRaw('COUNT(*) as total_amount')
            ->groupBy('month_key')
            ->pluck('total_amount', 'month_key');
    }

    private function calculateDiff(float|int $current, float|int $previous): float
    {
        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / abs($previous)) * 100, 2);
    }

    private function formatMoney(float $value): string
    {
        return number_format($value, 0, '.', ' ').' RUB';
    }

    private function periodLabel(string $currentPeriod, string $period, string $label): string
    {
        return ($currentPeriod === $period ? '[x] ' : '[ ] ').$label;
    }
}
