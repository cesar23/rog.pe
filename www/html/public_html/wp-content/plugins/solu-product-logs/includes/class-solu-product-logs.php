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
class Solu_Product_Logs {

    /**
     * Instancia única de la clase (Patrón Singleton)
     *
     * @var Solu_Product_Logs|null
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
     * $plugin = Solu_Product_Logs::get_instance();
     *
     * @return void
     */
    private function __construct() {
        // Cargar dependencias del plugin (clases y archivos necesarios)
        $this->load_dependencies();

        // Registrar los hooks necesarios para el funcionamiento del plugin
        $this->define_hooks();
    }

    /**
     * Obtener la instancia única de la clase (Singleton)
     *
     * Este método asegura que solo se crea una única instancia de la clase `Solu_Product_Logs`.
     * Si no existe una instancia previa, se crea una nueva. Si ya existe, se devuelve la existente.
     *
     * Ejemplo de uso:
     *
     * $plugin = Solu_Product_Logs::get_instance();
     *
     * @return Solu_Product_Logs La instancia única de la clase
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
     * En este caso, incluye la clase de administración `Solu_Product_Logs_Admin` que maneja
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
        require_once SOLU_PRODUCT_LOGS_PATH . 'includes/admin/class-solu-product-logs-admin.php';
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
        // Crear una instancia de la clase de administración
        $plugin_admin = new Solu_Product_Logs_Admin();

        // Registrar el hook para agregar el menú en el panel de administración
        add_action('admin_menu', array($plugin_admin, 'add_plugin_menu'));

        // Registrar el hook para encolar los assets del plugin en las páginas de administración
        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_admin_assets'));
    }
}
