<?php


namespace Libs;


class UtilHelper
{
    public static   $response = [
        'status' => "ok",
        "result" => array()
    ];

    public static  function ok(){
        self::$response['status'] = "ok";
        self::$response['application_date'] = date('Y-m-d H:i:s');
        self::$response['result'] = [];
        return self::$response;
    }

    public static  function error_405(){
        self::$response['status'] = "error";
        self::$response['error_id'] = "405";
        self::$response['application_date'] = date('Y-m-d H:i:s');
        self::$response['error_msg'] = "Metodo no permitido";
        self::$response['result'] = array(
        );
        return self::$response;
    }

    public static function error_200($valor = "Datos incorrectos"){
        self::$response['status'] = "error";
        self::$response['error_id'] = "200";
        self::$response['application_date'] = date('Y-m-d H:i:s');
        self::$response['error_msg'] = $valor;
        self::$response['result'] = array(
        );

        return self::$response;
    }


    public static function error_400(){
        self::$response['status'] = "error";
        self::$response['error_id'] = "400";
        self::$response['application_date'] = date('Y-m-d H:i:s');
        self::$response['error_msg'] = "Datos enviados incompletos o con formato incorrecto";
        self::$response['result'] = array(
        );
        return self::$response;
    }


    public static function error_500($valor = "Error interno del servidor"){
        self::$response['status'] = "error";
        self::$response['error_id'] = "500";
        self::$response['application_date'] = date('Y-m-d H:i:s');
        self::$response['error_msg'] = $valor;
        self::$response['result'] = array(
        );
        return self::$response;
    }


    public static function error_401($valor = "No autorizado"){
        self::$response['status'] = "error";
        self::$response['application_date'] = date('Y-m-d H:i:s');
        self::$response['error_id'] = "401";
        self::$response['error_msg'] = $valor;
        self::$response['result'] = array(
        );

        return self::$response;
    }
    public static function encriptar($string){
        return md5($string);
    }

    public static function  tokenPorCliente() {
        $aud = __SECRET_KEY__;

//        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//            $aud .= $_SERVER['HTTP_CLIENT_IP'];
//        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//            $aud .= $_SERVER['HTTP_X_FORWARDED_FOR'];
//        } else {
//            $aud .= $_SERVER['REMOTE_ADDR'];
//        }
//
//        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return ($aud);
    }

    public static function tokenGenerate($extra='') {
        $extra=str_pad($extra, 10, "0", STR_PAD_LEFT);
        return (UtilHelper::tokenPorCliente() . $extra);
    }

}
