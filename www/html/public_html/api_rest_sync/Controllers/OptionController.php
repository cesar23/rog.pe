<?php

namespace Controllers;

use Libs\UtilHelper;
use Models\OptionModel;
use Libs\Solulog;
use Libs\AuthJWT;
use PDO;

class OptionController extends OptionModel
{
    private $dataJSON = [];

    private $_auth;

    function __construct()
    {
        // Llamar al constructor del padre para inicializar wpPlugin
        parent::__construct();

        $this->_auth = new AuthJWT();
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
    }

    function test()
    {
        $postBody = file_get_contents("php://input");
        $datosArray = UtilHelper::ok();
        $this->dataJSON["data"] = $postBody;
        http_response_code(200);
        echo json_encode($datosArray);
        exit();
    }




    function updateOptionTipoCambio()
    {
        //-Validacio por token
        $datosArray = $this->_auth->estaAutenticado();
        //Si  hay  un error mostramos
        if (isset($datosArray["result"]["error_id"])) {
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
            echo json_encode($datosArray);
            exit();
        }

        $postBody = file_get_contents("php://input");

        try {


            $data = json_decode($postBody);
            $datosArray = UtilHelper::ok();
            $res=$this->updateOptionTipoCambioModel($data->tipo_cambio);
            $datosArray["result"][]=$res;
            http_response_code(200);
            echo json_encode($datosArray);
        } catch (\Exception $e) {

            http_response_code(404);
            echo json_encode(array("status" => 'error', "message" => $e->getMessage()));

            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($e->getMessage(), 'info');
        }


        exit();
    }
    
    function updateOptionTipoCambioV2()
    {
        //-Validacio por token
        $datosArray = $this->_auth->estaAutenticado();
        //Si  hay  un error mostramos
        if (isset($datosArray["result"]["error_id"])) {
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
            echo json_encode($datosArray);
            exit();
        }

        $postBody = file_get_contents("php://input");

        try {


            $data = json_decode($postBody);
            $datosArray = UtilHelper::ok();
            $res=$this->updateOptionTipoCambioV2Model($data->tipo_cambio,$data->currency_code);
            $datosArray["result"][]=$res;
            http_response_code(200);
            echo json_encode($datosArray);
        } catch (\Exception $e) {

            http_response_code(404);
            echo json_encode(array("status" => 'error', "message" => $e->getMessage()));

            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($e->getMessage(), 'info');
        }


        exit();
    }

}
