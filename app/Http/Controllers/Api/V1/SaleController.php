<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\Api\V1\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;

class SaleController extends Controller
{
    public function store(StoreSaleRequest $request, SaleService $service)
    {
        $sale = $service->register(
            $request->validated(),
            (int) $request->user()->id
        );

        $sale->load(['client', 'paymentMethod', 'user', 'details.product']);

        return (new SaleResource($sale))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Sale $sale)
    {
        $sale->load(['client', 'paymentMethod', 'user', 'details.product']);

        return new SaleResource($sale);
    }
}
