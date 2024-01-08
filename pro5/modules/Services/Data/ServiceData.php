<?php

namespace Modules\Services\Data;

use GuzzleHttp\Client;
use App\Models\System\Configuration;

class ServiceData
{
    public static function service($type, $number)
    {
        /*$configuration = Configuration::first();

        $url = $configuration->url_apiruc =! '' ? $configuration->url_apiruc : config('configuration.api_service_url');
        $token = $configuration->token_apiruc =! '' ? $configuration->token_apiruc : config('configuration.api_service_token');

        $client = new Client(['base_uri' => $url, 'verify' => false]);
        $parameters = [
            'http_errors' => false,
            'connect_timeout' => 5,
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ],
        ];

        $res = $client->request('GET', '/api/'.$type.'/'.$number, $parameters);
        $response = json_decode($res->getBody()->getContents(), true);

        return $response;*/

        if($type == "ruc"){

            $url_ruc = "http://144.217.215.6/sunat/libre.php";

            $client = new Client(['base_uri' => $url_ruc, 'verify' => false]);
            $parameters = [
                'http_errors' => false,
                'connect_timeout' => 5,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ];

            $res = $client->request('GET', '?ruc='.$number, $parameters);
            $response = json_decode($res->getBody()->getContents(), true);

            if($response["success"] == true){

                if($response["ubigeo"] != "-"){
                    $ubi_ex = str_split($response["ubigeo"], 2);
                    $ubigeo = array($ubi_ex[0], $ubi_ex[0].$ubi_ex[1], $ubi_ex[0].$ubi_ex[1].$ubi_ex[2]);

                }else{
                    $ubigeo = $response["ubigeo"];
                }

                $retornoRuc["success"]              = $response["success"];
                $retornoRuc["data"]["direccion"]    = $response["direccion"];
                $retornoRuc["data"]["direccion_completa"]    = $response["direccion_completa"];
                $retornoRuc["data"]["ruc"]          = $response["ruc"];
                $retornoRuc["data"]["nombre_o_razon_social"]    = $response["nombre_o_razon_social"];
                $retornoRuc["data"]["estado"]    = $response["estado_del_contribuyente"];
                $retornoRuc["data"]["condicion"] = $response["condicion_de_domicilio"];
                $retornoRuc["data"]["ubigeo"] = $ubigeo;

            }else{
                $retornoRuc["success"]   = $response["success"];
                $retornoRuc["message"]   = $response["msg"];
            }

            return $retornoRuc;


        }else if($type == "dni"){

            $url_dni = "http://servicio.fitcoders.com/v1/all";
            $token = "5b8e0a07c0307c1e5a5c55cb";

            $client = new Client(['base_uri' => $url_dni, 'verify' => false]);
            $parameters = [
                'http_errors' => false,
                'connect_timeout' => 5,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ];

            $res = $client->request('GET', '?service=DNI&id='.$number.'&key='.$token, $parameters);
            $response = json_decode($res->getBody()->getContents(), true);

            if($response["success"] == true){

                $retornoDni["success"]           = $response["success"];
                $retornoDni["data"]["numero"]    = $response["item"]["_id"];
                $retornoDni["data"]["nombre_completo"]    = $response["item"]["nombre"]." ".$response["item"]["paterno"]." ".$response["item"]["materno"];
                $retornoDni["data"]["nombres"]          = $response["item"]["nombre"];
                $retornoDni["data"]["apellido_paterno"] = $response["item"]["paterno"];
                $retornoDni["data"]["apellido_materno"] = $response["item"]["materno"];
                $retornoDni["data"]["codigo_verificacion"] = $response["item"]["codio_verificacion"];

            }else{
                $retornoDni["success"]   = $response["success"];
                $retornoDni["message"]   = $response["message"];
            }

            return $retornoDni;

        }

    }

    /*
     * apiperu.net.pe --- para verificar envio de datos y url
     */
    public function validar_cpe($ruc,$usuario,$clave,$file)
    {
        try {
            $configuration = Configuration::first();
            //  dd($configuration->url_apiruc,$configuration->token_apiruc,$ruc,$usuario,$clave,$file);
            $this->client = new Client(['base_uri' => $configuration->url_apiruc, 'verify' => false, 'http_errors' => false]);
            $curl = [
                CURLOPT_URL => $configuration->url_apiruc.'/api/validar/txt',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('file'=> new \CURLFILE(public_path('storage/txt/'.$file)),'ruc' => $ruc,'usuario_sol' => $usuario,'clave_sol' => $clave),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$configuration->token_apiruc,
                ),
            ];
            $responses = $this->client->request(strtoupper("POST"),'/api/validar/txt', [
                'curl' => $curl,
            ]);
            return $responses->getBody()->getContents();

        } catch (GuzzleHttp\Exception\RequestException $exception) {
            return $exception->getResponse()->getBody();
        }

    }
}
