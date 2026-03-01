<?php

namespace Controllers;

use Models\UserModel;
use Libs\Solulog;
use Libs\AuthJWT;
use Libs\UtilHelper;


//Token manual con DB
//class AuthController extends Auth
//Token  con JWT
class AuthJWTController extends AuthJWT
{

    private $_auth;

    function __construct()
    {
        $this->_auth = new AuthJWT();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header("Content-Type: application/json; charset=UTF-8");
    }

    public function test()
    {



        $postBody = file_get_contents("php://input");
        try {


            //$datosArray = $this->_auth->autenticar($postBody);


            http_response_code(200);
            echo json_encode($postBody);


        } catch (\Exception $e) {

            http_response_code(404);


            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($e->getMessage(), 'info');


        }


        exit();


    }

    public function test2()
    {




        try {


            //$datosArray = $this->_auth->autenticar($postBody);


            http_response_code(200);
            echo json_encode(array("tstatus"=>"ook"));


        } catch (\Exception $e) {

            http_response_code(404);


            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($e->getMessage(), 'info');


        }


        exit();


    }

    public function login()
    {



            $postBody = file_get_contents("php://input");
            try {

                $datosArray = $this->_auth->autenticar($postBody);

                //Si  hay  un error mostramos
                if (isset($datosArray["error_id"])) {
                    $responseCode = $datosArray["error_id"];
                    http_response_code($responseCode);
                    echo json_encode($datosArray);
                    exit();
                }else{
                    http_response_code(200);
                    echo json_encode($datosArray);
                }




            } catch (\Exception $e) {

                http_response_code(404);


                $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
                $log->LogInfo($e->getMessage(), 'info');


            }


        exit();


    }

    public function logout()
    {




            try {


                $datosArray = $this->_auth->destruir();

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

        try {

            $datosArray = $this->_auth->user();
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


            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($e->getMessage(), 'info');


        }


        exit();


    }
}
