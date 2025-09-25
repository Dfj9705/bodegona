<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @page {
        header: page-header;
        footer: page-footer;
        }
        td {
            text-align: center
        }
    </style>
</head>
<body>
    <htmlpageheader name="page-header">
       {DATE j-m-Y}
    </htmlpageheader>
    <htmlpagefooter name="page-footer">
        PÁGINA {PAGENO} DE {nbpg}
    </htmlpagefooter>

    <h1 style="text-align: center">COMPROBANTE</h1>
    <p>Despachado por: {{ $sale->user->name }}</p>
    <p>Fecha de la operación: {{ date('d-m-Y H:i', strtotime($sale->date)) }}</p>

    <h2 style="text-align: center">Detalle de productos</h2>
    <table style="width: 100%; border-collapse: collapse;" border="1">
        <thead>
            <tr>
                <th style="width: 10%">NO. </th>
                <th style="width: 50%">NOMBRE</th>
                <th >CANTIDAD</th>
                <th>PRECIO</th>
                <th>SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($sale->details as $detail)
                @php
                    $subtotal = $detail->product->price * $detail->amount;
                    $total += $subtotal
                @endphp
                <tr>
                    <td>{{$loop->index + 1}}</td>
                    <td>{{$detail->product->name}}</td>
                    <td>{{$detail->amount}}</td>
                    <td>Q. {{$detail->product->price}}</td>
                    <td>Q. {{ number_format( $subtotal, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" >TOTAL</td>
                <td>Q. {{  number_format( $total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>