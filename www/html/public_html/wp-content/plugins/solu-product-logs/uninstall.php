<?php

/**
 * Archivo de desinstalación del plugin
 *
 * Este archivo es ejecutado cuando el plugin es desinstalado desde el panel de administración
 * de WordPress. Se encarga de limpiar cualquier dato que el plugin haya creado, como
 * eliminar tablas en la base de datos y opciones almacenadas.
 */

// Verificar si el archivo está siendo ejecutado en el contexto correcto
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit(); // Si no se está desinstalando desde WordPress, salir
}

// Eliminar la tabla `product_logs` de la base de datos
global $wpdb;
$table_name = SOLU_PRODUCT_LOGS_TABLE; // Nombre de la tabla de logs
$wpdb->query("DROP TABLE IF EXISTS {$table_name}"); // Eliminar la tabla

$table_name = SOLU_PRODUCT_LOGS_TABLE_DEBUG; // Nombre de la tabla de logs
$wpdb->query("DROP TABLE IF EXISTS $table_name"); // Eliminar la tabla

// Opcional: Si el plugin guarda opciones en la base de datos, eliminarlas
// Eliminar cualquier opción creada en la base de datos
delete_option('solu_product_logs_version');
