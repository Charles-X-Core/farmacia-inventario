<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:12px;
    color:#333;
    margin:40px;
}

.header{
    border-bottom:3px solid #0d6efd;
    padding-bottom:12px;
    margin-bottom:20px;
}

.title{
    text-align:center;
    color:#0d6efd;
    font-size:22px;
    font-weight:bold;
}

.subtitle{
    text-align:center;
    color:#777;
    margin-top:4px;
}

.info{
    margin-top:15px;
    line-height:1.8;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th{
    background:#0d6efd;
    color:white;
    padding:8px;
    border:1px solid #dcdcdc;
    text-align:center;
}

td{
    border:1px solid #dcdcdc;
    padding:7px;
}

tr:nth-child(even){
    background:#f5f5f5;
}

.footer{
    position:fixed;
    bottom:-10px;
    left:0;
    right:0;
    text-align:center;
    font-size:10px;
    color:#777;
    border-top:1px solid #ccc;
    padding-top:6px;
}

.logo{
    width:70px;
    margin-bottom:10px;
}

</style>

</head>

<body>

<div class="header">

    {{-- Logo opcional --}}
    {{-- <img class="logo" src="{{ public_path('images/logo.png') }}"> --}}

    <div class="title">
        REPORTE DE CONTROL DE LOTES
    </div>

    <div class="subtitle">
        Sistema de Inventario
    </div>

</div>

<div class="info">

    <strong>Usuario:</strong>
    {{ auth()->user()->name }}

    <br>

    <strong>Correo:</strong>
    {{ auth()->user()->email }}

    <br>

    <strong>Fecha:</strong>
    {{ now()->format('d/m/Y H:i') }}

</div>

<table>
    <thead>
        <tr>
            <th>F. Ingreso</th>       <th>Serie-Corr.</th>    <th>Lote</th>
            <th>Producto</th>
            <th>Vencimiento</th>
            <th>Días Restantes</th> <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allotments as $lote)
        <tr>
            <td>{{ $lote->created_at->format('d/m/Y') }}</td>
            <td>{{ $lote->movement->serie }}-{{ $lote->movement->number }}</td>
            <td>{{ $lote->code }}</td>
            <td>{{ optional($lote->allotmentDetails->first()?->product)->name }}</td>
            <td>{{ $lote->expiration_date ? \Carbon\Carbon::parse($lote->expiration_date)->format('d/m/Y') : 'N/A' }}</td>

            {{-- Cálculo de días restantes --}}
            <td>
                @if($lote->expiration_date)
                    {{ \Carbon\Carbon::parse($lote->expiration_date)->diffInDays(now(), false) }} días
                @else
                    -
                @endif
            </td>

            <td>{{ $lote->allotmentDetails->sum('quantity') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">

Documento generado automáticamente por el Sistema de Inventario

<br>

© {{ date('Y') }}

</div>

</body>

</html>
