<?php

/**
 * Variables globales para almacenar datos temporales sobre la imagen destacada
 * y la galería de imágenes de productos antes de que se actualicen.
 *
 * Estas variables se utilizan para comparar el estado anterior de un producto
 * con el nuevo estado cuando se guarda o actualiza un producto.
 */


function solu_currencies_exchange_plugin_activation()
{
//    solu_log("Activacion plugin:".SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN);
    create_tables();
    set_options();
    // =====================================================
    // Eliminar cualquier opción creada en la base de datos (solo en Unsinstall)
    //    delete_option('solu_currencies_exchange_version');
    //    delete_logs_table();
}


function solu_currencies_exchange_plugin_disable()
{
    solu_log(message: "Desactivacion plugin:".SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN);
    // Eliminar cualquier opción creada en la base de datos (solo en Unsinstall)
    delete_option(SOLU_CURRENCIES_EXCHANGE_OPTION_VERSION_PLUGIN);
    delete_option(SOLU_CURRENCIES_EXCHANGE_OPTION_SHORTEN_TITLE);
}


function set_options()
{

    // Obtiene la opción de acortar el título utilizando la clase.
    $option_shorten_title = get_option(SOLU_CURRENCIES_EXCHANGE_OPTION_SHORTEN_TITLE);
    if (empty($option_shorten_title)) {
        update_option(SOLU_CURRENCIES_EXCHANGE_OPTION_SHORTEN_TITLE, 'off'); // Establece el valor predeterminado como 'off')
    }

    update_option(SOLU_CURRENCIES_EXCHANGE_OPTION_VERSION_PLUGIN, SOLU_CURRENCIES_EXCHANGE_VERSION);
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
function save_log_db_table($label, $data)
{
    global $wpdb;
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE_LOG;
    $current_user = wp_get_current_user();
    $username_wp = $current_user ? $current_user->user_login : null;
    $id_user_wp = $current_user ? $current_user->ID : null;

    // Preparar los datos para insertar
    $data_php = print_r($data, true);
    if (is_array($data)) {
        $data = json_encode($data); // Si es un array, convertirlo a JSON
    }

    // Obtener la hora actual con la zona horaria configurada en WordPress
    $current_time_with_timezone = solu_get_date_hour_pe(); // Esta función debe devolver la hora en formato 'Y-m-d H:i:s' con la zona horaria correcta

    // Insertar los datos en la tabla de logs
    $wpdb->insert($table_name, [
        'user_id' => $id_user_wp,
        'username' => $username_wp,
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
function create_tables()
{
    global $wpdb;
    // -----------------------------------------------
    // ------------ tabla uno
    // -----------------------------------------------
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE; // Nombre de la tabla con prefijo de WordPress
    $charset_collate = $wpdb->get_charset_collate(); // Obtener el collation para la tabla

    // Consulta SQL para crear la tabla de logs si no existe
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          id BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'ID único',
          user_id BIGINT(20) NOT NULL COMMENT 'ID del usuario creo la moneda',
          username VARCHAR(100) NOT NULL COMMENT 'Nombre del usuario creo la moneda',
          currency_name VARCHAR(100) NOT NULL COMMENT 'Nombre de moneda (Soles)',
          currency_description VARCHAR(100) NULL COMMENT 'informacion de la moneda',
          currency_symbol VARCHAR(50) NOT NULL COMMENT 'Simbolo de moneda ($, S/., €)',
          currency_code VARCHAR(100) NOT NULL COMMENT 'Código o nombre de la moneda (ej: USD, EUR, MXN)',
          currency_value VARCHAR(100) NOT NULL COMMENT 'Valor asignado a la moneda (tasa de cambio, valor nominal, etc.)',
          currency_local BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Indica si es la moneda local del país (1 = sí, 0 = no). Solo una puede ser local por país.',
          currency_order tinyint NOT NULL DEFAULT 0 COMMENT 'numero de orden',
          active BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Indica si la moneda esta a ctiva (1 = sí, 0 = no)',
          created_at DATETIME NOT NULL COMMENT 'Fecha y hora de creación del registro',
          update_at DATETIME NOT NULL COMMENT 'Fecha y hora de la última actualización del registro',
          PRIMARY KEY (id)
    )COMMENT='Tabla de configuración de monedas y tasas de cambio' $charset_collate;";

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

    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE_LOG;

    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              user_id BIGINT(20) NOT NULL COMMENT 'ID del usuario creo la moneda',
              username VARCHAR(100) NOT NULL COMMENT 'Nombre del usuario creo la moneda',
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

function delete_table()
{

    

    // Eliminar la tabla `product_logs` de la base de datos
    global $wpdb;
    
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE; // Nombre de la tabla de logs
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}"); // Eliminar la tabla

    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE_LOG; // Nombre de la tabla de logs
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}"); // Eliminar la tabla
    

}
