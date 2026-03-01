<?php

// Configuración de reporte de errores
error_reporting(E_ALL); // Reportar todos los errores
ini_set('display_errors', 1); // Mostrar errores en pantalla (solo desarrollo)
ini_set('log_errors', 1); // Registrar errores en archivo de log
ini_set('error_log', __DIR__ . '/php-errors.log'); // Ruta del archivo de log
$is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';


$saltoLinea = PHP_EOL;
$is_cli = php_sapi_name() === 'cli';
if (!$is_cli) {
  $saltoLinea= "<br>";
}
define("SALTO_LINEA", "<br>");

// =============================================================================
// 📅 SECTION: Variables de fecha, hora y sistema
// =============================================================================
// Función para obtener fecha en diferentes zonas horarias
function getDateInTimezone($timezone = 'America/Lima', $format = 'Y-m-d_H:i:s')
{
  $dateTime = new DateTime();
  $dateTime->setTimezone(new DateTimeZone($timezone));
  return $dateTime->format($format);
}





// =============================================================================
// 📅 SECTION: Variables de fecha, hora y sistema
// =============================================================================

// Timestamp actual
$CURRENT_TIME = time();
// Fecha y hora actual en formato: YYYY-MM-DD_HH:MM:SS (hora local)
$DATE_HOUR = date("Y-m-d_H:i:s", $CURRENT_TIME);
// Fecha y hora actual en Perú usando la función
$DATE_HOUR_PE = getDateInTimezone('America/Lima');
$CURRENT_USER = get_current_user();             // Nombre del usuario actual.
$CURRENT_USER_HOME = getenv('HOME') ?: getenv('USERPROFILE');  // Ruta del perfil del usuario actual.
$CURRENT_PC_NAME = gethostname();        // Nombre del equipo actual.
$MY_INFO = $CURRENT_USER . "@" . $CURRENT_PC_NAME;  // Información combinada del usuario y del equipo.
$PATH_SCRIPT = __FILE__;  // Ruta completa del script actual.
$SCRIPT_NAME = basename($PATH_SCRIPT);           // Nombre del archivo del script.
$CURRENT_DIR = dirname($PATH_SCRIPT);            // Ruta del directorio donde se encuentra el script.
$NAME_DIR = basename($CURRENT_DIR);              // Nombre del directorio actual.
$TEMP_PATH_SCRIPT = str_replace('.php', '.tmp', $PATH_SCRIPT);  // Ruta para un archivo temporal basado en el nombre del script.
$TEMP_PATH_SCRIPT_SYSTEM = sys_get_temp_dir() . '/' . str_replace('.php', '.tmp', $SCRIPT_NAME);  // Ruta para un archivo temporal en /tmp.
$ROOT_PATH = realpath($CURRENT_DIR . '/..');


define('DATE_HOUR_PE',$DATE_HOUR_PE);
define('CURRENT_USER',$CURRENT_USER);
define('CURRENT_USER_HOME',$CURRENT_USER_HOME);
define('CURRENT_PC_NAME',$CURRENT_PC_NAME);
define('MY_INFO',$MY_INFO);
define('PATH_SCRIPT',$PATH_SCRIPT);
define('SCRIPT_NAME',$SCRIPT_NAME);
define('CURRENT_DIR',$CURRENT_DIR);
define('NAME_DIR',$NAME_DIR);
define('TEMP_PATH_SCRIPT',$TEMP_PATH_SCRIPT);
define('TEMP_PATH_SCRIPT_SYSTEM',$TEMP_PATH_SCRIPT_SYSTEM);
define('ROOT_PATH',$ROOT_PATH);

// Colores Regulares
$Color_Off = '\033[0m';       // Reset de color.
$Black = '\033[0;30m';        // Negro.
$Red = '\033[0;31m';          // Rojo.
$Green = '\033[0;32m';        // Verde.
$Yellow = '\033[0;33m';       // Amarillo.
$Blue = '\033[0;34m';         // Azul.
$Purple = '\033[0;35m';       // Púrpura.
$Cyan = '\033[0;36m';         // Cian.
$White = '\033[0;37m';        // Blanco.
$Gray = '\033[0;90m';         // Gris.

// Colores en Negrita
$BBlack = '\033[1;30m';       // Negro (negrita).
$BRed = '\033[1;31m';         // Rojo (negrita).
$BGreen = '\033[1;32m';       // Verde (negrita).
$BYellow = '\033[1;33m';      // Amarillo (negrita).
$BBlue = '\033[1;34m';        // Azul (negrita).
$BPurple = '\033[1;35m';      // Púrpura (negrita).
$BCyan = '\033[1;36m';        // Cian (negrita).
$BWhite = '\033[1;37m';       // Blanco (negrita).
$BGray = '\033[1;90m';        // Gris (negrita).

// =============================================================================
// 🎨 SECTION: Colores para su uso
// =============================================================================
// Definición de colores que se pueden usar en la salida del terminal.


// Función para colorear texto según el entorno
function colorize($text, $color_code)
{
  global $is_cli;
  if ($is_cli) {
    return $color_code . $text . '\033[0m';
  } else {
    // Convertir códigos ANSI a colores HTML
    $html_colors = [
        '\033[0;30m' => '<span style="color: black;">',
        '\033[0;31m' => '<span style="color: red;">',
        '\033[0;32m' => '<span style="color: green;">',
        '\033[0;33m' => '<span style="color: orange;">',
        '\033[0;34m' => '<span style="color: blue;">',
        '\033[0;35m' => '<span style="color: purple;">',
        '\033[0;36m' => '<span style="color: cyan;">',
        '\033[0;37m' => '<span style="color: white;">',
        '\033[0;90m' => '<span style="color: gray;">',
        '\033[1;30m' => '<span style="color: black; font-weight: bold;">',
        '\033[1;31m' => '<span style="color: red; font-weight: bold;">',
        '\033[1;32m' => '<span style="color: green; font-weight: bold;">',
        '\033[1;33m' => '<span style="color: orange; font-weight: bold;">',
        '\033[1;34m' => '<span style="color: blue; font-weight: bold;">',
        '\033[1;35m' => '<span style="color: purple; font-weight: bold;">',
        '\033[1;36m' => '<span style="color: cyan; font-weight: bold;">',
        '\033[1;37m' => '<span style="color: white; font-weight: bold;">',
        '\033[1;90m' => '<span style="color: gray; font-weight: bold;">',
        '\033[0m' => '</span>'
    ];
    return str_replace(array_keys($html_colors), array_values($html_colors), $color_code . $text . '\033[0m');
  }
}


/**
 * Función para forzar la descarga de un archivo.
 *
 * @param string $file_path Ruta completa del archivo a descargar.
 * @param string $file_name Nombre del archivo que se mostrará al usuario.
 */
function download_file($file_path, $file_name) {
  if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    ob_clean(); // Limpia el búfer de salida
    flush(); // Fuerza la salida del búfer
    readfile($file_path);

    // Opcional: Elimina el archivo después de la descarga para mantener el servidor limpio
    // unlink($file_path);
    exit;
  } else {
    echo "Error: El archivo no existe o no se pudo descargar.";
    exit;
  }
}


