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
$table_name = SOLU_CURRENCIES_EXCHANGE_TABLE; // Nombre de la tabla de logs
$sql = "DROP TABLE IF EXISTS %s";
$wpdb->query($wpdb->prepare($sql, $table_name)); // Eliminar la tabla

$table_name = SOLU_CURRENCIES_EXCHANGE_TABLE_LOG; // Nombre de la tabla de logs
$sql = "DROP TABLE IF EXISTS %s";
$wpdb->query($wpdb->prepare($sql, $table_name)); // Eliminar la tabla

// Opcional: Si el plugin guarda opciones en la base de datos, eliminarlas
// Eliminar cualquier opción creada en la base de datos
delete_option(SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN.'_version');
