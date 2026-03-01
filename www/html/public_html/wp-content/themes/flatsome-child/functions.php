<?php
/*
Server: adcomputers
Versión de archivo: 2.0.1
*/



/*
===================================================
START  - REGISTRACION PARA FLATSOME 
:::: esto se puede desactivar una vez este activado
===================================================
*/

$site_url = get_site_url();

$domain_name = wp_parse_url($site_url, PHP_URL_HOST);

$update_option_data = array(
    'id'           => 'new_id_123456',
    'type'         => 'PUBLIC',
    'domain'       => $domain_name,
    'registeredAt' => '2021-07-18T12:51:10.826Z',
    'purchaseCode' => 'abcd1234-5678-90ef-ghij-klmnopqrstuv',
    'licenseType'  => 'Regular License',
    'errors'       => array(),
    'show_notice'  => false
);

update_option('flatsome_registration', $update_option_data, 'yes');

add_action('init', function () {
    remove_action('admin_notices', 'flatsome_status_check_admin_notice');
    remove_action('admin_notices', 'flatsome_maintenance_admin_notice');
    remove_filter('pre_set_site_transient_update_themes', 'flatsome_get_update_info', 1, 999999);
    remove_filter('pre_set_transient_update_themes', 'flatsome_get_update_info', 1, 999999);
});

/*
// ---------------------------------------------------------------------------
// AGREGAR SCRIPT y CSS DIRECTAMENTE EN EL HEAD
// ---------------------------------------------------------------------------
*/


function add_enqueue_scripts_and_css()
{
    // Agregar fuentes
    wp_enqueue_style(
        'google-fonts-montserrat',
        'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,700;0,800;0,900;1,400;1,500;1,700;1,800;1,900&display=swap',
        false
    );
}
add_action('wp_enqueue_scripts', 'add_enqueue_scripts_and_css');







// ---------------------------------------------------------------------------
// AGREGAR SCRIPT DIRECTAMENTE EN EL HEAD
// ---------------------------------------------------------------------------
add_action('wp_head', 'add_inline_script_to_head');
function add_inline_script_to_head()
{
?>
    <!-- Fuentes para el thema -->
    <!-- Fuente Rajdhani -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <!-- Fuente Open Sans -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet"> -->
<?php
}


// ---------------------------------------------------------------------------
// VARIABLES GLOBALES Y CONSTANTES
// ---------------------------------------------------------------------------
$my_url = home_url('/');
$url_amigable = 'www.' . preg_replace('/(http|https):\/\/(\S+)(\/)/i', '${2}', $my_url);
global $config_child_cesar;
define('VERSION', '2025-07-17.10');

// ---------------------------------------------------------------------------
// FUNCIONES GENERALES
// ---------------------------------------------------------------------------

/**
 * Obtiene la versión del sitio.
 * En modo de depuración, devuelve la hora actual.
 * @return string|int La versión del sitio o la hora actual en modo de depuración.
 */
function version_id()
{
    return WP_DEBUG ? time() : VERSION;
}

// ---------------------------------------------------------------------------
// CREAR ROL GESTOR
// ---------------------------------------------------------------------------
add_action('init', 'cloneRole');
function cloneRole()
{
    global $wp_roles;
    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }
    $adm = $wp_roles->get_role('administrator');
    // Agregar un nuevo rol con todas las capacidades de administrador
    $wp_roles->add_role('gestor', 'Gestor', $adm->capabilities);
}

