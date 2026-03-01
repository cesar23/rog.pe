<?php

/**
 * Clase principal de administración del plugin Solu Admin Utils
 *
 * Maneja toda la funcionalidad del backend de forma simple y estándar.
 * Incluye la gestión de assets, páginas de administración y funcionalidades
 * principales del plugin.
 *
 * @package Solu_Admin_Utils
 * @since 1.2.0
 * @author César Auris [perucaos@gmail.com]
 * 
 * @example
 * // La clase se instancia automáticamente en menus.php
 * $admin_home = new Solu_Admin_Utils_Home();
 */
class Solu_Admin_Utils_Home
{
  /**
   * Constructor de la clase
   * 
   * Inicializa la clase y carga los assets necesarios para el admin
   * 
   * @since 1.2.0
   */
  public function __construct()
  {
    $hook_suffix = '';
    if (is_admin()) {
        $this->enqueue_admin_assets($hook_suffix);
    }
  }

  /**
   * Cargar los assets CSS y JS en las páginas de administración
   * 
   * @param string $hook_suffix Sufijo del hook de la página actual
   * @since 1.2.0
   */
  public function enqueue_admin_assets($hook_suffix)
  {
    // Verificar si estamos en una página del plugin
    if (strpos($hook_suffix, SOLU_ADMIN_UTIL_NAME_PLUGIN) !== false) {
      // Bootstrap CSS y JS
      wp_enqueue_style(SOLU_ADMIN_UTIL_NAME_PLUGIN.'-bootstrap-css', SOLU_ADMIN_UTIL_URL . 'assets/admin/bootstrap-5.3.7/css/bootstrap.min.css', array(), '5.3.7');
      wp_enqueue_script(SOLU_ADMIN_UTIL_NAME_PLUGIN.'-bootstrap-js', SOLU_ADMIN_UTIL_URL . 'assets/admin/bootstrap-5.3.7/js/bootstrap.bundle.min.js', array('jquery'), '5.3.7', true);

      // Prism.js para resaltado de sintaxis
      wp_enqueue_style(SOLU_ADMIN_UTIL_NAME_PLUGIN.'-prismjs-css', SOLU_ADMIN_UTIL_URL . 'assets/admin/prismjs/css/prism.css', array(), '1.29.0');
      wp_enqueue_script(SOLU_ADMIN_UTIL_NAME_PLUGIN.'-prismjs-js', SOLU_ADMIN_UTIL_URL . 'assets/admin/prismjs/js/prism.js', array(), '1.29.0', true);

      // Estilos personalizados
      wp_enqueue_style(SOLU_ADMIN_UTIL_NAME_PLUGIN.'-admin-styles', SOLU_ADMIN_UTIL_URL . 'assets/admin/css/admin-styles.css', array(), SOLU_ADMIN_UTIL_VERSION);
    }
  }

  /**
   * Página de inicio principal del plugin
   * 
   * @since 1.2.0
   */
  public function display_inicio_page()
  {
    $template_data = array(
      'titulo' => __('Bienvenido a Solu Admin Utils', 'solu-admin-utils'),
      'message' => __('Panel de herramientas administrativas para WordPress y WooCommerce. Desde aquí puedes acceder a todas las utilidades del plugin.', 'solu-admin-utils')
    );
    
    include SOLU_ADMIN_UTIL_PATH . 'templates/home/home.php';
  }

  /**
   * Página de ayuda e información
   * 
   * @since 1.2.0
   */
  public function display_help_page()
  {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html__('Ayuda - Solu Admin Utils', 'solu-admin-utils'); ?></h1>
      
      <div class="card" style="margin-top: 20px;">
        <h2><?php echo esc_html__('Información del Plugin', 'solu-admin-utils'); ?></h2>
        <table class="form-table">
          <tr>
            <th scope="row"><?php echo esc_html__('Versión', 'solu-admin-utils'); ?></th>
            <td><?php echo esc_html(SOLU_ADMIN_UTIL_VERSION); ?></td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html__('Autor', 'solu-admin-utils'); ?></th>
            <td>César Auris - <a href="mailto:perucaos@gmail.com">perucaos@gmail.com</a></td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html__('Sitio Web', 'solu-admin-utils'); ?></th>
            <td><a href="https://solucionessystem.com" target="_blank">https://solucionessystem.com</a></td>
          </tr>
        </table>
      </div>


      <div class="card" style="margin-top: 20px;">
        <h2><?php echo esc_html__('Soporte', 'solu-admin-utils'); ?></h2>
        <p><?php echo esc_html__('Para obtener soporte técnico o reportar problemas, contacta:', 'solu-admin-utils'); ?></p>
        <ul>
          <li>Email: <a href="mailto:perucaos@gmail.com">perucaos@gmail.com</a></li>
          <li>Sitio Web: <a href="https://solucionessystem.com" target="_blank">solucionessystem.com</a></li>
        </ul>
      </div>
    </div>
    <?php
  }
}
