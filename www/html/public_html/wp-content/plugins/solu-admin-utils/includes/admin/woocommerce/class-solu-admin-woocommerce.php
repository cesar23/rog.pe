<?php
/**
 * Clase para funcionalidades de WooCommerce en el admin
 * 
 * Añade filtros de "Destacados" y "Stock" al listado de productos en el admin
 * y columnas personalizadas de Stock, Precio y Destacado.
 * 
 * @package Solu_Admin_Utils
 * @since 1.2.0
 * @author César Auris [perucaos@gmail.com]
 */

if (!defined('ABSPATH')) exit;



/**
 * Clase principal para funcionalidades de WooCommerce
 */
class Solu_Admin_WooCommerce {

  /**
   * Constructor de la clase
   */
  public function __construct() {


    // Dropdowns (filtros) arriba del listado de productos
    add_action('restrict_manage_posts', [$this, 'render_filters']);

    // Aplicar filtros a la consulta principal del listado
    add_action('pre_get_posts', [$this, 'apply_filters_to_query']);

    // Agregar estilos CSS personalizados
    add_action('admin_head', [$this, 'add_custom_styles']);

  }

  /**
   * Agrega estilos CSS personalizados para los filtros del plugin
   * 
   * @return void
   */
  public function add_custom_styles(): void {
    ?>
    <style type="text/css">
      /* Estilos formales y elegantes para filtros personalizados de Solu Admin Utils */
      .solu-admin-filter {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
        border: 1px solid #2c3e50 !important;
        border-radius: 4px !important;
        color: #ecf0f1 !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0px 10px !important;
        margin-right: 8px !important;
        box-shadow: 0 1px 3px rgba(44, 62, 80, 0.2) !important;
        transition: all 0.2s ease !important;
        min-width: 160px !important;
        position: relative !important;
      }

      .solu-admin-filter:hover {
        background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%) !important;
        border-color: #3498db !important;
        box-shadow: 0 2px 6px rgba(44, 62, 80, 0.3) !important;
      }

