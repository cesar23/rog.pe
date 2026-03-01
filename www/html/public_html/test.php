<?php
require_once 'wp-load.php';

$WP_CLI = "php wp-cli.phar";


$backup_dir = "backups/";

/**
 * Función para forzar la descarga de un archivo.s
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

function exec_backup_db_2() {
  global $wpdb, $WP_CLI, $backup_dir;

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

  echo "Ejecutando comando: " . $command . "<br>";

  exec($command, $output, $return_code);

  if ($return_code === 0) {
    echo "Comando ejecutado correctamente. Backup creado en: " . $backup_file_path . "<br>";

    // Llamar a la función de descarga
    download_file($backup_file_path, $backup_file_name);

  } else {
    echo "Error al ejecutar el comando. Código de error: " . $return_code . "<br>";
    echo "Salida de error:\n";
    foreach ($output as $line) {
      echo $line . "\n";
    }
    return false;
  }
  return true; // Retorna true si todo fue exitoso
}


function create_temp_file()
{

// Crea un archivo temporal
  $temp_file = tmpfile();

// Verifica si se creó correctamente
  if ($temp_file) {
    // Escribe datos en el archivo
    fwrite($temp_file, "Este es un archivo temporal.");

    // Regresa al inicio del archivo
    rewind($temp_file);

    // Lee los datos del archivo
    $contenido = fread($temp_file, 1024);
    echo "Contenido del archivo temporal: " . $contenido . "\n";

    // Cierra el archivo (se eliminará automáticamente)
    fclose($temp_file);
  } else {
    echo "Error al crear el archivo temporal.";
  }
}

function exec_backup_db()
{
  global $wpdb, $WP_CLI;
  echo "DB_HOST: " . DB_HOST . "<br>";
  echo "DB_USER: " . DB_USER . "<br>";
  echo "DB_PASSWORD: " . DB_PASSWORD . "<br>";
  echo "DB_NAME: " . DB_NAME . "<br>";

  echo "DB_NAME: " . DB_NAME . "<br>";
  $command = "{$WP_CLI} core version";
  exec($command, $output, $return_code);

  if ($return_code === 0) {
    echo "Comando ejecutado correctamente. Salida:\n";
    foreach ($output as $line) {
      echo $line . "\n";
    }
  } else {
    echo "Error al ejecutar el comando. Código de error: " . $return_code . "\n";
  }
  exit;


}

// Actualiza el bloque de procesamiento AJAX
if (isset($_POST['action'])) {
  if ($_POST['action'] === 'exec_backup_db') {
//    exec_backup_db();
    exec_backup_db_2();
    exit;
  }

}
//  $result = $wpdb->get_var(
//      $wpdb->prepare(
//          "SELECT COUNT(*) FROM information_schema.tables
//             WHERE table_schema = %s AND table_name = %s",
//          DB_NAME,
//          $table_name
//      )
//  );
//
//  return $result > 0;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple POST Form</title>
</head>
<body>
<form action="" method="post">
    <input type="hidden" name="action" value="exec_backup_db">
    <input type="submit" value="Submit">
</form>
</body>
</html>