// ---------------------------------------------------------------------------
// AGREGAR CONTENIDO AL ABRIR EL BODY
// ---------------------------------------------------------------------------
add_action('wp_body_open', 'add_custom_body_open_code');
function add_custom_body_open_code()
{
    global $url_amigable;

?>

<a href="https://www.messenger.com/t/109163381806898" id="botn_facebook" class="float2" target="_blank">
    <img style="max-width: 37px;" src="https://cesar23.github.io/cdn_webs/iconos_svg/facebook-messenger-brands.svg" class="my-float">
</a>

<div style="display: none" class="contenedor_wapsa apertura_what">
    <div style="width: 100%; text-align: center">
        <h4 style="color: #009237">¿Con quién quieres hablar?</h4>
    </div>
    <div class="contenedor_inferior">
        <div class="contacto-item">
            <a target="_blank" href="https://api.whatsapp.com/send?phone=51902523099&text=Buen día, escribo desde la web <?php echo $url_amigable; ?>. Quisiera más información">
                <i class="btn-icon fa-brands fa-whatsapp"></i>
                <div>
                    <strong>Distribucion</strong><br>902523099
                </div>
            </a>
        </div>
        <div class="contacto-item">
            <a target="_blank" href="https://api.whatsapp.com/send?phone=51946118274&text=Buen día, escribo desde la web <?php echo $url_amigable; ?>. Quisiera más información">
                <i class="btn-icon fa-brands fa-whatsapp"></i>
                <div>
                    <strong>Ventas</strong><br>946118274
                </div>
            </a>
        </div>
    </div>
</div>

<span id="botn_whapsa" style="cursor: pointer" class="float" target="_blank">
    <i class="fa-brands fa-whatsapp fa-lg my-float"></i>
</span>

    <style>
      :root {
    --cant_items_phone: calc(2 * 53px); /* Cambiado a 2 porque tienes 2 contactos */
    --contenedor_wapsa_height: calc(90px + var(--cant_items_phone));
    --contenedor_inferior_height: calc(33px + var(--cant_items_phone));
}

.float {
    padding-top: 7px;
    position: fixed;
    width: 60px;
    height: 60px;
    bottom: 40px;
    right: 42px;
    background-color: #25d366;
    color: #FFF;
    border-radius: 50px;
    text-align: center;
    font-size: 30px;
    box-shadow: 2px 2px 3px #999;
    z-index: 100;
}

.contenedor_wapsa {
    width: 320px;
    height: var(--contenedor_wapsa_height);
    background-color: #ffffff;
    padding-top: 7px;
    position: fixed;
    bottom: 108px;
    right: 52px;
    color: #FFF;
    border-radius: 5px;
    z-index: 100;
    border: 1px solid green;
    overflow: hidden;
    box-shadow: 12px 13px 16px -8px rgba(0, 0, 0, 0.75);
}

.apertura_what {
    animation-name: animacion_whapBox;
    animation-duration: 0.7s;
}

@keyframes animacion_whapBox {
    0% {
        width: 0px;
        height: 0px;
    }
    100% {
        width: 275px;
        height: var(--contenedor_wapsa_height);
    }
}

.contenedor_inferior {
    padding: 15px;
    margin: 10px;
    background-color: #f3f3f3;
    height: var(--contenedor_inferior_height);
    border-radius: 5px;
    overflow: auto;
}

.float:hover {
    cursor: pointer;
}

.btn-icon {
    padding: 10px;
    background-color: #1bc159;
    color: white;
    border-radius: 50%;
}

.float2 {
    padding-top: 4px;
    position: fixed;
    width: 60px;
    height: 60px;
    bottom: 40px;
    right: 120px;
    background-color: #1094f4;
    color: #FFF;
    border-radius: 50px;
    text-align: center;
    font-size: 30px;
    box-shadow: 2px 2px 3px #999;
    z-index: 100;
}

/* === NUEVAS CLASES PARA ALINEACIÓN === */
.contacto-item {
    width: 100%;
    margin-bottom: 12px;
}

.contacto-item:last-child {
    margin-bottom: 0;
}

.contacto-item a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #333;
}

.contacto-item a:hover {
    color: #25d366;
}

.contacto-item .btn-icon {
    margin-right: 12px;
    flex-shrink: 0;
}

.contacto-item div {
    line-height: 1.3;
}
/* === FIN NUEVAS CLASES === */

@media screen and (max-width: 600px) {
    .float {
        right: 21px;
    }
    .float2 {
        right: 110px;
    }
    .contenedor_wapsa {
        bottom: 107px;
        right: 28px;
    }
}
    </style>

    <script>
        (function($) {
            var valConst = false;
            $(document).ready(function() {
                $("#botn_whapsa").hover(
                    function() {
                        $(".contenedor_wapsa").attr("style", "display: block")
                    },
                    function() {
                        setTimeout(function() {
                            if (!valConst) {
                                $(".contenedor_wapsa").attr("style", "display: none")
                            }
                        }, 100)
                    }
                );
                $(".contenedor_wapsa").hover(
                    function() {
                        valConst = true;
                    },
                    function() {
                        valConst = false;
                        $(".contenedor_wapsa").attr("style", "display: none")
                    }
                );
            });
        })(jQuery);
    </script>