      .solu-admin-filter:focus {
        outline: none !important;
        border-color: #3498db !important;
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2) !important;
      }

      .solu-admin-filter option {
        background: white !important;
        color: #2c3e50 !important;
        padding: 6px 10px !important;
        font-weight: normal !important;
      }

      .solu-admin-filter option:hover {
        background: #ecf0f1 !important;
      }

      /* Badge elegante del plugin */
      .solu-admin-filter::before {
        content: "SOLU" !important;
        position: absolute !important;
        top: -6px !important;
        left: 6px !important;
        background: linear-gradient(45deg, #3498db 0%, #2980b9 100%) !important;
        color: white !important;
        font-size: 9px !important;
        padding: 1px 4px !important;
        border-radius: 3px !important;
        font-weight: 600 !important;
        z-index: 10 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
      }

      /* Contenedor de filtros elegante */
      .solu-filters-container {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin-right: 10px !important;
        padding: 6px 10px !important;
        background: linear-gradient(135deg, rgba(44, 62, 80, 0.05) 0%, rgba(52, 73, 94, 0.05) 100%) !important;
        border-radius: 6px !important;
        border: 1px solid rgba(52, 152, 219, 0.2) !important;
        box-shadow: 0 1px 3px rgba(44, 62, 80, 0.1) !important;
      }

      /* Responsive */
      @media (max-width: 768px) {
        .solu-filters-container {
          flex-direction: column !important;
          align-items: stretch !important;
          margin-bottom: 10px !important;
        }
        
        .solu-admin-filter {
          margin-right: 0 !important;
          margin-bottom: 4px !important;
        }
      }
    </style>
    <?php
  }

  /**
   * Renderiza los filtros de "Destacados" y "Stock" en el listado
   * 
   * @param string $post_type Tipo de post actual
   */
  public function render_filters($post_type) {
    if ($post_type !== 'product') return;

    $featured_selected = isset($_GET['featured_filter']) ? sanitize_text_field(wp_unslash($_GET['featured_filter'])) : '';
    $stock_selected    = isset($_GET['stock_filter'])    ? sanitize_text_field(wp_unslash($_GET['stock_filter']))    : '';

    // Contenedor de filtros con estilos personalizados
    ?>
    <div class="solu-filters-container">
      <!-- Filtro Destacados (todos / sí / no) -->
      <select name="featured_filter" id="featured_filter" class="solu-admin-filter">
        <option value=""><?php esc_html_e('Destacados (todos)', 'solu-admin-utils'); ?></option>
        <option value="yes" <?php selected($featured_selected, 'yes'); ?>><?php esc_html_e('Destacados: Sí', 'solu-admin-utils'); ?></option>
        <option value="no"  <?php selected($featured_selected, 'no');  ?>><?php esc_html_e('Destacados: No', 'solu-admin-utils');  ?></option>
      </select>

      <!-- Filtro Stock (todos / en stock / sin stock) -->
      <select name="stock_filter" id="stock_filter" class="solu-admin-filter">
        <option value=""><?php esc_html_e('Stock (todos)', 'solu-admin-utils'); ?></option>
        <option value="instock"    <?php selected($stock_selected, 'instock');    ?>><?php esc_html_e('En stock', 'solu-admin-utils'); ?></option>
        <option value="outofstock" <?php selected($stock_selected, 'outofstock'); ?>><?php esc_html_e('Sin stock', 'solu-admin-utils'); ?></option>
      </select>
    </div>
    <?php
  }

  /**
   * Aplica los filtros seleccionados a la WP_Query del listado
   * 
   * @param WP_Query $query Objeto de consulta
   */
  public function apply_filters_to_query($query) {
    if (!is_admin() || !$query->is_main_query()) return;
    if ($query->get('post_type') !== 'product') return;

    $featured_filter = isset($_GET['featured_filter']) ? sanitize_text_field(wp_unslash($_GET['featured_filter'])) : '';
    $stock_filter    = isset($_GET['stock_filter'])    ? sanitize_text_field(wp_unslash($_GET['stock_filter']))    : '';

    // Filtro Destacados (tax_query)
    if ($featured_filter === 'yes' || $featured_filter === 'no') {
      $tax_query = (array) $query->get('tax_query');
      $tax_query[] = [
          'taxonomy' => 'product_visibility',
          'field'    => 'name',
          'terms'    => ['featured'],
          'operator' => ($featured_filter === 'yes') ? 'IN' : 'NOT IN',
      ];
      $query->set('tax_query', $tax_query);
    }

    // Filtro Stock (meta_query)
    if ($stock_filter === 'instock' || $stock_filter === 'outofstock') {
      $meta_query = (array) $query->get('meta_query');
      $meta_query[] = [
          'key'   => '_stock_status',
          'value' => $stock_filter,
      ];
      $query->set('meta_query', $meta_query);
    }
  }
}
/**
 * Inicializa la funcionalidad de WooCommerce de forma segura
 * 
 * @return void
 * @since 1.2.0
 */
function solu_admin_woocommerce_init(): void {
    // Verificar si estamos en el admin y en el listado de productos
    if (!is_admin() || !isset($_GET['post_type']) || $_GET['post_type'] !== 'product') {
        return;
    }

    // Verificar si WooCommerce está activo
    if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        AdminNotices::show_missing_dependency('Solu Admin Utils', 'WooCommerce');
        solu_log('Plugin no activado: WooCommerce no está disponible', 'error');
        return;
    }

    // Inicializar la clase
    new Solu_Admin_WooCommerce();
    AdminNotices::show_success('Filtros de Solu Admin Utils disponibles para productos');
}

// Inicializar cuando WordPress esté listo
add_action('admin_init', 'solu_admin_woocommerce_init');

