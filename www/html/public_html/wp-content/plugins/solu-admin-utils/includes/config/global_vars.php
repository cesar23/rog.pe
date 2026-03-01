<?php

/**
 * Archivo para definir las constantes del plugin.
 */

$GLOBAL_GRUPOS_ARRAYS = array(
    'grupo_categorias_menu' => 'Grupo de categorias de menú',
    'grupo_marcas' => 'Grupo de marcas html',
);

// SISTEMA DE SEGURIDAD MEJORADO
/**
 * Obtiene los usuarios autorizados para acceder al plugin
 * Usa opciones de WordPress para mayor seguridad
 */
function solu_admin_utils_get_allowed_users() {
    $default_allowed = array('perucaos@gmail.com', 'editor2@solucionesssystem.com', 'juan@gmail.com', 'ventas@pcbyte.com.pe');
    return get_option('solu_admin_utils_allowed_emails', $default_allowed);
}

/**
 * Verifica si el usuario actual tiene permisos para acceder
 */
function solu_admin_utils_user_can_access() {
    $current_user = wp_get_current_user();
    $allowed_emails = solu_admin_utils_get_allowed_users();
    
    // Verificar si es administrador O está en la lista de emails permitidos
    return current_user_can('manage_options') || in_array($current_user->user_email, $allowed_emails);
}

// Mantener compatibilidad con código existente
$allowed_emails = solu_admin_utils_get_allowed_users();

// Definición de tipos de grupos para códigos HTML
$GLOBAL_TIPOS_GRUPOS = array(
    'category_' => array(
        'nombre' => 'Categorías',
        'descripcion' => 'Códigos de Categorías',
        'color' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
    ),
    'brand_' => array(
        'nombre' => 'Marcas',
        'descripcion' => 'Códigos de Marcas',
        'color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
    ),
    'label_' => array(
        'nombre' => 'Etiquetas',
        'descripcion' => 'Códigos de Etiquetas',
        'color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
    ),
    'products_' => array(
        'nombre' => 'Productos',
        'descripcion' => 'Códigos de Productos',
        'color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
    ),
    'other' => array(
        'nombre' => 'Otros',
        'descripcion' => 'Otros Códigos',
        'color' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'
    )
);

// Función para obtener contadores de códigos por tipo
