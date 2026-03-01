<?php

namespace Libs;

use Libs\WpCore;
use Exception;

/**
 * Clase WpPlugin - Helper para usar funciones de plugins de WordPress
 *
 * Esta clase carga WordPress y permite usar funciones de plugins
 * como Solu Currencies Exchange.
 *
 * @package Libs
 * @version 1.0.0
 */
class WpPlugin
{
    /**
     * @var WpCore Instancia de WpCore
     */
    private $wpcore;

    /**
     * @var bool Indica si el plugin está cargado
     */
    private $plugin_loaded = false;

    /**
     * @var string Ruta del plugin
     */
    private $plugin_path;

    /**
     * Constructor
     *
     * @param string|null $wp_load_path Ruta a wp-load.php (opcional)
     * @throws Exception Si no puede cargar WordPress
     */
    public function __construct($wp_load_path = null)
    {
        // Cargar WordPress usando WpCore
        $this->wpcore = new WpCore($wp_load_path);

        if (!$this->wpcore->isLoaded()) {
            throw new Exception("WordPress no está cargado");
        }
    }

    /**
     * Verifica si un plugin está activo
     *
     * @param string $plugin Ruta del plugin (ej: 'solu-currencies-exchange-rate/solu-currencies-exchange-rate.php')
     * @return bool
     */
    public function isPluginActive($plugin)
    {
        if (!$this->wpcore->isLoaded()) {
            return false;
        }

        return $this->wpcore->call('is_plugin_active', [$plugin]);
    }

    /**
     * Carga un archivo de un plugin
     *
     * @param string $plugin_dir Directorio del plugin (ej: 'solu-currencies-exchange-rate')
     * @param string $file_path Ruta del archivo dentro del plugin (ej: 'includes/storage_functions.php')
     * @return bool True si se cargó correctamente
     * @throws Exception Si el archivo no existe
     */
    public function loadPluginFile($plugin_dir, $file_path)
    {
        // Construir ruta completa
        $full_path = WP_PLUGIN_DIR . '/' . $plugin_dir . '/' . $file_path;

        if (!file_exists($full_path)) {
            throw new Exception("El archivo del plugin no existe: {$full_path}");
        }

        // Cargar el archivo
        require_once $full_path;

        return true;
    }

    /**
     * Ejecuta una función de un plugin
     *
     * @param string $function_name Nombre de la función
     * @param array $args Argumentos de la función
     * @return mixed Resultado de la función
     * @throws Exception Si la función no existe
     */
    public function callPluginFunction($function_name, $args = [])
    {
        if (!function_exists($function_name)) {
            throw new Exception("La función {$function_name} no existe. ¿Cargaste el archivo del plugin?");
        }

        return call_user_func_array($function_name, $args);
    }

    /**
     * Obtiene una constante de un plugin
     *
     * @param string $constant_name Nombre de la constante
     * @return mixed Valor de la constante
     * @throws Exception Si la constante no existe
     */
    public function getPluginConstant($constant_name)
    {
        if (!defined($constant_name)) {
            throw new Exception("La constante {$constant_name} no está definida");
        }

        return constant($constant_name);
    }

    // ==========================================
    // MÉTODOS ESPECÍFICOS PARA SOLU CURRENCIES EXCHANGE
    // ==========================================

    /**
     * Carga el plugin Solu Currencies Exchange
     *
     * @return bool True si se cargó correctamente
     * @throws Exception Si el plugin no existe o no está activo
     */
    public function loadSoluCurrenciesExchange()
    {
        // Verificar si está activo
        $plugin_file = 'solu-currencies-exchange-rate/solu-currencies-exchange-rate.php';

        if (!$this->isPluginActive($plugin_file)) {
            throw new Exception("El plugin Solu Currencies Exchange no está activo");
        }

        // Cargar archivos necesarios
        $plugin_dir = 'solu-currencies-exchange-rate';

        // Cargar entities
        $this->loadPluginFile($plugin_dir, 'includes/entities/SoluCurrenciesExchange.php');
        $this->loadPluginFile($plugin_dir, 'includes/entities/SoluCurrencyAlternative.php');

        // Cargar funciones de storage
        $this->loadPluginFile($plugin_dir, 'includes/storage_functions.php');

        $this->plugin_loaded = true;

        return true;
    }

