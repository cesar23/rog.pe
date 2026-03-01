<?php

/**
 * Plugin Name: Solu WooCommerce Product Logs
 * Description: Logs user actions (create, update, delete) for WooCommerce products.
 * Version: 1.0.0
 * Author: César Auris [perucaos@gmail.com]
 */

global $wpdb;

// Definir constantes
// Definir la constante con la versión del plugin
define('SOLU_PRODUCT_LOGS_VERSION', '1.0.1');
define('SOLU_PRODUCT_LOGS_PATH', plugin_dir_path(__FILE__));
define('SOLU_PRODUCT_LOGS_URL', plugin_dir_url(__FILE__));
define('SOLU_PRODUCT_LOGS_TABLE', $wpdb->prefix . 'product_logs');
define('SOLU_PRODUCT_LOGS_TABLE_DEBUG', $wpdb->prefix . 'product_logs_debug');

// SOLO ESTOS USUARIOS PUEDEN VER EL MENU
$allowed_emails = array('perucaos@gmail.com', 'editor2@solucionesssystem.com', 'juan@gmail.com', 'ventas@pcbyte.com.pe'); // Solo estos correos tendrán acceso

// Mostrar mensaje si WooCommerce no está activo
function wc_product_logs_admin_notice()
{
    echo '<div class="notice notice-error is-dismissible">
            <p><strong>Solu WooCommerce Product Logs</strong> requiere que <strong>WooCommerce</strong> esté activado.</p>
          </div>';
}

// Incluir archivos principales
require_once SOLU_PRODUCT_LOGS_PATH . 'includes/class-solu-product-logs.php';
require_once SOLU_PRODUCT_LOGS_PATH . 'includes/functions.php';
require_once SOLU_PRODUCT_LOGS_PATH . 'includes/admin/utils.php';
require_once SOLU_PRODUCT_LOGS_PATH . 'includes/hooks.php';

// Inicializar el plugin
function solu_product_logs_init()
{
    // Verificar si WooCommerce está activo
    if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action('admin_notices', 'wc_product_logs_admin_notice');
        return;
    }


    Solu_Product_Logs::get_instance();
}
add_action('plugins_loaded', 'solu_product_logs_init');

// Crear la tabla de logs al activar el plugin
register_activation_hook(__FILE__, 'plugin_activation');
// cuando se  desactiva
register_deactivation_hook(__FILE__, 'plugin_disable');
// Hook para manejar la desinstalación del plugin (el archivo uninstall.php)
//register_uninstall_hook(__FILE__, 'plugin_uninstall');
