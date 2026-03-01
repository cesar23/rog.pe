<?php


namespace Libs;

use Libs\IAuth;
use Models\UserModel;
use Libs\Solulog;
use Libs\UtilHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthJWT extends UserModel implements IAuth
{
    private $encrypt = array('HS256');


    public function autenticar($datos)
    {
        $datos = json_decode($datos, true);

        if (!isset($datos['user']) || !isset($datos["password"])) {
            //error con los campos
            return UtilHelper::error_400();
        } else {
            //todo esta bien
            $user = $datos['user'];
            $password = $datos['password'];
//            $password = sha1($password);
            $user_data = $this->userLogin($user);
            if ($user_data) {
                //verificar si la contraseña es igual
                if (sha1($password) == $user_data->password) {
                    if ((int)$user_data->active === 1) {
                        //crear el token
                        //$result_token = $this->updateToken($user_data,UtilHelper::tokenPorCliente());

                        $time = time();
                        $time_new = $time + (3600 * (int)__EXPIRE_TOKEN_JWT__);
                        $serverName = $_SERVER['SERVER_NAME'];

                        $properties = array(
                            'iss' => $serverName,
                            'exp' => $time_new,
                            'aud' => UtilHelper::tokenPorCliente(),
                            'data_user' => array("id" => $user_data->id, "user" => $user_data->user, "email" => $user_data->email, "role" => $user_data->role)
                        );

                        $token = JWT::encode($properties, __SECRET_KEY__, 'HS256');

                        if ($token) {
                            // si se guardo
                            $result = UtilHelper::ok();
                            $result["token"] = $token;
                            $result["expiration_token"] = date('Y-m-d H:i:s', $time_new);
                            $result["result"] = [];
                            return $result;
                        } else {
                            //error al guardar
                            return UtilHelper::error_500("Error interno, No hemos podido guardar");
                        }
                    } else {
                        //el user esta inactivo
                        return UtilHelper::error_401("El user esta inactivo");
                    }
                } else {
                    //la contraseña no es igual
                    return UtilHelper::error_401("El password es invalido");
                }
            } else {
                //no existe el user
                return UtilHelper::error_401("El usuaro $user  no existe ");
            }
        }
    }


    public function estaAutenticado()
    {

        if (empty($_GET['token'])) {
            return UtilHelper::error_401("No esta autenticado");
        }

        $token = $_GET['token'];


        try {
            $decoded = JWT::decode($token, new Key(__SECRET_KEY__, 'HS256'));
            if($decoded->aud !== UtilHelper::tokenPorCliente()) {
                return UtilHelper::error_401("Su Token:{$token}  caduco ingresar denuevo:".UtilHelper::tokenPorCliente()." | ".$decoded->aud);
            }

        } catch (\Exception $ex) {
            return UtilHelper::error_401("Su Token:{$token}  es invalido");
        }

        return;
    }


    public function destruir()
    {

    }

    public function user()
    {
        $this->estaAutenticado();

        $token = $_GET['token'];
        $token_decoded = JWT::decode($token, new Key(__SECRET_KEY__, 'HS256'));

        return $token_decoded->data_user;


        //return $this->getUserToken(UtilHelper::tokenPorCliente());
    }


}