<?php
}

// ---------------------------------------------------------------------------
// CONFIGURACIÓN DEL TEMA
// ---------------------------------------------------------------------------
$array_users_admin = ["perucaos@gmail.com"];
$config_child_cesar = [
    'id' => 11,
    'load_script_front_end' => [
        'active' => 1,
        'js_add' => [
            "script-func-cesar-numbers-sol" => "soluciones-tools/js/func.cesar.numbers.js",
            "script-frontend-sol" => "soluciones-tools/js/front-end.js",
        ],
        'css_add' => [
            "css-style-front-sol" => "soluciones-tools/css/front-end.css",
            "cdn-font-awesome-sol" => "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css",
        ],
    ],
    // al activar solo sube imagen [original,thumbnail]
    'solo_thumbnail' => [
        'active' => 1,
        'tam_allow' => [],
    ],
    // eliminar all imagenes generadas
    'delete_images_genereadas' => [
        'active' => 0,
        'tam_allow' => [],
    ],
    'secret_login' => [
        'active' => 0,
        'secret_key' => 'clave',
        'secret_password' => 'peru203',
    ],
    'my_style_login' => [
        'active' => 1,
        'tam_allow' => [],
    ],
    'valid_size_image' => [
        'active' => 1,
        'maximum_width' => '2000',
        'maximum_height' => '2000',
        'minimum_width' => '10',
        'minimum_height' => '10',
    ],
    'valid_ip_country_capa_1' => [
        'active' => 0
    ],
    'valid_ip_country_capa_2' => [
        'active' => 1,
        'modal_info' => 1,
        'country_allows' => ['PE'],
        'ips_allows' => ['77.111.246.39'],
    ],
    'restrict_paths_url' => [
        'active' => 1,
        'not_allowed' => ["/mi-cuenta/", "/my-account/"],
    ],
    'usuarios_opciones_limitadas' => [
        'active' => 1,
        'array_users_admin' => $array_users_admin,
    ],
    'usuarios_opciones_limitadas_menu' => [
        'active' => 1,
        'array_users_admin' => $array_users_admin,
    ],
    'usuarios_opciones_menu_adicionales' => [2, 3],
    'personalizar_menu' => [
        'debug' => 1,
        'active' => 1,
        'show_menus' => [
            'index.php',
            'upload.php',
            'woocommerce',
            'edit.php?post_type=product',
            'menu_v1_root',
        ],
        'sub_menus_del_array' => [
            'index.php' => ['update-core.php', 'wpforms-getting-started'],
            'options-general.php' => ['options-writing.php'],
            'tools.php' => ['import.php', 'export.php'],
            'plugins.php' => ['plugin-editor.php', 'plugin-install.php'],
            'woocommerce' => ['wc-settings', 'wc-addons', 'pcfme_plugin_options'],
        ],
        'remover_nav_menus' => [
            'edit-profile',
            'updates',
            'comments',
            'new-content',
            'wp-logo',
            'customize',
            'themes',
            'widgets',
            'menus',
            'archive',
            'flatsome_panel',
            'flatsome-activate',
            'wpforms-menu',
            'wpseo-menu',
            'rocket-settings',
            'preload-cache',
            'docs',
            'faq',
            'support',
            'regenerate-critical-path',
        ],
        'remover_link_nav_menus' => ['user-info'],
    ],
    'theme_recursos_url_bloqueados' => [
        'active' => 1, // Si queremos que esté activo
        'recursos_links' => [
            '/wp-admin/edit.php?post_type=page',
            '/wp-admin/options-general.php',
            '/wp-admin/options-writing.php',
            '/wp-admin/options-reading.php',
            '/wp-admin/options-discussion.php',
            '/wp-admin/options-media.php',
            '/wp-admin/options-permalink.php',
            '/wp-admin/options-privacy.php',
            '/wp-admin/themes.php',
            '/wp-admin/customize.php',
            '/wp-admin/widgets.php',
            '/wp-admin/nav-menus.php',
            '/wp-admin/theme-editor.php',
            '/wp-admin/tools.php',
            '/wp-admin/import.php',
            '/wp-admin/export.php',
            '/wp-admin/site-health.php',
            '/wp-admin/export-personal-data.php',
            '/wp-admin/erase-personal-data.php',
            '/wp-admin/users.php',
            '/wp-admin/user-new.php',
            '/wp-admin/profile.php',
            '/wp-admin/plugins.php',
            '/wp-admin/plugin-install.php',
            '/wp-admin/plugin-editor.php',
        ]
    ],
    //Ocultar admin_barra superior de  administracion en el FronEnd . solo tiene accesos los que estan en :array_users_admin
    'hide__show_admin_bar' => [
        'active' => 1, //si queremso que este activo
    ],
];

