<?php

namespace Controllers;

use Libs\UtilHelper;
use Libs\Solulog;
use Libs\AuthJWT;

class CacheController
{
    private $dataJSON = [];

    private $_auth;

    function __construct()
    {
        $this->_auth = new AuthJWT();
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
    }

    function _auth()
    {
        //-Validacio por token
        $datosArray = $this->_auth->estaAutenticado();
        //Si  hay  un error mostramos
        if (isset($datosArray['status']) && $datosArray['status'] == 'error') {
            http_response_code(401);
            echo json_encode($datosArray);
            exit();
        }
    }





    function cleannerCacheAll()
    {
        $this->_auth();

        try {

            // Método 1: Enviar header X-LiteSpeed-Purge (como WordPress plugin)
            // Este es el método CORRECTO y más eficiente
            $purge_sent = $this->sendLiteSpeedPurgeHeader();

            // Ahora sí, limpiar archivos físicos (opcional pero recomendado)
            $cache_path = $_ENV['CACHE_PATH'] . $_ENV['DOMAIN_NAME'];
            $is_deleted_pub = false;
            $is_deleted_priv = false;
            
            if (is_dir($cache_path . '/pub')) {
                $is_deleted_pub = $this->clearDirectory($cache_path . '/pub');
            }
            if (is_dir($cache_path . '/priv')) {
                $is_deleted_priv = $this->clearDirectory($cache_path . '/priv');
            }

            
            // Construir mensaje de respuesta
            $messages = [];
            if ($purge_sent) {
                $messages[] = 'Header X-LiteSpeed-Purge enviado correctamente';
            }
            if ($is_deleted_pub) {
                $messages[] = 'Caché pública eliminada';
            }
            if ($is_deleted_priv) {
                $messages[] = 'Caché privada eliminada';
            }

            $msg = !empty($messages) ? implode('. ', $messages) : 'No se pudo limpiar la caché';

            if (empty($messages)) {
                $datosArray['status'] = 'error';
                $datosArray['message'] = $msg;
                http_response_code(404);
                echo json_encode($datosArray);
                exit();
            }

            http_response_code(200);
            $datosArray = UtilHelper::ok();
            $datosArray['message'] = $msg;
            $datosArray['details'] = [
                "purge_header_sent" => $purge_sent,
                "pub_cleared" => $is_deleted_pub,
                "priv_cleared" => $is_deleted_priv
            ];
            header('Content-Type: application/json');
            echo json_encode($datosArray);


        } catch (\Exception $e) {

            http_response_code(404);
            echo json_encode(array("status" => 'error', "message" => $e->getMessage()));

            $log = new Solulog("error_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
            $log->LogInfo($e->getMessage(), 'info');
        }



        exit();
    }

     /**
     * 🏠 LIMPIAR CACHÉ DE PORTADA/HOME
     * 
     * Limpia únicamente la caché de la página principal (home/portada)
     * Igual que el plugin de WordPress cuando haces "Purge Front Page"
     * 
     * Uso: POST /api/cache/purge-frontpage
     */
    public function purgeFrontPage()
    {
        $this->_auth();

  
        
        try {
            // Verificar que los headers no se hayan enviado
            if (headers_sent()) {
                throw new \Exception("Headers ya fueron enviados, no se puede limpiar caché");
            }

            // Tags que usa WordPress para la portada:
            // - tag_prefix (el sitio)
            // - tipo 'front_page'
            // En tu caso, simplemente enviamos los tags de home
            $tags = [
                'home',           // Tag de home page
                'front_page',     // Tag de front page
                'homepage'        // Tag alternativo
            ];

            // Construir header de purge
            $purge_value = 'public,' . implode(',', $tags);
            header('X-LiteSpeed-Purge: ' . $purge_value);

            // Log
            $msg = 'Caché de portada/home limpiada correctamente';
          

            // Respuesta exitosa
            http_response_code(200);
            echo json_encode([
                "status" => 'success',
                "message" => $msg,
                "details" => [
                    "type" => "front_page"
                    
                ]
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => 'error',
                "message" => $e->getMessage()
            ]);

            
        }

        exit();
    }

     /**
     * 📄 LIMPIAR CACHÉ DE POST ESPECÍFICO
     * 
     * Limpia la caché de un post/página/artículo específico por su ID o URL
     * Igual que el plugin de WordPress cuando editas un post
     * 
     * Uso: POST /api/cache/purge-post
     * Body: { "post_id": 123 } o { "post_url": "/blog/mi-articulo" }
     * 
     * @return void
     */
    public function purgePost()
    {
        $this->_auth();

        $log = new Solulog("info_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
        
        try {
            // Obtener datos del POST
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input)) {
                throw new \Exception("No se recibieron datos. Envía post_id o post_url");
            }

            // Verificar que los headers no se hayan enviado
            if (headers_sent()) {
                throw new \Exception("Headers ya fueron enviados, no se puede limpiar caché");
            }

            $tags = [];
            $post_identifier = '';
            $cache_files_to_delete = [];

            // Opción 1: Limpiar por ID de post/producto
            if (isset($input['post_id']) && !empty($input['post_id'])) {
                $post_id = intval($input['post_id']);
                $post_identifier = "ID: $post_id";
                
                // Tags básicos que usa WordPress/LiteSpeed para cualquier post
                $tags = [
                    'post_' . $post_id,      // Tag principal del post
                    'single_' . $post_id,    // Tag de página individual
                    'content_' . $post_id    // Tag de contenido
                ];

                // Tags adicionales para productos WooCommerce
                // El plugin de LiteSpeed usa estos tags específicos para WooCommerce
                $tags[] = 'product_' . $post_id;        // Tag de producto
                $tags[] = 'product-single_' . $post_id; // Tag de página de producto individual
                $tags[] = 'wc_product_' . $post_id;     // Tag alternativo WooCommerce
                
                // URLs a limpiar (ambas formas de acceder al producto)
                $domain = $_ENV['DOMAIN_NAME'];
                $cache_path = $_ENV['CACHE_PATH'] . $domain;
                
                // Formato 1: /?p=363
                $cache_files_to_delete[] = $cache_path . '/pub/index.html@p=' . $post_id;
                $cache_files_to_delete[] = $cache_path . '/pub/@p=' . $post_id . '.html';
                $cache_files_to_delete[] = $cache_path . '/pub/index@p=' . $post_id . '.html';
                
                $log->LogInfo("Limpiando caché del post/producto ID: $post_id con tags: " . implode(', ', $tags), 'info');
            }
            
            // Opción 2: Limpiar por URL del post/producto
            elseif (isset($input['post_url']) && !empty($input['post_url'])) {
                $post_url = $input['post_url'];
                $post_identifier = "URL: $post_url";
                
                // Limpiar URL para crear tag válido
                $url_path = parse_url($post_url, PHP_URL_PATH);
                if (!$url_path) {
                    $url_path = $post_url;
                }
                
                // Remover slash inicial y final
                $url_path = trim($url_path, '/');
                
                // Tags para URLs
                $tags = [
                    'url:/' . $url_path,     // Tag de URL específica
                    'page:' . $url_path,     // Tag alternativo
                ];

                // Si es un producto WooCommerce (contiene /product/)
                if (strpos($url_path, 'product/') !== false) {
                    // Extraer el slug del producto
                    $product_slug = str_replace('product/', '', $url_path);
                    $tags[] = 'product_slug_' . $product_slug;
                    $tags[] = 'wc_product_url_' . $product_slug;
                    
                    $log->LogInfo("Detectado producto WooCommerce con slug: $product_slug", 'info');
                }

                // Limpiar archivos físicos
                $domain = $_ENV['DOMAIN_NAME'];
                $cache_path = $_ENV['CACHE_PATH'] . $domain;
                
                // Archivos de caché a eliminar
                $cache_files_to_delete[] = $cache_path . '/pub/' . $url_path . '/index.html';
                $cache_files_to_delete[] = $cache_path . '/pub/' . $url_path . '.html';
            }
            
            // Opción 3: Limpiar ambos (ID + URL slug del producto) - RECOMENDADO
            elseif (isset($input['product_id']) && isset($input['product_slug'])) {
                $post_id = intval($input['product_id']);
                $product_slug = $input['product_slug'];
                $post_identifier = "Producto ID: $post_id, Slug: $product_slug";
                
                // Combinar todos los tags posibles
                $tags = [
                    // Tags por ID
                    'post_' . $post_id,
                    'single_' . $post_id,
                    'content_' . $post_id,
                    'product_' . $post_id,
                    'product-single_' . $post_id,
                    'wc_product_' . $post_id,
                    
                    // Tags por URL/Slug
                    'url:/product/' . $product_slug,
                    'page:product/' . $product_slug,
                    'product_slug_' . $product_slug,
                    'wc_product_url_' . $product_slug
                ];
                
                $domain = $_ENV['DOMAIN_NAME'];
                $cache_path = $_ENV['CACHE_PATH'] . $domain;
                
                // Limpiar ambas formas de URL
                $cache_files_to_delete[] = $cache_path . '/pub/index.html@p=' . $post_id;
                $cache_files_to_delete[] = $cache_path . '/pub/@p=' . $post_id . '.html';
                $cache_files_to_delete[] = $cache_path . '/pub/index@p=' . $post_id . '.html';
                $cache_files_to_delete[] = $cache_path . '/pub/product/' . $product_slug . '/index.html';
                $cache_files_to_delete[] = $cache_path . '/pub/product/' . $product_slug . '.html';
            }
            
            else {
                throw new \Exception("Debes enviar 'post_id', 'post_url' o 'product_id' + 'product_slug'");
            }

            // Eliminar archivos físicos de caché
            $files_deleted = 0;
            foreach ($cache_files_to_delete as $file) {
                if (file_exists($file)) {
                    if (@unlink($file)) {
                        $files_deleted++;
                        $log->LogInfo("Archivo de caché eliminado: $file", 'info');
                    }
                }
            }

            // Construir y enviar header de purge
            $purge_value = 'public,' . implode(',', $tags);
            header('X-LiteSpeed-Purge: ' . $purge_value);

            // Log
            $msg = "Caché del post/producto limpiada correctamente ($post_identifier)";
            $log->LogInfo($msg . ' - Tags: ' . implode(', ', $tags), 'info');

            // Respuesta exitosa
            http_response_code(200);
            echo json_encode([
                "status" => 'success',
                "message" => $msg,
                "details" => [
                    "type" => "post",
                    "identifier" => $post_identifier,
                    "tags_purged" => $tags,
                    "files_deleted" => $files_deleted
                ]
            ]);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                "status" => 'error',
                "message" => $e->getMessage()
            ]);

