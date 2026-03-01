<?php

/**
 * Clase principal del plugin "Solu WooCommerce Product Logs"
 *
 * Esta clase es responsable de inicializar el plugin, cargar las dependencias y registrar
 * los hooks necesarios. También implementa el patrón Singleton para asegurarse de que
 * solo se cree una instancia de la clase.
 *
 * @package SoluProductLogs
 */
class Solu_Currencies_Exchange {

    /**
     * Instancia única de la clase (Patrón Singleton)
     *
     * @var Solu_Currencies_Exchange|null
     */
    private static $instance = null;

    /**
     * Constructor privado para evitar múltiples instancias
     *
     * Esta función es privada para asegurar que la clase solo puede ser instanciada desde
     * dentro de la propia clase mediante el método `get_instance()`. Aquí se cargan las
     * dependencias del plugin y se registran los hooks necesarios.
     *
     * Ejemplo de uso:
     *
     * $plugin = Solu_Currencies_Exchange::get_instance();
     *
     * @return void
     */
    private function __construct() {
        // Cargar dependencias del plugin (clases y archivos necesarios)
        $this->load_dependencies();

        // Registrar los hooks necesarios para el funcionamiento del plugin
        $this->define_hooks();

        $this->define_filters();
    }

    /**
     * Obtener la instancia única de la clase (Singleton)
     *
     * Este método asegura que solo se crea una única instancia de la clase `Solu_Currencies_Exchange`.
     * Si no existe una instancia previa, se crea una nueva. Si ya existe, se devuelve la existente.
     *
     * Ejemplo de uso:
     *
     * $plugin = Solu_Currencies_Exchange::get_instance();
     *
     * @return Solu_Currencies_Exchange La instancia única de la clase
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Cargar dependencias del plugin
     *
     * Esta función incluye los archivos necesarios para que el plugin funcione correctamente.
     * En este caso, incluye la clase de administración `Solu_Currencies_Exchange_Admin` que maneja
     * el menú del plugin en el panel de administración.
     *
     * Ejemplo de uso:
     *
     * $this->load_dependencies();
     *
     * @return void
     */
    private function load_dependencies() {
        // Incluir la clase de administración del plugin
        require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/admin/class-solu-currencies-exchange-admin.php';
    }

    /**
     * Registrar los hooks necesarios para el plugin
     *
     * En esta función se definen los hooks que se necesitan para que el plugin funcione correctamente.
     * Aquí se registra el hook `admin_menu` que agrega el menú del plugin en el panel de administración.
     *
     * Ejemplo de uso:
     *
     * $this->define_hooks();
     *
     * @return void
     */
    private function define_hooks() {
        // add_action( 'wp_footer', array( $this, 'display_currencies_list' ) );
        // add_action( 'woocommerce_single_product_summary', 'display_currency_info', 20 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_styles' ) );
    }

    private function define_filters() {
        add_filter('woocommerce_get_price_html', 'solu_currencies_exchange_woocommerce_price_format', 10, 2);
    }

    public function enqueue_front_styles() {
        wp_enqueue_style( 'solu-currencies-exchange-front-styles', SOLU_CURRENCIES_EXCHANGE_URL . 'assets/css/front-styles.css' );
        // cargamos si option es para recortar titulos
        if(get_option(SOLU_CURRENCIES_EXCHANGE_OPTION_SHORTEN_TITLE)== 'on') {
            wp_enqueue_style( 'solu-currencies-exchange-front-styles-short-title', SOLU_CURRENCIES_EXCHANGE_URL . 'assets/css/shorten-title.css' );
        }
    }

    /**
     * Muestra una lista de las monedas disponibles en el front-end.
     */

}

require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/front_filters.php';
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/front_hooks.php';
// adjuntamos las funciones para que despues podamos usalas en el theme ejemplo (wp-content/themes/flatsome-child/woocommerce/single-product/price.php)
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/theme_functions.php';
