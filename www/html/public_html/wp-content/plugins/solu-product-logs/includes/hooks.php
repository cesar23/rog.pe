<?php

/**
 * Hook para capturar la imagen destacada y la galería de imágenes
 * antes de que se actualice el post
 *
 * Este hook utiliza `wp_insert_post_data` para capturar el estado actual de la
 * imagen destacada y la galería de imágenes de un producto antes de que se realicen
 * cambios. Esto permite comparar los cambios antes y después de la actualización.
 *
 * @param array $data Los datos que se van a insertar en la base de datos
 * @param array $postarr Los datos completos del post antes de guardarse
 *
 * @return array Los datos de post que se van a guardar
 *
 * Ejemplo de uso:
 *
 * Este hook se registra automáticamente al activar el plugin:
 * add_filter('wp_insert_post_data', 'capture_old_thumbnail_and_gallery', 10, 2);
 */
add_filter('wp_insert_post_data', 'capture_old_thumbnail_and_gallery', 10, 2);

function capture_old_thumbnail_and_gallery($data, $postarr) {
    // Verificar si el tipo de post es 'product' (para WooCommerce)
    if ($data['post_type'] == 'product') {
        global $old_thumbnail, $old_gallery_ids;

        // Capturar la imagen destacada antes de actualizar
        $old_thumbnail = get_post_meta($postarr['ID'], '_thumbnail_id', true);

        // Capturar la galería de imágenes antes de actualizar
        $old_gallery_ids = get_product_image_gallery($postarr['ID']);
    }
    return $data;
}

/**
 * Hook para registrar datos del producto antes de la actualización
 *
 * Este hook utiliza `post_updated` para capturar los datos anteriores y posteriores de un producto
 * después de haber sido actualizado. Permite comparar el estado anterior del título y contenido
 * del producto con el estado actual.
 *
 * @param int $post_ID El ID del post (producto) que ha sido actualizado
 * @param object $post_after Los datos del post después de ser actualizado
 * @param object $post_before Los datos del post antes de ser actualizado
 *
 * Ejemplo de uso:
 *
 * Este hook se registra automáticamente al activar el plugin:
 * add_action('post_updated', 'get_data_product_before', 10, 3);
 */
add_action('post_updated', 'get_data_product_before', 10, 3);

function get_data_product_before($post_ID, $post_after, $post_before) {
    // Verificar si el tipo de post es 'product' (para WooCommerce)
    if ($post_before->post_type != 'product') {
        return;
    }

    global $changesProduct;

    // Comparar el título del producto
    if ($post_before->post_title != $post_after->post_title) {
        $changesProduct['title'] = [
            'before' => $post_before->post_title,
            'after' => $post_after->post_title,
        ];
    }

    // Comparar el contenido del producto
    if ($post_before->post_content != $post_after->post_content) {
        $changesProduct['content'] = [
            'before' => $post_before->post_content,
            'after' => $post_after->post_content,
        ];
    }
    save_my_log_db_v2("get_data_product_before",$changesProduct);
}

/**
 * Hook para guardar los cambios en la imagen destacada y la galería de productos
 *
 * Este hook se activa cuando se guarda un producto, ya sea porque fue creado o actualizado.
 * Compara los datos antiguos y nuevos de la imagen destacada y la galería de imágenes
 * y, si hay cambios, se registran en la tabla de logs.
 *
 * @param int $post_ID El ID del post (producto) que ha sido guardado
 * @param object $post El objeto del post después de ser guardado
 * @param bool $update Indica si el post ha sido actualizado (true) o creado (false)
 *
 * Ejemplo de uso:
 *
 * Este hook se registra automáticamente al activar el plugin:
 * add_action('save_post', 'my_custom_save_post_action', 10, 3);
 */
add_action('save_post', 'my_custom_save_post_action', 10, 3);

function my_custom_save_post_action($post_ID, $post, $update) {
    // Verificar si el tipo de post es 'product' (para WooCommerce)
    if ($post->post_type != 'product') {
        return;
    }

    global $changesProduct, $old_thumbnail, $old_gallery_ids;

    // Obtener la nueva imagen destacada después de guardar el producto
    $new_thumbnail = get_post_meta($post_ID, '_thumbnail_id', true);

    // Obtener la nueva galería de imágenes después de guardar el producto
    $new_gallery_ids = get_product_image_gallery($post_ID);

    // Comparar la imagen destacada
    if ($new_thumbnail != $old_thumbnail) {
        $changesProduct['featured_image'] = [
            'before' => $old_thumbnail,
            'after' => $new_thumbnail
        ];
    }

    // Asegurarse de que $old_gallery_ids y $new_gallery_ids sean arrays
    if (!is_array($old_gallery_ids)) {
        $old_gallery_ids = [];
    }
    if (!is_array($new_gallery_ids)) {
        $new_gallery_ids = [];
    }

    // Comparar la galería de imágenes
    if (array_diff($new_gallery_ids, $old_gallery_ids)) {
        $changesProduct['image_gallery'] = [
            'before' => $old_gallery_ids,
            'after' => $new_gallery_ids
        ];
    }

    save_my_log_db_v2("my_custom_save_post_action",$changesProduct);

    // Si se detectaron cambios, registrar la acción en los logs
    if (!empty($changesProduct)) {
        log_product_action($post_ID, 'updated', $changesProduct);
    }
}