// ---------------------------------------------------------------------------
// DESACTIVAR ACTUALIZACIONES AUTOMÁTICAS DE PLUGINS Y TEMAS
// ---------------------------------------------------------------------------
add_filter('auto_update_plugin', '__return_false');
add_filter('auto_update_theme', '__return_false');





// Desactiva tamaños grandes automáticos (WP 5.3+)
add_filter('big_image_size_threshold', '__return_false');

// Desactiva tamaños grandes automáticos (WP 5.3+)
add_filter('big_image_size_threshold', '__return_false');

// ---------------------------------------------------------------------------
// INCLUIR ARCHIVOS NECESARIOS
// ---------------------------------------------------------------------------

require(dirname(__FILE__) . '/classSoluciones/template_backend.php');


// define('LIMIT_FILTER_POST', 1);
// define('LIMIT_FILTER_MEDIA', 250);
// define('LIMIT_FILTER_PRODUCT', 20);
// define('LIMIT_FILTER_PAGE', 12);
// require(dirname(__FILE__) . '/classSoluciones/PostLimitWeb.php');

//---------------------------------------------------------------------
add_action('woocommerce_after_shop_loop_item_title', 'dcms_show_stock_list_products');
function dcms_show_stock_list_products()
{
    global $product;

    if ($product->is_in_stock()) {
        if ($product->get_stock_quantity() >= 10) {
            echo '<div class="list-prod-sku" ><strong>SKU:</strong> ' . $product->get_sku() . '</div>';
            echo '<div class="list-prod-stock" ><strong>STOCK:</strong> +10</div>';
        } else {
            echo '<div class="list-prod-sku" ><strong>SKU:</strong> ' . $product->get_sku() . '</div>';
            echo '<div class="list-prod-stock" ><strong>STOCK:</strong> ' . $product->get_stock_quantity() . '</div>';
        }
    } else {
        echo '<div class="list-prod-sku" ><strong>SKU:</strong> ' . $product->get_sku() . '</div>';
        echo '<div class="list-prod-stock" ><strong>STOCK:</strong> 0</div>';
    }
}







// ====================================================
// Para debug esto actualizara el style de css del tema
// ====================================================
function flatsome_child_estilos_dinamicos()
{
    $style_path = get_stylesheet_directory() . '/style.css';
    $style_uri  = get_stylesheet_uri();

    // Usar el timestamp de última modificación del archivo como versión
    $version = file_exists($style_path) ? filemtime($style_path) : '1.0.0';

    // Eliminar estilo actual si ya fue agregado por el tema
    wp_dequeue_style('flatsome-style');
    wp_deregister_style('flatsome-style');

    // Registrar y cargar nuevamente con versión dinámica
    wp_register_style('flatsome-style', $style_uri, [], $version);
    wp_enqueue_style('flatsome-style');
}
// add_action('wp_enqueue_scripts', 'flatsome_child_estilos_dinamicos', 20);

// ====================================================================
// WOOCOMERCE - PERSONALIZACIONES
// ====================================================================


require(dirname(__FILE__) . '/classSoluciones/helpers/util_helpers.php');

//-----------------------------------------------------------------
//  modificar el titulo de la pagina del producto
//-----------------------------------------------------------------

add_filter('the_title', 'custom_modificar_titulo_producto', 10, 2);

function custom_modificar_titulo_producto($title, $post_id) {
  // Excluir si es la página "single" de un producto
  if (is_singular('product')) {
    return $title; // Devolver el título original sin cambios
  }
  return $title;
}


//-----------------------------------------------------------------
//  modificar el titulo de los listados
//-----------------------------------------------------------------

// Remover función personalizada de Flatsome
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);

