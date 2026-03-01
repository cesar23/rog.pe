<?php

namespace Libs;

use Exception;

/**
 * Clase WpCore - Carga el core de WordPress para usar funciones nativas
 *
 * Esta clase carga wp-load.php para tener acceso a todas las funciones
 * nativas de WordPress y WooCommerce.
 *
 * VENTAJAS:
 * - Acceso a todas las funciones de WordPress (get_post, wp_insert_post, etc.)
 * - Acceso a funciones de WooCommerce (wc_get_product, etc.)
 * - Hooks y filters de WordPress
 * - API completa de WordPress
 *
 * DESVENTAJAS:
 * - Más lento que WP (carga todo el core)
 * - Mayor consumo de memoria
 * - Puede tener conflictos con headers ya enviados
 *
 * USO RECOMENDADO:
 * - Operaciones complejas que requieren lógica de WordPress/WooCommerce
 * - Cuando necesites hooks/filters
 * - Cuando necesites funciones específicas de plugins
 *
 * @package Libs
 * @version 1.0.0
 */
class WpCore
{
    /**
     * @var bool Indica si WordPress ya está cargado
     */
    private static $wp_loaded = false;

    /**
     * @var string Ruta al wp-load.php
     */
    private $wp_load_path;

    /**
     * Constructor
     *
     * @param string|null $wp_load_path Ruta personalizada a wp-load.php
     * @throws Exception Si no encuentra wp-load.php
     */
    public function __construct($wp_load_path = null)
    {
        if (self::$wp_loaded) {
            return; // Ya está cargado
        }

        // Determinar ruta a wp-load.php
        if ($wp_load_path !== null) {
            $this->wp_load_path = $wp_load_path;
        } else {
            // Intentar detectar automáticamente
            $this->wp_load_path = $this->detectWpLoadPath();
        }

        // Validar que existe
        if (!file_exists($this->wp_load_path)) {
            throw new Exception("No se encontró wp-load.php en: {$this->wp_load_path}");
        }

        // Cargar WordPress
        $this->loadWordPress();
    }

    /**
     * Detecta automáticamente la ruta a wp-load.php
     *
     * @return string Ruta a wp-load.php
     * @throws Exception Si no encuentra wp-load.php
     */
    private function detectWpLoadPath()
    {
        // Opciones de rutas posibles
        $possible_paths = [
            // Relativo a api_rest_sync (caso común)
            __DIR__ . '/../../wp-load.php',
            __DIR__ . '/../../../wp-load.php',
            __DIR__ . '/../../../../wp-load.php',

            // Desde la raíz del sistema
            SYSTEM_PATH . '../wp-load.php',
            SYSTEM_PATH . '../../wp-load.php',

            // Rutas absolutas comunes
            $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php',
            $_SERVER['DOCUMENT_ROOT'] . '/../wp-load.php',
        ];

        foreach ($possible_paths as $path) {
            $normalized = realpath($path);
            if ($normalized && file_exists($normalized)) {
                return $normalized;
            }
        }

        throw new Exception("No se pudo detectar automáticamente wp-load.php. Especifica la ruta manualmente.");
    }

    /**
     * Carga el core de WordPress
     *
     * @throws Exception Si hay error al cargar
     */
    private function loadWordPress()
    {
        try {
            // Definir constantes necesarias
            if (!defined('SHORTINIT')) {
                define('SHORTINIT', false); // Cargar WordPress completo
            }

            // Suprimir headers de WordPress si ya se enviaron
            if (headers_sent()) {
                // Usar output buffering para evitar conflictos
                ob_start();
            }

            // Cargar WordPress
            require_once $this->wp_load_path;

            // Limpiar output buffer si se usó
            if (ob_get_length()) {
                ob_end_clean();
            }

            self::$wp_loaded = true;

        } catch (Exception $e) {
            throw new Exception("Error al cargar WordPress: " . $e->getMessage());
        }
    }

    /**
     * Verifica si WordPress está cargado
     *
     * @return bool
     */
    public function isLoaded()
    {
        return self::$wp_loaded && function_exists('get_post');
    }

