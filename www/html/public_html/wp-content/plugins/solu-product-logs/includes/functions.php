<?php
/**
 * Variables globales para almacenar datos temporales sobre la imagen destacada
 * y la galería de imágenes de productos antes de que se actualicen.
 *
 * Estas variables se utilizan para comparar el estado anterior de un producto
 * con el nuevo estado cuando se guarda o actualiza un producto.
 */
global $changesProduct, $old_thumbnail, $old_gallery_ids;
$old_thumbnail = [];       // Almacena la imagen destacada anterior del producto
$old_gallery_ids = [];     // Almacena la galería anterior del producto
$changesProduct = [];      // Almacena los cambios realizados en el producto

function plugin_activation()
{
    update_option('solu_product_logs_version', SOLU_PRODUCT_LOGS_VERSION);
    create_logs_tables();
}

function plugin_disable()
{


    // Eliminar cualquier opción creada en la base de datos (solo en Unsinstall)
//    delete_option('solu_product_logs_version');
//    delete_logs_table();
}





/**
 * Función para obtener la galería de imágenes de un producto
 *
 * Consulta la base de datos para obtener los IDs de las imágenes de la galería
 * de un producto específico almacenados en el campo meta `_product_image_gallery`.
 *
 * @param int $post_id El ID del producto (post_id en WordPress)
 *
 * @return array|null Retorna un array con los IDs de las imágenes de la galería,
 *                    o null si no se encuentra la galería.
 *
 * Ejemplo de uso:
 *
 * $gallery_ids = get_product_image_gallery(123);
 * if ($gallery_ids) {
 *     foreach ($gallery_ids as $image_id) {
 *         // Haz algo con cada ID de imagen, por ejemplo, mostrar la imagen
 *         echo wp_get_attachment_image($image_id, 'thumbnail');
 *     }
 * }
 */
if (!function_exists('get_product_image_gallery')) {
    function get_product_image_gallery($post_id)
    {
        global $wpdb;

        // Consulta SQL para obtener el meta_value del campo _product_image_gallery
        $table_name = $wpdb->prefix . 'postmeta';
        $meta_key = '_product_image_gallery';

        // Ejecutar la consulta SQL para obtener la galería de imágenes
        $gallery = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM $table_name WHERE post_id = %d AND meta_key = %s",
                $post_id,
                $meta_key
            )
        );

        // Si se encuentra la galería, convertir los IDs de las imágenes en un array
        if ($gallery) {
            return explode(',', $gallery);
        }

        // Si no hay galería, devolver null
        return null;
    }
}


// ---------------------------------------------------------------------------
// FUNCION PARA GUARDAR LOGS EN LA BASE DE DATOS
// ---------------------------------------------------------------------------

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
function save_my_log_db_v2($label, $data)
{
    global $wpdb;
    $table_name = SOLU_PRODUCT_LOGS_TABLE_DEBUG; // Usar tabla de debug que tiene columna 'label'
    // Preparar los datos para insertar
    $data_php = print_r($data, true);
    if (is_array($data)) {
        $data = json_encode($data); // Si es un array, convertirlo a JSON
    }

    // Obtener la hora actual con la zona horaria configurada en WordPress
    $current_time_with_timezone = current_time('mysql');

    // Insertar los datos en la tabla de logs
    $wpdb->insert($table_name, [
        'label' => $label,
        'data' => $data,                 // Guardar el nombre del producto
        'data_php' => $data_php,                  // Guardar la URL del producto
        'created_at' => $current_time_with_timezone  // Guardar la hora con la zona horaria configurada en WordPress
    ]);
    // Verificar si hubo algún error
    if ($wpdb->last_error) {
        error_log("Error al insertar en la tabla $table_name: " . $wpdb->last_error);
    }
}


/**
 * Función para crear la tabla de logs en la base de datos
 *
 * Crea una tabla llamada `product_logs` donde se almacenarán los registros
 * de las acciones realizadas en productos de WooCommerce. Se ejecuta al activar el plugin.
 *
 * La tabla incluye campos como el ID del producto, el usuario que realizó la acción,
 * la dirección IP, el estado del producto, los cambios realizados y la fecha de la acción.
 *
 * @return void
 *
 * Ejemplo de uso:
 *
 * register_activation_hook(__FILE__, 'create_logs_table');
 */
