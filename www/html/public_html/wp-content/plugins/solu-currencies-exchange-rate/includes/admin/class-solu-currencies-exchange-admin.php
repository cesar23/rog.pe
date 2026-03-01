<?php
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/admin/utils.php';
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/admin/menus_admin.php';
/**
 * Clase responsable de manejar la administración del plugin "Solu Product Logs"
 *
 * @package SoluProductLogs
 */
class Solu_Currencies_Exchange_Admin
{
    public function __construct(){
        // Registra las funciones enqueue_admin_assets y admin_styles para ser llamadas cuando WordPress encola scripts para la página de administración.
        // Esto asegura que los estilos y scripts del plugin se carguen correctamente en el panel de administración.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }





    /**
     * Cargar los assets CSS y JS en las páginas de administración del plugin
     * @param string $hook_suffix El nombre del hook de la página actual.
     */
    public function enqueue_admin_assets($hook_suffix)
    {
        // Verificar si estamos en la página del plugin 'solu-currencies-exchange' o en una página relacionada.
        if (strpos($hook_suffix, 'page_solu-currencies-exchange') !== false) {
            // Registrar y encolar el CSS y JS de Bootstrap y otros assets
            wp_enqueue_style('solu-currencies-exchange-bootstrap-css', SOLU_CURRENCIES_EXCHANGE_URL . 'assets/admin/bootstrap-5.3.7/css/bootstrap.min.css', array(), '5.3.7'); // CSS de Bootstrap
            wp_enqueue_style('solu-currencies-exchange-prismjs-css', SOLU_CURRENCIES_EXCHANGE_URL . 'assets/admin/prismjs/css/prism.css', array(), '1.29.0'); // CSS de Prism.js

            wp_enqueue_script('solu-currencies-exchange-bootstrap-js', SOLU_CURRENCIES_EXCHANGE_URL . 'assets/admin/bootstrap-5.3.7/js/bootstrap.bundle.min.js', array('jquery'), '5.3.7', true); // JS de Bootstrap
            wp_enqueue_script('solu-currencies-exchange-bootstrap-tooltip-js', SOLU_CURRENCIES_EXCHANGE_URL . 'assets/admin/js/popper.min.js', array('jquery'), '2.11.8', true); // JS de Popper.js (para tooltips de Bootstrap)
            wp_enqueue_script('solu-currencies-exchange-prismjs-js', SOLU_CURRENCIES_EXCHANGE_URL . 'assets/admin/prismjs/js/prism.js', array(), '1.29.0', true); // JS de Prism.js
        }

        
        /**
         * Agrega estilos CSS personalizados al admin
         * Este estilo se aplica a todo el panel de administración de WordPress.
         */
        // Encola el archivo de estilos CSS personalizado para el admin
        wp_enqueue_style('solu-currencies-exchange-admin-styles', SOLU_CURRENCIES_EXCHANGE_URL . 'assets/admin/css/admin-styles.css');
    }

    


    public function display_currencies_page()
    {
        global $wpdb;

        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el formulario
            if (isset($_POST['action'])) {
                $current_user = wp_get_current_user();
                $username_wp = $current_user ? $current_user->user_login : null;
                $id_user_wp = $current_user ? $current_user->ID : null;
                switch ($_POST['action']) {
                    case 'create':
                        // Lógica para crear una nueva moneda
                        if (empty($_POST['currency_code'])) {
                            echo '<div class="alert alert-danger"><p>El campo Moneda es obligatorio. <a href="?page=solu-currencies-exchange">Regresar</a></p></div>';
                            return;
                        }
                        $currency_local = isset($_POST['local']) ? 1 : 0;
                        $existing_local_currency = is_local_currency_exists();
                        if ($currency_local == 1 && $existing_local_currency) {
                            echo '<div class="alert alert-danger"><p>Solo puede haber un registro como Moneda local.</p></div>';
                            return;
                        }
                        $data = array(
                            'user_id' => $id_user_wp,
                            'username' => $username_wp,
                            'currency_name' => sanitize_text_field($_POST['currency_name']),
                            'currency_description' => sanitize_text_field($_POST['currency_description']),
                            'currency_symbol' => sanitize_text_field($_POST['currency_symbol']),
                            'currency_code' => sanitize_text_field($_POST['currency_code']),
                            'currency_value' => sanitize_text_field($_POST['currency_value']),
                            'currency_local' => $currency_local,
                            'currency_order' => sanitize_text_field($_POST['currency_order']),
                            'active' => isset($_POST['active']) ? 1 : 0,
                            'created_at' => solu_get_date_hour_pe(),
                            'update_at' => solu_get_date_hour_pe()
                        );
                        insert_currency($data);
                        // actualizao el fichero json
                        saveStorageTable();
                        save_log_db_table('create', $data);
                        break;
                    case 'update':
                        // Lógica para actualizar una moneda existente
                        if (empty($_POST['currency_code'])) {
                            echo '<div class="alert alert-danger"><p>El campo Moneda es obligatorio. <a href="?page=solu-currencies-exchange">Regresar</a></p></div>';
                            return;
                        }
                        $id = intval($_POST['id']);
                        $currency_local = isset($_POST['local']) ? 1 : 0;
                        $existing_local_currency = is_local_currency_exists();
                        if ($currency_local == 1 && $existing_local_currency && $existing_local_currency['id'] != $id) {
                            echo '<div class="alert alert-danger"><p>Solo puede haber un registro como Moneda local.</p></div>';
                            return;
                        }
                        $data = array(
                            'user_id' => $id_user_wp,
                            'username' => $username_wp,
                            'currency_name' => sanitize_text_field($_POST['currency_name']),
                            'currency_description' => sanitize_text_field($_POST['currency_description']),
                            'currency_symbol' => sanitize_text_field($_POST['currency_symbol']),
                            'currency_code' => sanitize_text_field($_POST['currency_code']),
                            'currency_value' => sanitize_text_field($_POST['currency_value']),
                            'currency_local' => $currency_local,
                            'currency_order' => sanitize_text_field($_POST['currency_order']),
                            'active' => isset($_POST['active']) ? 1 : 0,
                            'update_at' => solu_get_date_hour_pe()
                        );
                        update_currency($id, $data);
                        // actualizao el fichero json
                        saveStorageTable();
                        // guardar un registro log en la db
                        save_log_db_table('update', $data);


                        break;
                    case 'delete':
                        // Lógica para eliminar una moneda
                        $id = intval($_POST['id']);
                        delete_currency($id);
                        break;
                }
            }
        }


        echo '<div class="wrap">';
        echo '<h1>Administración de Monedas</h1>';

        // Incluir la plantilla para listar las monedas
        include SOLU_CURRENCIES_EXCHANGE_PATH . 'templates/admin/list.php';

        echo '<a href="?page=solu-currencies-exchange&action=create" class="page-title-action">Agregar Nueva Moneda</a>';

        // Mostrar la plantilla para crear una nueva moneda si se ha solicitado
        if (isset($_GET['action']) && $_GET['action'] == 'create') {
            include SOLU_CURRENCIES_EXCHANGE_PATH . 'templates/admin/create.php';
        }

        // Mostrar la plantilla para editar una moneda existente si se ha solicitado
        if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $id = intval($_GET['id']);
            $currency = get_currency($id);
            include SOLU_CURRENCIES_EXCHANGE_PATH . 'templates/admin/edit.php';
        }

