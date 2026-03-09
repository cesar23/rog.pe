<?php

// Prevenir acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin Name: Solu Currencies Exchange
 * Description:  Manejo de monedas para Woocomerce, y flatsome
 * Version: 2.1.0
 * Author: César Auris [perucaos@gmail.com]
 * Author URI: https://solucionessystem.com
 * Requires at least: 6.7
 * Requires PHP: 8.0
 * License: GPLv2 or later
 */

// Acceder a la instancia global de la base de datos de WordPress
global $wpdb;

// ====================================================
// Definición de constantes
// ====================================================
// Nombre del plugin
define('SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN', 'solu_currencies_exchange');
// Versión del plugin
define('SOLU_CURRENCIES_EXCHANGE_VERSION', '2.2.0');
// Ruta del directorio del plugin
define('SOLU_CURRENCIES_EXCHANGE_PATH', plugin_dir_path(__FILE__));
// URL del directorio del plugin
define('SOLU_CURRENCIES_EXCHANGE_URL', plugin_dir_url(__FILE__));
// Nombre de la tabla principal del plugin en la base de datos
define('SOLU_CURRENCIES_EXCHANGE_TABLE', $wpdb->prefix . SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN);
// Nombre de la tabla de logs del plugin en la base de datos
define('SOLU_CURRENCIES_EXCHANGE_TABLE_LOG', $wpdb->prefix . SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN . '_log');
// Optiones del plugin en la base de datos
define('SOLU_CURRENCIES_EXCHANGE_OPTION_VERSION_PLUGIN', SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN . '_version');
define('SOLU_CURRENCIES_EXCHANGE_OPTION_SHORTEN_TITLE', SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN . '_shorten_title');
// Ruta al archivo JSON que almacena la información de las monedas
define('SOLU_CURRENCIES_EXCHANGE_STORAGE_JSON', WP_CONTENT_DIR . '/uploads/solu-currencies-storage/currencies.json');
// Ruta al archivo JSON que almacena la información de las monedas alternativas
define('SOLU_CURRENCY_ALTERNATIVE_STORAGE_JSON', WP_CONTENT_DIR . '/uploads/solu-currencies-storage/currency_alternative.json');


// ====================================================
// Función para mostrar un mensaje de error si WooCommerce no está activo
// ====================================================
function solu_currencies_exchange_admin_notice()
{
    echo '<div class="notice notice-error is-dismissible">
            <p><strong>Solu Currencies Exchange</strong> requiere que <strong>WooCommerce</strong> esté activado.</p>
            </div>';
}

// ====================================================
// Inclusión de archivos
// ====================================================

// Funciones utilitarios
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/utils/DateUtils.php';
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/utils/PriceUtils.php';
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/utils/LogUtils.php';

// Funciones generales del plugin (install, desactive, otras funciones como creación tablas)
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/functions.php';


// Funciones para manejo de storage json (Tablas en json, para uso de chache)
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/storage_functions.php';

// Variables globales compartidas
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/config/global_vars.php';
/*
 * === Front para el cliente ===
 * Incluye los ficheros:
 *  - /includes/front_filters.php
 *  - /includes/front_hooks.php
 *  - /includes/theme_functions.php
 */
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/class-solu-currencies-exchange.php';
/*
 * === Back para el admin ===
 * Incluye los ficheros:
 *  - /includes/admin/utils.php
 *  - /includes/admin/menus_admin.php
 */
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/admin/class-solu-currencies-exchange-admin.php';


// ====================================================
// Función para inicializar el plugin
// ====================================================
function solu_currencies_exchange_init()
{
    // Verificar si WooCommerce está activo
    if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action('admin_notices', 'solu_currencies_exchange_admin_notice');
        return;
    }

    // ====================================================
    // Modulo del front
    // ====================================================
    Solu_Currencies_Exchange::get_instance();

    // ====================================================
    // Modulo del Backend
    // ====================================================
    $Solu_Currencies_Exchange_Admin = new Solu_Currencies_Exchange_Admin();
}

// ====================================================
// Acciones y filtros
// ====================================================
// Inicializar el plugin cuando se carguen los plugins
add_action('plugins_loaded', 'solu_currencies_exchange_init');

// Crear la tabla de logs al activar el plugin
register_activation_hook(__FILE__, 'solu_currencies_exchange_plugin_activation');

// cuando se  desactiva
register_deactivation_hook(__FILE__, 'solu_currencies_exchange_plugin_disable');

// Hook para manejar la desinstalación del plugin (el archivo uninstall.php)
//register_uninstall_hook(__FILE__, 'plugin_uninstall');

// Agrega el menú de administración
add_action('admin_menu', 'solu_currencies_exchange_add_admin_menus');
