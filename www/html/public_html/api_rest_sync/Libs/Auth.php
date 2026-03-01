<?php


namespace Libs;


use Models\UserModel;
use Libs\Solulog;
use Libs\IAuth;

//class Auth implements IAuth, extends UserModel
class Auth extends UserModel implements IAuth
{



    public function autenticar($datos)
    {
        $datos = json_decode($datos,true);

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
                        $result_token = $this->updateToken($user_data,UtilHelper::tokenPorCliente());

                        if ($result_token) {
                            // si se guardo
                            $result = UtilHelper::ok();
                            $result["result"] = array(
                            );
                            $result['token'] = $result_token['token'];
                            $result['expiration_token'] = $result_token['expiration_token'];
                            return $result;
                        } else {
                            //error al guardar
                            return UtilHelper::error_500("Error interno, No hemos podido guardar");
                        }
                    } else {
                        //el user esta inactivo
                        return UtilHelper::error_200("El user esta inactivo");
                    }
                } else {
                    //la contraseña no es igual
                    return UtilHelper::error_200("El password es invalido");
                }
            } else {
                //no existe el user
                return UtilHelper::error_200("El usuaro $user  no existe ");
            }
        }
    }


    public function estaAutenticado()
    {
        $result = $this->getUserToken(UtilHelper::tokenPorCliente());

        if (!is_object($result)) {
            return UtilHelper::error_401("No esta autenticado");
            //throw new \Exception('No esta autenticado');
        }

        $token_fecha = new \DateTime($result->expiration_token);
        $fecha = new \DateTime();

        if ($token_fecha < $fecha) {
            return UtilHelper::error_401("Su Token  caduco ingresar denuevo");
            //throw new \Exception('No esta autenticado');
        }
        return $result;
    }


    public function destruir()
    {
        $this->EstaAutenticado();

        $this->deleteToken(UtilHelper::tokenPorCliente());
        $result = UtilHelper::ok();
        $result["result"] = array( );
        return $result;
    }

    public function user()
    {
       return $this->EstaAutenticado();
        //return $this->getUserToken(UtilHelper::tokenPorCliente());
    }



}
