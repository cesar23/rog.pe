<?php

/**
 * Clase principal de administración del plugin
 *
 * Maneja toda la funcionalidad del backend de forma simple y estándar.
 * Utiliza la clase Singleton Solu_Generate_HTML_Backend_Functions para
 * todas las operaciones de base de datos y utilidades.
 *
 * @package SoluGenerateHTML
 * 
 * @example
 * // La clase automáticamente obtiene la instancia Singleton del backend
 * $categoria = new Solu_Generate_HTML_Categoria();
 * 
 * // Acceder al backend desde cualquier método
 * $backend_Function = $this->backend_Function;
 * $categories = $backend_Function->get_categories('product_cat');
 * 
 * // O usar el método helper
 * $backend_Function = $this->get_backend();
 * $admins = $backend_Function->get_admin_users();
 */
class Solu_Generate_HTML_Categoria
{
  /**
   * Instancia de la clase backend (Singleton)
   *
   * @var Solu_Generate_HTML_Backend_Functions
   */
  private $backend_Function;

  /**
   * Constructor de la clase
   */
  public function __construct()
  {
    // Obtener la instancia Singleton del backend
    $this->backend_Function = Solu_Generate_HTML_Backend_Functions::getInstance();

    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
  }

  /**
   * Limpia el código HTML de comillas escapadas
   * 
   * @param string $code El código HTML a limpiar
   * @return string El código HTML limpio
   */
  private function clean_html_code($code)
  {
    // Si las comillas están escapadas, las limpiamos
    if (strpos($code, '\\"') !== false) {
      $code = stripslashes($code);
    }
    return $code;
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

      // ==================================================
      // Prism.js para resaltado de sintaxis
      //      wp_enqueue_style('solu-generate-html-prismjs-css', SOLU_GENERATE_HTML_URL . 'assets/admin/prismjs/css/prism.css', array(), '1.29.0');
      //      wp_enqueue_script('solu-generate-html-prismjs-js', SOLU_GENERATE_HTML_URL . 'assets/admin/prismjs/js/prism.js', array(), '1.29.0', true);

      // ==================================================
      // CodeMirror
      wp_enqueue_style('solu-generate-html-codemirror5-css', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/css/codemirror.min.css', array(), '1.29.0');
      wp_enqueue_style('solu-generate-html-codemirror5-dracula-css', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/theme/dracula.min.css', array(), '1.29.0');
      // CodeMirror para resaltado de sintaxis
      wp_enqueue_script('solu-generate-html-codemirror5-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/js/codemirror.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-mode-htmlmixed-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/mode/htmlmixed/htmlmixed.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-mode-javascript-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/mode/javascript/javascript.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-mode-php-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/mode/php/php.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-mode-css-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/mode/css/css.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-mode-xml-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/mode/xml/xml.min.js', array(), '1.29.0', true);

      //  Addons de CodeMirror para funcionalidades adicionales
      wp_enqueue_script('solu-generate-html-codemirror5-active-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/selection/active-line.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-lint-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/lint/lint.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-javascriptt-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/lint/javascript-lint.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-show-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/hint/show-hint.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-javascript-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/hint/javascript-hint.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-foldcodet-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/fold/foldcode.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-foldgutter-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/fold/foldgutter.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-brace-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/fold/brace-fold.min.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-closebracketst-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/edit/closebrackets.js', array(), '1.29.0', true);
      wp_enqueue_script('solu-generate-html-codemirror5-matchbrackets-js', SOLU_GENERATE_HTML_URL . 'assets/admin/codemirror5.65/addon/edit/matchbrackets.js', array(), '1.29.0', true);


      // Estilos personalizados
      wp_enqueue_style('solu-generate-html-admin-styles', SOLU_GENERATE_HTML_URL . 'assets/admin/css/admin-styles.css', array(), SOLU_GENERATE_HTML_VERSION);
    }
  }

  /**
   * Página principal - Lista de códigos HTML
   */
  public function display_main_page()
  {

    //      if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_POST['action']) || isset($_POST['_ajax_nonce']) || isset($_POST['solu_generate_html_nonce'])){
    if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_POST['action'])) {

      $action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : '';

      $current_user = wp_get_current_user();
      $username_wp = $current_user ? $current_user->user_login : null;
      $id_user_wp = $current_user ? $current_user->ID : null;

      switch ($action) {

        case 'create':
          $this->process_create($username_wp, $id_user_wp);
          break;
        case 'update':
          $this->process_update($username_wp, $id_user_wp);
          break;
        case 'delete':
          $this->process_delete();
          break;
        case 'select_categories':
          $this->display_selected_categories();
          break;
        case 'save_code_html':
          $this->process_save_code_html($username_wp, $id_user_wp);
          break;
      }
    } else {

      $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

      switch ($action) {
        case 'create':
          $this->display_create_form();
          break;
        case 'edit':
          $id = intval($_GET['id']);
          $this->display_edit_form($id);
          break;
        case 'delete':
          $id = intval($_GET['id']);
          $this->display_delete_confirmation($id);
          break;
        case 'categories':
          $this->display_list_category_products();
          break;

        default:
          $this->display_html_codes_list();
          break;
      }
    }
  }


  /**
   * Procesar la creación de un nuevo código HTML
   */
  private function process_create($username_wp, $id_user_wp)
  {
    // Verificar nonce para seguridad
    if (!isset($_POST['solu_generate_html_nonce']) || !wp_verify_nonce($_POST['solu_generate_html_nonce'], 'solu_generate_html_create')) {
      wp_die('Error de seguridad. Por favor, intenta de nuevo.');
    }

    if (empty($_POST['name_group'])) {
      echo '<div class="notice notice-error"><p>El campo Nombre del Grupo es obligatorio.</p></div>';
      return;
    }

    if (empty($_POST['name_code'])) {
      echo '<div class="notice notice-error"><p>El campo Nombre de Código es obligatorio.</p></div>';
      return;
    }

    if (empty($_POST['code'])) {
      echo '<div class="notice notice-error"><p>El campo Código HTML es obligatorio.</p></div>';
      return;
    }

    $data = array(
      'user_id' => $id_user_wp,
      'username' => $username_wp,
      'name_group' => sanitize_text_field($_POST['name_group']),
      'name_code' => sanitize_text_field($_POST['name_code']),
      'code' => $this->clean_html_code($_POST['code']), // Código HTML limpio
      'created_at' => solu_get_date_hour_pe(),
      'update_at' => solu_get_date_hour_pe()
    );

    $result = $this->backend_Function->insert_html_code($data);

    if ($result) {
      solu_generate_html_save_log_db_table('create', $data);
      echo '<div class="notice notice-success"><p>Código HTML creado exitosamente.</p></div>';
      echo '<div style="margin-top: 10px;"><a href="' . admin_url('admin.php?page=solu-generate-html') . '" class="button button-secondary">← Regresar a la lista</a></div>';
    } else {
      echo '<div class="notice notice-error"><p>Error al crear el código HTML.</p></div>';
    }
  }

  /**
   * Procesar la actualización de un código HTML
   */
  private function process_update($username_wp, $id_user_wp)
  {
    // Verificar nonce para seguridad
    if (!isset($_POST['solu_generate_html_nonce']) || !wp_verify_nonce($_POST['solu_generate_html_nonce'], 'solu_generate_html_update')) {
      wp_die('Error de seguridad. Por favor, intenta de nuevo.');
    }

    if (empty($_POST['name_group'])) {
      echo '<div class="notice notice-error"><p>El campo Nombre del Grupo es obligatorio.</p></div>';
      return;
    }

    if (empty($_POST['name_code'])) {
      echo '<div class="notice notice-error"><p>El campo Nombre de Código es obligatorio.</p></div>';
      return;
    }

    if (empty($_POST['code'])) {
      echo '<div class="notice notice-error"><p>El campo Código HTML es obligatorio.</p></div>';
      return;
    }

    $id = intval($_POST['id']);

    $data = array(
      'user_id' => $id_user_wp,
      'username' => $username_wp,
      'name_group' => sanitize_text_field($_POST['name_group']),
      'name_code' => sanitize_text_field($_POST['name_code']),
      'code' => $this->clean_html_code($_POST['code']), // Código HTML limpio
      'update_at' => solu_get_date_hour_pe()
    );

    $result = $this->backend_Function->update_html_code($id, $data);

    if ($result) {

      echo '<div class="notice notice-success"><p>Código HTML actualizado exitosamente.</p></div>';
      echo '<div style="margin-top: 10px;"><a href="' . admin_url('admin.php?page=solu-generate-html') . '" class="button button-secondary">← Regresar a la lista</a></div>';
    } else {
      echo '<div class="notice notice-error"><p>Error al actualizar el código HTML.</p></div>';
    }
  }

  /**
   * Procesar la eliminación de un código HTML
   */
  private function process_delete()
  {
    // Verificar nonce para seguridad
    if (!isset($_POST['solu_generate_html_nonce']) || !wp_verify_nonce($_POST['solu_generate_html_nonce'], 'solu_generate_html_delete')) {
      wp_die('Error de seguridad. Por favor, intenta de nuevo.');
    }

    $id = intval($_POST['id']);
    $result = $this->backend_Function->delete_html_code($id);

    if ($result) {
      echo '<div class="notice notice-success"><p>Código HTML eliminado exitosamente.</p></div>';
      echo '<div style="margin-top: 10px;"><a href="' . admin_url('admin.php?page=solu-generate-html') . '" class="button button-secondary">← Regresar a la lista</a></div>';
    } else {
      echo '<div class="notice notice-error"><p>Error al eliminar el código HTML.</p></div>';
    }
  }

  /**
   * Procesar el guardado de código HTML desde el botón "Guardar el codigo"
   */
  private function process_save_code_html($username_wp, $id_user_wp)
  {
    if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'solu_save_code_html')) {
      wp_send_json_error('Error de seguridad al guardar el código HTML.');
      return;
    }
    if (empty($_POST['html'])) {
      wp_send_json_error('No se recibió el código HTML a guardar.');
      return;
    }

    if (empty($_POST['name_code'])) {
      wp_send_json_error('El identificador del código es obligatorio.');
      return;
    }

    // Obtener el grupo seleccionado del formulario
    $selected_group = isset($_POST['select_group']) ? sanitize_text_field($_POST['select_group']) : 'HTML generado por categorías';

    // Si se seleccionó "nuevo_grupo", usar el valor del input
    if ($selected_group === 'nuevo_grupo' && !empty($_POST['nuevo_grupo'])) {
      $selected_group = sanitize_text_field($_POST['nuevo_grupo']);
    }

    // Usar el identificador proporcionado por el usuario
    $name_code = sanitize_text_field($_POST['name_code']);

    $data = array(
      'user_id' => $id_user_wp,
      'username' => $username_wp,
      'name_group' => $selected_group,
      'name_code' => $name_code,
      'code' => $this->clean_html_code($_POST['html']), // Código HTML limpio
      'created_at' => solu_get_date_hour_pe(),
      'update_at' => solu_get_date_hour_pe()
    );
    $result = $this->backend_Function->insert_html_code($data);
    if ($result) {
      // solu_generate_html_save_log_db_table('save_code_html', $data);
      wp_send_json_success('Código HTML guardado exitosamente.');
    } else {
      wp_send_json_error('Error al guardar el código HTML.');
    }
  }

  /**
   * Mostrar la lista de códigos HTML usando template
   */
  private function display_html_codes_list()
  {
    global $GLOBAL_TIPOS_GRUPOS;

    $template_data = array(
        'GLOBAL_TIPOS_GRUPOS' => $GLOBAL_TIPOS_GRUPOS,
        'instance_backend_function' => $this->backend_Function
    );
    include SOLU_GENERATE_HTML_PATH . 'templates/categoria/list.php';
  }


  /**
   * Mostrar la lista de categorías de productos usando template
   */
  private function display_list_category_products()
  {
    // Obtener solo las categorías padre de productos usando la instancia del backend
    $product_categories = $this->backend_Function->get_product_categories(array(), true);

    // Ordenar por nombre
    usort($product_categories, function ($a, $b) {
      return strcasecmp($a['name'], $b['name']);
    });

    // Calcular estadísticas
    $total_categories = count($product_categories);
    $total_products = array_sum(array_column($product_categories, 'count'));

    // Preparar datos para el template
    $template_data = array(
      'product_categories' => $product_categories,
      'total_categories' => $total_categories,
      'total_products' => $total_products,
      'instance_backend_function' => $this->backend_Function
    );

    // Incluir el template con los datos
    include SOLU_GENERATE_HTML_PATH . 'templates/categoria/list_category_products.php';
  }

  /**
   * Mostrar las categorías seleccionadas
   */
  private function display_selected_categories()
  {
    // Verificar nonce para seguridad
    if (!isset($_POST['solu_generate_html_nonce']) || !wp_verify_nonce($_POST['solu_generate_html_nonce'], 'solu_generate_html_select_categories')) {
      wp_die('Error de seguridad. Por favor, intenta de nuevo.');
    }

    // Obtener las categorías seleccionadas
    $selected_category_ids = isset($_POST['selected_categories']) ? array_map('intval', $_POST['selected_categories']) : array();

    if (empty($selected_category_ids)) {
      echo '<div class="notice notice-warning"><p>No se seleccionaron categorías.</p></div>';
      echo '<p><a href="?page=solu-generate-html&action=categories" class="button">← Volver a la lista</a></p>';
      return;
    }

    // Obtener información de las categorías seleccionadas
    $selected_categories = array();
    foreach ($selected_category_ids as $category_id) {
      $category = get_term($category_id, 'product_cat');
      if ($category && !is_wp_error($category)) {
        $selected_categories[] = array(
          'term_id' => $category->term_id,
          'name' => $category->name,
          'slug' => $category->slug,
          'description' => $category->description,
          'count' => $category->count
        );
      }
    }

    global $GLOBAL_TIPOS_GRUPOS;
    // Preparar datos para el template
    $template_data = array(
      'selected_categories' => $selected_categories,
      'total_selected' => count($selected_categories),
       'GLOBAL_TIPOS_GRUPOS'=>$GLOBAL_TIPOS_GRUPOS,
        'instance_backend_function' => $this->backend_Function
    );

    // Incluir el template con los datos
    include SOLU_GENERATE_HTML_PATH . 'templates/categoria/selected_categories.php';
  }


  /**
   * Mostrar formulario de creación usando template
   */
  private function display_create_form()
  {
    include SOLU_GENERATE_HTML_PATH . 'templates/categoria/create.php';
  }

  /**
   * Mostrar formulario de edición usando template
   */
  private function display_edit_form($id)
  {
    $html_code = $this->backend_Function->get_html_code($id);

    if (!$html_code) {
      echo '<div class="notice notice-error"><p>Código HTML no encontrado.</p></div>';
      return;
    }

    include SOLU_GENERATE_HTML_PATH . 'templates/categoria/edit.php';
  }

  /**
   * Mostrar confirmación de eliminación usando template
   */
  private function display_delete_confirmation($id)
  {
    $html_code = $this->backend_Function->get_html_code($id);

    if (!$html_code) {
      return;
    }

    include SOLU_GENERATE_HTML_PATH . 'templates/categoria/delete.php';
  }

  /**
   * Obtener la instancia del backend (método helper)
   * 
   * @return Solu_Generate_HTML_Backend_Functions
   */
  public function get_backend()
  {
    return $this->backend_Function;
  }

  /**
   * Página de ayuda
   */
  public function display_help_page()
  {
    include SOLU_GENERATE_HTML_PATH . 'templates/categoria/info.php';
  }
}
