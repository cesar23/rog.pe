<?php
// Versión: 6.0.1

// ---------------------------------------------------------------------------
// ELIMINAR LA VERSIÓN DE WORDPRESS (SEGURIDAD)
// ---------------------------------------------------------------------------
if (!function_exists('complete_version_removal')) {
    function complete_version_removal()
    {
        return '';
    }
    
    add_filter('the_generator', 'complete_version_removal');
}



// ---------------------------------------------------------------------------
// EVITAR MÁXIMO DE REVISIONES PARA LOS POST
// ---------------------------------------------------------------------------
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 5);
}

// ---------------------------------------------------------------------------
// FUNCION PARA MOSTRAR POPUP CON MENSAJE
// ---------------------------------------------------------------------------
if (!function_exists('showPopUp')) {
    function showPopUp($msg, $label = '')
    {
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var popup = document.createElement('div');
                popup.className = 'popup';
                popup.id = 'lightbox';
                popup.style = `
                    padding: 5px;
                    position: fixed;
                    z-index: 9999;
                    filter: drop-shadow(0 6px 5px rgba(0,0,0,0.7));
                    border: 2px solid #3c3c3c;
                    border-radius: 15px;
                    width: 200px;
                    height: 100px;
                    background: #1e1c1c;
                    color: white;
                    text-align: center;
                    bottom: 2%;
                    right: 2%;
                `;

                var cancel = document.createElement('div');
                cancel.className = 'cancel';
                cancel.style = `
                    color: white;
                    text-align: center;
                    display: flex;
                    justify-content: flex-end;
                `;

                cancel.innerHTML = `<span style="cursor: pointer;">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACnElEQVQ4jWWTy08TURTGfzOdKyW0IwEChVBKUSoooJJoWJH4iEld6cKE4MJEd7rTv8KYaOJGlsYHiC6UjRoDO2JcGaX4QJQ+MGBBSmHKq4W55k5bQfmSk0nOfOfLdx5Xqw91UoTGPzgANBUSP4BvxZ+2ts002I1G4CpwcofAFDAC3ANiOyv+cWDo+qVMZvVuNpczy8tNpJR5kqaRSi/jFsayx1N2LSflo2KNrhWsG7p+0bJW7huGy/TVVpNM/lY5DJdOMjlPfW0NLiFMy8o8FJrWo0uJEwWh2kxm9b4QBoMP+ngzNEBT0E90Kkb0e4x9wQCvhwZ4+qAPIQSZzOpDwOc4KAjcyOZyRmmpm472QwQa/Lx63o+3ooLyqkpevnhMoKGejraDKI7iAteLAvuBM6rnmdkkXd1nSSR+Egj4eTs8xOjwC0cwnvjJ8e4wM7NzKK6qUUNWSs2AXw2szlfNxMQkR7tOM/LqGUcOtzv2PnyMcCJ8gfRCimBTgE3bVumAWrXO/5Cg6RpS23kVWn7ayF10JTAJTKtVzfyao6U1xPt3wxztaCMy/sWJI4fbnFxzS8jhaHnxOPAVf6hTxS2zplk2HjwmF9NpqRCNJqTX1yz3+kIyFks4udTiogy2HpOK6w913lS1xRZuixKxub66wdjYJ+LxacLne7FSaZZSKcLneoknpolEPrO2to4hxCZwx2lOqSi4dL3HslYG1C2YpukU1PnUqm3HdrDBT3rZIpfL4vV6erZse9CpMytr8w9EynF3acmPjfXsqaVly11VVYEtbZXH4yljfiGlel/yesuubGn2E8e7vkMgDzkmhNHvdpfkgD1AGbAORNzukkdCGJellKN/G///MTnD3d6Uug91ZArfC9vKw1X4An8AJAwER29rYHYAAAAASUVORK5CYII=" alt="Cerrar" />
                </span>`;
                cancel.onclick = function () {
                    popup.remove();
                };

                var message = document.createElement('span');
                message.innerHTML = `<?php echo $msg; ?>`;
                popup.appendChild(cancel);
                popup.appendChild(message);

                document.body.appendChild(popup);
            });
        </script>
        <?php
    }
}

// ---------------------------------------------------------------------------
// FUNCION PARA GUARDAR LOGS EN LA BASE DE DATOS
// ---------------------------------------------------------------------------
if (!function_exists('save_my_log_db')) {
    /**
     * funcion que guardara la data en una tabla de log  (wp_my_log)
     *
     * @param string $label (optional) etiqueta con al que se guardara en al tabla
     * @param mixed $data (required) data para guardarlaen la db
     *
     * @return void
     * @author  Cesar Auris
     * @since    1.0.1
     * @access   private
     *
     * Example usage:
     *  save_my_log_db($label_log,$logdata);
     *
     *
     */
    function save_my_log_db(string $label, mixed $data)
    {
        global $wpdb;
        $tablename = $wpdb->prefix . "my_log";

        // Crear la tabla si no existe
        $create_table_query = "
            CREATE TABLE IF NOT EXISTS `{$tablename}` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `label` varchar(100) NOT NULL,
              `data` JSON NOT NULL,
              `data_php` text NOT NULL,
              `createdAt` varchar(100) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($create_table_query);

        // Preparar los datos para insertar
        $data_php = print_r($data, true);
        $data = json_encode($data);
        $now = new DateTime(); //string value use: %s
        $createdAt = $now->format('Y-m-d H:i:s'); //string value use: %s

        $sql = $wpdb->prepare("INSERT INTO `$tablename` (`label`, `data`, `data_php`, `createdAt`) values (%s, %s, %s, %s)", $label, $data, $data_php, $createdAt);

        $wpdb->query($sql);
    }
}

