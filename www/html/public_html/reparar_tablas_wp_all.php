<?php
/**
 * Script de Reparación de AUTO_INCREMENT en todas las tablas WordPress
 *
 * Este script realiza las siguientes operaciones:
 * - Recorre todas las tablas con prefijo wp_
 * - Verifica si cada tabla tiene AUTO_INCREMENT en su clave primaria
 * - Agrega AUTO_INCREMENT si no está presente
 *
 * @author  César
 * @version 1.0
 * @date    2025
 */

// ============================================================================
// DETECCIÓN DE ENTORNO Y CONFIGURACIÓN DE SALIDA
// ============================================================================

// Detectar si se ejecuta desde CLI o navegador web
define('IS_CLI', php_sapi_name() === 'cli');

// Configurar salida según el entorno
if (!IS_CLI) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reparación AUTO_INCREMENT WordPress</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Consolas", "Monaco", "Courier New", monospace;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #1e1e1e;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .output {
            padding: 30px;
            color: #e0e0e0;
            font-size: 14px;
            overflow-x: auto;
        }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        .info { color: #2196F3; font-weight: bold; }
        .highlight { color: #ffeb3b; font-weight: bold; }
        .separator {
            color: #666;
            margin: 15px 0;
            border-top: 1px solid #333;
        }
        .separator-major {
            color: #888;
            margin: 20px 0;
            border-top: 2px solid #444;
        }
        .table-name {
            color: #64B5F6;
            font-weight: bold;
            background: rgba(100, 181, 246, 0.1);
            padding: 2px 6px;
            border-radius: 3px;
        }
        .sql-box {
            background: #2d2d2d;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            overflow-x: auto;
        }
        .sql-box code {
            color: #a9b7c6;
            font-size: 13px;
        }
        .counter {
            display: inline-block;
            background: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            margin-right: 10px;
        }
        .footer {
            background: #2d2d2d;
            color: #888;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Reparación AUTO_INCREMENT - WordPress</h1>
            <p>Verificación y corrección automática de AUTO_INCREMENT en tablas wp_</p>
        </div>
        <div class="output"><pre>';
}

// Definir constante de salto de línea según entorno
define('LINE_BREAK', IS_CLI ? PHP_EOL : "<br>");

// ============================================================================
// CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================================================

const DB_HOST = 'localhost';
const DB_USER = 'rog_web';
const DB_PASS = 'cesar203';
const DB_NAME = 'rog_web';

// Prefijo de tablas de WordPress
const WP_PREFIX = 'wp_';

// ============================================================================
// TABLAS QUE DEBEN TENER AUTO_INCREMENT
// ============================================================================

/**
 * Tablas que DEBEN tener AUTO_INCREMENT
 * Solo estas tablas recibirán AUTO_INCREMENT en su PRIMARY KEY
 */
const TABLAS_AUTO_INCREMENT = [
 // WordPress Core (11)
 'wp_posts',
 'wp_postmeta',
 'wp_comments',
 'wp_commentmeta',
 'wp_users',
 'wp_usermeta',
 'wp_terms',
 'wp_term_taxonomy',
 'wp_termmeta',
 'wp_options',
 'wp_links',
 
 // WooCommerce (29)
 'wp_wc_admin_notes',
 'wp_wc_admin_note_actions',
 'wp_wc_customer_lookup',
 'wp_wc_download_log',
 'wp_wc_order_addresses',
 'wp_wc_order_operational_data',
 'wp_wc_order_product_lookup',
 'wp_wc_order_stats',
 'wp_wc_orders',
 'wp_wc_orders_meta',
 'wp_wc_product_download_directories',
 'wp_wc_product_meta_lookup',
 'wp_wc_rate_limits',
 'wp_wc_tax_rate_classes',
 'wp_wc_webhooks',
 'wp_woocommerce_api_keys',
 'wp_woocommerce_attribute_taxonomies',
 'wp_woocommerce_downloadable_product_permissions',
 'wp_woocommerce_log',
 'wp_woocommerce_order_itemmeta',
 'wp_woocommerce_order_items',
 'wp_woocommerce_payment_tokenmeta',
 'wp_woocommerce_payment_tokens',
 'wp_woocommerce_sessions',
 'wp_woocommerce_shipping_zone_locations',
 'wp_woocommerce_shipping_zone_methods',
 'wp_woocommerce_shipping_zones',
 'wp_woocommerce_tax_rate_locations',
 'wp_woocommerce_tax_rates',
 
 // MailPoet (37)
 'wp_mailpoet_automations',
 'wp_mailpoet_automation_runs',
 'wp_mailpoet_automation_run_logs',
 'wp_mailpoet_automation_run_subjects',
 'wp_mailpoet_automation_versions',
 'wp_mailpoet_custom_fields',
 'wp_mailpoet_dynamic_segment_filters',
 'wp_mailpoet_feature_flags',
 'wp_mailpoet_forms',
 'wp_mailpoet_log',
 'wp_mailpoet_migrations',
 'wp_mailpoet_newsletters',
 'wp_mailpoet_newsletter_links',
 'wp_mailpoet_newsletter_option',
 'wp_mailpoet_newsletter_option_fields',
 'wp_mailpoet_newsletter_posts',
 'wp_mailpoet_newsletter_segment',
 'wp_mailpoet_newsletter_templates',
 'wp_mailpoet_scheduled_tasks',
 'wp_mailpoet_segments',
 'wp_mailpoet_sending_queues',
 'wp_mailpoet_settings',
 'wp_mailpoet_statistics_bounces',
 'wp_mailpoet_statistics_clicks',
 'wp_mailpoet_statistics_forms',
 'wp_mailpoet_statistics_newsletters',
 'wp_mailpoet_statistics_opens',
 'wp_mailpoet_statistics_unsubscribes',
 'wp_mailpoet_statistics_woocommerce_purchases',
 'wp_mailpoet_stats_notifications',
 'wp_mailpoet_subscriber_custom_field',
 'wp_mailpoet_subscriber_segment',
 'wp_mailpoet_subscriber_tag',
 'wp_mailpoet_subscribers',
 'wp_mailpoet_tags',
 'wp_mailpoet_user_agents',
 'wp_mailpoet_user_flags',
 
 // Action Scheduler (4)
 'wp_actionscheduler_actions',
 'wp_actionscheduler_claims',
 'wp_actionscheduler_groups',
 'wp_actionscheduler_logs',
 
 // Yoast SEO (5)
 'wp_yoast_indexable',
 'wp_yoast_migrations',
 'wp_yoast_primary_term',
 'wp_yoast_prominent_words',
 'wp_yoast_seo_links',
 
 // YITH Wishlist (3)
 'wp_yith_wcwl',
 'wp_yith_wcwl_itemmeta',
 'wp_yith_wcwl_lists',
 
 // WPForms (4)
 'wp_wpforms_logs',
 'wp_wpforms_payment_meta',
 'wp_wpforms_payments',
 'wp_wpforms_tasks_meta',
 
 // WP Mail SMTP (2)
 'wp_wpmailsmtp_debug_events',
 'wp_wpmailsmtp_tasks_meta',
 
 // LiteSpeed Cache (3) - NUEVO
 'wp_litespeed_avatar',
 'wp_litespeed_url',
 'wp_litespeed_url_file',
 
 // Plugins personalizados (9)
 'wp_fbv',
 'wp_snippets',
 'wp_social_users',
 'wp_solu_currencies_exchange',
 'wp_solu_currencies_exchange_log',
 'wp_solu_generate_html',
 'wp_solu_generate_html_log',
 'wp_product_logs',
 'wp_product_logs_debug'

];

// ============================================================================
// CONEXIÓN A LA BASE DE DATOS
// ============================================================================

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("❌ ERROR: No se pudo conectar a la base de datos.\n   Detalle: {$conn->connect_error}\n");
}

$conn->set_charset('utf8mb4');

// Desactivar el modo estricto de SQL para permitir modificaciones
$conn->query("SET sql_mode = ''");

output_success("✓ Conexión exitosa a la base de datos '" . DB_NAME . "'");
output_info("✓ SQL Mode desactivado para permitir modificaciones");
output_separator_major();

// ============================================================================
// FUNCIONES DE UTILIDAD Y FORMATEO
// ============================================================================

/**
 * Formatea y muestra texto de éxito
 */
function output_success($text) {
    if (IS_CLI) {
        echo $text . LINE_BREAK;
    } else {
        echo '<span class="success">' . htmlspecialchars($text) . '</span>' . LINE_BREAK;
    }
}

/**
 * Formatea y muestra texto de error
 */
function output_error($text) {
    if (IS_CLI) {
        echo $text . LINE_BREAK;
    } else {
        echo '<span class="error">' . htmlspecialchars($text) . '</span>' . LINE_BREAK;
    }
}

/**
 * Formatea y muestra texto de advertencia
 */
function output_warning($text) {
    if (IS_CLI) {
        echo $text . LINE_BREAK;
    } else {
        echo '<span class="warning">' . htmlspecialchars($text) . '</span>' . LINE_BREAK;
    }
}

/**
 * Formatea y muestra texto informativo
 */
function output_info($text) {
    if (IS_CLI) {
        echo $text . LINE_BREAK;
    } else {
        echo '<span class="info">' . htmlspecialchars($text) . '</span>' . LINE_BREAK;
    }
}

/**
 * Formatea y muestra texto resaltado
 */
function output_highlight($text) {
    if (IS_CLI) {
        echo $text . LINE_BREAK;
    } else {
        echo '<span class="highlight">' . htmlspecialchars($text) . '</span>' . LINE_BREAK;
    }
}

/**
 * Formatea y muestra nombre de tabla
 */
function output_table_name($tableName) {
    if (IS_CLI) {
        echo "`$tableName`";
    } else {
        echo '<span class="table-name">' . htmlspecialchars($tableName) . '</span>';
    }
}

/**
 * Muestra un separador menor
 */
function output_separator() {
    if (IS_CLI) {
        echo "────────────────────────────────────────────────────────────────" . LINE_BREAK . LINE_BREAK;
    } else {
        echo '<div class="separator"></div>';
    }
}

/**
 * Muestra un separador mayor
 */
function output_separator_major() {
    if (IS_CLI) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . LINE_BREAK . LINE_BREAK;
    } else {
        echo '<div class="separator-major"></div>';
    }
}

