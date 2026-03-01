<?php
// Seguridad: evitar acceso directo
if (!defined('ABSPATH')) exit;

// Handler AJAX para generar HTML de categorías y subcategorías (anidación ilimitada)
add_action('wp_ajax_solu_generate_html_categorias', function () {
  check_ajax_referer('solu_generate_html_categorias');

  if (empty($_POST['categoria_ids']) || !is_array($_POST['categoria_ids'])) {
    wp_send_json_error('No se recibieron categorías.');
  }

  $ids = array_map('intval', $_POST['categoria_ids']);

  // Nueva función para generar el grid de categorías con subcategorías (un solo nivel)
  function solu_generate_html_cat_grid($cat_id)
  {
    $cat = get_term($cat_id, 'product_cat');
    if (!$cat || is_wp_error($cat)) return '';
    $html = '<div class="categoria">';
//    $html .= '<h3>' . esc_html($cat->name) . '</h3>';
    $html .= '<h3><a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a></h3>';

    $children = get_terms([
      'taxonomy' => 'product_cat',
      'parent' => $cat->term_id,
      'hide_empty' => false,
    ]);



    if (!empty($children) && !is_wp_error($children)) {
      $html .= '<ul>';
      foreach ($children as $child) {
        $html .= '<li><a href="' . esc_url(get_term_link($child)) . '">' . esc_html($child->name) . '</a></li>';
      }
      $html .= '</ul>';
    }
    $html .= '</div>';
    return $html;
  }

  $html = '<div class="grid-categorias">';
  foreach ($ids as $cat_id) {
    $html .= solu_generate_html_cat_grid($cat_id);
  }
  $html .= '</div>';

  $partial_html = file_get_contents(SOLU_GENERATE_HTML_PATH . 'templates/partials/partial_categori_style.html');
  $partial_html .= $html;
  $partial_html .= '';

  wp_send_json_success(['html' => $partial_html]);
});

// Handler AJAX para guardar código HTML
add_action('wp_ajax_save_code_html', function () {
  $backendFunction = Solu_Generate_HTML_Backend_Functions::getInstance();
  $class_date_utils = new Solu_Generate_HTML_DateUtils('America/Lima');
  error_log('AJAX save_code_html llamado'); // Debug

  check_ajax_referer('solu_save_code_html');

  error_log('POST data: ' . print_r($_POST, true)); // Debug

  if (empty($_POST['html'])) {
    wp_send_json_error('No se recibió el código HTML a guardar.');
  }

  if (empty($_POST['name_code'])) {
    wp_send_json_error('El identificador del código es obligatorio.');
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
    'user_id' => get_current_user_id(),
    'username' => wp_get_current_user()->user_login,
    'name_group' => $selected_group,
    'name_code' => $name_code,
    'code' => wp_kses($_POST['html'], wp_kses_allowed_html('post') + array(
      'style' => array(), // Permitir estilos CSS inline
      'script' => array() // Permitir JavaScript inline
    )), // Permite todas las etiquetas HTML estándar + CSS y JavaScript
    'created_at' => $class_date_utils->getCurrentDateTime(),
    'update_at' => $class_date_utils->getCurrentDateTime()
  );

  $result = $backendFunction->insert_html_code($data);
  if ($result) {
    wp_send_json_success('Código HTML guardado exitosamente.');
  } else {
    solu_log("[wp-content/plugins/solu-generate-html/includes/admin/backend_action.php:100] Error al guardar el código HTML","error");
    wp_send_json_error('Error al guardar el código HTML.');
  }
});