// ---------------------------------------------------------------------------
// FUNCION PARA ESCRIBIR LOGS EN UN ARCHIVO Y/O BASE DE DATOS
// ---------------------------------------------------------------------------
if (!function_exists('my_write_log')) {
    /**
     * funcion que guarda log en el servidor
     *
     * @param string $file_path_log (required) path the file log, example: (./ruta/logs.log)
     * @param mixed $log (required) payload o data a guardar , example: ('Hola',array(),object)
     * @param string $label_log (required) label example: (core,test,plugin,etc)
     * @param bool $save_db (optional) true si guardara en tabla: wp_my_log
     * @param string $__file__ (optional) desde donde se genera , example: __FILE__
     * @param string $level (optional) type log , example: (WARNING, ERROR, INFO)
     * @param string|int $code (optional) codigo interno , example: (200,500,405)
     *
     * @return void
     * @author     Cesar Auris
     * @since    1.0.1
     * @access   private
     *
     * Example usage:
     * my_write_log(SOLU_DEBUG_PATH_LOG, 'data info', 'label_01', true,__FILE__,'DEBUG',200);
     * my_write_log(ABSPATH . '/log_debug.log', 'data info', 'label_01', true,__FILE__,'DEBUG',200);
     * my_write_log(ABSPATH . '/log_debug.log', 'data info', 'label_01');
     *
     *
     */
    function my_write_log(string $file_path_log, $log, string $label_log, bool $save_db = false, string $__file__ = null, string $level = 'DEBUG', $code = 200)
    {
        // Verificar si está en modo debug
        if (true === SOLU_DEBUG) {
            // Crear archivo si no existe
            if (!file_exists($file_path_log)) {
                file_put_contents($file_path_log, '');
                chmod($file_path_log, 0644);
            }

            // Preparar datos para el log
            $current_date = current_time('mysql');
            $logdata = [
                'data' => '',
                'type_object' => gettype($log),
                'file' => $__file__,
                'code' => $code,
                'url' => $_SERVER['REQUEST_URI'] ?? '',
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? ''
            ];

            if (is_array($log) || is_object($log)) {
                $logdata['data'] = $log;
            } else {
                $logdata['data'] = $log;
            }

            if ($save_db) {
                save_my_log_db($label_log, $logdata);
            }

            $data_final = json_encode($logdata, JSON_PRETTY_PRINT);
            $output = "[{$current_date}] [label:{$label_log}] {$level}: {$data_final}\n";

            // Escribir log en el archivo
            file_put_contents($file_path_log, $output, FILE_APPEND);
        }
    }
}

// ---------------------------------------------------------------------------
// FUNCION PARA LOGS EN CONSOLA
// ---------------------------------------------------------------------------
if (!function_exists('phpConsoleLog')) {
    function phpConsoleLog($data, $label = '')
    {

        if (gettype($data) == "array") {

            echo "<script>";
            if ($label != '') {
                echo "console.group(`" . $label . "`);";
            }
            echo "console.log(`debugWP (array):`,`" . json_encode($data) . "`);";
            if ($label != '') {
                echo "console.groupEnd();";
            }
            echo "</script>";
        } else if (gettype($data) == "object") {


            echo "<script>";
            if ($label != '') {
                echo "console.group('" . $label . "');";
            }
            echo "console.log(`debugWP (Object):`,`" . json_encode($data) . "`);";
            if ($label != '') {
                echo "console.groupEnd();";
            }
            echo "</script>";


        } else if (gettype($data) == "integer") {
            echo "<script>";
            if ($label != '') {
                echo "console.group('" . $label . "');";
            }
            echo "console.log(`debugWP (Integer):`,`" . json_encode($data) . "`);";
            if ($label != '') {
                echo "console.groupEnd();";
            }
            echo "</script>";


        } else if (gettype($data) == "double") {
            echo "<script>";
            if ($label != '') {
                echo "console.group('" . $label . "');";
            }
            echo "console.log(`debugWP (double):`,`" . json_encode($data) . "`);";
            if ($label != '') {
                echo "console.groupEnd();";
            }
            echo "</script>";


        } else {
            echo "<script>";
            if ($label != '') {
                echo "console.group('" . $label . "');";
            }
            echo "console.log(`debugWP (string):`,`" . ($data) . "`);";
            if ($label != '') {
                echo "console.groupEnd();";
            }
            echo "</script>";
        }
    }
}

// ---------------------------------------------------------------------------
// FUNCION PARA OBTENER LA DIRECCIÓN IP DEL USUARIO
// ---------------------------------------------------------------------------
if (!function_exists('getUserIP')) {
    function getUserIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
}