    // ==========================================
    // POSTS - Funciones nativas de WordPress
    // ==========================================

    /**
     * Obtiene un post usando get_post()
     *
     * @param int $post_id ID del post
     * @param string $output Tipo de salida (OBJECT, ARRAY_A, ARRAY_N)
     * @return WP_Post|array|null Post encontrado
     */
    public function getPost($post_id, $output = OBJECT)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return get_post($post_id, $output);
    }

    /**
     * Obtiene posts usando get_posts()
     *
     * @param array $args Argumentos de get_posts
     * @return array Array de WP_Post
     */
    public function getPosts($args = [])
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return get_posts($args);
    }

    /**
     * Crea un nuevo post usando wp_insert_post()
     *
     * @param array $post_data Datos del post
     * @return int|WP_Error ID del post creado o WP_Error
     */
    public function insertPost($post_data)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return wp_insert_post($post_data);
    }

    /**
     * Actualiza un post usando wp_update_post()
     *
     * @param array $post_data Datos del post (debe incluir 'ID')
     * @return int|WP_Error ID del post o WP_Error
     */
    public function updatePost($post_data)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return wp_update_post($post_data);
    }

    /**
     * Elimina un post usando wp_delete_post()
     *
     * @param int $post_id ID del post
     * @param bool $force_delete Si true, elimina permanentemente
     * @return WP_Post|false|null Post eliminado o false
     */
    public function deletePost($post_id, $force_delete = false)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return wp_delete_post($post_id, $force_delete);
    }

    // ==========================================
    // POSTMETA - Funciones nativas
    // ==========================================

    /**
     * Obtiene un metadato usando get_post_meta()
     *
     * @param int $post_id ID del post
     * @param string $meta_key Clave del metadato
     * @param bool $single Si true, devuelve valor único
     * @return mixed Valor del metadato
     */
    public function getPostMeta($post_id, $meta_key = '', $single = true)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return get_post_meta($post_id, $meta_key, $single);
    }

    /**
     * Actualiza un metadato usando update_post_meta()
     *
     * @param int $post_id ID del post
     * @param string $meta_key Clave del metadato
     * @param mixed $meta_value Valor del metadato
     * @param mixed $prev_value Valor anterior (opcional)
     * @return int|bool Meta ID si se creó, true si se actualizó, false en error
     */
    public function updatePostMeta($post_id, $meta_key, $meta_value, $prev_value = '')
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
    }

    /**
     * Elimina un metadato usando delete_post_meta()
     *
     * @param int $post_id ID del post
     * @param string $meta_key Clave del metadato
     * @param mixed $meta_value Valor específico a eliminar (opcional)
     * @return bool True si se eliminó
     */
    public function deletePostMeta($post_id, $meta_key, $meta_value = '')
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return delete_post_meta($post_id, $meta_key, $meta_value);
    }

    /**
     * Añade un metadato usando add_post_meta()
     *
     * @param int $post_id ID del post
     * @param string $meta_key Clave del metadato
     * @param mixed $meta_value Valor del metadato
     * @param bool $unique Si true, no añade si ya existe
     * @return int|false Meta ID o false
     */
    public function addPostMeta($post_id, $meta_key, $meta_value, $unique = false)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return add_post_meta($post_id, $meta_key, $meta_value, $unique);
    }

    // ==========================================
    // OPTIONS - Funciones nativas
    // ==========================================

    /**
     * Obtiene una opción usando get_option()
     *
     * @param string $option_name Nombre de la opción
     * @param mixed $default Valor por defecto
     * @return mixed Valor de la opción
     */
    public function getOption($option_name, $default = false)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return get_option($option_name, $default);
    }

    /**
     * Actualiza una opción usando update_option()
     *
     * @param string $option_name Nombre de la opción
     * @param mixed $option_value Valor de la opción
     * @param string|bool $autoload Si se carga automáticamente
     * @return bool True si se actualizó
     */
    public function updateOption($option_name, $option_value, $autoload = null)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return update_option($option_name, $option_value, $autoload);
    }

    /**
     * Elimina una opción usando delete_option()
     *
     * @param string $option_name Nombre de la opción
     * @return bool True si se eliminó
     */
    public function deleteOption($option_name)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return delete_option($option_name);
    }

    // ==========================================
    // WOOCOMMERCE - Funciones nativas
    // ==========================================

    /**
     * Obtiene un producto usando wc_get_product()
     *
     * @param int|WC_Product $product ID del producto o objeto
     * @return WC_Product|false Producto o false
     */
    public function getProduct($product)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        if (!function_exists('wc_get_product')) {
            throw new Exception("WooCommerce no está activo");
        }

        return wc_get_product($product);
    }

    /**
     * Obtiene un producto por SKU usando wc_get_product_id_by_sku()
     *
     * @param string $sku SKU del producto
     * @return int|false ID del producto o false
     */
    public function getProductIdBySku($sku)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        if (!function_exists('wc_get_product_id_by_sku')) {
            throw new Exception("WooCommerce no está activo");
        }

        return wc_get_product_id_by_sku($sku);
    }

    /**
     * Obtiene un producto por SKU (objeto completo)
     *
     * @param string $sku SKU del producto
     * @return WC_Product|false Producto o false
     */
    public function getProductBySku($sku)
    {
        $product_id = $this->getProductIdBySku($sku);

        if (!$product_id) {
            return false;
        }

        return $this->getProduct($product_id);
    }

    /**
     * Actualiza el stock de un producto usando WooCommerce
     *
     * @param int $product_id ID del producto
     * @param int|float $stock_quantity Cantidad de stock
     * @param string $operation set, increase, decrease
     * @return int|bool Nueva cantidad de stock
     */
    public function updateProductStock($product_id, $stock_quantity, $operation = 'set')
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        $product = $this->getProduct($product_id);

        if (!$product) {
            return false;
        }

        // Actualizar stock según operación
        switch ($operation) {
            case 'set':
                $product->set_stock_quantity($stock_quantity);
                break;
            case 'increase':
                $product->set_stock_quantity($product->get_stock_quantity() + $stock_quantity);
                break;
            case 'decrease':
                $product->set_stock_quantity($product->get_stock_quantity() - $stock_quantity);
                break;
        }

        // Actualizar estado de stock
        if ($product->get_stock_quantity() > 0) {
            $product->set_stock_status('instock');
        } else {
            $product->set_stock_status('outofstock');
        }

        $product->save();

        return $product->get_stock_quantity();
    }

    /**
     * Actualiza el precio de un producto usando WooCommerce
     *
     * @param int $product_id ID del producto
     * @param float $price Precio regular
     * @param float|null $sale_price Precio de oferta (opcional)
     * @return bool True si se actualizó
     */
    public function updateProductPrice($product_id, $price, $sale_price = null)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        $product = $this->getProduct($product_id);

        if (!$product) {
            return false;
        }

        // Actualizar precio regular
        $product->set_regular_price($price);

        // Actualizar precio de oferta si se proporciona
        if ($sale_price !== null && $sale_price < $price) {
            $product->set_sale_price($sale_price);
            $product->set_price($sale_price);
        } else {
            $product->set_sale_price('');
            $product->set_price($price);
        }

        $product->save();

        return true;
    }

    /**
     * Crea un nuevo producto de WooCommerce
     *
     * @param array $product_data Datos del producto
     * @return int|WP_Error ID del producto o WP_Error
     */
    public function createProduct($product_data)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        if (!function_exists('wc_get_product')) {
            throw new Exception("WooCommerce no está activo");
        }

        $product = new \WC_Product_Simple();

        // Datos básicos
        if (isset($product_data['name'])) {
            $product->set_name($product_data['name']);
        }

        if (isset($product_data['sku'])) {
            $product->set_sku($product_data['sku']);
        }

        if (isset($product_data['price'])) {
            $product->set_regular_price($product_data['price']);
            $product->set_price($product_data['price']);
        }

        if (isset($product_data['stock'])) {
            $product->set_stock_quantity($product_data['stock']);
            $product->set_manage_stock(true);
            $product->set_stock_status($product_data['stock'] > 0 ? 'instock' : 'outofstock');
        }

        if (isset($product_data['description'])) {
            $product->set_description($product_data['description']);
        }

        if (isset($product_data['short_description'])) {
            $product->set_short_description($product_data['short_description']);
        }

        // Guardar
        $product_id = $product->save();

        return $product_id;
    }

    // ==========================================
    // USUARIOS - Funciones nativas
    // ==========================================

    /**
     * Obtiene un usuario usando get_userdata()
     *
     * @param int $user_id ID del usuario
     * @return WP_User|false Usuario o false
     */
    public function getUser($user_id)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return get_userdata($user_id);
    }

    /**
     * Obtiene un usuario por email usando get_user_by()
     *
     * @param string $email Email del usuario
     * @return WP_User|false Usuario o false
     */
    public function getUserByEmail($email)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return get_user_by('email', $email);
    }

    /**
     * Obtiene metadatos de usuario usando get_user_meta()
     *
     * @param int $user_id ID del usuario
     * @param string $meta_key Clave del metadato
     * @param bool $single Si true, devuelve valor único
     * @return mixed Valor del metadato
     */
    public function getUserMeta($user_id, $meta_key = '', $single = true)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return get_user_meta($user_id, $meta_key, $single);
    }

    /**
     * Actualiza metadatos de usuario usando update_user_meta()
     *
     * @param int $user_id ID del usuario
     * @param string $meta_key Clave del metadato
     * @param mixed $meta_value Valor del metadato
     * @return int|bool Meta ID o true/false
     */
    public function updateUserMeta($user_id, $meta_key, $meta_value)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return update_user_meta($user_id, $meta_key, $meta_value);
    }

    // ==========================================
    // TAXONOMÍAS - Funciones nativas
    // ==========================================

    /**
     * Obtiene términos usando get_terms()
     *
     * @param array $args Argumentos de get_terms
     * @return array|WP_Error Array de términos o WP_Error
     */
    public function getTerms($args = [])
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return get_terms($args);
    }

    /**
     * Obtiene las categorías de producto de WooCommerce
     *
     * @param array $args Argumentos adicionales
     * @return array Array de categorías
     */
    public function getProductCategories($args = [])
    {
        $default_args = [
            'taxonomy' => 'product_cat',
            'hide_empty' => false
        ];

        $args = array_merge($default_args, $args);

        return $this->getTerms($args);
    }

    // ==========================================
    // UTILIDADES
    // ==========================================

    /**
     * Ejecuta una función de WordPress directamente
     *
     * @param string $function_name Nombre de la función
     * @param array $args Argumentos de la función
     * @return mixed Resultado de la función
     * @throws Exception Si la función no existe
     */
    public function call($function_name, $args = [])
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        if (!function_exists($function_name)) {
            throw new Exception("La función {$function_name} no existe");
        }

        return call_user_func_array($function_name, $args);
    }

    /**
     * Ejecuta un hook/action de WordPress
     *
     * @param string $hook_name Nombre del hook
     * @param mixed ...$args Argumentos del hook
     */
    public function doAction($hook_name, ...$args)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        do_action($hook_name, ...$args);
    }

    /**
     * Aplica un filter de WordPress
     *
     * @param string $filter_name Nombre del filter
     * @param mixed $value Valor a filtrar
     * @param mixed ...$args Argumentos adicionales
     * @return mixed Valor filtrado
     */
    public function applyFilters($filter_name, $value, ...$args)
    {
        if (!$this->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }

        return apply_filters($filter_name, $value, ...$args);
    }

    /**
     * Verifica si WooCommerce está activo
     *
     * @return bool
     */
    public function isWooCommerceActive()
    {
        return $this->isLoaded() && function_exists('WC');
    }

    /**
     * Obtiene la versión de WordPress
     *
     * @return string Versión de WordPress
     */
    public function getWordPressVersion()
    {
        global $wp_version;
        return $wp_version ?? 'unknown';
    }

    /**
     * Obtiene la versión de WooCommerce
     *
     * @return string|null Versión de WooCommerce o null
     */
    public function getWooCommerceVersion()
    {
        if (!$this->isWooCommerceActive()) {
            return null;
        }

        return WC()->version ?? null;
    }
}