// Agregar tu versión personalizada
add_action('woocommerce_shop_loop_item_title', 'custom_loop_product_title_flatsome', 10);
function custom_loop_product_title_flatsome()
{
  echo '<p class="name product-title woocommerce-loop-product__title">';
  woocommerce_template_loop_product_link_open();

  //  Personaliza aquí el título
  $title = get_the_title();
  echo helperShortTitle($title,50);

  woocommerce_template_loop_product_link_close();
  echo '</p>';
}




// =============================================================
// FLATSOME - mejorar busqueda
// =============================================================

/**
 * Mejora el funcionamiento del buscador live search de Flatsome
 * Soluciona problemas de clics que no redirigen correctamente
 *
 * PROBLEMA: Los clics en resultados de búsqueda a veces no funcionan debido a:
 * - Eventos bloqueados por elementos hijos (img, span, div)
 * - Conflictos con el plugin jQuery Autocomplete
 * - Z-index y pointer-events mal configurados
 *
 * SOLUCIÓN:
 * - Intercepta clics antes que el plugin autocomplete los procese
 * - Desactiva pointer-events en elementos hijos
 * - Redirige directamente usando la URL de la sugerencia
 *
 * @return void
 *
 * @since 1.0.3
 * @author César Auris
 */
function fix_live_search_click() {
  ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            console.log('[Flatsome Fix] Inicializando fix para live search');

            /**
             * Método 1: Interceptar clics ANTES de que el autocomplete los maneje
             * Usamos capture:true para capturar en fase de captura, antes de la burbuja
             */
            $(document).on('mousedown touchstart', '.autocomplete-suggestion', function(e) {
                // No prevenir default aquí, solo capturar la sugerencia
                var $suggestion = $(this);
                var index = parseInt($suggestion.attr('data-index'));

                if (isNaN(index)) {
                    console.warn('[Flatsome Fix] No se pudo obtener el índice de la sugerencia');
                    return;
                }

                // Buscar el campo de búsqueda relacionado
                var $container = $suggestion.closest('.autocomplete-suggestions');
                var $searchField = null;

                // Intentar encontrar el campo de búsqueda relacionado
                $('.searchform').each(function() {
                    var $form = $(this);
                    var $field = $form.find('.search-field');
                    var autocomplete = $field.data('devbridgeAutocomplete');

                    if (autocomplete && autocomplete.suggestionsContainer === $container[0]) {
                        $searchField = $field;
                        return false; // break
                    }
                });

                if (!$searchField || !$searchField.length) {
                    console.warn('[Flatsome Fix] No se encontró el campo de búsqueda relacionado');
                    return;
                }

                var autocomplete = $searchField.data('devbridgeAutocomplete');

                if (!autocomplete) {
                    console.warn('[Flatsome Fix] No se encontró la instancia de autocomplete');
                    return;
                }

                // Obtener la URL de la sugerencia
                var suggestion = autocomplete.suggestions[index];

                if (!suggestion || !suggestion.url) {
                    console.warn('[Flatsome Fix] Sugerencia inválida o sin URL');
                    return;
                }

                // Si la URL es válida, prevenir el comportamiento default y redirigir
                if (suggestion.url && suggestion.id !== -1) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();

                    console.log('[Flatsome Fix] Redirigiendo a:', suggestion.url);

                    // Pequeño delay para asegurar que el evento se cancela correctamente
                    setTimeout(function() {
                        window.location.href = suggestion.url;
                    }, 10);
                }
            });

            /**
             * Método 2: Fallback usando el evento 'click'
             * Por si el mousedown no funciona en algunos navegadores
             */
            $(document).on('click', '.autocomplete-suggestion', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                var $suggestion = $(this);
                var index = parseInt($suggestion.attr('data-index'));

                if (!isNaN(index)) {
                    var $container = $suggestion.closest('.autocomplete-suggestions');
                    var $searchField = null;

                    $('.searchform').each(function() {
                        var $form = $(this);
                        var $field = $form.find('.search-field');
                        var autocomplete = $field.data('devbridgeAutocomplete');

                        if (autocomplete && autocomplete.suggestionsContainer === $container[0]) {
                            $searchField = $field;
                            return false;
                        }
                    });

                    if ($searchField && $searchField.length) {
                        var autocomplete = $searchField.data('devbridgeAutocomplete');

                        if (autocomplete && autocomplete.suggestions[index]) {
                            var suggestion = autocomplete.suggestions[index];

                            if (suggestion.url && suggestion.id !== -1) {
                                console.log('[Flatsome Fix] Click fallback - Redirigiendo a:', suggestion.url);
                                window.location.href = suggestion.url;
                            }
                        }
                    }
                }

                return false;
            });

            /**
             * Mtodo 3: Asegurar que los elementos hijos NO bloqueen clics
             */
            $(document).on('click mousedown touchstart', '.autocomplete-suggestion *', function(e) {
                e.stopPropagation();
                // Propagar el evento al padre (.autocomplete-suggestion)
                $(this).closest('.autocomplete-suggestion').trigger(e.type);
                return false;
            });

            /**
             * Mejorar visibilidad del contenedor de sugerencias
             */
            $(document).on('mouseenter focus', '.autocomplete-suggestions', function() {
                $(this).css({
                    'z-index': '99999',
                    'pointer-events': 'auto'
                });
            });

            console.log('[Flatsome Fix] Fix para live search cargado correctamente');
        });
    </script>
    <style>
    </style>
  <?php
}
add_action('wp_footer', 'fix_live_search_click', 999);