// ---------------------------------------------------------------------------
// PLANTILLA PARA MOSTRAR BLOQUEO DE IP
// ---------------------------------------------------------------------------
function plantilla_bloqueo($ip, $capaSeguridad = 'CAPA SEGURIDAD NUMERO 01', $extra = '', $country_code = '', $country_name = '', $DEBUG = '')
{
    if ($DEBUG != '') {
        $DEBUG = json_encode($DEBUG, JSON_PRETTY_PRINT);
    }

    $templateHtml = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Informativo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="text-center">
<form class="form-signin">
    <img class="mb-4" src="https://1.bp.blogspot.com/-aahfC3fLFXU/YFkuaFCCrQI/AAAAAAAAcs0/LJV9Ir2YIMwZSGVmoDTn2-JmjrR2YsIqgCLcBGAsYHQ/s16000/logo%2Bfacebook.png" alt="" width="50%" height="">
    <h1 class="h3 mb-12 font-weight-normal">No tienes acceso desde tu IP.</h1>
    <h3 class="h3 mb-3 font-weight-normal"><strong>%IP_DENIED%</strong></h3>
    <h3 class="h5 mb-3 font-weight-normal"><strong>%EXTRA%</strong></h5>
    <h3 class="h5 mb-3 font-weight-normal">%BANERA%</h5>
    <button type="button" class="btn btn-warning">%CAPA_SEGURITY%</button>
    <p>Contacta con el webmaster www.solucionessystem.com</p>
    <a class="btn btn-lg btn-primary btn-block" href="https://www.solucionessystem.com?type=bloqueado-por-ip">click aquí</a>
    <p class="mt-5 mb-3 text-muted">&copy; 2009-2021 - versión 01</p>
</form>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.group("DEBUG");
        console.log(`%DEBUG%`);
        console.groupEnd();
    });
</script>
</body>
</html>';

    $templateHtml = str_replace(['%IP_DENIED%', '%CAPA_SEGURITY%', '%DEBUG%', '%EXTRA%'], [$ip, $capaSeguridad, $DEBUG, $extra], $templateHtml);

    if ($country_code != "") {
        $templateHtml = str_replace('%BANERA%', "<img alt=\"{$country_name}\" src=\"//cesar23.github.io/cdn_webs/assets/country-flag-icons-gh-pages/1x1//{$country_code}.svg\"/>", $templateHtml);
    } else {
        $templateHtml = str_replace('%BANERA%', '', $templateHtml);
    }

    header('HTTP/1.0 403 Forbidden');
    exit($templateHtml);
}

// ---------------------------------------------------------------------------
// ACCIÓN AL INICIAR EL LOGIN
// ---------------------------------------------------------------------------
function action_login_init()
{
    global $config_child_cesar;
    $ip = $_SERVER['REMOTE_ADDR'];

    if ($config_child_cesar['valid_ip_country_capa_1']['active'] == 1) {
        $ips_allows = $config_child_cesar['valid_ip_country_capa_1']['ips_allows'];
        if (in_array($ip, $ips_allows)) {
            return true;
        }

        if (strlen($ip) <= 15) {
            require_once(dirname(__FILE__) . '/CheckRangeIpCountry.php');
            $isTrueIpRange = CheckRangeIpCountry::validIpCountry($ip, "PE");
            if ($isTrueIpRange !== true) {
                plantilla_bloqueo($ip, 'CAPA SEGURIDAD NUMERO 01', "", "", "", "La ip:{$ip} no está en el rango de IPs permitidas");
            } else {
                phpConsoleLog("Se validó IP {$ip}", "valid_ip_capa_1");
                if ($config_child_cesar['valid_ip_country_capa_1']['modal_info'] == 1) {
                    showPopUp("Se validó IP {$ip} valid_ip_capa_1");
                }
                return true;
            }
        }
    }

    if ($config_child_cesar['valid_ip_country_capa_2']['active'] == 1) {
        if (isset($_COOKIE['cook_ip'])) {
            $cookie = json_decode(stripslashes($_COOKIE['cook_ip']));
            $messages = implode(",", (array)$cookie->messages);
            $debug = implode(",", (array)$cookie->debug);

            if ($cookie->ip == $ip && $cookie->valid == 0) {
                plantilla_bloqueo($ip, 'CAPA SEGURIDAD NUMERO 02 - cookie', $messages, $cookie->country_code, $cookie->country_name, $cookie);
            }
            if ($cookie->ip == $ip && $cookie->valid == 1) {
                if ($config_child_cesar['valid_ip_country_capa_2']['modal_info'] == 1) {
                    showPopUp("Se validó IP {$ip} valid_ip_capa_2, cookie");
                }
                return true;
            }
        }

        require_once(dirname(__FILE__) . '/filterGeoIp.php');
        try {
            $CountryIpAllowClass = new FilterGeoIp();
            $country_allows = $config_child_cesar['valid_ip_country_capa_2']['country_allows'];
            $ips_allows = $config_child_cesar['valid_ip_country_capa_2']['ips_allows'];
            $objRes = $CountryIpAllowClass->validIpCountry($ip, $country_allows, $ips_allows);
            $debug = implode(",", (array)$objRes->debug);
            $messages = implode(",", (array)$objRes->messages);

            if ($objRes->success === false) {
                setcookie('cook_ip', json_encode([
                    "ip" => $ip,
                    "valid" => 0,
                    "messages" => $messages,
                    "country_code" => $objRes->country_code,
                    "country_name" => $objRes->country_name,
                    "debug" => $objRes
                ]), time() + 3600);

                plantilla_bloqueo($ip, 'CAPA SEGURIDAD NUMERO 02', $messages, $objRes->country_code, $objRes->country_name, $objRes);
            } else {
                setcookie('cook_ip', json_encode([
                    "ip" => $ip,
                    "valid" => 1,
                    "messages" => "IP válida [{$ip}]",
                    "country_code" => $objRes->country_code,
                    "country_name" => $objRes->country_name,
                    "debug" => $debug
                ]), time() + 3600);

                if ($config_child_cesar['valid_ip_country_capa_2']['modal_info'] == 1) {
                    showPopUp("Se validó IP {$ip} valid_ip_capa_2");
                }
                phpConsoleLog($objRes, "debug");
            }
        } catch (Exception $e) {
            wp_die('Error: ' . $e->getMessage(), 'Error', ['back_link' => true]);
        }
    }
}

