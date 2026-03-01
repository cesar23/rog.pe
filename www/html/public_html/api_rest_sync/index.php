<?php

const APP_VERSION = "3.0.0";
// Load Composer autoloader
require_once __DIR__ . "/vendor/autoload.php";

// Load environment variables from .env.development file
// Detectar el entorno: usar .env.development.development si existe, sino usar .env.development.production
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env.production');
$dotenv->load();

// Set timezone from .env.development or default
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Lima');

// Include additional configuration
include __DIR__ . '/Config/Config.php';

// Generar y almacenar el requestId
$requestId = uniqid('req_', true);
$_SERVER['REQUEST_ID'] = $requestId;

// Headers de respuesta
header('X-Request-ID: ' . $requestId);
header('X-API-Version: ' . APP_VERSION);

// Función para registrar access log con rotación
function logAccessRequest($requestId)
{
    $logDir = __DIR__ . '/Logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/access_log.log';
    $maxFileSize = 5 * 1024 * 1024; // 5MB en bytes
    $maxBackups = 5; // Mantener 5 archivos históricos

    $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';

    // Capturar el body de la petición para POST, PUT, PATCH
    $requestBody = '';
    if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
        $rawBody = file_get_contents('php://input');
        if (!empty($rawBody)) {
            // Intentar decodificar JSON para mejor legibilidad
            $decodedBody = json_decode($rawBody, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Si es JSON válido, formatearlo sin saltos de línea
                $requestBody = json_encode($decodedBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                // Si no es JSON, usar el raw body (truncar si es muy largo)
                $requestBody = strlen($rawBody) > 1000
                    ? substr($rawBody, 0, 1000) . '... [truncated]'
                    : $rawBody;
            }
        }
    }

    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'request_id' => $requestId,
        'method' => $method,
        'uri' => $_SERVER['REQUEST_URI'] ?? '',
        'query_string' => $_SERVER['QUERY_STRING'] ?? '',
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? '',
        'http_host' => $_SERVER['HTTP_HOST'] ?? '',
        'request_body' => $requestBody
    ];

    // Formato de log
    $logLine = sprintf(
        "[%s] [%s] %s %s - IP: %s - User-Agent: %s - Host: %s%s%s",
        $logData['timestamp'],
        $logData['request_id'],
        $logData['method'],
        $logData['uri'],
        $logData['remote_addr'],
        $logData['user_agent'],
        $logData['http_host'],
        !empty($requestBody) ? ' - Body: ' . $requestBody : '',
        PHP_EOL
    );

    // Verificar si el archivo necesita rotación
    if (file_exists($logFile) && filesize($logFile) >= $maxFileSize) {
        // Eliminar el archivo más antiguo si existe (access_log.log.5)
        if (file_exists($logFile . '.' . $maxBackups)) {
            unlink($logFile . '.' . $maxBackups);
        }

        // Rotar archivos: .4 -> .5, .3 -> .4, etc.
        for ($i = $maxBackups - 1; $i >= 1; $i--) {
            $oldFile = $logFile . '.' . $i;
            $newFile = $logFile . '.' . ($i + 1);
            if (file_exists($oldFile)) {
                rename($oldFile, $newFile);
            }
        }

        // Mover el archivo actual a .1
        rename($logFile, $logFile . '.1');
    }

    // Escribir la línea de log
    file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
}

// Función para registrar response log con rotación
function logResponseData($requestId, $responseBody, $statusCode)
{
    $logDir = __DIR__ . '/Logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/response_log.log';
    $maxFileSize = 5 * 1024 * 1024; // 5MB en bytes
    $maxBackups = 5; // Mantener 5 archivos históricos

    // Formato de log
    $logLine = sprintf(
        "[%s] [%s] Status: %s - Response: %s%s",
        date('Y-m-d H:i:s'),
        $requestId,
        $statusCode,
        $responseBody,
        PHP_EOL
    );

    // Verificar si el archivo necesita rotación
    if (file_exists($logFile) && filesize($logFile) >= $maxFileSize) {
        // Eliminar el archivo más antiguo si existe (response_log.log.5)
        if (file_exists($logFile . '.' . $maxBackups)) {
            unlink($logFile . '.' . $maxBackups);
        }

        // Rotar archivos: .4 -> .5, .3 -> .4, etc.
        for ($i = $maxBackups - 1; $i >= 1; $i--) {
            $oldFile = $logFile . '.' . $i;
            $newFile = $logFile . '.' . ($i + 1);
            if (file_exists($oldFile)) {
                rename($oldFile, $newFile);
            }
        }

        // Mover el archivo actual a .1
        rename($logFile, $logFile . '.1');
    }

    // Escribir la línea de log
    file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
}

// Registrar la petición
logAccessRequest($requestId);

// Iniciar output buffering para capturar la respuesta
ob_start();

// Registrar función de shutdown para capturar la respuesta antes de enviarla
register_shutdown_function(function () use ($requestId) {
    // Capturar el contenido del buffer
    $response = ob_get_contents();

    // Obtener el código de estado HTTP
    $statusCode = http_response_code();

    // Limpiar buffer para evitar duplicados
    ob_end_clean();

    $finalResponse = $response;

    // Determinar Content-Type actual por cabeceras (si existe)
    $contentType = null;
    foreach (headers_list() as $h) {
        if (stripos($h, 'content-type:') === 0) {
            $contentType = trim(substr($h, strlen('content-type:')));
            break;
        }
    }

    // Detectar si la respuesta es JSON
    $isJson = false;
    if (!empty($contentType) && stripos($contentType, 'application/json') !== false) {
        $isJson = true;
    } else {
        $trim = trim($response);
        if ($trim !== '' && ($trim[0] === '{' || $trim[0] === '[')) {
            json_decode($trim);
            if (json_last_error() === JSON_ERROR_NONE) {
                $isJson = true;
            }
        }
    }

    if ($isJson) {
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Si es array asociativo/objeto, insertar requestId
            if (is_array($decoded) && array_keys($decoded) !== range(0, count($decoded) - 1)) {
                $decoded['requestId'] = $requestId;
                $finalResponse = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                // Si es array numérico o valor simple, envolver en data
                $finalResponse = json_encode(['requestId' => $requestId, 'data' => $decoded], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
        } else {
            // Si no se pudo decodificar por alguna razón, dejamos $finalResponse igual (no romper output).
        }
    } else {
        // No es JSON: no modificamos el body (la cabecera X-Request-ID ya fue enviada)
    }

    // Registrar la respuesta final (siempre)
    logResponseData($requestId, $finalResponse, $statusCode);

    // Intentar actualizar Content-Length si an es posible
    if (!headers_sent()) {
        header('Content-Length: ' . strlen($finalResponse));
    }

    // Enviar la respuesta final al cliente
    echo $finalResponse;
});

// Define constants from environment variables
define('__AUTH__', 'token');
define('__SECRET_KEY__', $_ENV['SECRET_KEY'] ?? 'asdawdsd8ws.6@');
define('__EXPIRE_TOKEN__', $_ENV['EXPIRE_TOKEN'] ?? 'PT1H'); // 1 Hour (DateInterval format)
define('__EXPIRE_TOKEN_JWT__', (int)($_ENV['EXPIRE_TOKEN_JWT'] ?? 24));  // 24 Hours (for JWT)

use Routers\InitRouter;

define('SYSTEM_PATH', __DIR__ . DIRECTORY_SEPARATOR);

// Define basepath from environment variables
define('BASEPATH', $_ENV['APP_BASEPATH'] ?? '/api_rest_sync/');

new InitRouter();
