<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 40px; }
        .header { border-bottom: 3px solid #198754; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; color: #198754; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #198754; color: white; padding: 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">REPORTE DE VENTAS</div>
    </div>

    <div class="info">
        <strong>Usuario:</strong> {{ auth()->user()->name }} <br>
        <strong>Correo:</strong> {{ auth()->user()->email }} <br>
        <strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }} <br>

        <strong>Total ventas registradas:</strong> {{ $sales->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                @foreach($sale->details as $detail)
                <tr>
                    <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                    <td>{{ $sale->client->name ?? 'Consumidor final' }}</td>
                    <td>{{ $detail->product->name ?? 'N/A' }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td class="text-right">S/. {{ number_format($detail->subtotal, 2) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