add_action('login_init', 'action_login_init', 10, 0);

// ---------------------------------------------------------------------------
// MODIFICAR APARIENCIA DE LOGIN
// ---------------------------------------------------------------------------
function my_login_logo()
{
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo home_url('/');?>soluciones-tools/images/logo-sistema.png);
            height: 238px !important;
            background-size: 238px !important;
            width: 256px !important;
        }
    </style>
    <?php
}

function my_login_stylesheet()
{
    $filepath = $_SERVER['DOCUMENT_ROOT'] . '/soluciones-tools/js/funciones-mias.js';
    if (file_exists($filepath)) {
        wp_enqueue_script('script-func-mias', home_url('/') . 'soluciones-tools/js/funciones-mias.js', [], version_id(), true);
    }

    wp_enqueue_style('custom-login', home_url('/') . 'soluciones-tools/css/style-login.css', [], version_id(), 'all');
    wp_enqueue_script('custom-login', home_url('/') . 'soluciones-tools/js/style-login.js', [], version_id(), true);
}

if (isset($config_child_cesar) && $config_child_cesar['my_style_login']['active'] == 1) {
    add_action('login_enqueue_scripts', 'my_login_logo');
    add_action('login_enqueue_scripts', 'my_login_stylesheet');
}

// ---------------------------------------------------------------------------
// CUSTOM ADMIN FOOTER
// ---------------------------------------------------------------------------
function filter_admin_footer_text($span_id_footer_thankyou_text_span)
{
    return str_replace('<a href="https://es.wordpress.org/">WordPress</a>', '<a href="https://solucionessystem.com/">www.solucionessystem.com</a>', $span_id_footer_thankyou_text_span);
}

add_filter('admin_footer_text', 'filter_admin_footer_text', 10, 1);

// ---------------------------------------------------------------------------
// WIDGET PARA EL DASHBOARD
// ---------------------------------------------------------------------------
function dashboard_widget_function1()
{
    echo '<img src="http://icons.iconarchive.com/icons/apathae/wren/128/Utilities-icon.png" /><br>
    Cuenta con una interfaz que controla una o varias bases de datos donde se aloja el contenido del sitio web. El sistema permite manejar de manera independiente el contenido y el diseño. Así, es posible manejar el contenido y darle en cualquier momento un diseño distinto al sitio web sin tener que darle formato al contenido de nuevo, además de permitir la fácil y controlada publicación en el sitio a varios editores. Un ejemplo clásico es el de editores que cargan el contenido al sistema y otro de nivel superior (moderador o administrador) que permite que estos contenidos sean visibles a todo el público.';
}

function dashboard_help_widget_function()
{
    echo '<img src="http://icons.iconarchive.com/icons/oxygen-icons.org/oxygen/128/Actions-help-about-icon.png" /><br>
    Puedes encontrar más ayuda contactándote: 937516027<br>email:ventas@solucionessytem.com.';
}

function add_custom_dashboard_widget()
{
    wp_add_dashboard_widget('custom_dashboard_widget', 'Ayuda amigos', 'dashboard_help_widget_function');
}

function add_custom_dashboard_widget2()
{
    wp_add_dashboard_widget('dashboard_widget', 'Panel de administración usuario', 'dashboard_widget_function1');
}

add_action('wp_dashboard_setup', 'add_custom_dashboard_widget');
add_action('wp_dashboard_setup', 'add_custom_dashboard_widget2');

