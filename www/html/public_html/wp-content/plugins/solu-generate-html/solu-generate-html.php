<?php

/**
 * Plugin Name: Solu Generate HTML
 * Description: Plugin para generar y gestionar códigos HTML desde el backend de WordPress
 * Version: 1.2.0
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
define('SOLU_GENERATE_HTML_NAME_PLUGIN', 'solu_generate_html');
// Versión del plugin
define('SOLU_GENERATE_HTML_VERSION', '1.0.4');
// Ruta del directorio del plugin
define('SOLU_GENERATE_HTML_PATH', plugin_dir_path(__FILE__));
// URL del directorio del plugin
define('SOLU_GENERATE_HTML_URL', plugin_dir_url(__FILE__));
// Nombre de la tabla principal del plugin en la base de datos
define('SOLU_GENERATE_HTML_TABLE', $wpdb->prefix . SOLU_GENERATE_HTML_NAME_PLUGIN);
// Nombre de la tabla de logs del plugin en la base de datos
define('SOLU_GENERATE_HTML_TABLE_LOG', $wpdb->prefix . SOLU_GENERATE_HTML_NAME_PLUGIN . '_log');
// Optiones del plugin en la base de datos
define('SOLU_GENERATE_HTML_OPTION_VERSION_PLUGIN', SOLU_GENERATE_HTML_NAME_PLUGIN . '_version');
// Ruta al archivo JSON que almacena la información de los códigos HTML
define('SOLU_GENERATE_HTML_STORAGE_JSON', WP_CONTENT_DIR . '/uploads/solu-html-storage/html-codes.json');


// ====================================================
// Función para mostrar un mensaje de error si WooCommerce no está activo
// ====================================================
function solu_generate_html_admin_notice()
{
    echo '<div class="notice notice-error is-dismissible">
            <p><strong>Solu Generate HTML</strong> requiere que <strong>WooCommerce</strong> esté activado.</p>
            </div>';
}

// ====================================================
// Inclusión de archivos
// ====================================================

// Funciones utilitarios
require_once SOLU_GENERATE_HTML_PATH . 'includes/utils/DateUtils.php';
require_once SOLU_GENERATE_HTML_PATH . 'includes/utils/PriceUtils.php';
require_once SOLU_GENERATE_HTML_PATH . 'includes/utils/LogUtils.php';
require_once SOLU_GENERATE_HTML_PATH . 'includes/utils/StringUtils.php';

// Clase de funciones del backend
require_once SOLU_GENERATE_HTML_PATH . 'includes/admin/class-backend-functions.php';

// Funciones generales del plugin (install, desactive, otras funciones como creación tablas)
require_once SOLU_GENERATE_HTML_PATH . 'includes/functions.php';

// El sistema de storage JSON se eliminó para simplificar la estructura

// Variables globales compartidas
require_once SOLU_GENERATE_HTML_PATH . 'includes/config/global_vars.php';

// ====================================================
// Backend - Administración
// ====================================================
require_once SOLU_GENERATE_HTML_PATH . 'includes/admin/clas-solu-generate-html-inicio.php';
require_once SOLU_GENERATE_HTML_PATH . 'includes/admin/clas-solu-generate-html-categoria.php';
require_once SOLU_GENERATE_HTML_PATH . 'includes/admin/menus.php';

// Incluir acciones backend personalizadas (AJAX, etc)
require_once SOLU_GENERATE_HTML_PATH . 'includes/admin/backend_action.php';
// Incluir filtros backend personalizados (si los hay)
require_once SOLU_GENERATE_HTML_PATH . 'includes/admin/backend_filters.php';

// ====================================================
// Función para inicializar el plugin
// ====================================================
function solu_generate_html_init()
{
    // Verificar si WooCommerce está activo
    if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action('admin_notices', 'solu_generate_html_admin_notice');
        solu_log('Plugin no activado: WooCommerce no está disponible', 'error');
        return;
    }

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
add_action('plugins_loaded', 'solu_generate_html_init');

// Crear la tabla de logs al activar el plugin
register_activation_hook(__FILE__, 'solu_generate_html_plugin_activation');

// cuando se  desactiva
register_deactivation_hook(__FILE__, 'solu_generate_html_plugin_disable');

// Hook para manejar la desinstalación del plugin (el archivo uninstall.php)
//register_uninstall_hook(__FILE__, 'plugin_uninstall');

// El menú de administración ahora se maneja a través del Module Manager