/**
 * Muestra código SQL formateado
 */
function output_sql($sql) {
    if (IS_CLI) {
        echo "    ═══════════════════════════════════════════════════════════════" . LINE_BREAK;
        echo "    SQL: {$sql}" . LINE_BREAK;
        echo "    ═══════════════════════════════════════════════════════════════" . LINE_BREAK;
    } else {
        echo '<div class="sql-box"><code>' . htmlspecialchars($sql) . '</code></div>';
    }
}

/**
 * Ejecuta una consulta SQL y muestra el resultado
 *
 * @param mysqli $conn Conexión a la base de datos
 * @param string $query Consulta SQL a ejecutar
 * @param string $tableName Nombre de la tabla (para mensajes)
 * @return bool Verdadero si la consulta fue exitosa
 */
function ejecutarConsulta($conn, $query, $tableName)
{
    output_sql($query);

    if ($conn->query($query) === TRUE) {
        output_success("    ✓ Tabla `$tableName` actualizada correctamente");
        return true;
    } else {
        output_error("     Error en tabla `$tableName`: {$conn->error}");
        return false;
    }
}

// ============================================================================
// FUNCIÓN DE VERIFICACIÓN Y CORRECCIÓN DE AUTO_INCREMENT
// ============================================================================

/**
 * Verifica si una tabla tiene AUTO_INCREMENT y lo agrega si no lo tiene
 * Solo procesa tablas que estén en la lista TABLAS_AUTO_INCREMENT
 *
 * @param mysqli $conn Conexión a la base de datos
 * @param string $tableName Nombre de la tabla
 * @return void
 */