// ---------------------------------------------------------------------------
// AGREGAR ESTILO AL ADMIN PANEL
// ---------------------------------------------------------------------------
add_action('admin_head', 'my_custom_fonts',1);
function my_custom_fonts()
{
    global $config_child_cesar;
    $user = new WP_User(get_current_user_id());
    $rol_first = $user->roles[0];
    $user_email = $user->user_email;

    $filepath = $_SERVER['DOCUMENT_ROOT'] . '/soluciones-tools/js/funciones-mias.js';
    if (file_exists($filepath)) {
        wp_enqueue_script('script-func-miass', home_url('/') . 'soluciones-tools/js/funciones-mias.js', [], version_id(), true);
    }

    if ($config_child_cesar['usuarios_opciones_limitadas']['active'] != 1) {
        return;
    }

    if ($rol_first === "administrator" && !in_array($user_email, $config_child_cesar['usuarios_opciones_limitadas']['array_users_admin'])) {
        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/soluciones-tools/js/back-end-gestor.js';
        if (file_exists($filepath)) {
            wp_enqueue_script('backend-js-gestor-v1', home_url('/') . 'soluciones-tools/js/back-end-gestor.js', [], version_id(), true);
        }

        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/soluciones-tools/css/back-end-gestor.css';
        if (file_exists($filepath)) {
            wp_enqueue_style('backend-css-gestor-v1', home_url('/') . 'soluciones-tools/css/back-end-gestor.css', [], version_id(), 'all');
        }

        ?>
        <style>
            /* ==================================
            START PRECARGA
             ================================== */
            /* Oculta el contenido inicialmente con opacidad 0 y añade un fondo oscuro */
            body {
                opacity: 0;
                background-color: #2b2929; /* Fondo oscuro mientras carga */

            }

            /* Clase que muestra el contenido con opacidad 1 */
            body.visible {
                opacity: 1;
                background-color: #fff; /* Cambia el fondo a blanco o el color que prefieras */
                transition: opacity 3s ease-in-out; /* Transición suave de 1 segundo */
            }

            /* ==================================
            END PRECARGA
             ================================== */

            .updated, .update-nag, .bsf-update-nag, #welcome-panel, #footer-upgrade {
                display: none;
            }
            .n2_getting_started__video, .n2_dashboard_manager, .n2_dashboard_info, .n2_help_center {
                display: none;
            }
            #wpadminbar #wp-admin-bar-flatsome_panel, #wp-admin-bar-flatsome-activate {
                display: none;
            }
            #wpadminbar #wp-admin-bar-wpseo-menu {
                display: none;
            }
        </style>

        <?php

        echo '<script>console.log("Modo Gestor con limitaciones")</script>';

    }

    echo '<link href="https://fonts.googleapis.com/css?family=Roboto|Rubik" rel="stylesheet">';
    echo '<style>
        #wpadminbar { background: #8e0d0d; }
        #adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap { background-color: #20314e; }
    </style>';
}




// ---------------------------------------------------------------------------
// SCRIPT PARA EL FRONTEND
// ---------------------------------------------------------------------------
function script_front_end()
{
    global $config_child_cesar;
    if ($config_child_cesar['load_script_front_end']['active'] != 1) {
        return;
    }

    foreach ($config_child_cesar['load_script_front_end']['js_add'] as $id_recurso => $script) {
        if (strpos($script, '://') !== false) {
            wp_register_script($id_recurso, $script, null, null, true);
        } else {
            $filepath = $_SERVER['DOCUMENT_ROOT'] . "/" . $script;
            if (file_exists($filepath)) {
                if (strpos($id_recurso, '_depJquery') !== false) {
                    wp_enqueue_script($id_recurso, home_url('/') . $script, ['jquery'], version_id(), true);
                } else {
                    wp_enqueue_script($id_recurso, home_url('/') . $script, [], version_id(), true);
                }
            }
        }
    }

    foreach ($config_child_cesar['load_script_front_end']['css_add'] as $id_recurso => $script) {
        if (strpos($script, '://') !== false) {
            wp_enqueue_style($id_recurso, $script);
        } else {
            $filepath = $_SERVER['DOCUMENT_ROOT'] . "/" . $script;
            if (file_exists($filepath)) {
                wp_enqueue_style($id_recurso, home_url('/') . $script, [], version_id(), 'all');
            }
        }
    }
}

add_action('wp_enqueue_scripts', 'script_front_end');

// ---------------------------------------------------------------------------
// MIME TYPES PERMITIDOS
// ---------------------------------------------------------------------------
function bp_mime_type($mime_types)
{
    $mime_types['svg'] = 'image/svg+xml';
    $mime_types['webp'] = 'image/webp';
    return $mime_types;
}

add_filter('upload_mimes', 'bp_mime_type', 1, 1);

// ---------------------------------------------------------------------------
// VALIDAR TIPOS DE ARCHIVOS PERMITIDOS AL SUBIR
// ---------------------------------------------------------------------------
function filter_pre_upload($file)
{
    global $config_child_cesar;

    if ($config_child_cesar['usuarios_opciones_limitadas']['active'] == 1) {
        $user = new WP_User(get_current_user_id());
        $rol_first = $user->roles[0];
        $user_email = $user->user_email;
        $file_type = wp_check_filetype($file['name']);
        $extension = $file_type['ext'];

        if ($rol_first === "administrator" && !in_array($user_email, $config_child_cesar['usuarios_opciones_limitadas']['array_users_admin'])) {
            if ($extension !== 'webp') {
                $file['error'] = 'Solo se permiten archivos .webp.';
            }
        }
    }

    return $file;
}

add_filter('wp_handle_upload_prefilter', 'filter_pre_upload');

// ---------------------------------------------------------------------------
// VALIDAR QUE LAS IMÁGENES WEBP SEAN DISPLAYABLES
// ---------------------------------------------------------------------------
function webp_is_displayable($result, $path)
{
    if ($result === false) {
        $displayable_image_types = [IMAGETYPE_WEBP];
        $info = @getimagesize($path);

        if (empty($info)) {
            $result = false;
        } elseif (!in_array($info[2], $displayable_image_types)) {
            $result = false;
        } else {
            $result = true;
        }
    }

    return $result;
}

add_filter('file_is_displayable_image', 'webp_is_displayable', 10, 2);

// ---------------------------------------------------------------------------
// REMOVER TAMAÑOS DE IMAGEN POR DEFECTO
// ---------------------------------------------------------------------------
function remove_default_image_sizes($sizes)
{
    global $config_child_cesar;
    if ($config_child_cesar['delete_images_genereadas']['active'] != 1) {
        return $sizes;
    }

    foreach ($sizes as $mkey => $mval) {
        $tam_allow = $config_child_cesar['delete_images_genereadas']['tam_allow'];
        if (!in_array($mkey, $tam_allow)) {
            unset($sizes[$mkey]);
        }
    }

    return $sizes;
}