function wp_cli_backup_db() {
  global $wpdb, $WP_CLI, $backup_dir,$is_windows;
  if($is_windows){
    return [
        'success' => false,
        'message' => 'Solo se peude ejecutar en un Servidor Linux. el sistema es: '.PHP_OS,
        'command_output'=>'',
        'error' =>  ''
    ];
  }

  // AÑADE LA VALIDACIÓN DE SEGURIDAD AQUÍ
//  if (!is_user_logged_in() || !current_user_can('manage_options')) {
//    echo "Acceso denegado. No tienes permisos suficientes.";
//    return false; // Retorna false si no hay permisos
//  }

  if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
  }

  $backup_file_name = DB_NAME . '_' . date('Y-m-d_H-i-s') . '.sql';
  $backup_file_path = $backup_dir . $backup_file_name;

  $command = "{$WP_CLI} db export " . escapeshellarg($backup_file_path);
  $command_output ='';
  $output_response ='';

  $command_output .= "Ejecutando comando: " . $command . "<br>";

  exec($command, $output, $return_code);

  if ($return_code === 0) {
    $output_response .=  "Comando ejecutado correctamente. Backup creado en: " . $backup_file_path . "<br>";

    // Llamar a la función de descarga

    return [
        'success' => true,
        'message' => $output_response,
        'path_file'=> $backup_file_path,
        'path_name'=> $backup_file_name,
        'command_output'=>$command_output,
        'file' => "Comando ejecutado correctamente. Backup creado en: " . $backup_file_path
    ];

  } else {

    $output_response .= "Error al ejecutar el comando. Código de error: " . $return_code . "\n";
    $output_response .= "Salida de error:\n";
    $command_output .='Lineas:\n';
    foreach ($output as $line) {
      $command_output .= $line . "\n";
    }


    return [
        'success' => false,
        'message' => 'Error al generar el backup '.$output_response,
        'path_file'=> '',
        'path_name'=> '',
        'command_output'=>$command_output,
        'error' =>  $command_output
    ];

  }

}



/**
 * Obtiene la URL completa de la página actual con detección básica de HTTPS
 *
 * Esta función construye la URL completa a partir de las variables del servidor,
 * incluyendo protocolo (HTTP/HTTPS), dominio y URI con query string.
 *
 * @version 1.0.0
 * @since 2023-10-20
 * @author TuNombre
 *
 * @return string URL completa en formato "protocolo://host/uri?query"
 *
 * @example
 * // Ejemplo básico de uso
 * $currentUrl = get_current_url();
 * echo "URL actual: " . $currentUrl;
 *
 * // Ejemplo con redirección
 * header("Location: " . get_current_url());
 *
 * @uses $_SERVER['HTTPS'] Para detectar conexiones seguras
 * @uses $_SERVER['HTTP_HOST'] Para obtener el dominio
 * @uses $_SERVER['REQUEST_URI'] Para obtener la ruta y parámetros
 */
function get_current_url() {
  // Determinar si la conexión es HTTPS
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

  // Obtener el nombre del host
  $host = $_SERVER['HTTP_HOST'];

  // Obtener la URI solicitada (incluye path y query string)
  $uri = $_SERVER['REQUEST_URI'];

  // Combinar todo para formar la URL completa
  return $protocol . $host . $uri;
}

/**
 * Obtiene la URL completa con detección avanzada de HTTPS y manejo de proxies
 *
 * Versión mejorada de get_current_url() que incluye:
 * - Detección de HTTPS detrás de balanceadores de carga
 * - Manejo de valores nulos con operador de fusión
 * - Soporte para headers X-Forwarded
 *
 * @version 1.1.0
 * @since 2023-10-20
 * @author TuNombre
 *
 * @return string URL completa en formato "protocolo://host/uri?query"
 *
 * @example
 * // Uso en sistemas AJAX
 * echo "<script>const API_URL = '" . get_full_url() . "';</script>";
 *
 * // Uso en logging
 * file_put_contents('access.log', get_full_url() . PHP_EOL, FILE_APPEND);
 *
 * @uses $_SERVER['HTTPS'] Detección básica de HTTPS
 * @uses $_SERVER['HTTP_X_FORWARDED_PROTO'] Soporte para proxies
 * @uses $_SERVER['HTTP_X_FORWARDED_SSL'] Soporte para proxies SSL
 * @uses $_SERVER['SERVER_PORT'] Detección por puerto
 * @uses $_SERVER['HTTP_HOST'] Primera opción para host
 * @uses $_SERVER['SERVER_NAME'] Fallback para host
 * @uses $_SERVER['REQUEST_URI'] Ruta y parámetros
 */
