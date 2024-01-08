<?php

namespace App\Imports;

use App\Models\Tenant\Document;
use App\Models\Tenant\Person;
use App\Models\Tenant\Item;
use App\Models\Tenant\Warehouse;
use App\Models\Tenant\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DocumentsImportTectelFormat implements ToCollection
{
    use Importable;

    protected $data;

    public function collection(Collection $rows)
    {
            $total = count($rows);
            $registered = 0;
            unset($rows[0]);
            foreach ($rows as $row)
            {
                // dd($row);
                //serie-correlativo
                $serie = $row[9];

                //tipo de documento
                if($row[10] == '03'){
                    $document_type = '03';
                    $document_type_operation = '0101';
                } elseif ($row[10] == '01') {
                    $document_type = '01';
                    $document_type_operation = '0101';
                }else {
                    return 'el tipo de documento: '.$row[10].' no es válido para documentos electrónicos';
                }

                //fecha de documento

                $create_date = Carbon::instance(Date::excelToDateTimeObject($row[7]));
                // $date_document = Carbon::parse($create_date)->format('Y-m-d');

                /*$date_create = Carbon::createFromFormat('d/m/Y', $create_date);*/
                $date_document = $create_date->format('Y-m-d');
                //dd($date_document);
                //moneda
                $currency = 'PEN' ;
                $company_address = "";
                $ubigeo = "";
                //cliente
                $co_number = rtrim($row[8]);

                $persona = Person::where('number', $co_number)->first();
                
                //dd($persona->email);
                if($persona){
                    $email = isset($persona->email) ? $persona->email : "";
                }else{
                    $email = "";
                }

                if ($co_number > 0) {
                    if (strlen($co_number) == 11) {
                        $client_document_type = '6';
                        $company_number = $co_number;

                        $token = "7c0d548772a5b9c006251f7a404bbaa955c5b201101c4fb1d3c021b02bf0ae5f";
                        
                        $cURLConnection = curl_init();
                        curl_setopt($cURLConnection, CURLOPT_URL, "https://apiperu.dev/api/ruc/".$company_number."?api_token=".$token);
                        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

                        $datosruc = curl_exec($cURLConnection);
                        curl_close($cURLConnection);
                        $datos = json_decode($datosruc, true);

                        //dd($datos);

                        $company_address = $datos["data"]["direccion"];
                        $ubigeo = ($datos["data"]["ubigeo_sunat"] != "-") ? $datos["data"]["ubigeo_sunat"]:"";

                    } elseif (strlen($co_number) == 8) {
                        $client_document_type = '1';
                        $company_number = $co_number;
                    }
                }else {
                    $client_document_type = '0';
                    $company_number = '00000000'; 
                }

                $company_name = $row[1];
                

                //totales
                $mtototal = $row[6];
                $mtosubtotal = number_format($mtototal / 1.18, 2, '.', '');
                $mtoimpuesto = number_format($mtototal - $mtosubtotal, 2, '.', '');

                //unidad de medida
                $unit_type = 'NIU';

                //genero json y envio a api para no hacer insert 

                //valores
                $cantidad = $row[4];
                $precio_unitario = $row[5];
                $subtotal = $mtosubtotal;
                $total_impuesto = $mtoimpuesto;
                
                $json = array(
                    "serie_documento" => $serie,
                    "numero_documento" => "#",
                    "fecha_de_emision" => $date_document,
                    "hora_de_emision" => "11:00:00",
                    "codigo_tipo_operacion" => $document_type_operation,
                    "codigo_tipo_documento" => $document_type,
                    "codigo_tipo_moneda" => $currency,
                    "fecha_de_vencimiento" => $date_document,
                    "totales" => [
                        "total_exportacion" => 0.00,
                        "total_operaciones_gravadas" => $mtosubtotal,
                        "total_operaciones_inafectas" => 0.00,
                        "total_operaciones_exoneradas" => 0.00,
                        "total_operaciones_gratuitas" => 0.00,
                        "total_igv" => $mtoimpuesto,
                        "total_impuestos" => $mtoimpuesto,
                        "total_valor" => $mtosubtotal,
                        "total_venta" => $mtototal
                    ],
                    "datos_del_emisor" => [
                        "codigo_del_domicilio_fiscal" => "0000"
                    ],
                    "datos_del_cliente_o_receptor" => [
                        "codigo_tipo_documento_identidad" => $client_document_type,
                        "numero_documento" => $company_number,
                        "apellidos_y_nombres_o_razon_social" => rtrim($company_name),
                        "codigo_pais" => "PE",
                        "ubigeo" => $ubigeo,
                        "direccion" => rtrim($company_address),
                        "correo_electronico" => $email,
                        "telefono" => ""
                    ],
                    "items" => [
                        [
                            "codigo_interno" => $row[2],
                            "descripcion" => rtrim($row[3]),
                            "codigo_producto_sunat" => "",
                            "unidad_de_medida" => $unit_type,
                            "cantidad" => $cantidad,
                            "valor_unitario" => $precio_unitario - ($total_impuesto / $cantidad),
                            "codigo_tipo_precio" => "01",
                            "precio_unitario" => $precio_unitario,
                            "codigo_tipo_afectacion_igv" => "10",
                            "total_base_igv" => $subtotal,
                            "porcentaje_igv" => "18",
                            "total_igv" => $total_impuesto,
                            "total_impuestos" => $total_impuesto,
                            "total_valor_item" => $subtotal,
                            "total_item" => $subtotal + $total_impuesto,
                        ]
                    ]
                );

                if(!empty($email)){
                    $json["acciones"]["enviar_email"] = true;
                }
                
                $url = url('/api/documents');
                //$token = \Auth::user()->api_token;
                $token = User::where("name", $row[0])->first();

                //dd($json);

                try {

                    $client = new \GuzzleHttp\Client();

                    $response = $client->post($url, [
                        'headers' => [
                            'Content-Type' => 'Application/json',
                            'Authorization' => 'Bearer '.$token->api_token
                        ],
                        'json' => $json
                    ]);
                } catch (Exception $e) {
                    dd($e);
                }

                // dd($response);
                sleep(4);
                $registered += 1;
            }
            $this->data = compact('total', 'registered');

    }

    public function getData()
    {
        return $this->data;
    }
}
