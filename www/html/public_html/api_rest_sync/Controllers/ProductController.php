<?php

namespace Controllers;

use Libs\UtilHelper;
use PDO;
use Models\ProductModel;
use Libs\Solulog;
use Libs\AuthJWT;

class ProductController extends ProductModel
{
    private $dataJSON = [];

    private $_auth;

    function __construct()
    {
        $this->_auth = new AuthJWT();
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
    }

    function _auth(){
        //-Validacio por token
        $datosArray = $this->_auth->estaAutenticado();
        //Si  hay  un error mostramos
        if (isset($datosArray['status']) && $datosArray['status'] == 'error') {
            http_response_code(401);
            echo json_encode($datosArray);
            exit();
        }
    }

    function test()
    {
//        $postBody = file_get_contents("php://input");
        $postBody=array("fff"=>"fff");
        $datosArray = UtilHelper::ok();
        $datosArray["data"]=($postBody);
        $this->dataJSON["data"] = $postBody;
        http_response_code(200);
        echo json_encode($datosArray,true);
        exit();
    }

    function test_ids()
    {

        $datosArray = UtilHelper::ok();
        $postBody = file_get_contents("php://input");
        $data = json_decode($postBody);
        if (count($data) > 0) {
            foreach ($data as $item) {
                //getProductMeta($post_id)
            }
        }


        $this->dataJSON["data"] = $postBody;
        http_response_code(200);
        echo json_encode($datosArray);
        exit();
    }


    function getProductsIDSoftLink()
    {
        $this->_auth();


        try {


            $stmt = $this->getProductsIDSoftLinkModel();
            $itemCount = $stmt->rowCount();

            $datosArray = UtilHelper::ok();

            //echo json_encode($itemCount);

            if ($itemCount > 0) {


//                $datosArray["result"]['items']= array();
                $datosArray['rowCount'] = $itemCount;
                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                    $datosArray["result"][]=array(
                        "post_id" => $row->post_id,
                        "sku" => $row->meta_value

                    );
                }
                http_response_code(200);
                echo json_encode($datosArray);
            } else {
                http_response_code(200);
                $datosArray['rowCount'] = 0;
                echo json_encode($datosArray);
            }
        } catch (\Exception $e) {

            http_response_code(404);
            echo json_encode(array("status" => 'error', "message" => $e->getMessage()));

            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($e->getMessage(), 'info');
        }


        exit();
    }

    function updateProductStock()
    {
        //-Validacio por token
        $this->_auth();
        $postBody = file_get_contents("php://input");


        try {

            $data = json_decode($postBody);
            //----- start data base response
            $datosArray = UtilHelper::ok();

            if (count($data) > 0){
                foreach ($data as $item) {
                    $res=$this->updateProductStockModel(
                        $item->stock_act,
                        $item->cod_prod,
                        $item->post_id);
                    $datosArray["result"][]=$res;

                }
            }

            //----- end data base response
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

    function updateProductPrice()
    {
        //-Validacio por token
        $this->_auth();

        $postBody = file_get_contents("php://input");


        try {

            $data = json_decode($postBody);
            //----- start data base response
            $datosArray = UtilHelper::ok();

            if (count($data) > 0){
                foreach ($data as $item) {
                    $res=$this->updateProductPrecioModel(
                        $item->precio_venta,
                        $item->cod_prod,
                        $item->post_id);
                    $datosArray["result"][]=$res;

                }
            }

            //----- end data base response
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


    function updateProductStock_ant()
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
        $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
        $log->LogInfo($postBody, 'info');

        try {

            $regUpdate=0;


            $data = json_decode($postBody);
            $datosArray = UtilHelper::ok();
            $datosArray["data"]=[];
            $product_update=0;
            $stock_update=0;
            $precio_update=0;

            //        /Aqui es  donde  actualizamos el stock en la  web
            if (count($data) > 0)
                foreach ($data as $item) {
                    //                $informacion="post_id:{$item->post_id},cod_prod:{$item->cod_prod},stock_act_sys:{$item->stock_act_sys}";




                    if($item->type==="product"){
                      $res=$this->updateProductNombreModel(
                            $item->nom_prod,
                            $item->post_id);
                        $item->querysql=$res;

                        $datosArray["data"][]=$item;
                        $regUpdate++;
                    }

                    if($item->type==="stock"){



                        $res=$this->updateProductStockModel(
                            $item->stock_act,
                            $item->post_id);

                        $item->querysql=$res;
                        $datosArray["data"][]=$item;
                        $regUpdate++;
                    }

                    if($item->type==="precio"){
                        $res=$this->updateProductPrecioModel(
                            $item->precio_venta,
                            $item->post_id);
                        $item->querysql=$res;
                        $datosArray["data"][]=$item;
                        $regUpdate++;

                    }



                }

            $datosArray["updateCount"]=$regUpdate;
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


  //-------------Nuevas Funcioens
    function updateProductV2()
    {
        //-Validacio por token
        $this->_auth();

        $postBody = file_get_contents("php://input");
//        $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
//        $log->LogInfo($postBody, 'info');

        try {

            $data = json_decode($postBody);

            $res=$this->updateProductNombreModel(
                $data->nom_prod,
                $data->post_id);

            //----- start data base response
            $datosArray = UtilHelper::ok();

            $datosArray["result"]=$res;
            //----- end data base response
            http_response_code(200);
            echo json_encode($datosArray);
            exit();
        } catch (\Exception $e) {

            http_response_code(404);
            echo json_encode(array("status" => 'error', "message" => $e->getMessage()));

            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($e->getMessage(), 'info');
        }


        exit();
    }

    function updateProductV3()
    {
        //-Validacio por token
        $this->_auth();

        $postBody = file_get_contents("php://input");
        $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
        $log->LogInfo($postBody, 'info');

        try {

            $regUpdate=0;


            $data = json_decode($postBody);
            //----- start data base response
            $datosArray = UtilHelper::ok();

            if (count($data) > 0){
                foreach ($data as $item) {
                    $res=$this->updateProductNombreModel(
                        $item->nom_prod,
                        $item->cod_prod,
                        $item->post_id);
                    $datosArray["result"][]=$res;

                }
            }

            //----- end data base response
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
