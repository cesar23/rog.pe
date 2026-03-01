
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