function get_full_url() {
  $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
      (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ||
      (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') ||
      (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

  $protocol = $ssl ? 'https://' : 'http://';
  $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
  $request_uri = $_SERVER['REQUEST_URI'] ?? '';

  return $protocol . $host . $request_uri;
}
/**
 * Registra un acceso en el archivo de log con información detallada
 *
 * Esta función registra cada acceso con marca de tiempo, IP del cliente,
 * URL solicitada, user agent y método HTTP. Los logs se escriben en formato
 * Apache Combined Log Format con información adicional.
 *
 * @version 2.0.0
 * @since 2023-10-20
 * @author TuNombre
 *
 * @param string $log_file Ruta del archivo de log (opcional)
 * @param bool $include_agent Incluir User-Agent en el log (default: true)
 * @param bool $include_referer Incluir Referer en el log (default: true)
 * @return bool True si se escribió correctamente, False en caso de error
 *
 * @throws RuntimeException Si no se puede escribir en el archivo de log
 *
 * @example
 * // Uso básico
 * log_acceso();
 *
 * // Especificar archivo de log personalizado
 * log_acceso('/var/log/myapp_access.log');
 *
 * // Log sin User-Agent
 * log_acceso('access.log', false);
 *
 * @uses $_SERVER['REMOTE_ADDR'] Dirección IP del cliente
 * @uses $_SERVER['REQUEST_METHOD'] Método HTTP (GET, POST, etc.)
 * @uses $_SERVER['HTTP_USER_AGENT'] User-Agent del cliente (opcional)
 * @uses $_SERVER['HTTP_REFERER'] Página de origen (opcional)
 * @uses get_full_url() Para obtener la URL completa
 */
function log_acceso(string $log_file = 'access.log', bool $include_agent = true, bool $include_referer = true): bool {
  // Validar que exista la IP del cliente
  $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

  // Obtener información adicional
  $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
  $agent = $include_agent ? ($_SERVER['HTTP_USER_AGENT'] ?? '-') : '-';
  $referer = $include_referer ? ($_SERVER['HTTP_REFERER'] ?? '-') : '-';

  // Formato extendido tipo Apache Combined Log Format
  $log = sprintf(
      "[%s] %s %s \"%s %s\" \"%s\" \"%s\"\n",
      date('Y-m-d H:i:s'),
      $ip,
      '-', // user identity (not commonly used)
      $method,
      get_full_url(),
      $referer,
      $agent
  );

  // Intentar escribir el log
  try {
    $result = file_put_contents($log_file, $log, FILE_APPEND | LOCK_EX);

    if ($result === false) {
      throw new RuntimeException("No se pudo escribir en el archivo de log: {$log_file}");
    }

    return true;
  } catch (Exception $e) {
    error_log("Error en log_acceso(): " . $e->getMessage());
    return false;
  }
}


// =============================================================================
// 🔥 FUNCIONES DE REEMPLAZO WORDPRESS
// =============================================================================

/**
 * Reemplazo de esc_html() - Escapa HTML
 */
if (!function_exists('esc_html')) {
  function esc_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
  }
}

/**
 * Reemplazo de esc_attr() - Escapa atributos HTML
 */
if (!function_exists('esc_attr')) {
  function esc_attr($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
  }
}

/**
 * Reemplazo de sanitize_text_field() - Sanitiza campos de texto
 */
if (!function_exists('sanitize_text_field')) {
  function sanitize_text_field($str) {
    $filtered = trim($str);
    $filtered = stripslashes($filtered);
    $filtered = strip_tags($filtered);
    return $filtered;
  }
}

/**
 * Reemplazo de sanitize_textarea_field() - Sanitiza textarea
 */
if (!function_exists('sanitize_textarea_field')) {
  function sanitize_textarea_field($str) {
    $filtered = trim($str);
    $filtered = stripslashes($filtered);
    return $filtered;
  }
}

/**
 * Reemplazo de wp_die() - Muestra un error y detiene la ejecución
 */
if (!function_exists('wp_die')) {
  function wp_die($message, $title = 'Error', $args = array()) {
    if (php_sapi_name() === 'cli') {
      die("ERROR: $message\n");
    } else {
      header('HTTP/1.1 500 Internal Server Error');
      echo "<!DOCTYPE html><html><head><title>$title</title></head><body>";
      echo "<h1>$title</h1><p>$message</p>";
      echo "</body></html>";
      exit;
    }
  }
}

// =============================================================================
// 🔥 CLASE WPDB WRAPPER
// =============================================================================

/**
 * Clase que emula los métodos básicos de wpdb de WordPress usando MySQLi
 */
class Simple_WPDB {
  private $mysqli;
  public $last_error = '';

  public function __construct($host, $user, $pass, $dbname) {
    $this->mysqli = new mysqli($host, $user, $pass, $dbname);

    if ($this->mysqli->connect_error) {
      die("❌ ERROR: No se pudo conectar a la base de datos.\n   Detalle: {$this->mysqli->connect_error}\n");
    }

    $this->mysqli->set_charset('utf8mb4');
  }

  /**
   * Ejecuta una consulta SQL
   */
  public function query($query) {
    $result = $this->mysqli->query($query);
    if ($result === false) {
      $this->last_error = $this->mysqli->error;
      return false;
    }
    return $this->mysqli->affected_rows;
  }

  /**
   * Obtiene una columna de resultados
   */
  public function get_col($query, $column_offset = 0) {
    $result = $this->mysqli->query($query);
    if (!$result) {
      $this->last_error = $this->mysqli->error;
      return [];
    }

    $output = [];
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
      $output[] = $row[$column_offset];
    }
    return $output;
  }

  /**
   * Obtiene una fila de resultados
   */
  public function get_row($query, $output_type = MYSQLI_ASSOC) {
    $result = $this->mysqli->query($query);
    if (!$result) {
      $this->last_error = $this->mysqli->error;
      return null;
    }

    if ($output_type === 'ARRAY_N') {
      return $result->fetch_array(MYSQLI_NUM);
    }
    return $result->fetch_array($output_type);
  }

  /**
   * Obtiene todos los resultados
   */
  public function get_results($query, $output_type = MYSQLI_ASSOC) {
    $result = $this->mysqli->query($query);
    if (!$result) {
      $this->last_error = $this->mysqli->error;
      return [];
    }

    $output = [];
    if ($output_type === 'ARRAY_A') {
      while ($row = $result->fetch_assoc()) {
        $output[] = $row;
      }
    } else {
      while ($row = $result->fetch_array($output_type)) {
        $output[] = $row;
      }
    }
    return $output;
  }

  /**
   * Obtiene un único valor
   */
  public function get_var($query, $column_offset = 0, $row_offset = 0) {
    $result = $this->mysqli->query($query);
    if (!$result) {
      $this->last_error = $this->mysqli->error;
      return null;
    }

    // Mover al row específico
    if ($row_offset > 0) {
      $result->data_seek($row_offset);
    }

    $row = $result->fetch_array(MYSQLI_NUM);
    return $row ? $row[$column_offset] : null;
  }

  /**
   * Escapa un string para uso en SQL
   */
  public function escape($data) {
    return $this->mysqli->real_escape_string($data);
  }

  /**
   * Prepara una consulta SQL (versión simplificada)
   */
  public function prepare($query, ...$args) {
    // Reemplazar placeholders %s, %d, %f con los argumentos
    $query = str_replace("'%s'", '%s', $query); // Remover comillas extras
    $query = str_replace('"%s"', '%s', $query);

    foreach ($args as $arg) {
      $pos = strpos($query, '%s');
      if ($pos !== false) {
        $escaped = "'" . $this->mysqli->real_escape_string($arg) . "'";
        $query = substr_replace($query, $escaped, $pos, 2);
      }
    }

    return $query;
  }

  /**
   * Cierra la conexión
   */
  public function close() {
    $this->mysqli->close();
  }
}

// =============================================================================
// 🔥 CODE
// =============================================================================

// ============================================================================
// CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================================================

const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'test01';

// Prefijo de tablas de WordPress
const WP_PREFIX = 'wp_';

$WP_CLI = "php wp-cli.phar";
$backup_dir = $CURRENT_DIR."/backups/";

// ============================================================================
// CONEXIÓN A LA BASE DE DATOS
// ============================================================================

$wpdb = new Simple_WPDB(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Desactivar el modo estricto de SQL para permitir modificaciones
$wpdb->query("SET sql_mode = ''");

/**
 * Solu Util Clean - WordPress Database Cleanup Utility
 * Ejecuta consultas SQL de limpieza de forma asíncrona con AJAX
 */

// Actualiza el bloque de procesamiento AJAX
if (isset($_POST['action'])) {
  if ($_POST['action'] === 'solu_execute_query') {
    solu_execute_query_callback();
    exit;
  } elseif ($_POST['action'] === 'solu_execute_all_queries') {
    solu_execute_all_queries_callback();
    exit;
  } elseif ($_POST['action'] === 'generate_db_backup') {
    generate_db_backup();
    exit;
  }elseif ($_POST['action'] === 'download_file') {
    $path_file=$_POST['path_file'];
    $path_name=$_POST['path_name'];
    download_file($path_file, $path_name);
    exit;
  }elseif ($_POST['action'] === 'generate_db_backup_cli') {
    $result = wp_cli_backup_db();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
  }elseif ($_POST['action'] === 'add_autoincrement_all_tables') {
    add_autoincrement_all_tables_callback();
    exit;
  }
}






/**
 * Genera backup de la base de datos usando php-cli.phar
 *
 * @param string $output_dir Directorio donde guardar el backup
 * @return array Resultado de la operación
 */
function generate_db_backup_cli($output_dir = null) {
  global $wpdb,$CURRENT_DIR;

  // Configurar directorio de salida
//  $output_dir = $output_dir ?: sys_get_temp_dir();
  $output_dir = $CURRENT_DIR;
  $backup_file = $output_dir . '/wordpress-backup-' . date('Y-m-d-His') . '.sql';

  // Comando para generar el backup
  $command = sprintf(
      'php-cli.phar -r \'$db = new mysqli("%s", "%s", "%s", "%s"); $dump = new mysqli_dump($db); file_put_contents("%s", $dump);\'',
      DB_HOST,
      DB_USER,
      DB_PASSWORD,
      DB_NAME,
      $backup_file
  );

  // Ejecutar el comando
  exec($command, $output, $return_var);

  if ($return_var !== 0) {
    return [
        'success' => false,
        'message' => 'Error al generar el backup',
        'error' => implode("\n", $output)
    ];
  }

  return [
      'success' => true,
      'message' => 'Backup generado correctamente',
      'file' => $backup_file
  ];
}


/**
 * Descarga un archivo de backup generado
 *
 * @param string $file_path Ruta completa al archivo .sql
 */
function download_backup_file($file_path) {
  if ($file_path !== null && !file_exists($file_path)) {
    wp_die('El archivo de backup no existe');
  }

  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
  header('Content-Length: ' . filesize($file_path));
  header('Pragma: no-cache');
  header('Expires: 0');

  readfile($file_path);
  unlink($file_path); // Opcional: eliminar el archivo después de descargar
  exit;
}

/**
 * Genera y descarga un backup completo de la base de datos WordPress
 *
 * @version 1.0.0
 * @since 2023-10-20
 *
 * @return void Descarga un archivo .sql con el backup
 *
 * @throws Exception Si hay errores al generar el backup
 *
 * @example
 * // Uso directo
 * generate_db_backup();
 *
 * // Uso via AJAX
 * add_action('wp_ajax_generate_db_backup', 'generate_db_backup');
 */
function generate_db_backup() {


  global $wpdb;
  file_put_contents('debug.log', "Función generate_db_backup llamada\n", FILE_APPEND);

  // Debug: Verificar headers
  ob_start();
  print_r(headers_list());
  file_put_contents('debug.log', "Headers: " . ob_get_clean() . "\n", FILE_APPEND);
  // Verificar si es una petición de descarga
  if (empty($_GET['action']) || $_GET['action'] !== 'generate_db_backup') {
    return;
  }
  file_put_contents('debug.log', "Función generate_db_backup llamada 2 \n", FILE_APPEND);

  $backup_file_name = 'wordpress-backup-' . date('Y-m-d-His') . '.sql';


  $tables = $wpdb->get_col("SHOW TABLES");

  // Crear contenido del backup
  $output = "-- WordPress Database Backup\n";
  $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
  $output .= "-- Host: " . DB_HOST . "\n";
  $output .= "-- Database: " . DB_NAME . "\n\n";

  // Recorrer todas las tablas
  foreach ($tables as $table) {
    $output .= "--\n-- Table structure for table `$table`\n--\n";
    $output .= "DROP TABLE IF EXISTS `$table`;\n";

    // Obtener estructura de la tabla
    $create_table = $wpdb->get_row("SHOW CREATE TABLE `$table`", ARRAY_N);
    $output .= $create_table[1] . ";\n\n";

    // Obtener datos de la tabla
    $rows = $wpdb->get_results("SELECT * FROM `$table`", ARRAY_A);
    if (count($rows) > 0) {
      $output .= "--\n-- Dumping data for table `$table`\n--\n";

      foreach ($rows as $row) {
        $fields = array();
        foreach ($row as $key => $value) {
          $fields[] = "`$key`=" . (is_null($value) ? 'NULL' : "'" . $wpdb->escape($value) . "'");
        }
        $output .= "INSERT INTO `$table` SET " . implode(', ', $fields) . ";\n";
      }
      $output .= "\n";
    }
  }

  // Forzar descarga del archivo
  // Headers para forzar descarga
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="' . $backup_file_name . '"');
  header('Content-Length: ' . strlen($output));
  header('Pragma: no-cache');
  header('Expires: 0');

  echo $output;
  exit;
}

function solu_execute_query_callback()
{

  // check_ajax_referer('solu_clean_nonce', 'nonce'); // Eliminar
  $query_id = isset($_POST['query_id']) ? sanitize_text_field($_POST['query_id']) : '';
  $query = isset($_POST['query']) ? sanitize_textarea_field($_POST['query']) : '';
  $query = stripslashes($query);
  global $wpdb;
  $result = array();

  try {
    if (stripos($query, 'DELETE FROM') !== false || stripos($query, 'DELETE a,b,c') !== false) {
      $table_name = solu_extract_table_name($query);


      if ($table_name && !solu_table_exists($table_name)) {
        $result = array(
          'success' => false,
          'message' => "La tabla {$table_name} no existe",
          'query_id' => $query_id
        );
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
      }
    }

//    echo "Processing query ID: {$query_id}".SALTO_LINEA; // Para depuración
//    $query2 = stripslashes($query);
//    echo "table_name: {$query}".SALTO_LINEA; // Para depuración
//    echo "table_name: {$query2}".SALTO_LINEA; // Para depuración
//    echo "table_name: {$_POST['query']}".SALTO_LINEA; // Para depuración
//    $rows_affected = $wpdb->query($query);
//    echo "result: {$rows_affected}".SALTO_LINEA; // Para depuración
//    exit;

    $rows_affected = $wpdb->query($query);
    if ($rows_affected === false) {
      $result = array(
        'success' => false,
        'message' => 'Error en la consulta: ' . $wpdb->last_error,
        'query_id' => $query_id
      );
    } else {
      $result = array(
        'success' => true,
        'message' => "Consulta ejecutada exitosamente. Filas afectadas: {$rows_affected}",
        'rows_affected' => $rows_affected,
        'query_id' => $query_id
      );
    }
  } catch (Exception $e) {
    $result = array(
      'success' => false,
      'message' => 'Excepción: ' . $e->getMessage(),
      'query_id' => $query_id
    );
  }
  header('Content-Type: application/json');
  echo json_encode($result);
  exit;
}

function solu_execute_all_queries_callback()
{
  // check_ajax_referer('solu_clean_nonce', 'nonce'); // Eliminar
  $queries = solu_get_cleanup_queries();
  $results = array();
  foreach ($queries as $query_id => $query_data) {
    $result = solu_execute_single_query($query_id, $query_data['query']);
    $results[] = $result;
    // Enviar progreso en tiempo real (opcional)
    // echo "data: " . json_encode($result) . "\n\n";
    // ob_flush(); flush();
    usleep(100000); // 0.1 segundos
  }
  header('Content-Type: application/json');
  echo json_encode($results);
  exit;
}

function solu_execute_single_query($query_id, $query)
{
  global $wpdb;

  try {
    // Verificar si es un DELETE y si la tabla existe
    if (stripos($query, 'DELETE FROM') !== false || stripos($query, 'DELETE a,b,c') !== false) {
      $table_name = solu_extract_table_name($query);
      if ($table_name && !solu_table_exists($table_name)) {
        return array(
          'success' => false,
          'message' => "La tabla {$table_name} no existe",
          'query_id' => $query_id
        );
      }
    }

    // Ejecutar la consulta
    $rows_affected = $wpdb->query($query);

    if ($rows_affected === false) {
      return array(
        'success' => false,
        'message' => 'Error en la consulta: ' . $wpdb->last_error,
        'query_id' => $query_id
      );
    } else {
      return array(
        'success' => true,
        'message' => "Consulta ejecutada exitosamente. Filas afectadas: {$rows_affected}",
        'rows_affected' => $rows_affected,
        'query_id' => $query_id
      );
    }
  } catch (Exception $e) {
    return array(
      'success' => false,
      'message' => 'Excepción: ' . $e->getMessage(),
      'query_id' => $query_id
    );
  }
}

function solu_extract_table_name($query)
{
  // Extraer nombre de tabla de consultas DELETE
  if (preg_match('/DELETE\s+(?:a,b,c\s+)?FROM\s+`?(\w+)`?/i', $query, $matches)) {
    return $matches[1];
  }
  return null;
}

function solu_table_exists($table_name)
{
  global $wpdb;

  // Asegurar que el nombre de la tabla tenga el prefijo correcto
  if (strpos($table_name, 'wp_') !== 0) {
    $table_name = 'wp_' . $table_name;
  }

  $result = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT COUNT(*) FROM information_schema.tables 
             WHERE table_schema = %s AND table_name = %s",
      DB_NAME,
      $table_name
    )
  );

  return $result > 0;
}