        // Mostrar la plantilla para eliminar una moneda existente si se ha solicitado
        if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            $id = intval($_GET['id']);
            $currency = get_currency($id);
            include SOLU_CURRENCIES_EXCHANGE_PATH . 'templates/admin/delete.php';
        }

        echo '</div>';
    }
    public function display_info_page()
    {
        include SOLU_CURRENCIES_EXCHANGE_PATH . 'templates/admin/info.php';
    }

    public function display_definir_moneda_page()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el formulario
            if (isset($_POST['action']) && $_POST['action'] == 'create') {
                if (isset($_POST['currency_alternative_code'])) {
                    $currency_alternative_code = sanitize_text_field($_POST['currency_alternative_code']);
                    $currency_alternative = new SoluCurrencyAlternative(
                        currency_alternative_code: $currency_alternative_code
                    );
                    saveStorageCurrencyAlternative($currency_alternative);
                }
            }
        }
        $get_currencies_no_locals = get_currencies_no_locals();
        $get_storage_currency_alternative = getStorageCurrencyAlternative();
        include SOLU_CURRENCIES_EXCHANGE_PATH . 'templates/admin/definir_moneda.php';
    }

    public function display_shorten_titles_page()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['action']) && $_POST['action'] == 'update_shorten_titles') {
                $shorten_titles_option = isset($_POST['shorten_titles_option']) && $_POST['shorten_titles_option'] === 'on' ? 'on' : 'off';
                update_option(SOLU_CURRENCIES_EXCHANGE_OPTION_SHORTEN_TITLE, $shorten_titles_option);
                echo '<div class="toast-container position-fixed bottom-0 end-0 p-3">
                        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                            <img src="..." class="rounded me-2" alt="...">
                            <strong class="me-auto">Bootstrap</strong>
                            <small>11 mins ago</small>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                            Changes saved.
                            </div>
                        </div>
                        </div>';
            }
        }

        include SOLU_CURRENCIES_EXCHANGE_PATH . 'templates/admin/shorten_titles.php';
    }
}
