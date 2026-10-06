<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $monthStart = now()->startOfMonth();

        return view('dashboard', [
            'salesMonth'     => Sale::where('active', true)->where('sale_date', '>=', $monthStart)->sum('total'),
            'purchasesMonth' => Purchase::where('active', true)->where('purchase_date', '>=', $monthStart)->sum('total'),
            'clientsCount'   => Client::where('active', true)->count(),
            'productsCount'  => Product::where('active', true)->count(),
            'lowStock'       => Product::where('active', true)->where('stock', '<=', 5)->orderBy('stock')->limit(8)->get(),
            'latestSales'    => Sale::with(['client', 'paymentMethod'])->latest('id')->limit(5)->get(),
        ]);
    }
}
