<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if($format === 'pdf')
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8"/>
    @else
        <meta http-equiv="Content-Type"
              content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8"/>
    @endif
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inventario</title>
    <style>
        html {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        .title {
            font-weight: 500;
            text-align: center;
            font-size: 24px;
        }

        .label {
            width: 120px;
            font-weight: 500;
            font-family: sans-serif;
        }

        .table-records {
            margin-top: 24px;
        }

        .table-records tr th {
            font-weight: bold;
            background: #0088cc;
            color: white;
        }

        .table-records tr th,
        .table-records tr td {
            border: 1px solid #000;
            font-size: 9px;
        }
    </style>
</head>
<body>
<table style="width: 100%" class="table-records">
    <thead>
    <tr>
        <th><strong>Alm. Origen</strong></th>
        <th><strong>Alm. Destino</strong></th>
        <th><strong>Detalle</strong></th>
        <th><strong>Producto</strong></th>
        <th><strong>Cantidad</strong></th>
    </tr>
    </thead>
    <tbody>
    @foreach($records->inventory as $key => $row)
        <tr>
            <td>{{ $row->warehouse->description }}</td>
            <td>{{ $row->warehouse_destination->description }}</td>
            <td>{{ $row->description }}</td>
            <td>{{ $row->item->description }}</td>
            <td>{{ $row->quantity }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
