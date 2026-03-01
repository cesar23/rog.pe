<?php

namespace Models;

use Config\Database;
use Libs\UtilHelper;
use \PDO;

class UserModel extends Database
{


// Table
    private $db_table = "users_auth";

    // Columns
    protected $id;
    protected $user;
    protected $email;
    protected $email_verified_at;
    protected $password;
    protected $role;
    protected $token;
    protected $expiration_token;
    protected $active;

    private $DB;

// Db connection
    public function __construct()
    {
        $this->DB = Database::connect();
    }

     function getConnection(){

       return  $this->DB = Database::connect();


    }


    public function userLogin($user)
    {

        try {

            $stm =  $this->getConnection()->prepare(
                "SELECT * FROM {$this->db_table} WHERE user =? "
            );

            $stm->execute([
                $user

            ]);

            if($stm->rowCount()>0){
                return $stm->fetch(PDO::FETCH_OBJ);
            }

            return false;




        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (Exception $e) {
            throw new \Exception($e);
        }

    }


    public function updateToken($user,$token)
    {
        $fecha = new \DateTime();
        $fecha->add(new \DateInterval(__EXPIRE_TOKEN__));

        try {

            $stmt =  $this->getConnection()->prepare("UPDATE {$this->db_table} SET 
						token          = ?, 
						expiration_token = ?
				    WHERE id = ?");

            $stmt->execute(
                array(
                    $token,
                    $fecha->format('Y-m-d H:i:s'),
                    $user->id
                )
            );

            return ["token"=>$token,"expiration_token"=>$fecha->format('Y-m-d H:i:s')];


        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (Exception $e) {
            throw new \Exception($e);
        }
    }

    public function deleteToken($token)
    {


        $stmt=null;

        try {

            $stmt =  $this->getConnection()->prepare("UPDATE {$this->db_table} SET 
						token          = ?, 
						expiration_token = ?
				    WHERE token = ?");

            $stmt->execute(
                array(
                    null,
                    null,
                    $token
                )
            );

            return true;

        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (Exception $e) {
            throw new \Exception($e);
        }
    }

    public function getUserToken($token)
    {


        try {
            $sql="SELECT * FROM {$this->db_table} WHERE token =? ";
           // $sql="SELECT * FROM {$this->db_table} WHERE token = '{$token}' ";
           // echo $sql;
            $stm = $this->getConnection()->prepare( $sql);
//
            $stm->execute([
                $token
            ]);

//            $stm->execute();

            return $stm->fetch(PDO::FETCH_OBJ);


        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (Exception $e) {
            throw new \Exception($e);
        }
    }
//


}
