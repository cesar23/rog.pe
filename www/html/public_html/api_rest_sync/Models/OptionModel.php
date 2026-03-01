<?php

namespace Models;

use Config\Database;
use \PDO;
use Libs\WpPlugin;
use Exception;

class OptionModel extends Database
{
    private $wpPlugin;


    // Tables
    private $db_table = "wp_options";



    // Columns
    protected $id;
    protected $title;
    protected $status;
    protected $created;

    private $DB;

    // Db connection
    public function __construct()
    {
        //$this->DB = Database::connect();
        try {
            // Inicializar WpPlugin (carga WordPress automáticamente)
            $this->wpPlugin = new WpPlugin();

            // Cargar el plugin Solu Currencies Exchange
            $this->wpPlugin->loadSoluCurrenciesExchange();

        } catch (Exception $e) {
            die("Error al cargar WordPress/Plugin: " . $e->getMessage());
        }
    }

    function getConnection()
    {

        return $this->DB = Database::connect();
    }



    public function updateOptionTipoCambioModel($tipoCambioSoles)
    {





        $tipoCambioSoles  = floatval($tipoCambioSoles);
        $data=array (
            'USD' =>
                array (
                    'name' => 'USD',
                    'rate' => 1.0,
                    'symbol' => '&#36;',
                    'position' => 'left',
                    'is_etalon' => 1,
                    'hide_cents' => 0,
                    'hide_on_front' => 0,
                    'rate_plus' => 0.0,
                    'decimals' => 2,
                    'description' => 'USA dollar',
                    'flag' => 'https://pcbyte.com.pe/wp-content/uploads/2020/05/usa-x24.png',
                ),
            'PER' =>
                array (
                    'name' => 'PER',
                    'rate' => $tipoCambioSoles,
                    'symbol' => 'S/.',
                    'position' => 'left',
                    'is_etalon' => 0,
                    'hide_cents' => 0,
                    'hide_on_front' => 0,
                    'rate_plus' => 0.0,
                    'decimals' => 2,
                    'description' => 'soles Peruanos',
                    'flag' => 'https://pcbyte.com.pe/wp-content/uploads/2020/05/peru-iconx24.png',
                ),
        );
        $data=serialize($data);

        try {

            //1. Actualizar  Nombre
            $sql = "UPDATE {$this->db_table} SET
            option_value=?      
            where  option_name='woocs'";

            $stmt =  $this->getConnection()->prepare($sql);

            $stmt->execute(
                array(
                    $data
                )
            );
            $rowAfecct = $stmt->rowCount();
            if ($rowAfecct == 0) {
                return array(
                    "rowCount" => 0,// filas afectadas, total filas, filas deltes
                    "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                    "error" => true,
                    "msg_error" => "No se pudo actualizar el stock en la tabla [wp_postmeta -> meta_key(_stock) ] para el ",
                    "rows" => []
                );
            }



            return array(
                "rowCount" => 0,// filas afectadas, total filas, filas deltes
                "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                "error" => false,
                "msg_error" => "",
                "rows" => []
            );


        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Actualiza el tipo de cambio en la tabla wp_solu_currencies_exchange
     * @param $tipoCambioSoles
     * @return array
     */
    public function updateOptionTipoCambioV2Model($tipoCambioSoles,$currency_code)
    {
        $this->db_table = 'wp_solu_currencies_exchange';

        $tipoCambioSoles  = floatval($tipoCambioSoles);

        try {

            //1. Actualizar  Nombre
            $sql = "UPDATE {$this->db_table} SET
            currency_value=?
            where  currency_code=?";

            $stmt =  $this->getConnection()->prepare($sql);

            $stmt->execute(
                array(
                    $tipoCambioSoles,
                    $currency_code
                )
            );
            $rowAfecct = $stmt->rowCount();
            if ($rowAfecct == 0) {
                return array(
                    "rowCount" => 0,// filas afectadas, total filas, filas deltes
                    "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                    "error" => true,
                    "msg_error" => "No se pudo actualizar el tipo cambio a $tipoCambioSoles",
                    "rows" => []
                );
            }

            // Llamar a saveStorageTable() del plugin
            $this->wpPlugin->saveCurrenciesStorage();



            return array(
                "rowCount" => 0,// filas afectadas, total filas, filas deltes
                "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                "error" => false,
                "msg_error" => "",
                "rows" => []
            );


        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }



}