function verificarYAgregarAutoIncrement($conn, $tableName)
{
    // Verificar si la tabla está en la lista de tablas permitidas
    if (!in_array($tableName, TABLAS_AUTO_INCREMENT)) {
        output_info("   Tabla no configurada para AUTO_INCREMENT (omitida)");
        return;
    }

    $structureQuery = "SHOW COLUMNS FROM `$tableName`";
    $structureResult = $conn->query($structureQuery);

    if (!$structureResult || $structureResult->num_rows === 0) {
        output_warning("  ⚠ No se pudo obtener la estructura de la tabla");
        return;
    }

    $primaryKey = [
        'exists' => false,
        'name' => null,
        'type' => null,
        'hasAutoIncrement' => false,
        'maxValue' => null
    ];

    // Buscar la clave primaria
    while ($column = $structureResult->fetch_assoc()) {
        if (strpos($column['Key'], 'PRI') !== false) {
            $primaryKey['exists'] = true;
            $primaryKey['name'] = $column['Field'];
            $primaryKey['type'] = $column['Type'];
            $primaryKey['hasAutoIncrement'] = strpos($column['Extra'], 'auto_increment') !== false;
            break;
        }
    }

    if (!$primaryKey['exists']) {
        output_warning("  ⚠ La tabla no tiene clave primaria");
        return;
    }

    // Obtener el valor máximo de la clave primaria
    $maxQuery = "SELECT MAX(`{$primaryKey['name']}`) AS max_value FROM `$tableName`";
    $maxResult = $conn->query($maxQuery);
    if ($maxResult && $maxRow = $maxResult->fetch_assoc()) {
        $primaryKey['maxValue'] = $maxRow['max_value'];
    }

    // Mostrar información de la clave primaria
    output_highlight("  🔑 PRIMARY KEY: `{$primaryKey['name']}` ({$primaryKey['type']})");

    if ($primaryKey['hasAutoIncrement']) {
        output_success("  ✓ AUTO_INCREMENT: Sí");
    } else {
        output_warning("  ✗ AUTO_INCREMENT: No");
    }

    if ($primaryKey['maxValue'] !== null) {
        output_info("  📊 Valor máximo actual: {$primaryKey['maxValue']}");
    }

    // Agregar AUTO_INCREMENT si no está configurado
    if (!$primaryKey['hasAutoIncrement']) {
        output_info("  ➜ Agregando AUTO_INCREMENT...");
        agregarAutoIncrement($conn, $tableName, $primaryKey['name'], $primaryKey['type'], $primaryKey['maxValue']);
    }
}

