<?php
/**
 * Función que formatea el valor de un cambio, ya sea un array o un string
 *
 * @param mixed $change El valor del cambio (puede ser un array o un string)
 * @return string El cambio formateado como texto legible
 */
if (!function_exists('customizeMessage')) {
    function customizeMessage($change,$key,$site_url) {
        $type_change=$key; // Featured_image ,Image_gallery ,Content ,Title
        $result="";
        if (is_array($change) ) {
            $result.= implode(', ', array_map('esc_html', $change)) ;
        }else{
            if($type_change=="featured_image"){
                $result.= '<img src="'.$site_url.'/?p='.esc_html($change).'" width="100px" class="img-thumbnail" >' ;
            }else{
                $result.= esc_html($change) ;
            }


        }
        return $result;
    }
}

/**
 * Función para obtener todos los usuarios con rol de administrador
 *
 * @return array Lista de nombres de usuario, correos electrónicos y IDs de los administradores
 */
if (!function_exists('get_admin_users')) {
    function get_admin_users() {
        // Obtener todos los usuarios con rol de administrador
        $admin_users = get_users(array(
            'role'    => 'administrator', // Filtrar por rol de administrador
            'orderby' => 'display_name',  // Ordenar por nombre mostrado
            'order'   => 'ASC'            // Orden ascendente
        ));

        // Preparar un array para almacenar los datos
        $admins = array();

        // Recorrer los usuarios y almacenar el ID, nombre y el correo
        foreach ($admin_users as $user) {
            $admins[] = array(
                'user_id'      => $user->ID,         // Agregar el ID del usuario
                'display_name' => $user->display_name,
                'email'        => $user->user_email
            );
        }

        return $admins;
    }
}

/**
 * Obtiene todos los registros de la tabla SOLU_CURRENCIES_EXCHANGE_TABLE
 * @return array
 */
function get_currencies() {
    global $wpdb;
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE;
    $sql = "SELECT * FROM $table_name";
    $results = $wpdb->get_results($sql, ARRAY_A);
    return $results;
}

/**
 * Obtiene un registro de la tabla SOLU_CURRENCIES_EXCHANGE_TABLE por su ID
 * @param int $id
 * @return array|null
 */
function get_currency($id) {
    global $wpdb;
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE;
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id);
    $result = $wpdb->get_row($sql, ARRAY_A);
    return $result;
}

/**
 * Inserta un nuevo registro en la tabla SOLU_CURRENCIES_EXCHANGE_TABLE
 * @param array $data
 * @return int|false El ID del nuevo registro o false en caso de error
 */
function insert_currency($data) {
    global $wpdb;
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE;
    $result = $wpdb->insert($table_name, $data);
    if ($result) {
        return $wpdb->insert_id;
    } else {
        return false;
    }
}


function insert_currency_log($data) {
    global $wpdb;
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE_LOG;
    $result = $wpdb->insert($table_name, $data);
    if ($result) {
        return $wpdb->insert_id;
    } else {
        return false;
    }
}


/**
 * Actualiza un registro existente en la tabla SOLU_CURRENCIES_EXCHANGE_TABLE
 * @param int $id
 * @param array $data
 * @return bool
 */
function update_currency($id, $data) {
    global $wpdb;
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE;
    $result = $wpdb->update($table_name, $data, array('id' => $id));
    return $result !== false;
}

/**
 * Elimina un registro de la tabla SOLU_CURRENCIES_EXCHANGE_TABLE
 * @param int $id
 * @return bool
 */
function delete_currency($id)
{
    global $wpdb;
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE;
    $result = $wpdb->delete($table_name, array('id' => $id));
    return $result !== false;
}
/**
 * Verifica si ya existe una moneda local.
 * @return array|null El registro de la moneda local o null si no existe.
 */
function is_local_currency_exists() {
    global $wpdb;
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE;
    $sql = "SELECT * FROM $table_name WHERE currency_local = 1";
    $result = $wpdb->get_row($sql, ARRAY_A);
    return $result;
}


/**
 * Obtiene todas las monedas que no son locales de la tabla de monedas.
 *
 * @return array|null Un array de objetos con la información de las monedas, o null si no se encuentran monedas.
 */
function get_currencies_no_locals() {
    global $wpdb;
    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE;
    $sql = "SELECT * FROM $table_name WHERE currency_local = 0";
    $result = $wpdb->get_results($sql);
    return $result;
}
