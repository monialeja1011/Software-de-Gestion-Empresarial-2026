<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Kardex de {{ $product->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 24px;
        }

        body {
            color: #1e293b;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        h1 {
            color: {{ $branding->accentHex() }};
            font-size: 18px;
            margin: 0 0 5px;
        }

        .muted {
            color: #64748b;
        }

        .summary {
            border: 1px solid #cbd5e1;
            margin: 12px 0;
            padding: 9px;
        }

        .summary td {
            padding: 4px 8px;
        }

        table.movements {
            border-collapse: collapse;
            width: 100%;
        }

        .movements th {
            background: {{ $branding->accentHex() }};
            color: #ffffff;
            padding: 7px 5px;
            text-align: left;
        }

        .movements td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 5px;
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .entry {
            color: #047857;
        }

        .exit {
            color: #be123c;
        }
    </style>
</head>
<body>
    @include('pdf.company-header')

    <h1>Kardex de inventario</h1>
    <div class="muted">{{ $product->name }} · Producto #{{ $product->id }}</div>

    <table class="summary">
        <tr>
            <td><strong>Stock actual:</strong> {{ $product->stock }}</td>
            <td><strong>Entradas en el periodo:</strong> {{ $totals['entradas'] }}</td>
            <td><strong>Salidas en el periodo:</strong> {{ $totals['salidas'] }}</td>
            <td><strong>Desde:</strong> {{ $filters['from'] ?? 'inicio' }}</td>
            <td><strong>Hasta:</strong> {{ $filters['to'] ?? 'hoy' }}</td>
        </tr>
    </table>

    <table class="movements">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>Documento</th>
                <th class="right">Entrada</th>
                <th class="right">Salida</th>
                <th class="right">Saldo</th>
                <th class="right">Costo unitario</th>
                <th>Usuario</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
                @php
                    $reference = $movement->reference;
                    $document = $reference?->invoice_number
                        ?? $reference?->order_number
                        ?? ($reference ? class_basename($reference).' #'.$reference->getKey() : '—');
                    $concept = $movement->conceptLabel();
                @endphp
                <tr>
                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $concept }}</td>
                    <td>{{ $document }}</td>
                    <td class="right entry">{{ $movement->type === 'entrada' ? $movement->quantity : '' }}</td>
                    <td class="right exit">{{ $movement->type === 'salida' ? $movement->quantity : '' }}</td>
                    <td class="right">{{ $movement->balance }}</td>
                    <td class="right">{{ $movement->unit_cost !== null ? '$'.number_format((float) $movement->unit_cost, 2, ',', '.') : '—' }}</td>
                    <td>{{ $movement->user?->name ?? 'Sistema' }}</td>
                    <td>{{ $movement->notes ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No hay movimientos para los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
