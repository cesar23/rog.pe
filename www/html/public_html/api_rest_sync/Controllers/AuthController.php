<?php

namespace Controllers;

use Models\UserModel;
use Libs\Solulog;
use Libs\Auth;
use Libs\AuthJWT;
use Libs\UtilHelper;
use PDO;

//Token manual con DB
//class AuthController extends Auth
//Token  con JWT
class AuthController extends Auth
{

    private $_auth;

    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
    }

    public function login()
    {
        $_auth = new Auth();

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $postBody = file_get_contents("php://input");
            try {


                $datosArray = $_auth->autenticar($postBody);

                //Si  hay  un error mostramos
                if (isset($datosArray["result"]["error_id"])) {
                    $responseCode = $datosArray["result"]["error_id"];
                    http_response_code($responseCode);
                }

                http_response_code(200);
                echo json_encode($datosArray);


            } catch (\Exception $e) {

                http_response_code(404);
                echo json_encode(array("error" => 'malllllllll'));

                $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
                $log->LogInfo($e->getMessage(), 'info');


            }

        } else {
            $datosArray = UtilHelper::error_405();
            echo json_encode($datosArray);

        }
        exit();


    }

    public function logout()
    {
        $_auth = new Auth();



            try {


                $datosArray = $_auth->destruir();

                //Si  hay  un error mostramos
                if (isset($datosArray["result"]["error_id"])) {
                    $responseCode = $datosArray["result"]["error_id"];
                    http_response_code($responseCode);
                }

                http_response_code(200);
                echo json_encode($datosArray);
                exit();


            } catch (\Exception $e) {

                http_response_code(404);
                echo json_encode(array("error" => 'malllllllll'));

                $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
                $log->LogInfo($e->getMessage(), 'info');


            }


        exit();


    }


    public function user()
    {
        $_auth = new Auth();



        try {


            $datosArray = $_auth->user();
            $datosArray = json_decode(json_encode($datosArray), true);


            //Si  hay  un error mostramos
            if (isset($datosArray["result"]["error_id"])) {
                $responseCode = $datosArray["result"]["error_id"];
                http_response_code($responseCode);
            }

            http_response_code(200);
            echo json_encode($datosArray);


        } catch (\Exception $e) {

            http_response_code(404);
            echo json_encode(array("error" => 'malllllllll'));

            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($e->getMessage(), 'info');


        }


        exit();


    }
}
