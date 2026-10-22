<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden de compra {{ $purchase->order_number }}</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h1, h2, p { margin: 0; }

        .header {
            padding-bottom: 16px;
            border-bottom: 2px solid #db2777;
        }

        .brand {
            color: #be185d;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .title {
            margin-top: 8px;
            color: #111827;
            font-size: 22px;
        }

        .subtitle {
            margin-top: 5px;
            color: #6b7280;
        }

        .panel {
            margin-top: 16px;
            padding: 12px;
            border: 1px solid #f3d2e2;
            border-radius: 8px;
        }

        .info {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            width: 50%;
            padding: 6px;
            vertical-align: top;
        }

        .label {
            display: block;
            margin-bottom: 3px;
            color: #6b7280;
        }

        .value {
            color: #111827;
            font-weight: bold;
        }

        h2 {
            margin: 20px 0 8px;
            color: #831843;
            font-size: 14px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .items th {
            padding: 8px 5px;
            background: #fce7f3;
            color: #831843;
            text-align: left;
        }

        .items td {
            padding: 7px 5px;
            border-bottom: 1px solid #f3d2e2;
        }

        .right { text-align: right !important; }
        .center { text-align: center !important; }

        .totals {
            width: 45%;
            margin: 12px 0 0 auto;
            border-collapse: collapse;
        }

        .totals td {
            padding: 5px;
        }

        .grand-total {
            border-top: 1px solid #db2777;
            color: #831843;
            font-size: 13px;
            font-weight: bold;
        }

        .note {
            margin-top: 20px;
            padding: 10px;
            background: #fdf2f8;
            color: #831843;
            font-size: 9px;
        }

        .observations {
            margin-top: 14px;
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <header class="header">
        <p class="brand">Sistema de Gestión Empresarial</p>
        <h1 class="title">
            Orden de compra {{ $purchase->order_number ?? 'OC-'.str_pad((string) $purchase->id, 6, '0', STR_PAD_LEFT) }}
        </h1>
    </header>

    <section class="panel">
        <table class="info">
            <tr>
                <td>
                    <span class="label">Proveedor</span>
                    <span class="value">{{ $purchase->provider?->name ?? '—' }}</span>
                </td>
                <td>
                    <span class="label">Estado de la orden</span>
                    <span class="value">{{ ucfirst(str_replace('_', ' ', $purchase->status ?? '—')) }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Fecha de la orden</span>
                    <span class="value">{{ $purchase->purchase_date?->format('d/m/Y') ?? '—' }}</span>
                </td>
                <td>
                    <span class="label">Llegada prometida</span>
                    <span class="value">{{ $purchase->expected_delivery_date?->format('d/m/Y') ?? 'No indicada' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Medio de pago</span>
                    <span class="value">{{ $purchase->paymentMethod?->name ?? '—' }}</span>
                </td>
                <td>
                    <span class="label">Plazo / vencimiento</span>
                    <span class="value">
                        {{ $purchase->payment_term_days ?? 0 }} días
                        @if ($purchase->due_date)
                            — {{ $purchase->due_date->format('d/m/Y') }}
                        @endif
                    </span>
                </td>
            </tr>
        </table>
    </section>

    <h2>Productos solicitados</h2>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="right">Cantidad</th>
                <th class="right">Costo cotizado</th>
                <th class="center">IVA</th>
                <th class="right">Base</th>
                <th class="right">Valor IVA</th>
                <th class="right">Total línea</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->details as $detail)
                <tr>
                    <td>{{ $detail->product?->name ?? 'Producto eliminado' }}</td>
                    <td class="right">{{ $detail->quantity }}</td>
                    <td class="right">${{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="center">
                        {{ number_format($detail->iva_percentage, 2, ',', '.') }}%
                        <br>
                        {{ $detail->iva_included ? 'Incluido' : 'Adicional' }}
                    </td>
                    <td class="right">${{ number_format($detail->base_subtotal, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format($detail->iva_amount, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $baseTotal = $purchase->details->sum(fn ($detail) => (float) $detail->base_subtotal);
        $ivaTotal = $purchase->details->sum(fn ($detail) => (float) $detail->iva_amount);
    @endphp

    <table class="totals">
        <tr>
            <td>Base de compra</td>
            <td class="right">${{ number_format($baseTotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>IVA</td>
            <td class="right">${{ number_format($ivaTotal, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total de la orden</td>
            <td class="right">${{ number_format($purchase->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if ($purchase->observations)
        <section class="observations">
            <strong>Observaciones:</strong>
            <p>{{ $purchase->observations }}</p>
        </section>
    @endif

</body>
</html>