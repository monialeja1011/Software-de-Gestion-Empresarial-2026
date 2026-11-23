<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Exports\ProductsTemplateExport;
use App\Exports\SalesExport;
use App\Imports\ProductsImport;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function exportProducts(Request $request)
    {
        $filters = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'categoria_id' => ['nullable', 'integer', 'exists:categories,id'],
            'estado' => ['nullable', 'in:activo,inactivo'],
        ]);

        return Excel::download(
            new ProductsExport($filters),
            'productos_'.now()->format('Ymd_His').'.xlsx'
        );
    }

    public function importForm(): View
    {
        return view('products.import');
    }

    public function template()
    {
        return Excel::download(new ProductsTemplateExport, 'plantilla_productos.xlsx');
    }

    public function import(Request $request, InventoryService $inventory): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        $import = new ProductsImport($request->user()->id, $inventory);
        Excel::import($import, $data['file']);

        return redirect()
            ->route('products.import')
            ->with('import_summary', [
                'created' => $import->createdCount(),
                'updated' => $import->updatedCount(),
            ])
            ->with('failures', $import->failures());
    }

    public function exportSales(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return Excel::download(
            new SalesExport($filters),
            'ventas_'.now()->format('Ymd_His').'.xlsx'
        );
    }
}