/**
 * Agrega AUTO_INCREMENT a una clave primaria
 *
 * @param mysqli $conn Conexión a la base de datos
 * @param string $tableName Nombre de la tabla
 * @param string $primaryKeyName Nombre de la columna PRIMARY KEY
 * @param string $typeColumn Tipo de dato de la columna
 * @param int|null $maxPrimaryKeyValue Valor máximo actual
 * @return void
 */
function agregarAutoIncrement($conn, $tableName, $primaryKeyName, $typeColumn, $maxPrimaryKeyValue)
{
    // Verificar si el tipo ya incluye UNSIGNED
    if (stripos($typeColumn, 'UNSIGNED') !== false) {
        $alterQuery = "ALTER TABLE `$tableName`
                       CHANGE `$primaryKeyName` `$primaryKeyName` $typeColumn NOT NULL AUTO_INCREMENT";
    } else {
        $alterQuery = "ALTER TABLE `$tableName`
                       CHANGE `$primaryKeyName` `$primaryKeyName` $typeColumn UNSIGNED NOT NULL AUTO_INCREMENT";
    }

    ejecutarConsulta($conn, $alterQuery, $tableName);
}

// ============================================================================
// FUNCIONES ESPECÍFICAS DE TABLAS
// ============================================================================

/**
 * Elimina registros corruptos o basura de las tablas WordPress
 * - Detecta automáticamente la PRIMARY KEY de la tabla
 * - Elimina registros con PRIMARY KEY = 0 (IDs inválidos)
 *
 * @param mysqli $conn Conexión a la base de datos
 * @param string $tableName Nombre de la tabla
 * @return void
 */
function delete_registros_corruptos($conn, $tableName)
{
    // Paso 1: Obtener la estructura de la tabla para encontrar la PRIMARY KEY
    $structureQuery = "SHOW COLUMNS FROM `$tableName`";
    $structureResult = $conn->query($structureQuery);

    if (!$structureResult || $structureResult->num_rows === 0) {
        return;
    }

    $primaryKeyColumn = null;

    // Buscar la columna que tiene PRIMARY KEY
    while ($column = $structureResult->fetch_assoc()) {
        if (strpos($column['Key'], 'PRI') !== false) {
            $primaryKeyColumn = $column['Field'];
            break;
        }
    }

    // Si no hay PRIMARY KEY, salir
    if ($primaryKeyColumn === null) {
        return;
    }

    // Paso 2: Verificar si existen registros con PRIMARY KEY = 0
    $checkQuery = "SELECT COUNT(*) as total FROM `$tableName` WHERE `$primaryKeyColumn` = 0";
    $result = $conn->query($checkQuery);

    if ($result && $row = $result->fetch_assoc()) {
        $totalCorruptos = $row['total'];

        if ($totalCorruptos > 0) {
            output_warning("  ⚠ Encontrados $totalCorruptos registros corruptos (`$primaryKeyColumn` = 0)");
            output_info("  ➜ Eliminando registros basura...");

            $deleteQuery = "DELETE FROM `$tableName` WHERE `$primaryKeyColumn` = 0";
            ejecutarConsulta($conn, $deleteQuery, $tableName);
        }
    }
}

/**
 * Optimiza y corrige la tabla wp_posts
 * - Actualiza fechas inválidas (0000-00-00 00:00:00)
 * - Modifica los campos de fecha para usar DEFAULT CURRENT_TIMESTAMP
 *
 * @param mysqli $conn Conexión a la base de datos
 * @param string $tableName Nombre de la tabla
 * @return void
 */
