<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 35px;
        }

        .header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo {
            width: 70px;
        }

        .title {
            text-align: center;
        }

        .title h1 {
            margin: 0;
            color: #0d6efd;
            font-size: 22px;
        }

        .title h3 {
            margin-top: 5px;
            color: #666;
            font-weight: normal;
        }

        .info {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .info table {
            width: 100%;
            border: none;
        }

        .info td {
            border: none;
            padding: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #0d6efd;
            color: white;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        tbody td {
            border: 1px solid #ddd;
            padding: 7px;
        }

        tbody tr:nth-child(even) {
            background: #f5f5f5;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .footer {

            position: fixed;

            bottom: -10px;

            left: 0;

            right: 0;

            text-align: center;

            color: #777;

            font-size: 10px;

            border-top: 1px solid #ccc;

            padding-top: 8px;

        }
    </style>

</head>

<body>

    <div class="header">

        <table width="100%">

            <tr>

                <td width="15%" style="border:none;">

                    <img src="{{ public_path('images/logo.png') }}" class="logo">

                </td>

                <td width="85%" class="title" style="border:none;">

                    <h1>SISTEMA DE INVENTARIO</h1>

                    <h3>Reporte de Movimientos de Almacén</h3>

                </td>

            </tr>

        </table>

    </div>

    <div class="info">

        <table>

            <tr>

                <td><strong>Usuario:</strong></td>

                <td>{{ auth()->user()->name }}</td>

                <td><strong>Fecha:</strong></td>

                <td>{{ now()->format('d/m/Y H:i') }}</td>

            </tr>

            <tr>

                <td><strong>Correo:</strong></td>

                <td>{{ auth()->user()->email }}</td>

                <td><strong>Total:</strong></td>

                <td>{{ $movements->count() }} movimientos</td>

            </tr>

        </table>

    </div>

    <table>

        <thead>

            <tr>

                <th>Fecha</th>

                <th>Serie</th>

                <th>Correlativo</th>

                <th>Producto</th>

                <th>Tipo</th>

                <th>Cantidad</th>

                <th>Lote</th>

            </tr>

        </thead>

        <tbody>

            @foreach ($movements as $movement)
                <tr>

                    <td class="center">
                        {{ $movement->created_at->format('d/m/Y') }}
                    </td>

                    <td class="center">
                        {{ $movement->serie }}
                    </td>

                    <td class="center">
                        {{ $movement->number }}
                    </td>

                    <td>

                        @foreach ($movement->movementDetails as $detail)
                            {{ $detail->product->name }}<br>
                        @endforeach

                    </td>

                    <td class="center">

                        @if ($movement->movement_type_id == 1)
                            Entrada
                        @elseif($movement->movement_type_id == 2)
                            Salida
                        @else
                            Venta
                        @endif

                    </td>

                    <td class="center">

                        @foreach ($movement->movementDetails as $detail)
                            {{ $detail->quantity_in + $detail->quantity_out }}
                        @endforeach

                    </td>

                    <td>

                        @foreach ($movement->allotments as $lot)
                            {{ $lot->code }}<br>
                        @endforeach

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

    <div class="footer">

        Sistema de Inventario • Documento generado automáticamente

        <br>

        © {{ now()->year }} CorpIB Group

    </div>

    <script type="text/php">

if(isset($pdf)){

$pdf->page_text(

500,

815,

"Página {PAGE_NUM} de {PAGE_COUNT}",

null,

9

);

}

</script>

</body>

</html>