function create_logs_tables()
{
    global $wpdb;
    // -----------------------------------------------
    // ------------ tabla uno
    // -----------------------------------------------
    $table_name = SOLU_PRODUCT_LOGS_TABLE; // Nombre de la tabla con prefijo de WordPress
    $charset_collate = $wpdb->get_charset_collate(); // Obtener el collation para la tabla

    // Consulta SQL para crear la tabla de logs si no existe
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        product_id BIGINT(20) NOT NULL,
        product_name VARCHAR(255) NOT NULL,         -- Nueva columna para el nombre del producto
        product_url TEXT NOT NULL,                  -- Nueva columna para la URL del producto
        user_id BIGINT(20) NOT NULL,
        username VARCHAR(100) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        product_status VARCHAR(20) NOT NULL,
        action VARCHAR(50) NOT NULL,
        changes TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Ejecutar la consulta SQL para crear la tabla
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $wpdb->query($sql);

    // Verificar si hubo algún error
    if ($wpdb->last_error) {
        error_log("Error al crear la tabla $table_name: " . $wpdb->last_error);
    }

    // -----------------------------------------------
    // ------------ tabla dos
    // -----------------------------------------------

    $table_name = SOLU_PRODUCT_LOGS_TABLE_DEBUG;

    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `label` varchar(100) NOT NULL,
              `data` JSON NOT NULL,
              `data_php` text NOT NULL,
              `created_at` varchar(100) NOT NULL,
              PRIMARY KEY (`id`)
            ) $charset_collate;";

    dbDelta($sql);

    // Verificar si hubo algún error al crear la tabla
    if ($wpdb->last_error) {
        error_log("Error al crear la tabla $table_name: " . $wpdb->last_error);
    }


//    // Incluir la función `dbDelta` para crear/actualizar la tabla
//    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//    dbDelta($sql);
    // veriones antiguas USAR:
    // Ejecutar la consulta usando $wpdb->query()
//    $result = $wpdb->query($sql);
}


function delete_logs_table(){

    // Eliminar la tabla `product_logs` de la base de datos
    global $wpdb;
    $table_name =SOLU_PRODUCT_LOGS_TABLE; // Nombre de la tabla de logs
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}"); // Eliminar la tabla

    $table_name = SOLU_PRODUCT_LOGS_TABLE_DEBUG; // Nombre de la tabla de logs
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}"); // Eliminar la tabla

}

/**
 * Función para registrar acciones de un producto en WooCommerce
 *
 * Esta función guarda un registro de una acción realizada en un producto de WooCommerce,
 * como una actualización, creación o eliminación, junto con información sobre el usuario
 * que realizó la acción y los cambios que se hicieron.
 *
 * @param int $product_id ID del producto afectado
 * @param string $action Acción realizada (e.g., "created", "updated", "deleted")
 * @param array $changes Cambios realizados en el producto (opcional)
 *
 * @return void
 *
 * Ejemplo de uso:
 *
 * log_product_action(123, 'updated', ['title' => ['before' => 'Título Anterior', 'after' => 'Nuevo Título']]);
 */
function log_product_action($product_id, $action, $changes = [])
{
    // Si el usuario no está logueado, no realizar ninguna acción
    if (!is_user_logged_in()) {
        return;
    }
    save_my_log_db_v2("log_product_action", 1);

    global $wpdb;
    $user_id = get_current_user_id();         // Obtener el ID del usuario actual
    $user_info = get_userdata($user_id);      // Obtener información del usuario
    $username = $user_info->user_login;       // Obtener el nombre de usuario

    // Obtener la dirección IP del usuario
    $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';

    // Obtener el estado del producto (publicado, borrador, etc.)
    $product_status = get_post_status($product_id);

    $table_name = SOLU_PRODUCT_LOGS_TABLE; // Nombre de la tabla de logs

    // Obtener el nombre y la URL del producto
    $product_name = get_the_title($product_id);               // Nombre del producto
    $product_url = get_permalink($product_id);                // URL del producto

    // Convertir los cambios realizados en el producto a formato JSON para guardarlos en la base de datos
    $changes_json = json_encode($changes);

    // Obtener la hora actual con la zona horaria configurada en WordPress
    $current_time_with_timezone = current_time('mysql');

    // Insertar los datos en la tabla de logs
    $wpdb->insert($table_name, [
        'product_id' => $product_id,
        'product_name' => $product_name,                 // Guardar el nombre del producto
        'product_url' => $product_url,                  // Guardar la URL del producto
        'user_id' => $user_id,
        'username' => $username,
        'ip_address' => $ip_address,
        'product_status' => $product_status,
        'action' => $action,
        'changes' => $changes_json,    // Guardar los cambios como JSON
        'created_at' => $current_time_with_timezone  // Guardar la hora con la zona horaria configurada en WordPress
    ]);
}


/**
 * Función para limpiar (vaciar) la tabla de logs "product_logs"
 *
 * @return void
 */
function clear_product_logs_table()
{
    global $wpdb;

    // Obtener el prefijo de la tabla de WordPress
    $table_name = SOLU_PRODUCT_LOGS_TABLE;

    // Realizar la consulta SQL para eliminar todos los registros de la tabla
    $wpdb->query("TRUNCATE TABLE $table_name");

    // Verificar si hubo errores al ejecutar la consulta
    if ($wpdb->last_error) {
        // Puedes usar esto para depuración, si es necesario
        error_log("Error al limpiar la tabla {$table_name}: " . $wpdb->last_error);
    } else {
        // Mensaje de éxito (opcional)
        error_log("Tabla {$table_name} limpiada correctamente.");
    }
}