/**
 * Callback para agregar AUTO_INCREMENT a todas las tablas WordPress
 */
function add_autoincrement_all_tables_callback()
{
  global $wpdb;

  // Lista de tablas que deben tener AUTO_INCREMENT
  $tablas_autoincrement = [
    // WordPress Core
    'wp_posts', 'wp_postmeta', 'wp_comments', 'wp_commentmeta',
    'wp_users', 'wp_usermeta', 'wp_terms', 'wp_term_taxonomy',
    'wp_termmeta', 'wp_options', 'wp_links',
    // WooCommerce
    'wp_wc_admin_notes', 'wp_wc_admin_note_actions', 'wp_wc_customer_lookup',
    'wp_wc_download_log', 'wp_wc_order_addresses', 'wp_wc_order_operational_data',
    'wp_wc_order_product_lookup', 'wp_wc_order_stats', 'wp_wc_orders',
    'wp_wc_orders_meta', 'wp_wc_product_download_directories', 'wp_wc_product_meta_lookup',
    'wp_wc_rate_limits', 'wp_wc_tax_rate_classes', 'wp_wc_webhooks',
    'wp_woocommerce_api_keys', 'wp_woocommerce_attribute_taxonomies',
    'wp_woocommerce_downloadable_product_permissions', 'wp_woocommerce_log',
    'wp_woocommerce_order_itemmeta', 'wp_woocommerce_order_items',
    'wp_woocommerce_payment_tokenmeta', 'wp_woocommerce_payment_tokens',
    'wp_woocommerce_sessions', 'wp_woocommerce_shipping_zone_locations',
    'wp_woocommerce_shipping_zone_methods', 'wp_woocommerce_shipping_zones',
    'wp_woocommerce_tax_rate_locations', 'wp_woocommerce_tax_rates',
    // Action Scheduler
    'wp_actionscheduler_actions', 'wp_actionscheduler_claims',
    'wp_actionscheduler_groups', 'wp_actionscheduler_logs'
  ];

  $results = [];
  $total_procesadas = 0;
  $total_modificadas = 0;

  foreach ($tablas_autoincrement as $tableName) {
    // Verificar si la tabla existe
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$tableName'");
    if (!$table_exists) {
      continue;
    }

    $total_procesadas++;

    // Obtener estructura de la tabla
    $columns = $wpdb->get_results("SHOW COLUMNS FROM `$tableName`");
    if (!$columns) {
      continue;
    }

    $primaryKey = null;

    // Buscar la clave primaria
    foreach ($columns as $column) {
      if (strpos($column->Key, 'PRI') !== false) {
        $primaryKey = [
          'name' => $column->Field,
          'type' => $column->Type,
          'hasAutoIncrement' => strpos($column->Extra, 'auto_increment') !== false
        ];
        break;
      }
    }

    if (!$primaryKey) {
      continue;
    }

    // Si ya tiene AUTO_INCREMENT, continuar
    if ($primaryKey['hasAutoIncrement']) {
      continue;
    }

    // Agregar AUTO_INCREMENT
    $type = $primaryKey['type'];
    $name = $primaryKey['name'];

    // Verificar si el tipo ya incluye UNSIGNED
    if (stripos($type, 'UNSIGNED') !== false) {
      $alterQuery = "ALTER TABLE `$tableName` CHANGE `$name` `$name` $type NOT NULL AUTO_INCREMENT";
    } else {
      $alterQuery = "ALTER TABLE `$tableName` CHANGE `$name` `$name` $type UNSIGNED NOT NULL AUTO_INCREMENT";
    }

    $result = $wpdb->query($alterQuery);
    if ($result !== false) {
      $total_modificadas++;
      $results[] = "✓ Tabla `$tableName` actualizada con AUTO_INCREMENT";
    } else {
      $results[] = "✗ Error en tabla `$tableName`: {$wpdb->last_error}";
    }
  }

  header('Content-Type: application/json');
  echo json_encode([
    'success' => true,
    'message' => "Proceso completado. $total_modificadas de $total_procesadas tablas fueron modificadas.",
    'details' => implode("\n", $results),
    'total_procesadas' => $total_procesadas,
    'total_modificadas' => $total_modificadas
  ]);
}