            $log->LogInfo('Error al limpiar post: ' . $e->getMessage(), 'error');
        }

        exit();
    }


    
    /**
     * 📝 LIMPIAR CACHÉ DE MÚLTIPLES POSTS
     * 
     * Limpia la caché de varios posts a la vez
     * 
     * Uso: POST /api/cache/purge-posts
     * Body: { "post_ids": [1, 2, 3, 4] } o { "post_urls": ["/post1", "/post2"] }
     * 
     * @return void
     */
    public function purgePosts()
    {
        $this->_auth();

        $log = new Solulog("info_" . date('Y') . ".log", SYSTEM_PATH . "/Logs/");
        
        try {
            // Obtener datos del POST
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input)) {
                throw new \Exception("No se recibieron datos");
            }

            if (headers_sent()) {
                throw new \Exception("Headers ya fueron enviados");
            }

            $tags = [];
            $count = 0;

            // Opción 1: Array de IDs
            if (isset($input['post_ids']) && is_array($input['post_ids'])) {
                foreach ($input['post_ids'] as $post_id) {
                    $post_id = intval($post_id);
                    $tags[] = 'post_' . $post_id;
                    $tags[] = 'single_' . $post_id;
                    $count++;
                }
            }
            
            // Opción 2: Array de URLs
            elseif (isset($input['post_urls']) && is_array($input['post_urls'])) {
                foreach ($input['post_urls'] as $url) {
                    $url_path = trim(parse_url($url, PHP_URL_PATH), '/');
                    $tags[] = 'url:/' . $url_path;
                    $count++;
                }
            }
            
            else {
                throw new \Exception("Debes enviar 'post_ids' o 'post_urls' como array");
            }

            if (empty($tags)) {
                throw new \Exception("No se generaron tags para purgar");
            }

            // Enviar header
            $purge_value = 'public,' . implode(',', $tags);
            header('X-LiteSpeed-Purge: ' . $purge_value);

            $msg = "Caché de $count posts limpiada correctamente";
            $log->LogInfo($msg, 'info');

            http_response_code(200);
            echo json_encode([
                "status" => 'ok',
                "message" => $msg,
                "details" => [
                    "posts_count" => $count,
                    "tags_count" => count($tags)
                ]
            ]);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                "status" => 'error',
                "message" => $e->getMessage()
            ]);

            $log->LogInfo('Error al limpiar posts: ' . $e->getMessage(), 'error');
        }

        exit();
    }



    /**
     * Detecta si el servidor está usando HTTPS
     * @return bool
     */
    private function isHttps()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
            return true;
        }
        return false;
    }

    /**
     * Obtiene el protocolo actual
     * @return string 'https' o 'http'
     */
    private function getProtocol()
    {
        return $this->isHttps() ? 'https' : 'http';
    }



    private function clearDirectory($dir)
    {
        if (!is_dir($dir))
            return false;
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->clearDirectory($file) : unlink($file);
            if (is_dir($file))
                @rmdir($file);
        }
        return true;
    }
     /**
     * Envía el header X-LiteSpeed-Purge para limpiar la caché
     * Este es el método que usa el plugin de WordPress
     * 
     * @return bool True si se envió correctamente
     */
    private function sendLiteSpeedPurgeHeader()
    {
        // Verificar si los headers ya fueron enviados
        if (headers_sent()) {
            return false;
        }

        // MÉTODO 1: Enviar header directamente (si estamos en el mismo servidor)
        // Este es el método más directo y eficiente
        if (!headers_sent()) {
            // Purgar TODO (equivalente a *)
            header('X-LiteSpeed-Purge: *');
            
            // También podemos enviar cache control
            header('X-LiteSpeed-Cache-Control: no-cache');
            
            return true;
        }

        return false;
    }

}
