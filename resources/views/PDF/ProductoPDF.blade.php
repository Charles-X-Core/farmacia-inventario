<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <style>

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:12px;
            color:#333;
            margin:35px;
        }

        /*ENCABEZADO*/

        .header{
            width:100%;
            border-bottom:2px solid #0d6efd;
            padding-bottom:15px;
            margin-bottom:20px;
        }

        .header table{
            border:none;
            margin:0;
        }

        .header td{
            border:none;
            vertical-align:middle;
        }

        .logo{
            width:80px;
        }

        .title{
            text-align:center;
        }

        .title h1{
            margin:0;
            color:#0d6efd;
            font-size:24px;
        }

        .title h3{
            margin:5px 0 0 0;
            color:#555;
            font-weight:normal;
        }

        /*INFORMACIÓN*/

        .info{
            margin-top:20px;
            margin-bottom:20px;
            line-height:1.8;
        }

        .info strong{
            display:inline-block;
            width:80px;
        }

        /*==============================
        TABLA
        ==============================*/

        table{
            width:100%;
            border-collapse:collapse;
        }

        thead th{
            background:#0d6efd;
            color:#fff;
            padding:10px;
            border:1px solid #dcdcdc;
            text-align:center;
        }

        tbody td{
            border:1px solid #dcdcdc;
            padding:8px;
        }

        tbody tr:nth-child(even){
            background:#f5f5f5;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        /*==============================
        FOOTER
        ==============================*/

        .footer{
            position:fixed;
            bottom:-20px;
            left:0;
            right:0;
            border-top:1px solid #ccc;
            text-align:center;
            font-size:10px;
            color:#777;
            padding-top:8px;
        }

    </style>

</head>

<body>

    {{-- ENCABEZADO --}}

    <div class="header">

        <table>

            <tr>

                <td width="15%">

                    <img
                        src="{{ public_path('images/logo.png') }}"
                        class="logo">

                </td>

                <td width="85%" class="title">

                    <h1>SISTEMA DE INVENTARIO</h1>

                    <h3>Reporte de Productos</h3>

                </td>

            </tr>

        </table>

    </div>

    {{-- INFORMACIÓN --}}

    <div class="info">

        <strong>Usuario:</strong>
        {{ auth()->user()->name }}
        <br>

        <strong>Correo:</strong>
        {{ auth()->user()->email }}
        <br>

        <strong>Fecha:</strong>
        {{ now()->format('d/m/Y H:i') }}
        <br>

        <strong>Registros:</strong>
        {{ $products->count() }}

    </div>

    {{-- TABLA --}}

    <table>

        <thead>

            <tr>

                <th width="15%">Código</th>

                <th>Producto</th>

                <th width="18%">Precio</th>

                <th width="18%">Costo</th>

            </tr>

        </thead>

        <tbody>

            @foreach($products as $product)

            <tr>

                <td class="text-center">
                    {{ $product->code }}
                </td>

                <td>
                    {{ $product->name }}
                </td>

                <td class="text-right">
                    S/. {{ number_format($product->price,2) }}
                </td>

                <td class="text-right">
                    S/. {{ number_format($product->price_cost,2) }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    {{-- FOOTER --}}

    <div class="footer">

        Sistema de Inventario · Documento generado automáticamente

        <br>
        © {{ now()->year }} PharmaTrack

    </div>

    {{-- PAGINACIÓN --}}

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
