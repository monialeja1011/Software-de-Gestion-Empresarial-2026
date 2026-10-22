<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de venta {{ $sale->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #334155;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h1 {
            margin: 0;
            color: #be185d;
            font-size: 21px;
        }

        h2 {
            margin: 18px 0 8px;
            color: #9d174d;
            font-size: 14px;
        }

        .muted { color: #64748b; }

        .box {
            margin: 14px 0;
            padding: 12px;
            border: 1px solid #fbcfe8;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
        }

        .details td {
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }

        table.items {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
            font-size: 9px;
        }

        .items th {
            background: #fce7f3;
            color: #831843;
            text-align: left;
        }

        .items th,
        .items td {
            border: 1px solid #e2e8f0;
            padding: 7px 5px;
        }

        .right { text-align: right; }
        .center { text-align: center; }

        table.totals {
            width: 45%;
            margin: 12px 0 0 auto;
            border-collapse: collapse;
        }

        .totals td { padding: 5px; }

        .total {
            border-top: 1px solid #db2777;
            color: #9d174d;
            font-size: 14px;
            font-weight: bold;
        }

        .notice {
            margin-top: 20px;
            padding: 10px;
            background: #fff1f2;
        }

        .cancelled {
            margin-top: 10px;
            color: #b91c1c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>COMPROBANTE INTERNO DE VENTA</h1>
    <p class="muted">{{ $sale->invoice_number ?? 'Venta '.$sale->id }}</p>

    @if ($sale->status === 'anulada')
        <p class="cancelled">VENTA ANULADA</p>
    @endif

    <div class="box">
        <h2>Datos de la venta</h2>
        <table class="details">
            <tr>
                <td><strong>Cliente:</strong> {{ $sale->client?->name ?? 'No disponible' }}</td>
                <td><strong>Fecha:</strong> {{ $sale->sale_date?->format('d/m/Y') ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>Medio de pago:</strong> {{ $sale->paymentMethod?->name ?? '—' }}</td>
                <td><strong>Atendió:</strong> {{ $sale->user?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Estado:</strong> {{ ucfirst($sale->status ?? 'registrada') }}
                </td>
            </tr>
        </table>
    </div>

    <h2>Productos</h2>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="right">Cantidad</th>
                <th class="right">Precio unitario<br>(IVA incluido)</th>
                <th class="right">Base sin IVA</th>
                <th class="center">Tarifa IVA</th>
                <th class="right">IVA incluido</th>
                <th class="right">Total línea</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->details as $detail)
                @php
                    $lineTotal = (float) $detail->subtotal + (float) $detail->iva_amount;
                @endphp
                <tr>
                    <td>{{ $detail->product?->name ?? 'Producto no disponible' }}</td>
                    <td class="right">{{ $detail->quantity }}</td>
                    <td class="right">${{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    <td class="center">
                        @if ((float) $detail->iva_percentage > 0)
                            {{ number_format($detail->iva_percentage, 2, ',', '.') }}%
                        @else
                            No aplica
                        @endif
                    </td>
                    <td class="right">${{ number_format($detail->iva_amount, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format($lineTotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal antes de IVA</td>
            <td class="right">${{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>IVA incluido</td>
            <td class="right">${{ number_format($sale->tax, 0, ',', '.') }}</td>
        </tr>
        <tr class="total">
            <td>Total pagado</td>
            <td class="right">${{ number_format($sale->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if ($sale->notes)
        <div class="notice">
            <strong>Notas:</strong><br>
            {{ $sale->notes }}
        </div>
    @endif

</body>
</html>