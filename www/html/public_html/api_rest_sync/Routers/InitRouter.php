<?php

namespace Routers;

use Libs\Route;
use Routers\ProductRouter;
use Routers\OptionRouter;
use Routers\UserRouter;
use Routers\CacheRouter;

class InitRouter
{
    // Constructor que inicializa las rutas y configura las cabeceras necesarias
    public function __construct()
    {
        // Configuración de CORS para permitir acceso desde cualquier origen
        // NOTA: Para producción, es recomendable restringir a dominios específicos
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        # ruta health check
        Route::add('/health-check', function () {
            echo json_encode(['status' => 'ok']);
            exit();
        }, ['get']);

        // Inicializar las rutas específicas para cada entidad del sistema
        ProductRouter::initRouters();
        CacheRouter::initRouters();
        UserRouter::initRouters();
        OptionRouter::initRouters();

        // Ruta para manejar errores 404 - Recurso no encontrado
        Route::pathNotFound(function ($path) {
            // Establecer código de respuesta 404
            header('HTTP/1.0 404 Not Found');

            // Respuesta en formato JSON para recursos no encontrados
            echo json_encode(['Error' => '404 Recurso no encontrado']);
        });

        // Ejecutar el enrutador con la basepath definida
        Route::run(BASEPATH);
    }
}