/**
 * Asegura que la búsqueda también incluya variaciones de productos
 * 
 * @param WP_Query $query Objeto de consulta de WordPress
 * @return void
 * 
 * @since 1.0.0
 * @author César Auris
 */
function include_product_variations_in_search($query) {
    // Solo aplicar en búsquedas del frontend
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // Solo aplicar si es una búsqueda
    if (!$query->is_search()) {
        return;
    }

    // Solo aplicar si WooCommerce está activo
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Verificar si la búsqueda por SKU está habilitada en Flatsome
    $search_by_sku = get_theme_mod('search_by_sku', 0);
    
    if (!$search_by_sku) {
        return;
    }

    // Obtener post_type actual
    $post_type = $query->get('post_type');
    
    // Si estamos buscando productos, también incluir variaciones
    if ($post_type === 'product' || (is_array($post_type) && in_array('product', $post_type))) {
        if (!is_array($post_type)) {
            $post_type = array('product', 'product_variation');
        } elseif (!in_array('product_variation', $post_type)) {
            $post_type[] = 'product_variation';
        }
        $query->set('post_type', $post_type);
    } elseif (empty($post_type) && (is_shop() || (is_search() && isset($_GET['post_type']) && $_GET['post_type'] === 'product'))) {
        // Si no hay post_type pero estamos en shop o búsqueda de productos
        $query->set('post_type', array('product', 'product_variation'));
    }
}
add_action('pre_get_posts', 'include_product_variations_in_search', 20);

// ==============================================================
// Woocommerce Shortcode Marcas: [listar_marcas_woo]
// ==============================================================
add_shortcode('listar_marcas_woo', 'funcion_listar_marcas_woo');

function funcion_listar_marcas_woo()
{
    // Verifica si WooCommerce está activo
    if (!class_exists('WooCommerce')) {
        return '<p><strong>Error:</strong> WooCommerce no está activo.</p>';
    }

    // Obtener todas las marcas (taxonomía product_brand)
    $marcas = get_terms(array(
        'taxonomy'   => 'product_brand',
        'hide_empty' => false,
    ));

    if (is_wp_error($marcas) || empty($marcas)) {
        return '<p>No hay marcas disponibles.</p>';
    }

    // Construir HTML
    $output = '<div class="marcas-woocommerce" style="display: flex; flex-wrap: wrap; gap: 20px;">';

    foreach ($marcas as $marca) {
        // Obtener imagen de la marca (thumbnail)
        $thumb_id = get_term_meta($marca->term_id, 'thumbnail_id', true);
        $img_url  = wp_get_attachment_url($thumb_id);

        // URL de la página de la marca
        $term_link = get_term_link($marca);

        $output .= '<div class="marca" style="width: 150px; text-align: center;">';

        if ($img_url) {
            $output .= '<a href="' . esc_url($term_link) . '">';
            $output .= '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($marca->name) . '" style="max-width: 100%; height: auto;">';
            $output .= '</a>';
        }

        $output .= '<p><a href="' . esc_url($term_link) . '">' . esc_html($marca->name) . '</a></p>';
        $output .= '</div>';
    }

    $output .= '</div>';

    return $output;
}


require(dirname(__FILE__) . '/features/woo_product_buttons.php');
