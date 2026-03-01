<?php
// Evitar que se muestren los mensajes de "warning", "deprecated" y "notice"
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED & ~E_NOTICE);
require_once __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../Config/Config.php';


// Cargar los routers
use Routers\ProductRouter;
use Routers\UserRouter;
use Routers\OptionRouter;
use Libs\Route;
use Libs\ConsoleColor;

// Definir la ruta base de la aplicación
define('BASEPATH', '/api_rest_pcbyte_sincronizador');

class RouteLister
{
    /**
     * Lista todas las rutas registradas en la aplicación.
     *
     * @return void
     */
    public static function listRoutes()
    {
        // Inicializar routers para cargar todas las rutas
        ProductRouter::initRouters();
        UserRouter::initRouters();
        OptionRouter::initRouters();

        // Obtener todas las rutas registradas
        $routes = Route::getAll();
        foreach ($routes as $route) {
            // Convertir el método en un arreglo si no lo es, para manejar múltiples métodos
            $methods = (array) $route['method'];
            foreach ($methods as $method) {
                $method=strtoupper($method);
//                $expression=BASEPATH.$route['expression'];
                $expression=$route['expression'];

                switch ($method){
                    case 'GET':
                        echo ConsoleColor::green($method) .ConsoleColor::white("    ".$expression) . PHP_EOL;
                        break;
                    case 'POST':
                        echo ConsoleColor::yellow($method) .ConsoleColor::white("   ".$expression) . PHP_EOL;
                        break;
                    case 'PUT':
                        echo ConsoleColor::blue($method) .ConsoleColor::white("    ".$expression) . PHP_EOL;
                        break;
                    case 'DELETE':
                        echo ConsoleColor::red($method) .ConsoleColor::white("   ".$expression) . PHP_EOL;
                        break;
                    default:
                        echo ConsoleColor::red($method) .ConsoleColor::white($expression) . PHP_EOL;
                }
            }
        }
    }
}

// Ejecutar el listado de rutas
RouteLister::listRoutes();
