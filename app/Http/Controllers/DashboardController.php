<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        return view('dashboard', [
            'salesMonth' => Sale::where('active', true)
                ->where('status', '!=', 'anulada')
                ->whereBetween('sale_date', [$monthStart, $monthEnd])
                ->sum('total'),

            'purchasesMonth' => Purchase::where('active', true)
                ->whereBetween('purchase_date', [$monthStart, $monthEnd])
                ->sum('total'),

            'clientsCount' => Client::where('active', true)->count(),

            'productsCount' => Product::where('active', true)->count(),

            'lowStock' => Product::where('active', true)
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->limit(8)
                ->get(),

            'latestSales' => Sale::with(['client', 'paymentMethod'])
                ->latest('id')
                ->limit(5)
                ->get(),

            'topProducts' => DB::table('sale_details')
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
                ->get(),
        ]);
    }
}