add_filter('intermediate_image_sizes_advanced', 'remove_default_image_sizes');

// ---------------------------------------------------------------------------
// Solo Activar el tamaño 'thumbnail' para las imágenes subidas
// ---------------------------------------------------------------------------

add_filter('intermediate_image_sizes_advanced', function($sizes) {

  global $config_child_cesar;
  if ($config_child_cesar['solo_thumbnail']['active'] != 1) {
    return $sizes;
  }

  // Validar que exista la clave 'thumbnail'
  if (isset($sizes['thumbnail'])) {
    return [
        'thumbnail' => $sizes['thumbnail'],
    ];
  }
  // Si no existe, no devolver ningún tamaño adicional
  return [];
});

// ---------------------------------------------------------------------------
// VALIDAR DIMENSIONES DE IMÁGENES SUBIDAS
// ---------------------------------------------------------------------------
function check_valid_image_size($file)
{
    global $config_child_cesar;
    if ($config_child_cesar['valid_size_image']['active'] != 1) {
        return $file;
    }

    $allowed_mimetypes = ['image/gif', 'image/jpeg', 'image/png', 'image/bmp', 'image/webp'];
    if (!in_array($file['type'], $allowed_mimetypes)) {
        return $file;
    }

    $image = getimagesize($file['tmp_name']);
    $maximum = [
        'width' => $config_child_cesar['valid_size_image']['maximum_width'],
        'height' => $config_child_cesar['valid_size_image']['maximum_height']
    ];
    $minimum = [
        'width' => $config_child_cesar['valid_size_image']['minimum_width'],
        'height' => $config_child_cesar['valid_size_image']['minimum_height']
    ];

    $image_width = $image[0];
    $image_height = $image[1];

    if ($image_width < $minimum['width'] || $image_height < $minimum['height']) {
        if ($file['type'] == 'image/png') {
            return $file;
        }
        $file['error'] = "Las dimensiones de la imagen son demasiado pequeñas. Ancho mínimo: {$minimum['width']} píxeles, Alto mínimo: {$minimum['height']} píxeles. La imagen cargada tiene {$image_width} x {$image_height} píxeles.";
        return $file;
    } elseif ($image_width > $maximum['width'] || $image_height > $maximum['height']) {
        $file['error'] = "Las dimensiones de la imagen son demasiado grandes. Ancho máximo: {$maximum['width']} píxeles, Alto máximo: {$maximum['height']} píxeles. La imagen cargada tiene {$image_width} x {$image_height} píxeles.";
        return $file;
    } else {
        return $file;
    }
}

add_filter('wp_handle_upload_prefilter', 'check_valid_image_size');

// ---------------------------------------------------------------------------
// DEPURAR MENÚS DE ADMINISTRACIÓN
// ---------------------------------------------------------------------------
function debug_admin_menus()
{
    global $submenu, $menu, $pagenow;
    global $config_child_cesar;
    //Haremos el debug si  esta  activo
    if ($config_child_cesar['personalizar_menu']['debug'] != 1) {
        return;
    }

    if ($pagenow == 'index.php') {  // PRINTS ON DASHBOARD


//        echo '----------------------------------------------';
//        echo '----------MENUS---------------------------';
//        echo '----------------------------------------------';
//        echo '<pre>';
//        print_r( $menu );
//        echo '</pre>'; // TOP LEVEL MENUS
//        echo '----------------------------------------------';
//        echo '----------Sub MENUS---------------------------';
//        echo '----------------------------------------------';
//        echo '<pre>';
//        print_r( $submenu );
//        echo '</pre>'; // SUBMENUS
    }
}

add_action('admin_notices', 'debug_admin_menus');

// ---------------------------------------------------------------------------
// CAMBIAR MENÚS DE ADMINISTRACIÓN
// ---------------------------------------------------------------------------
function change_admin_menus()
{
    global $config_child_cesar;
    global $pagenow;

    if ($config_child_cesar['personalizar_menu']['active'] != 1) {
        return;
    }

    $user = new WP_User(get_current_user_id());
    $rol_first = $user->roles[0];
    $user_email = $user->user_email;

    if ($rol_first === "administrator" && !in_array($user_email, $config_child_cesar['usuarios_opciones_limitadas']['array_users_admin'])) {
        $show_menus = $config_child_cesar['personalizar_menu']['show_menus'];
        $sub_menus_del_array = $config_child_cesar['personalizar_menu']['sub_menus_del_array'];

        foreach ($GLOBALS['menu'] as $mkey => $mval) {
            $slug_menu = $mval[2];
            if (!in_array($slug_menu, $show_menus)) {
                unset($GLOBALS['menu'][$mkey]);
            }
        }

        foreach ($GLOBALS['submenu'] as $mkey => $subMenu) {
            if (!array_key_exists($mkey, $sub_menus_del_array)) {
                continue;
            }
            $opt_menu_del_array = $sub_menus_del_array[$mkey];
            foreach ($subMenu as $key => $option) {
                $slug_submenu = $option[2];
                if (in_array($slug_submenu, $opt_menu_del_array)) {
                    unset($GLOBALS['submenu'][$mkey][$key]);
                }
            }
        }
    }
}

