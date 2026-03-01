<?php

namespace Routers;

use Controllers\AuthJWTController;
use Controllers\CacheController;
use Libs\Route;

/**
 * Class CacheRouter
 *
 * Esta clase se encarga de definir y registrar las rutas relacionadas con la gestión de cache.
 */
class CacheRouter
{
    /**
     * Ruta base para los endpoints de cache.
     */
    const API_PATH = '/cache';

    /**
     * Inicializa las rutas para la gestión de cache.
     *
     * Este método registra todas las rutas disponibles bajo la ruta base definida en `API_PATH`.
     * Las rutas incluyen operaciones como la limpieza de cache.
     *
     * @return void
     */
    final public static function initRouters()
    {
 
        /**
         * Ruta para limpiar el cache.
         *
         * @method GET
         */
        Route::add(self::API_PATH . '/cleanner-all-cache', function () {
            $control = new CacheController();
            $control->cleannerCacheAll();
        }, ['get']);


/**
         * Ruta para limpiar la caché de la portada.
         *
         * @method GET
         */
        Route::add(self::API_PATH . '/purge-frontpage', function () {
            $control = new CacheController();
            $control->purgeFrontPage();
        }, ['get']);


        
/**
         * Ruta para limpiar la caché de la post específico.
         *
         * @method GET
         */
        Route::add(self::API_PATH . '/purge-post', function () {
            $control = new CacheController();
            $control->purgePost();
        }, ['post']);


        /**
         * Ruta para limpiar la caché de la post específico.
         *
         * @method GET
         */
        Route::add(self::API_PATH . '/purge-posts', function () {
            $control = new CacheController();
            $control->purgePosts();
        }, ['post']);




        // Ejecutar las rutas definidas
        Route::run(BASEPATH);
    }
}
