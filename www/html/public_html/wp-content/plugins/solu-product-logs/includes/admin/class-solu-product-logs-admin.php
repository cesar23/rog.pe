<?php

/**
 * Clase responsable de manejar la administración del plugin "Solu Product Logs"
 *
 * @package SoluProductLogs
 */
class Solu_Product_Logs_Admin {

    /**
     * Agrega el menú del plugin en el panel de administración de WordPress
     */
    public function add_plugin_menu() {
        global $allowed_emails;
        $current_user = wp_get_current_user();

        if (in_array($current_user->user_email, $allowed_emails)) {
            add_menu_page(
                'Product Logs',
                'Product Logs',
                'manage_options',
                'solu-product-logs',
                array($this, 'display_logs_page'),
                'dashicons-clipboard',
                26
            );
        }
    }

    /**
     * Cargar los assets CSS y JS en las páginas de administración del plugin
     */
    public function enqueue_admin_assets($hook_suffix) {
        // Verificar si estamos en la página del plugin 'solu-product-logs'
        if ($hook_suffix == 'toplevel_page_solu-product-logs') {
            // Registrar y encolar el CSS y JS de Bootstrap y otros assets
            wp_enqueue_style('solu-product-logs-bootstrap-css', SOLU_PRODUCT_LOGS_URL. 'assets/admin/bootstrap.min.css');
            wp_enqueue_script('solu-product-logs-bootstrap-js', SOLU_PRODUCT_LOGS_URL. 'assets/admin/bootstrap.bundle.min.js', array(), false, true);
        }
    }

    /**
     * Muestra la página de logs en el panel de administración
     */
    public function display_logs_page() {
        include SOLU_PRODUCT_LOGS_PATH . 'templates/logs-template.php';
    }

}