function solu_get_cleanup_queries()
{
  return array(
    'spam_comments' => array(
      'title' => 'Eliminar comentarios SPAM',
      'description' => 'Elimina todos los comentarios marcados como SPAM',
      'query' => "DELETE FROM wp_comments WHERE wp_comments.comment_approved = 'spam'"
    ),
    'unapproved_comments' => array(
      'title' => 'Eliminar comentarios no aprobados',
      'description' => 'Elimina comentarios pendientes de aprobación',
      'query' => "DELETE FROM wp_comments WHERE comment_approved = '0'"
    ),
    'revisions' => array(
      'title' => 'Eliminar revisiones',
      'description' => 'Elimina todas las revisiones del editor de WordPress',
      'query' => "DELETE a,b,c FROM wp_posts a LEFT JOIN wp_term_relationships b ON ( a.ID = b.object_id) LEFT JOIN wp_postmeta c ON ( a.ID = c.post_id ) LEFT JOIN wp_term_taxonomy d ON ( b.term_taxonomy_id = d.term_taxonomy_id) WHERE a.post_type = 'revision' AND d.taxonomy != 'link_category'"
    ),
    'orphan_terms' => array(
      'title' => 'Eliminar términos huérfanos',
      'description' => 'Elimina tags que no tienen posts asociados',
      'query' => "DELETE FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE count = 0 )"
    ),
    'orphan_taxonomy' => array(
      'title' => 'Eliminar taxonomías huérfanas',
      'description' => 'Elimina taxonomías sin términos',
      'query' => "DELETE FROM wp_term_taxonomy WHERE term_id not IN (SELECT term_id FROM wp_terms)"
    ),
    'orphan_relationships' => array(
      'title' => 'Eliminar relaciones huérfanas',
      'description' => 'Elimina relaciones de términos sin taxonomías',
      'query' => "DELETE FROM wp_term_relationships WHERE term_taxonomy_id not IN (SELECT term_taxonomy_id FROM wp_term_taxonomy)"
    ),
    'pingbacks' => array(
      'title' => 'Eliminar pingbacks',
      'description' => 'Elimina todos los pingbacks',
      'query' => "DELETE FROM wp_comments WHERE comment_type = 'pingback'"
    ),
    'trackbacks' => array(
      'title' => 'Eliminar trackbacks',
      'description' => 'Elimina todos los trackbacks',
      'query' => "DELETE FROM wp_comments WHERE comment_type = 'trackback'"
    ),
    'transients' => array(
      'title' => 'Eliminar transients',
      'description' => 'Elimina todos los transients de wp_options',
      'query' => "DELETE FROM wp_options WHERE option_name LIKE ('%\\_transient\\_%')"
    ),
    'aiowps_failed_logins' => array(
      'title' => 'Limpiar logs de login fallidos',
      'description' => 'Elimina logs de intentos de login fallidos',
      'query' => "DELETE FROM wp_aiowps_failed_logins"
    ),
    'actionscheduler_logs' => array(
      'title' => 'Limpiar logs de acciones',
      'description' => 'Elimina logs de acciones programadas',
      'query' => "DELETE FROM wp_actionscheduler_logs"
    ),
    'aiowps_events' => array(
      'title' => 'Limpiar eventos de seguridad',
      'description' => 'Elimina eventos de seguridad',
      'query' => "DELETE FROM wp_aiowps_events"
    ),
    'aiowps_global_meta' => array(
      'title' => 'Limpiar meta global',
      'description' => 'Elimina información de archivos cambiados',
      'query' => "DELETE FROM wp_aiowps_global_meta"
    ),
    'aiowps_login_activity' => array(
      'title' => 'Limpiar actividad de login',
      'description' => 'Elimina actividad de usuarios',
      'query' => "DELETE FROM wp_aiowps_login_activity"
    ),
    'aiowps_login_lockdown' => array(
      'title' => 'Limpiar bloqueos de login',
      'description' => 'Elimina bloqueos de usuarios',
      'query' => "DELETE FROM wp_aiowps_login_lockdown"
    ),
    'completed_actions' => array(
      'title' => 'Limpiar acciones completadas',
      'description' => 'Elimina acciones completadas o fallidas',
      'query' => "DELETE FROM wp_actionscheduler_actions WHERE status IN ('complete', 'failed')"
    ),
    'old_actions' => array(
      'title' => 'Limpiar acciones antiguas',
      'description' => 'Elimina acciones completadas hace más de 30 días',
      'query' => "DELETE FROM wp_actionscheduler_actions WHERE status = 'complete' AND scheduled_date_gmt < NOW() - INTERVAL 30 DAY"
    ),
    'orphan_term_relationships' => array(
      'title' => 'Limpiar relaciones de términos huérfanas',
      'description' => 'Elimina relaciones sin posts correspondientes',
      'query' => "DELETE FROM wp_term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM wp_posts)"
    ),
    'orphan_usermeta' => array(
      'title' => 'Limpiar metadatos de usuarios huérfanos',
      'description' => 'Elimina metadatos de usuarios que no existen',
      'query' => "DELETE FROM wp_usermeta WHERE user_id NOT IN (SELECT ID FROM wp_users)"
    ),
    'orphan_termmeta' => array(
      'title' => 'Limpiar metadatos de términos huérfanos',
      'description' => 'Elimina metadatos de términos que no existen',
      'query' => "DELETE FROM wp_termmeta WHERE term_id NOT IN (SELECT term_id FROM wp_terms)"
    ),
    'auto_drafts' => array(
      'title' => 'Eliminar borradores automáticos',
      'description' => 'Elimina posts con estado auto-draft',
      'query' => "DELETE FROM wp_posts WHERE post_status = 'auto-draft'"
    ),
    'trash_posts' => array(
      'title' => 'Eliminar posts en papelera',
      'description' => 'Elimina posts con estado trash',
      'query' => "DELETE FROM wp_posts WHERE post_status = 'trash'"
    ),
    'trash_comments' => array(
      'title' => 'Eliminar comentarios en papelera',
      'description' => 'Elimina comentarios con estado trash',
      'query' => "DELETE FROM wp_comments WHERE comment_approved = 'trash'"
    ),
    'orphan_postmeta' => array(
      'title' => 'Limpiar metadatos de posts huérfanos',
      'description' => 'Elimina metadatos de posts que no existen',
      'query' => "DELETE pm FROM wp_postmeta pm LEFT JOIN wp_posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL"
    ),
    'orphan_commentmeta' => array(
      'title' => 'Limpiar metadatos de comentarios huérfanos',
      'description' => 'Elimina metadatos de comentarios que no existen',
      'query' => "DELETE FROM wp_commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM wp_comments)"
    ),
    'expired_transients' => array(
      'title' => 'Limpiar transients expirados',
      'description' => 'Elimina solo transients que han expirado',
      'query' => "DELETE a, b FROM wp_options a, wp_options b WHERE a.option_name LIKE '%_transient_%' AND a.option_name NOT LIKE '%_transient_timeout_%' AND b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, CHAR_LENGTH('_transient_') + 1)) AND b.option_value < UNIX_TIMESTAMP()"
    ),
    'wp_sessions' => array(
      'title' => 'Limpiar sesiones de WordPress',
      'description' => 'Elimina sesiones de WordPress',
      'query' => "DELETE FROM wp_options WHERE option_name LIKE '_wp_session_%'"
    ),
    'woocommerce_sessions' => array(
      'title' => 'Limpiar sesiones de WooCommerce',
      'description' => 'Elimina sesiones de WooCommerce',
      'query' => "DELETE FROM wp_woocommerce_sessions"
    ),
    'woocommerce_logs' => array(
      'title' => 'Limpiar logs de WooCommerce',
      'description' => 'Elimina logs de WooCommerce',
      'query' => "DELETE FROM wp_woocommerce_log"
    ),
    'woocommerce_webhooks' => array(
      'title' => 'Limpiar webhooks de WooCommerce',
      'description' => 'Elimina webhooks de WooCommerce',
      'query' => "DELETE FROM wp_woocommerce_webhooks"
    ),
    'woocommerce_payment_tokens' => array(
      'title' => 'Limpiar tokens de pago expirados',
      'description' => 'Elimina tokens de pago expirados de WooCommerce',
      'query' => "DELETE FROM wp_woocommerce_payment_tokens"
    )
  );
}