add_filter('admin_menu', 'change_admin_menus', PHP_INT_MAX);

// ---------------------------------------------------------------------------
// DEPURAR BARRA DE ADMINISTRACIÓN
// ---------------------------------------------------------------------------
function debug_admin_bar_cesar($all_toolbar_nodes)
{
    global $config_child_cesar;

    if ($config_child_cesar['personalizar_menu']['debug'] != 1) {
        return;
    }

    phpConsoleLog($all_toolbar_nodes, 'ADMINBAR DEBUG');
}

// ---------------------------------------------------------------------------
// REMOVER ELEMENTOS DE LA BARRA DE ADMINISTRACIÓN
// ---------------------------------------------------------------------------
function remove_from_admin_bar($wp_admin_bar)
{
    global $config_child_cesar;
    $all_toolbar_nodes = $wp_admin_bar->get_nodes();

    debug_admin_bar_cesar($all_toolbar_nodes);

    if ($config_child_cesar['personalizar_menu']['active'] != 1) {
        return;
    }

    $user = new WP_User(get_current_user_id());
    $rol_first = $user->roles[0];
    $user_email = $user->user_email;

    if (!in_array($user->ID, $config_child_cesar['usuarios_opciones_limitadas_menu'])) {
        return;
    }

    if ($config_child_cesar['usuarios_opciones_limitadas_menu']['active'] != 1) {
        return;
    }

    if ($rol_first === "administrator" && !in_array($user_email, $config_child_cesar['usuarios_opciones_limitadas']['array_users_admin'])) {
        $remover_nav_menus = $config_child_cesar['personalizar_menu']['remover_nav_menus'];
        $remover_link_nav_menus = $config_child_cesar['personalizar_menu']['remover_link_nav_menus'];

        foreach ($all_toolbar_nodes as $node) {
            if (in_array($node->id, $remover_nav_menus)) {
                $wp_admin_bar->remove_node($node->id);
            }
        }

        foreach ($all_toolbar_nodes as $node) {
            if (in_array($node->id, $remover_link_nav_menus)) {
                $args = $node;
                $args->href = '';
                $wp_admin_bar->add_node($args);
            }
        }
    }
}

add_action('admin_bar_menu', 'remove_from_admin_bar', PHP_INT_MAX);

// ---------------------------------------------------------------------------
// BLOQUEAR RECURSOS DEL TEMA
// ---------------------------------------------------------------------------
function theme_recursos_bloqueados()
{
    global $wp, $config_child_cesar;
    $user = new WP_User(get_current_user_id());
    $rol_first = $user->roles[0];
    $user_email = $user->user_email;

    if ($config_child_cesar['theme_recursos_url_bloqueados']['active'] != 1) {
        return;
    }

    if ($rol_first === "administrator" && !in_array($user_email, $config_child_cesar['usuarios_opciones_limitadas']['array_users_admin'])) {
        $path_url = add_query_arg($wp->query_vars);
        $urls_array = $config_child_cesar['theme_recursos_url_bloqueados']['recursos_links'];

        foreach ($urls_array as $url) {
            if (strpos($path_url, $url) !== false) {
                wp_die('Página Error Deshabilitada <br><a href="/wp-admin/" target="_self">Panel</a>', 'Información');
            }
        }
    }
}

add_action('admin_init', 'theme_recursos_bloqueados');

if (@$config_child_cesar['secret_login']['active'] == 1) {
    add_filter('wp_login_errors', 'my_login_form_lock_down', 90, 2);
}

// ---------------------------------------------------------------------------
// BLOQUEAR FORMULARIO DE LOGIN CON PARÁMETRO SECRETO
// ---------------------------------------------------------------------------
function my_login_form_lock_down($errors, $redirect_to)
{
    global $config_child_cesar;
    $secret_key = "clave";
    $secret_password = $config_child_cesar['secret_login']['secret_password'];

    if (!isset($_GET[$secret_key]) || $_GET[$secret_key] != $secret_password) {
        login_header(__('Log In'), '', $errors);
        echo "</div>";
        do_action('login_footer');
        echo "</body></html>";
        exit;
    }

    return $errors;
}

