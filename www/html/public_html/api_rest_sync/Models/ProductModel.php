<?php

namespace Models;

use Config\Database;
use Libs\Solulog;
use \PDO;

class ProductModel extends Database
{


    // Tables
    private $db_table = "wp_posts";
    private $db_table_2 = "wp_postmeta";


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
    }

    function getConnection()
    {

        return $this->DB = Database::connect();
    }

    // GET ALL
    public function getPost($meta_value)
    {


        //
        //        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
        //
        //        $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
        //
        //        $stmt -> execute();
        //
        //        return $stmt -> fetch();
        //
        //


        try {

            $sqlQuery = "select  post.ID,meta.* from {$this->db_table} as post
inner join {$this->db_table_2} as meta
on post.ID=meta.post_id
where post_type='product'
and meta.meta_key='_sku'
and meta.meta_value='{$meta_value}' ";
            //$this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $this->getConnection()->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;


            //            $sqlQuery = "select  post.ID,meta.* from {$this->db_table} as post
            //inner join {$this->db_table_2} as meta
            //on post.ID=meta.post_id
            //where post_type='product'
            //and meta.meta_key='_sku'
            //and meta.meta_value='{$meta_value}' ";
            //
            ////            echo $sqlQuery;
            //
            //            $stmt = $this->getConnection()->prepare($sqlQuery);
            //            $stmt->execute();
            //
            //
            //           // return $stmt -> fetch();


        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (\Exception $e) {
            throw new \Exception($e);
        } finally {
            $stmt = null;
        }
    }

    public function getProductsIDSoftLinkModel()
    {


        //
        //        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
        //
        //        $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
        //
        //        $stmt -> execute();
        //
        //        return $stmt -> fetch();
        //
        //


        try {

            $sqlQuery = "select  post.ID,meta.post_id,meta.meta_key,meta.meta_value from wp_posts as post
inner join wp_postmeta as meta
on post.ID=meta.post_id
where post_type='product'
and meta.meta_key='_sku'
  -- Aqui pasamo el id que esta  en el sistema
and meta.meta_value !=''";


            $stmt = $this->getConnection()->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (\Exception $e) {
            throw new \Exception($e);
        } finally {
            $stmt = null;
        }
    }


    public function getProductMeta($post_id)
    {


        //
        //        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
        //
        //        $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
        //
        //        $stmt -> execute();
        //
        //        return $stmt -> fetch();
        //
        //


        try {

            $sqlQuery = "select  post.ID,meta.post_id,meta.meta_key,meta.meta_value from wp_posts as post
inner join wp_postmeta as meta
on post.ID=meta.post_id
where post_type='product'
and meta.meta_key='_sku'
  -- Aqui pasamo el id que esta  en el sistema
and meta.post_id ={$post_id}";


            $stmt = $this->getConnection()->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (\Exception $e) {
            throw new \Exception($e);
        } finally {
            $stmt = null;
        }
    }


    public function updateProductStockModel($stock_value,$cod_prod, $id_product)
    {

        try {

            $_stock=$this->getTablePostmeta('_stock',$id_product,$cod_prod);
            $_stock_status=$this->getTablePostmeta('_stock_status',$id_product,$cod_prod);
            //---------------------------------------------------------------
            //1. Actualizar  numero de stock
            $sql = "INSERT INTO wp_postmeta ( meta_value,post_id, meta_key) VALUES (?,?,'_stock')";
            if($_stock['rowCount']>0){
                $sql = "UPDATE wp_postmeta SET  
                        meta_value=?     
                        where  meta_key='_stock' and post_id=?";
            }

            $stmt = $this->getConnection()->prepare($sql);


            $stmt->execute(
                array(
                    $stock_value,
                    $id_product
                )
            );
            //validamos la update
            $rowAfecct = $stmt->rowCount();
            if ($rowAfecct == 0) {
                return array(
                    "rowCount" => 0,// filas afectadas, total filas, filas deltes
                    "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                    "post_id" => $id_product,
                    "cod_prod" => $cod_prod,
                    "error" => true,
                    "msg_error" => "No se pudo actualizar el stock en la tabla [wp_postmeta -> meta_key(_stock) ] para el id_product:{$id_product} con el stock:{$stock_value}",
                    "rows" => []
                );
            }

            //---------------------------------------------------------------
            //2. Actualizar  el estado del stock
            $stock_status = "instock";
            if ((int)$stock_value <= 0) {
                $stock_status = "outofstock";
            }




            $sql = "INSERT INTO wp_postmeta ( meta_value,post_id, meta_key) VALUES (?,?,'_stock_status')";
            if($_stock_status['rowCount']>0){
                $sql = "UPDATE wp_postmeta SET
                meta_value=?        
                where  meta_key='_stock_status' and post_id=?";
            }



            $stmt = $this->getConnection()->prepare($sql);

            $stmt->execute(
                array(
                    $stock_status,
                    $id_product
                )
            );
            //validamos la update
            $rowAfecct = $stmt->rowCount();
            if ($rowAfecct == 0) {
                return array(
                    "rowCount" => 0,// filas afectadas, total filas, filas deltes
                    "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                    "post_id" => $id_product,
                    "cod_prod"=>$cod_prod,
                    "error" => true,
                    "msg_error" => "No se pudo actualizar el stock en la tabla [wp_postmeta -> meta_key(_stock_status) ] para el id_product:{$id_product} con el stock:{$stock_value} ",
                    "rows" => []
                );
            }

            //---------------------------------------------------------------
            //3. Actualizar  el meta_lookup del  producto

            $sql = "UPDATE wp_wc_product_meta_lookup SET
             stock_quantity=?,
             stock_status=?
             where  product_id=?";

            $stmt = $this->getConnection()->prepare($sql);

            $stmt->execute(
                array(
                    $stock_value,
                    $stock_status,
                    $id_product
                )
            );
            //validamos la update
            $rowAfecct = $stmt->rowCount();
            if ($rowAfecct == 0) {
                return array(
                    "rowCount" => 0,// filas afectadas, total filas, filas deltes
                    "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                    "post_id" => $id_product,
                    "cod_prod"=>$cod_prod,
                    "error" => true,
                    "msg_error" => "No se pudo actualizar el stock en la tabla [wp_wc_product_meta_lookup] para el id_product:{$id_product}  con el stock:{$stock_value}",
                    "rows" => []
                );
            }


            return array(
                "rowCount" => 0,// filas afectadas, total filas, filas deltes
                "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                "post_id" => $id_product,
                "cod_prod"=>$cod_prod,
                "error" => false,
                "msg_error" => "",
                "rows" => []
            );
        } catch (\PDOException $ex) {
            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($ex->getMessage(), 'info');
            throw new \Exception($ex);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    public function updateProductNombreModel($nom_prod, $cod_prod,$id_product)
    {

        try {

            //1. Actualizar  Nombre
            $sql = "UPDATE wp_posts SET
            post_title=?      
            where   ID=?";

            $stmt = $this->getConnection()->prepare($sql);

            $stmt->execute(
                array(
                    $nom_prod,
                    $id_product
                )
            );


            return array(
                "rowCount" => 0,// filas afectadas, total filas, filas deltes
                "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                "post_id" => $id_product,
                "cod_prod" => $cod_prod,
                "error" => false,
                "msg_error" => '',
                "rows" => []
            );


        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }



    public function getTablePostmeta($meta_key,$post_id,$cod_prod)
    {

        try {


            //3.1. Actualizar  precio
            $sql = "SELECT * FROM wp_postmeta where 1=1
and  meta_key=?
and post_id=? ";

            $stmt = $this->getConnection()->prepare($sql);


            $stmt->execute(
                array(
                    $meta_key,
                    $post_id
                )
            );

            //validamos la update


                return array(
                    "rowCount" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                    "post_id" => $post_id,
                    "cod_prod" => $cod_prod,

                );


        } catch (\PDOException $ex) {
            throw new \Exception($ex);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    public function updateProductPrecioModel($precio_venta,$cod_prod, $id_product)
    {

        try {
            $_price=$this->getTablePostmeta('_price',$id_product,$cod_prod);
            $_regular_price=$this->getTablePostmeta('_regular_price',$id_product,$cod_prod);


            //3.1. Actualizar  precio
            $sql = "INSERT INTO wp_postmeta ( meta_value,post_id, meta_key) VALUES (?,?,'_price')";
            if($_price['rowCount']>0){
                $sql = "UPDATE wp_postmeta SET 
                        meta_value=?      
                        where  meta_key='_price' and post_id=?";
            }
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute(
                array(
                    $precio_venta,
                    $id_product
                )
            );

            //validamos la update
            $rowAfecct = $stmt->rowCount();
            if ($rowAfecct == 0) {
                return array(
                    "rowCount" => 0,// filas afectadas, total filas, filas deltes
                    "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                    "post_id" => $id_product,
                    "cod_prod" => $cod_prod,
                    "error" => true,
                    "msg_error" => "No se pudo actualizar el precio en la tabla [wp_postmeta -> meta_key(_price) ] para el id_product:{$id_product} con el precio :{$precio_venta}",
                    "rows" => []
                );
            }



            //3.2. Actualizar  precio
            $sql = "INSERT INTO wp_postmeta ( meta_value,post_id, meta_key) VALUES (?,?,'_regular_price')";
            if($_regular_price['rowCount']>0){
                $sql = "UPDATE wp_postmeta SET 
                        meta_value=?      
                        where  meta_key='_regular_price' and post_id=?";
            }

            $stmt = $this->getConnection()->prepare($sql);

            $stmt->execute(
                array(
                    $precio_venta,
                    $id_product
                )
            );

            //validamos la update
            $rowAfecct = $stmt->rowCount();
            if ($rowAfecct == 0) {
                return array(
                    "rowCount" => 0,// filas afectadas, total filas, filas deltes
                    "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                    "post_id" => $id_product,
                    "cod_prod"=>$cod_prod,
                    "error" => true,
                    "msg_error" => "No se pudo actualizar el precio en la tabla [wp_postmeta -> meta_key(_regular_price) ] para el id_product:{$id_product} con el precio :{$precio_venta}",
                    "rows" => []
                );
            }



            //4. Actualizar  el meta_lookup del  producto

            $sql = "UPDATE wp_wc_product_meta_lookup SET
                                     min_price=?,
                                     max_price=?
             where  product_id=?";

            $stmt = $this->getConnection()->prepare($sql);

            $stmt->execute(
                array(
                    $precio_venta,
                    $precio_venta,
                    $id_product
                )
            );

            //validamos la update
            $rowAfecct = $stmt->rowCount();
            if ($rowAfecct == 0) {
                return array(
                    "rowCount" => 0,// filas afectadas, total filas, filas deltes
                    "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                    "post_id" => $id_product,
                    "cod_prod"=>$cod_prod,
                    "error" => true,
                    "msg_error" => "No se pudo actualizar el precio en la tabla [wp_wc_product_meta_lookup] para el id_product:{$id_product} con el precio :{$precio_venta}",
                    "rows" => []
                );
            }


            return array(
                "rowCount" => 0,// filas afectadas, total filas, filas deltes
                "rowAffect" => $stmt->rowCount(),// filas afectadas, total filas, filas deltes
                "post_id" => $id_product,
                "cod_prod"=>$cod_prod,
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