    /**
     * Guarda las monedas en el archivo JSON usando saveStorageTable()
     *
     * @return array Array de objetos SoluCurrenciesExchange
     * @throws Exception Si el plugin no está cargado
     */
    public function saveCurrenciesStorage()
    {
        if (!$this->plugin_loaded) {
            $this->loadSoluCurrenciesExchange();
        }

        return $this->callPluginFunction('saveStorageTable');
    }

    /**
     * Obtiene todas las monedas desde el archivo JSON
     *
     * @return array Array de monedas
     * @throws Exception Si el plugin no está cargado
     */
    public function getCurrenciesStorage()
    {
        if (!$this->plugin_loaded) {
            $this->loadSoluCurrenciesExchange();
        }

        return $this->callPluginFunction('getStorageTableAll');
    }

    /**
     * Obtiene una moneda por código
     *
     * @param string $currency_code Código de la moneda (ej: 'USD', 'PEN')
     * @return object|null Objeto SoluCurrenciesExchange o null
     * @throws Exception Si el plugin no está cargado
     */
    public function getCurrencyByCode($currency_code)
    {
        if (!$this->plugin_loaded) {
            $this->loadSoluCurrenciesExchange();
        }

        return $this->callPluginFunction('getStorageTableJsonRow', [$currency_code]);
    }

    /**
     * Obtiene la moneda local
     *
     * @return object|null Objeto SoluCurrenciesExchange o null
     * @throws Exception Si el plugin no está cargado
     */
    public function getLocalCurrency()
    {
        if (!$this->plugin_loaded) {
            $this->loadSoluCurrenciesExchange();
        }

        return $this->callPluginFunction('getStorageTableJsonRowLocal');
    }

    /**
     * Guarda la moneda alternativa
     *
     * @param string $currency_code Código de la moneda alternativa
     * @return object SoluCurrencyAlternative
     * @throws Exception Si el plugin no está cargado
     */
    public function saveCurrencyAlternative($currency_code)
    {
        if (!$this->plugin_loaded) {
            $this->loadSoluCurrenciesExchange();
        }

        // Crear objeto SoluCurrencyAlternative
        $currency = new \SoluCurrencyAlternative(
            currency_alternative_code: $currency_code
        );

        return $this->callPluginFunction('saveStorageCurrencyAlternative', [$currency]);
    }

    /**
     * Obtiene la moneda alternativa
     *
     * @return object|null Objeto SoluCurrencyAlternative o null
     * @throws Exception Si el plugin no está cargado
     */
    public function getCurrencyAlternative()
    {
        if (!$this->plugin_loaded) {
            $this->loadSoluCurrenciesExchange();
        }

        return $this->callPluginFunction('getStorageCurrencyAlternative');
    }

    /**
     * Actualiza una moneda en la base de datos
     *
     * @param int $id ID de la moneda
     * @param array $data Datos a actualizar
     * @return bool True si se actualizó
     * @throws Exception Si el plugin no está cargado
     */
    public function updateCurrency($id, $data)
    {
        if (!$this->plugin_loaded) {
            $this->loadSoluCurrenciesExchange();
        }

        // Cargar funciones de admin
        $this->loadPluginFile('solu-currencies-exchange-rate', 'includes/admin/utils.php');

        return $this->callPluginFunction('update_currency', [$id, $data]);
    }

    /**
     * Obtiene información de una moneda desde la BD
     *
     * @param int $id ID de la moneda
     * @return object|null Moneda
     * @throws Exception Si el plugin no está cargado
     */
    public function getCurrency($id)
    {
        if (!$this->plugin_loaded) {
            $this->loadSoluCurrenciesExchange();
        }

        // Cargar funciones de admin
        $this->loadPluginFile('solu-currencies-exchange-rate', 'includes/admin/utils.php');

        return $this->callPluginFunction('get_currency', [$id]);
    }

    /**
     * Obtiene la ruta del archivo JSON de monedas
     *
     * @return string Ruta del archivo
     * @throws Exception Si la constante no existe
     */
    public function getCurrenciesJsonPath()
    {
        return $this->getPluginConstant('SOLU_CURRENCIES_EXCHANGE_STORAGE_JSON');
    }

    /**
     * Obtiene la tabla de monedas de la BD
     *
     * @return string Nombre de la tabla
     * @throws Exception Si la constante no existe
     */
    public function getCurrenciesTableName()
    {
        return $this->getPluginConstant('SOLU_CURRENCIES_EXCHANGE_TABLE');
    }
}