// ---------------------------------------------------------------------------
// RESTRINGIR URLS PARA CIERTAS IP
// ---------------------------------------------------------------------------
function action_url_restric_for_ip()
{
    global $config_child_cesar;

    if ($config_child_cesar['restrict_paths_url']['active'] != 1) {
        return true;
    }

    if (is_user_logged_in()) {
        return true;
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $uri = $_SERVER['REQUEST_URI'];
    $not_allowed = $config_child_cesar['restrict_paths_url']['not_allowed'];

    if ($config_child_cesar['valid_ip_country_capa_1']['active'] == 1) {
        $ruta_filtrar = false;
        foreach ($not_allowed as $check) {
            if (strpos(strtolower($uri), strtolower($check)) !== false) {
                $ruta_filtrar = true;
            }
        }

        if ($ruta_filtrar == true) {
            $ips_allows = $config_child_cesar['valid_ip_country_capa_1']['ips_allows'];
            if (in_array($ip, $ips_allows)) {
                return true;
            }

            if (strlen($ip) <= 15) {
                require_once(dirname(__FILE__) . '/CheckRangeIpCountry.php');
                $isTrueIpRange = CheckRangeIpCountry::validIpCountry($ip, "PE");
                if ($isTrueIpRange !== true) {
                    plantilla_bloqueo($ip, 'CAPA SEGURIDAD NUMERO 01', "", "", "", "La ip:{$ip} no está en el rango de IPs permitidas");
                } else {
                    phpConsoleLog("Se validó IP {$ip}", "valid_ip_capa_1");
                    if ($config_child_cesar['valid_ip_country_capa_1']['modal_info'] == 1) {
                        showPopUp("Se validó IP {$ip} valid_ip_capa_1");
                    }
                    return true;
                }
            }
        }
    }

    if ($config_child_cesar['valid_ip_country_capa_2']['active'] == 1) {
        $ruta_filtrar = false;
        foreach ($not_allowed as $check) {
            if (strpos(strtolower($uri), strtolower($check)) !== false) {
                $ruta_filtrar = true;
            }
        }

        if ($ruta_filtrar == true) {
            if (isset($_COOKIE['cook_ip'])) {
                $cookie = json_decode(stripslashes($_COOKIE['cook_ip']));
                $messages = implode(",", (array)$cookie->messages);
                $debug = implode(",", (array)$cookie->debug);

                if ($cookie->ip == $ip && $cookie->valid == 0) {
                    plantilla_bloqueo($ip, 'CAPA SEGURIDAD NUMERO 02 - cookie', $messages, $cookie->country_code, $cookie->country_name, $cookie);
                }
                if ($cookie->ip == $ip && $cookie->valid == 1) {
                    if ($config_child_cesar['valid_ip_country_capa_2']['modal_info'] == 1) {
                        showPopUp("Se validó IP {$ip} valid_ip_capa_2, cookie");
                    }
                    return true;
                }
            }

            require_once(dirname(__FILE__) . '/filterGeoIp.php');
            try {
                $CountryIpAllowClass = new FilterGeoIp();
                $country_allows = $config_child_cesar['valid_ip_country_capa_2']['country_allows'];
                $ips_allows = $config_child_cesar['valid_ip_country_capa_2']['ips_allows'];
                $objRes = $CountryIpAllowClass->validIpCountry($ip, $country_allows, $ips_allows);
                $debug = implode(",", (array)$objRes->debug);
                $messages = implode(",", (array)$objRes->messages);

                if ($objRes->success === false) {
                    setcookie('cook_ip', json_encode([
                        "ip" => $ip,
                        "valid" => 0,
                        "messages" => $messages,
                        "country_code" => $objRes->country_code,
                        "country_name" => $objRes->country_name,
                        "debug" => $objRes
                    ]), time() + 3600);

                    plantilla_bloqueo($ip, 'CAPA SEGURIDAD NUMERO 02', $messages, $objRes->country_code, $objRes->country_name, $objRes);
                } else {
                    setcookie('cook_ip', json_encode([
                        "ip" => $ip,
                        "valid" => 1,
                        "messages" => "IP válida [{$ip}]",
                        "country_code" => $objRes->country_code,
                        "country_name" => $objRes->country_name,
                        "debug" => $debug
                    ]), time() + 3600);

                    if ($config_child_cesar['valid_ip_country_capa_2']['modal_info'] == 1) {
                        showPopUp("Se validó IP {$ip} valid_ip_capa_2");
                    }
                    phpConsoleLog($objRes, "debug");
                }
            } catch (Exception $e) {
                wp_die('Error: ' . $e->getMessage(), 'Error', ['back_link' => true]);
            }
        }
    }
}

add_action('template_redirect', 'action_url_restric_for_ip');

// ---------------------------------------------------------------------------
// OCULTAR BARRA DEL FRONTEND EN USUARIO NO SUPERADMIN
// ---------------------------------------------------------------------------
function ocultar_admin_bar_para_administradores_excepto_super_admin()
{
    global $config_child_cesar;

    // Solo ejecutar en el frontend
    if (is_admin()) {
        return;
    }

    if ($config_child_cesar['hide__show_admin_bar']['active'] != 1) {
        return;
    }


    // Obtener el objeto del usuario actual
    $user = wp_get_current_user();
    if (in_array('administrator', $user->roles)) {
        // If the user is an admin, do something
        if (!in_array($user->user_email, $config_child_cesar['usuarios_opciones_limitadas']['array_users_admin'])) {
            // Aquí no desactivamos toda la barra con show_admin_bar(false)
            show_admin_bar(false);

            // Este codigo es para ocultar elementos de edicion como el de flatsome en el frontend
            $scriptCss="soluciones-tools/css/front-end-gestor.css";
            $filepath = $_SERVER['DOCUMENT_ROOT'] . "/" . $scriptCss;
            if (file_exists($filepath)) {
                wp_enqueue_style("front-end-gestor-css-solu", home_url('/') . $scriptCss, [], version_id(), 'all');
            }
            // Este codigo es para ocultar elementos de edicion como el de flatsome en el frontend
            $scriptJs="soluciones-tools/js/front-end-gestor.js";
            $filepath = $_SERVER['DOCUMENT_ROOT'] . "/" . $scriptJs;
            if (file_exists($filepath)) {
                wp_enqueue_script("front-end-gestor-js-solu", home_url('/') . $scriptJs, [], version_id(), true);
            }


        }
    }


}

// Oculta  barra front_end para los usuarios qeu no son  super admin
add_action('after_setup_theme', 'ocultar_admin_bar_para_administradores_excepto_super_admin');

