<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Series</title>
    </head>
    <body>  
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Marca</th>
                                <th>Fecha de Vencimiento</th>
                                <th>Lote</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)

                            @php
                                

                            @endphp 

                            <tr>
                                <td class="celda">{{$loop->iteration}}</td>
                                <td class="celda">{{$value->item->description}}</td>
                                <td class="celda">{{$value->item->brand->name}}</td>
                                <td class="celda">{{$value->date_of_due}}</td>
                                <td class="celda">{{$value->code}}</td>
                                <td class="celda">{{$value->quantity}}</td>
                            </tr> 
                            @endforeach 
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div>
                <p>No se encontraron registros.</p>
            </div>
        @endif
    </body>
</html>