function solu_clean_admin_page()
{
  $queries = solu_get_cleanup_queries();
  ?>
    <div class="wrap dark-theme" style="
    padding: 32px;
">
        <!-- Header con título y descripción -->
        <div class="solu-header mb-4">
            <h1 class="text-primary mb-2">Solu Util Clean</h1>
            <p>Herramienta para limpiar la base de datos de WordPress de forma asíncrona.</p>
        </div>

        <div class="container-fluid">
            <!-- Sección de ejecución masiva -->
            <div class="card bg-dark-2 border-0 mb-4 shadow-lg">
                <div class="card-body">
                    <h2 class="h4 text-white mb-3">Ejecutar Todas las Consultas</h2>
                    <button id="execute-all" class="btn btn-primary btn-lg">
                        <i class="fas fa-play-circle me-2"></i>Ejecutar Todas las Consultas
                    </button>

                    <!-- Barra de progreso -->
                    <div id="all-progress" class="progress mt-3" style="display: none; height: 10px; background-color: #2a2a2a;">
                        <div id="progress-bar" class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                             role="progressbar" style="width: 0%"></div>
                    </div>

                    <!-- Resultados -->
                    <div id="all-results" class="solu-results mt-3"></div>
                </div>
            </div>




            <div class="card bg-dark-3 border-dark mb-4 0">
                <div class="card-body">
                    <h3 class="h5 text-white">
                        <i class="fas fa-database me-2"></i>Backup Rápido (CLI)
                    </h3>
                    <p class="text-muted small mb-3">
                        Genera un backup optimizado usando php-cli.phar
                    </p>

                    <button id="generate-backup-cli" class="btn btn-success w-100">
                        <i class="fas fa-bolt me-2"></i>Generar Backup Rápido
                    </button>

                    <div id="backup-cli-status" class="mt-2 small solu-text"></div>
                    <div id="backup-cli-status-form" class="mt-2 small solu-text" style="display: none">
                        <form id="backup-cli-form" method="post">
                            <input type="hidden" name="action" value="download_file">
                            <input type="hidden" name="path_file" value="">
                            <input type="hidden" name="path_name" value="">
                            <button type="submit" class="btn btn-primary w-50">
                                <i class="fas fa-download me-2"></i>Descargar Backup
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sección AUTO_INCREMENT -->
            <div class="card bg-dark-3 border-dark mb-4 0">
                <div class="card-body">
                    <h3 class="h5 text-white">
                        <i class="fas fa-key me-2"></i>Agregar AUTO_INCREMENT a Tablas
                    </h3>
                    <p class="text-muted small mb-3">
                        Agrega AUTO_INCREMENT a las claves primarias de las tablas de WordPress que lo necesiten
                    </p>

                    <button id="add-autoincrement-tables" class="btn btn-warning w-100">
                        <i class="fas fa-magic me-2"></i>Agregar AUTO_INCREMENT
                    </button>

                    <div id="autoincrement-status" class="mt-2 small solu-text"></div>
                    <div id="autoincrement-details" class="mt-2 small solu-text" style="display: none;">
                        <div class="sql-query-box">
                            <div class="sql-label">DETALLES:</div>
                            <pre id="autoincrement-details-content" style="color: #a9b7c6; font-size: 11px; margin: 0;"></pre>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Sección de consultas individuales -->
            <div class="card bg-dark-2 border-0 shadow-lg">
                <div class="card-body">
                    <h2 class="h4 text-white mb-3">Consultas Individuales</h2>

                    <div class="row row-cols-1 row-cols-md-2 g-4">
                      <?php foreach ($queries as $query_id => $query_data): ?>
                          <div class="col">
                              <div class="card bg-dark-3 border-dark h-100">
                                  <div class="card-body">
                                      <h3 class="h5 text-white"><?php echo esc_html($query_data['title']); ?></h3>
                                      <p class="text-muted small"><?php echo esc_html($query_data['description']); ?></p>

                                      <!-- SQL Query Display -->
                                      <div class="sql-query-box mb-3" id="sql-<?php echo esc_attr($query_id); ?>" style="display: none;">
                                          <div class="sql-label">SQL:</div>
                                          <code class="sql-code"><?php echo esc_html($query_data['query']); ?></code>
                                      </div>

                                      <button class="btn btn-outline-primary execute-single"
                                              data-query-id="<?php echo esc_attr($query_id); ?>"
                                              data-query="<?php echo esc_attr($query_data['query']); ?>">
                                          <i class="fas fa-play me-1"></i> Ejecutar
                                      </button>
                                      <div class="solu-result result-message mt-2 small" id="result-<?php echo esc_attr($query_id); ?>"></div>
                                  </div>
                              </div>
                          </div>
                      <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estilos personalizados para el tema oscuro -->
    <style>
        :root {
            --dark-1: #121212;
            --dark-2: #1e1e1e;
            --dark-3: #252525;
            --dark-4: #2d2d2d;
            --primary: #4e73df;
        }

        body {
            background-color: var(--dark-1);
            color: #e0e0e0;
        }

        .dark-theme {
            background-color: var(--dark-1);
        }

        .bg-dark-2 {
            background-color: var(--dark-2) !important;
        }

        .bg-dark-3 {
            background-color: var(--dark-3) !important;
        }

        .bg-dark-4 {
            background-color: var(--dark-4) !important;
        }

        .card {
            border-radius: 0.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3) !important;
        }

        .text-primary {
            color: var(--primary) !important;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
        }

        .result-message {
            min-height: 20px;
            font-size: 0.85rem;
            border-radius: 4px;
            padding: 4px 8px;
        }

        #all-results {
            max-height: 400px;
            overflow-y: auto;
            background-color: var(--dark-3);
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .list-group-item {
            background-color: var(--dark-3);
            color: #e0e0e0;
            border-color: var(--dark-4);
            margin-bottom: 0.5rem;
            border-radius: 0.25rem !important;
        }

        .list-group-item.success {
            border-left: 3px solid #28a745;
        }

        .list-group-item.error {
            border-left: 3px solid #dc3545;
        }

        /* Scrollbar personalizada */
        #all-results::-webkit-scrollbar {
            width: 8px;
        }

        #all-results::-webkit-scrollbar-track {
            background: var(--dark-2);
        }

        #all-results::-webkit-scrollbar-thumb {
            background: var(--dark-4);
            border-radius: 4px;
        }

        #all-results::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .solu-text{
            color: #e0e0e0 !important;
        }

        .solu-clean-container {
            max-width: 1200px;
            margin: 20px 0;
        }

        .solu-section {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
        }

        .solu-queries-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .solu-query-card {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .solu-query-card h3 {
            margin-top: 0;
            color: #23282d;
        }

        .solu-query-card p {
            color: #666;
            margin-bottom: 15px;
        }

        .solu-result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }

        .solu-result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .solu-result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .solu-progress {
            margin: 20px 0;
        }

        .solu-progress-bar {
            width: 100%;
            height: 20px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .solu-progress-fill {
            height: 100%;
            background: #0073aa;
            width: 0%;
            transition: width 0.3s ease;
        }

        .solu-progress-text {
            text-align: center;
            margin-top: 5px;
            font-weight: bold;
        }

        .solu-results {
            margin-top: 20px;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            background: #f9f9f9;
        }

        .solu-result-item {
            padding: 8px;
            margin: 5px 0;
            border-radius: 4px;
            border-left: 4px solid;
        }

        .solu-result-item.success {
            background: #d4edda;
            border-left-color: #28a745;
        }

        .solu-result-item.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        h1, h2, h3, h4, h5, h6 ,p {
            color: #e0e0e0;
        }
        /* Estilo para el botón de backup */
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        /* Estilo para el botón de warning */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #000;
        }


        /* Estilos para la sección de backup */
        #backup-status {
            min-height: 24px;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        /* Efecto hover para las tarjetas */
        .card-hover-effect:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        /* Texto en botones deshabilitados */
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Estilos para la caja SQL */
        .sql-query-box {
            background-color: #1a1a1a;
            border-left: 3px solid #4e73df;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
            overflow-x: auto;
        }

        .sql-label {
            color: #4e73df;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sql-code {
            display: block;
            color: #a9b7c6;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>

    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery y Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // var ajaxurl = 'solu-util-clean.php';
        var ajaxurl = '<?php echo get_full_url(); ?>';
        console.log("UrlBAse: " + ajaxurl);

    </script>

    <script>
        jQuery(document).ready(function($) {
            // Manejar AUTO_INCREMENT de tablas
            $('#add-autoincrement-tables').on('click', function() {
                const button = $(this);
                const statusDiv = $('#autoincrement-status');
                const detailsDiv = $('#autoincrement-details');
                const detailsContent = $('#autoincrement-details-content');

                button.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1"></span> Procesando...');

                statusDiv.removeClass('alert-danger alert-success')
                    .text('')
                    .hide();
                detailsDiv.hide();

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'add_autoincrement_all_tables'
                    },
                    success: function(response) {
                        statusDiv.removeClass('success error')
                            .addClass(response.success ? 'success' : 'error')
                            .html('<strong>' + (response.success ? '✓' : '✗') + '</strong> ' + response.message)
                            .show();

                        if (response.details) {
                            detailsContent.text(response.details);
                            detailsDiv.slideDown(300);
                        }
                    },
                    error: function() {
                        statusDiv.removeClass('success error')
                            .addClass('error')
                            .html('<strong>✗</strong> Error de conexión')
                            .show();
                    },
                    complete: function() {
                        button.prop('disabled', false)
                            .html('<i class="fas fa-magic me-2"></i>Agregar AUTO_INCREMENT');
                    }
                });
            });

            // Manejar backup de la base de datos
            $('#generate-backup-cli').on('click', function() {
                const button = $(this);
                const statusDiv = $('#backup-cli-status');

                button.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1"></span> Generando...');

                statusDiv.removeClass('alert-danger alert-success')
                    .text('')
                    .hide();

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'generate_db_backup_cli'
                    },
                    success: function(response) {
                        let message=response.message
                        let success=response.success
                        let path_file=response.path_file
                        let path_name=response.path_name
                        let command_output=response.command_output
                        statusDiv.removeClass('success error')
                            .addClass(response.success ? 'success' : 'error')
                            .html('<strong>' + (response.success ? '✓' : '✗') + '</strong> ' + response.message+'<br>'+
                            `<strong>Comando:</strong> <code>${command_output}</code>`)
                            .show();

                        if(success){
                            showForm('backup-cli-status-form', path_file, path_name);
                        }else {
                            hideForm('backup-cli-status-form', path_file, path_name);
                        }
                    },
                    error: function() {
                        statusDiv.removeClass('success error')
                            .addClass('error')
                            .html('<strong>✗</strong> Error de conexión')
                            .show();
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Ejecutar');
                    }
                });
            });

            // Ejecutar consulta individual
            $('.execute-single').on('click', function() {
                const button = $(this);
                const queryId = button.data('query-id');
                const query = button.data('query');
                const resultDiv = $('#result-' + queryId);
                const sqlDiv = $('#sql-' + queryId);

                button.prop('disabled', true).text('Ejecutando...');
                resultDiv.hide();

                // Mostrar la sentencia SQL
                sqlDiv.slideDown(300);

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'solu_execute_query',
                        query_id: queryId,
                        query: query
                    },
                    success: function(response) {
                        resultDiv.removeClass('success error')
                            .addClass(response.success ? 'success' : 'error')
                            .html('<strong>' + (response.success ? '✓' : '✗') + '</strong> ' + response.message)
                            .show();
                    },
                    error: function() {
                        resultDiv.removeClass('success error')
                            .addClass('error')
                            .html('<strong>✗</strong> Error de conexión')
                            .show();
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Ejecutar');
                    }
                });
            });
            // Ejecutar todas las consultas
            $('#execute-all').on('click', function() {
                const button = $(this);
                const progressDiv = $('#all-progress');
                const resultsDiv = $('#all-results');
                const progressBar = $('.solu-progress-fill');
                const progressText = $('.solu-progress-text');
                button.prop('disabled', true).text('Ejecutando...');
                progressDiv.show();
                resultsDiv.empty();
                const queries = <?php echo json_encode($queries); ?>;
                const totalQueries = Object.keys(queries).length;
                let completedQueries = 0;

                function updateProgress() {
                    const percentage = Math.round((completedQueries / totalQueries) * 100);
                    progressBar.css('width', percentage + '%');
                    progressText.text(percentage + '%');
                }

                function executeNextQuery(queryId, queryData) {
                    return new Promise((resolve) => {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'solu_execute_query',
                                query_id: queryId,
                                query: queryData.query
                            },
                            success: function(response) {
                                completedQueries++;
                                updateProgress();
                                const resultItem = $('<div class="solu-result-item ' + (response.success ? 'success' : 'error') + '">')
                                    .html('<strong>' + queryData.title + ':</strong> ' + response.message);
                                resultsDiv.append(resultItem);
                                resolve(response);
                            },
                            error: function() {
                                completedQueries++;
                                updateProgress();
                                const resultItem = $('<div class="solu-result-item error">')
                                    .html('<strong>' + queryData.title + ':</strong> Error de conexión');
                                resultsDiv.append(resultItem);
                                resolve({
                                    success: false,
                                    message: 'Error de conexión'
                                });
                            }
                        });
                    });
                }
                async function executeAllQueries() {
                    for (const [queryId, queryData] of Object.entries(queries)) {
                        await executeNextQuery(queryId, queryData);
                        await new Promise(resolve => setTimeout(resolve, 100));
                    }
                    button.prop('disabled', false).text('Ejecutar Todas las Consultas');
                }
                executeAllQueries();
            });
        });


        function showForm(formID,path_file, path_name) {
            const form = document.getElementById(formID);
            const pathFile = document.querySelector('input[name="path_file"]');
            const pathName = document.querySelector('input[name="path_name"]');
            const statusDiv = document.getElementById('backup-cli-status');

            form.style.display = 'block';
            pathFile.value= path_file;
            pathName.value= path_name;
        }
        function hideForm(formID) {
            const form = document.getElementById(formID);
            form.style.display = 'none';

        }
    </script>
  <?php
}
// Si se accede directamente al archivo, mostrar la página

solu_clean_admin_page();

?>