<?php
namespace Controllers;
use Models\UserModel;
use Libs\Solulog;
use PDO;

class UserController extends UserModel
{


    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
    }

    function login()
    {
        $postBody = file_get_contents("php://input");

        try {

            $items = new UserModel();

            $stmt = $items->login();
            $itemCount = $stmt->rowCount();


//echo json_encode($itemCount);

            if($itemCount > 0){

                $employeeArr = array();
                $employeeArr["data"] = array();
                $employeeArr["itemCount"] = $itemCount;

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $e = array(
                        "id" => $id,
                        "nombre" => $nombre,
                        "edad" => $edad
                    );

                    array_push($employeeArr["data"], $e);
                }
                echo json_encode($employeeArr);
            }

            else{
                http_response_code(404);
                echo json_encode(
                    array("message" => "No record found.")
                );
            }
        } catch (\Exception $e) {

            http_response_code(404);
            echo json_encode(array("error" => 'malllllllll'));

            $log = new Solulog("error_".date('Y').".log", SYSTEM_PATH."/Logs/");
            $log->LogInfo($e->getMessage(),'info');


        }




//        include SYSTEM_PATH.'./Views/product/index.php';

    }
}