function tunear_table_wp_posts($conn, $tableName)
{
    output_info("  ➜ Optimizando tabla wp_posts...");

    // Paso 1: Actualizar fechas inválidas
    output_info("  ➜ Paso 1: Actualizando fechas inválidas (0000-00-00)...");

    $updateQuery = "UPDATE `$tableName`
    SET
      post_date = CASE
        WHEN post_date = '0000-00-00 00:00:00' THEN '2025-11-07 17:58:21'
        ELSE post_date
      END,
      post_date_gmt = CASE
        WHEN post_date_gmt = '0000-00-00 00:00:00' THEN '2025-11-07 22:58:21'
        ELSE post_date_gmt
      END,
      post_modified = CASE
        WHEN post_modified = '0000-00-00 00:00:00' THEN '2025-11-07 17:58:22'
        ELSE post_modified
      END,
      post_modified_gmt = CASE
        WHEN post_modified_gmt = '0000-00-00 00:00:00' THEN '2025-11-07 22:58:22'
        ELSE post_modified_gmt
      END";

    ejecutarConsulta($conn, $updateQuery, $tableName);

    // Paso 2: Modificar los campos de fecha
    output_info("  ➜ Paso 2: Modificando campos de fecha con DEFAULT CURRENT_TIMESTAMP...");

    $alterQuery = "ALTER TABLE `$tableName`
      MODIFY post_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      MODIFY post_date_gmt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      MODIFY post_modified datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      MODIFY post_modified_gmt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP";

    ejecutarConsulta($conn, $alterQuery, $tableName);

    output_success("   Tabla wp_posts optimizada correctamente");
}

// ============================================================================
// FUNCIÓN PRINCIPAL DE PROCESAMIENTO
// ============================================================================

/**
 * Procesa todas las tablas WordPress (wp_*) de la base de datos
 *
 * @param mysqli $conn Conexión a la base de datos
 * @return void
 */
function procesarTablasWordPress($conn)
{
    // Obtener todas las tablas que comienzan con el prefijo wp_
    $query = "SHOW TABLES LIKE '" . WP_PREFIX . "%'";
    $result = $conn->query($query);

    if (!$result || $result->num_rows === 0) {
        output_warning("⚠ No se encontraron tablas con prefijo '" . WP_PREFIX . "' en la base de datos");
        return;
    }

    $totalTablas = $result->num_rows;
    $contador = 0;
    $tablasModificadas = 0;

    output_highlight("📊 Total de tablas WordPress encontradas: $totalTablas");
    echo LINE_BREAK;

    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        $contador++;

        if (IS_CLI) {
            echo "[$contador/$totalTablas] 📋 Procesando tabla: `$tableName`" . LINE_BREAK;
        } else {
            echo '<span class="counter">[' . $contador . '/' . $totalTablas . ']</span>';
            echo '<span class="info">📋 Procesando tabla: </span>';
            output_table_name($tableName);
            echo LINE_BREAK;
        }

        // condicionar switch 
        switch ($tableName) {
            case 'wp_posts':
                tunear_table_wp_posts($conn, $tableName);
                break;

        }
        
        // Eliminar datos corruptos
        delete_registros_corruptos($conn, $tableName);

        // Verificar y agregar AUTO_INCREMENT
        verificarYAgregarAutoIncrement($conn, $tableName);

        //fix 

        output_separator();
    }

    output_separator_major();
    output_success("✓ Procesamiento completado: $totalTablas tablas WordPress procesadas");
}

// ============================================================================
// EJECUCIÓN DEL SCRIPT
// ============================================================================

// Iniciar medicin de tiempo
$tiempoInicio = microtime(true);

procesarTablasWordPress($conn);

// Calcular tiempo de ejecución
$tiempoFin = microtime(true);
$tiempoTotal = round($tiempoFin - $tiempoInicio, 2);

// Cerrar conexión
$conn->close();
output_separator_major();
output_success(" Conexión cerrada correctamente");
output_info("⏱️  Tiempo total de ejecución: {$tiempoTotal} segundos");

// Cerrar HTML si se ejecuta desde navegador
if (!IS_CLI) {
    echo '</pre></div>
        <div class="footer">
            <p><strong>Script de Reparación AUTO_INCREMENT WordPress</strong></p>
            <p>Desarrollado por Csar | Versión 1.0 | ' . date('Y-m-d H:i:s') . '</p>
            <p>Tiempo de ejecución: ' . $tiempoTotal . ' segundos</p>
        </div>
    </div>
</body>
</html>';
}
