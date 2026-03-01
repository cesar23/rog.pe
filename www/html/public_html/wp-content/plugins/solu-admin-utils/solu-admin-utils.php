<?php

/**
 * Plugin Name: Solu Admin Utils
 * Description: Herramientas administrativas para WordPress/WooCommerce - Generador HTML, filtros avanzados y utilidades
 * Version: 1.2.0
 * Author: César Auris [perucaos@gmail.com]
 * Author URI: https://solucionessystem.com
 * Requires at least: 6.7
 * Requires PHP: 8.0
 * License: GPLv2 or later
 * Text Domain: solu-admin-utils
 */

// Acceder a la instancia global de la base de datos de WordPress
global $wpdb;

// ====================================================
// Definición de constantes
// ====================================================
// Nombre del plugin
define('SOLU_ADMIN_UTIL_NAME_PLUGIN', 'solu_admin_util');
// Versión del plugin
define('SOLU_ADMIN_UTIL_VERSION', '1.2.0');
// Ruta del directorio del plugin
define('SOLU_ADMIN_UTIL_PATH', plugin_dir_path(__FILE__));
// URL del directorio del plugin
define('SOLU_ADMIN_UTIL_URL', plugin_dir_url(__FILE__));
// Nombre de la tabla principal del plugin en la base de datos
define('SOLU_ADMIN_UTIL_TABLE', $wpdb->prefix . SOLU_ADMIN_UTIL_NAME_PLUGIN);
// Nombre de la tabla de logs del plugin en la base de datos
define('SOLU_ADMIN_UTIL_TABLE_LOG', $wpdb->prefix . SOLU_ADMIN_UTIL_NAME_PLUGIN . '_log');
// Optiones del plugin en la base de datos
define('SOLU_ADMIN_UTIL_OPTION_VERSION_PLUGIN', SOLU_ADMIN_UTIL_NAME_PLUGIN . '_version');



// ====================================================
// Inclusión de archivos
// ====================================================

// Funciones utilitarios
require_once SOLU_ADMIN_UTIL_PATH . 'utils/DateUtils.php';
require_once SOLU_ADMIN_UTIL_PATH . 'utils/PriceUtils.php';
require_once SOLU_ADMIN_UTIL_PATH . 'utils/LogUtils.php';
require_once SOLU_ADMIN_UTIL_PATH . 'utils/StringUtils.php';
require_once SOLU_ADMIN_UTIL_PATH . 'utils/AdminNotices.php';

// Funciones generales del plugin (install, desactive, otras funciones como creación tablas)
require_once SOLU_ADMIN_UTIL_PATH . 'includes/functions.php';

// Variables globales compartidas
require_once SOLU_ADMIN_UTIL_PATH . 'includes/config/global_vars.php';

// ====================================================
// Backend - Administración
// ====================================================
require_once SOLU_ADMIN_UTIL_PATH . 'includes/admin/class-solu-admin-utils-home.php';
require_once SOLU_ADMIN_UTIL_PATH . 'includes/admin/menus.php';

// Clase de funciones del backend de woocommerce
require_once SOLU_ADMIN_UTIL_PATH . 'includes/admin/woocommerce/class-solu-admin-woocommerce.php';

// ====================================================
// Función para inicializar el plugin
// ====================================================
function solu_admin_util_init()
{
  // ====================================================
  // Inicializar sistema de notificaciones
  // ====================================================
  AdminNotices::init();

  // ====================================================
  // Modulo del Backend
  // ====================================================
  // La clase admin se instancia automáticamente en menus.php

  // solu_log('Plugin inicializado correctamente', 'info');
}

// ====================================================
// Acciones y filtros
// ====================================================
// Inicializar el plugin cuando se carguen los plugins
add_action('plugins_loaded', 'solu_admin_util_init');

// Crear la tabla de logs al activar el plugin
register_activation_hook(__FILE__, 'solu_admin_utils_plugin_activation');

// cuando se  desactiva
register_deactivation_hook(__FILE__, 'solu_admin_utils_plugin_disable');

