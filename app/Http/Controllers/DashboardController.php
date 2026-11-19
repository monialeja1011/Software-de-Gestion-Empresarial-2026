<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $salesMonthQuery = Sale::where('active', true)
            ->where('status', '!=', 'anulada')
            ->whereBetween('sale_date', [$monthStart, $monthEnd]);

        $purchasesMonthQuery = Purchase::where('active', true)
            ->where('status', '!=', 'anulada')
            ->whereBetween('purchase_date', [$monthStart, $monthEnd]);

        $topProducts = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'products.id', '=', 'sale_details.product_id')
            ->where('sales.active', true)
            ->where('sales.status', '!=', 'anulada')
            ->whereNull('sales.deleted_at')
            ->select(
                'products.name',
                DB::raw('SUM(sale_details.quantity) as quantity_sold')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'salesMonth' => (clone $salesMonthQuery)->sum('total'),
            'purchasesMonth' => (clone $purchasesMonthQuery)->sum('total'),
            'clientsCount' => Client::where('active', true)->count(),
            'productsCount' => Product::where('active', true)->count(),

            'lowStock' => Product::where('active', true)
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->limit(8)
                ->get(),

            'latestSales' => Sale::with(['client', 'paymentMethod'])
                ->where('active', true)
                ->latest('id')
                ->limit(5)
                ->get(),

            'topProducts' => $topProducts,
            'charts' => $this->charts(),
        ]);
    }

    private function charts(): array
    {
        $charts = [];

        // Barras: ventas y compras de los últimos seis meses.
        $months = collect(range(5, 0))
            ->map(fn ($offset) => now()->startOfMonth()->subMonths($offset));

        $from = $months->first()->copy()->startOfMonth();

        $salesByMonth = Sale::where('active', true)
            ->where('status', '!=', 'anulada')
            ->whereDate('sale_date', '>=', $from)
            ->selectRaw("DATE_FORMAT(sale_date, '%Y-%m') AS month_key, SUM(total) AS amount")
            ->groupBy('month_key')
            ->pluck('amount', 'month_key');

        $purchasesByMonth = Purchase::where('active', true)
            ->where('status', '!=', 'anulada')
            ->whereDate('purchase_date', '>=', $from)
            ->selectRaw("DATE_FORMAT(purchase_date, '%Y-%m') AS month_key, SUM(total) AS amount")
            ->groupBy('month_key')
            ->pluck('amount', 'month_key');

        $charts[] = [
            'id' => 'sales-purchases',
            'title' => 'Ventas frente a compras',
            'subtitle' => 'Últimos seis meses',
            'type' => 'bar',
            'money' => true,
            'wide' => true,
            'labels' => $months->map(fn ($month) => ucfirst($month->locale('es')->isoFormat('MMM YY')))->all(),
            'datasets' => [
                ['label' => 'Ventas', 'data' => $months->map(fn ($month) => (float) ($salesByMonth[$month->format('Y-m')] ?? 0))->all()],
                ['label' => 'Compras', 'data' => $months->map(fn ($month) => (float) ($purchasesByMonth[$month->format('Y-m')] ?? 0))->all()],
            ],
        ];

        // Línea: ventas diarias de los últimos treinta días.
        $days = collect(range(29, 0))
            ->map(fn ($offset) => now()->startOfDay()->subDays($offset));

        $dailySales = Sale::where('active', true)
            ->where('status', '!=', 'anulada')
            ->whereDate('sale_date', '>=', $days->first()->toDateString())
            ->selectRaw('DATE(sale_date) AS sale_day, SUM(total) AS amount')
            ->groupBy('sale_day')
            ->pluck('amount', 'sale_day');

        $charts[] = [
            'id' => 'daily-sales',
            'title' => 'Ventas diarias',
            'subtitle' => 'Últimos treinta días',
            'type' => 'line',
            'money' => true,
            'labels' => $days->map(fn (Carbon $day) => $day->format('d/m'))->all(),
            'datasets' => [
                ['label' => 'Ventas', 'data' => $days->map(fn ($day) => (float) ($dailySales[$day->toDateString()] ?? 0))->all()],
            ],
        ];

        // Dona: unidades disponibles agrupadas por categoría.
        $stockByCategory = Product::where('active', true)
            ->selectRaw('category_id, SUM(stock) AS units')
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get();

        $charts[] = [
            'id' => 'stock-category',
            'title' => 'Inventario por categoría',
            'subtitle' => 'Unidades disponibles',
            'type' => 'doughnut',
            'labels' => $stockByCategory->map(fn ($row) => $row->category?->name ?? 'Sin categoría')->all(),
            'datasets' => [
                ['label' => 'Unidades', 'data' => $stockByCategory->map(fn ($row) => (int) $row->units)->all()],
            ],
        ];

        // Dona: compras agrupadas por estado actual.
        $purchasesByStatus = Purchase::query()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $charts[] = [
            'id' => 'purchase-status',
            'title' => 'Órdenes de compra',
            'subtitle' => 'Distribución por estado',
            'type' => 'doughnut',
            'labels' => $purchasesByStatus->keys()
                ->map(fn ($status) => ucfirst(str_replace('_', ' ', $status)))
                ->all(),
            'datasets' => [
                ['label' => 'Órdenes', 'data' => $purchasesByStatus->values()->map(fn ($total) => (int) $total)->all()],
            ],
        ];

        // Barras horizontales: cinco productos más vendidos en el mes.
        $topProducts = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'products.id', '=', 'sale_details.product_id')
            ->where('sales.active', true)
            ->where('sales.status', '!=', 'anulada')
            ->whereNull('sales.deleted_at')
            ->whereBetween('sales.sale_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->select('products.name', DB::raw('SUM(sale_details.quantity) AS quantity_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();

        $charts[] = [
            'id' => 'top-products-month',
            'title' => 'Productos más vendidos del mes',
            'subtitle' => 'Unidades vendidas',
            'type' => 'bar',
            'horizontal' => true,
            'labels' => $topProducts->pluck('name')->all(),
            'datasets' => [
                ['label' => 'Unidades', 'data' => $topProducts->pluck('quantity_sold')->map(fn ($quantity) => (int) $quantity)->all()],
            ],
        ];

        return collect($charts)->map(function ($chart) {
            $chart['empty'] = collect($chart['datasets'])
                ->flatMap(fn ($dataset) => $dataset['data'])
                ->sum() == 0;

            return $chart;
        })->all();
    }
}
