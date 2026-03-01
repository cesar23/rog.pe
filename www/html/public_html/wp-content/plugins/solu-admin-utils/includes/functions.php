<?php

/**
 * Variables globales para almacenar datos temporales sobre la imagen destacada
 * y la galería de imágenes de productos antes de que se actualicen.
 *
 * Estas variables se utilizan para comparar el estado anterior de un producto
 * con el nuevo estado cuando se guarda o actualiza un producto.
 */

/**
 * Función de activación del plugin
 * 
 * @since 1.2.0
 * @return void
 */
function solu_admin_utils_plugin_activation()
{
    try {
        // Crear tablas del plugin
        
        // Establecer opciones del plugin
        solu_admin_utils_set_options();
        
        solu_log('Plugin activado: ' . SOLU_ADMIN_UTIL_NAME_PLUGIN, 'info');
    } catch (Exception $e) {
        solu_log('Error al activar plugin: ' . $e->getMessage(), 'error');
        throw $e; // Re-lanzar para que WordPress muestre el error
    }
}

/**
 * Función de desactivación del plugin
 * 
 * @since 1.2.0
 * @return void
 */
function solu_admin_utils_plugin_disable()
{
    solu_log(message: "Desactivacion plugin:" . SOLU_ADMIN_UTIL_NAME_PLUGIN);
    // Eliminar cualquier opción creada en la base de datos (solo en Uninstall)
    delete_option(SOLU_ADMIN_UTIL_OPTION_VERSION_PLUGIN);
}

/**
 * Establece las opciones por defecto del plugin
 * 
 * @since 1.2.0
 * @return void
 */
function solu_admin_utils_set_options()
{
    update_option(SOLU_ADMIN_UTIL_OPTION_VERSION_PLUGIN, SOLU_ADMIN_UTIL_VERSION);
}

/**
 * Funciones de conveniencia para usar las clases de utilidades
 */

/**
 * Obtiene una instancia de la clase DateUtils
 * @param string $timezone Zona horaria (opcional)
 * @return Solu_Admin_Utils_DateUtils
 */
function solu_date_utils($timezone = 'America/Lima') {
    return new Solu_Admin_Utils_DateUtils($timezone);
}

/**
 * Obtiene una instancia de la clase StringUtils
 * @return Solu_Admin_Utils_StringUtils
 */
function solu_string_utils() {
    return new Solu_Admin_Utils_StringUtils();
}

/**
 * Función de conveniencia para logging específica del plugin
 * @param mixed $message Mensaje a registrar
 * @param string $level Nivel del log (info, error, debug)
 */
function solu_admin_utils_log($message, $level = 'info') {
    solu_log('[ADMIN_UTILS] ' . $message, $level);
}


