<?php

/**
 * Clase principal de administración del plugin
 * 
 * Maneja toda la funcionalidad del backend de forma simple y estándar
 * 
 * @package SoluGenerateHTML
 */
class Solu_Generate_HTML_Inicio
{
  private $backend_Function;
  public function __construct()
  {
    $this->backend_Function = Solu_Generate_HTML_Backend_Functions::getInstance();
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
  }

  /**
   * Cargar los assets CSS y JS en las páginas de administración
   */
  public function enqueue_admin_assets($hook_suffix)
  {
    // Verificar si estamos en una página del plugin
    if (strpos($hook_suffix, 'page_solu-generate-html') !== false) {
      // Bootstrap CSS y JS
      wp_enqueue_style('solu-generate-html-bootstrap-css', SOLU_GENERATE_HTML_URL . 'assets/admin/bootstrap-5.3.7/css/bootstrap.min.css', array(), '5.3.7');
      wp_enqueue_script('solu-generate-html-bootstrap-js', SOLU_GENERATE_HTML_URL . 'assets/admin/bootstrap-5.3.7/js/bootstrap.bundle.min.js', array('jquery'), '5.3.7', true);

      // Prism.js para resaltado de sintaxis
      wp_enqueue_style('solu-generate-html-prismjs-css', SOLU_GENERATE_HTML_URL . 'assets/admin/prismjs/css/prism.css', array(), '1.29.0');
      wp_enqueue_script('solu-generate-html-prismjs-js', SOLU_GENERATE_HTML_URL . 'assets/admin/prismjs/js/prism.js', array(), '1.29.0', true);

      // Estilos personalizados
      wp_enqueue_style('solu-generate-html-admin-styles', SOLU_GENERATE_HTML_URL . 'assets/admin/css/admin-styles.css', array(), SOLU_GENERATE_HTML_VERSION);
    }
  }





  /**
   * Página de ayuda
   */
  public function display_inicio_page()
  {
    global $GLOBAL_TIPOS_GRUPOS;
    // Preparar datos para el template
    $template_data = array(
        'GLOBAL_TIPOS_GRUPOS' => $GLOBAL_TIPOS_GRUPOS,
        'instance_backend_function' => $this->backend_Function
    );

      include SOLU_GENERATE_HTML_PATH . 'templates/inicio/home.php';
  }
}
