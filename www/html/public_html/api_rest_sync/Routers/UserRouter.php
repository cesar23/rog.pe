<?php

namespace Routers;

use Controllers\AuthController;
use Controllers\AuthJWTController;
use Libs\Route;

/**
 * Class UserRouter
 *
 * Esta clase se encarga de definir y registrar las rutas relacionadas con la gestión de usuarios.
 */
class UserRouter
{
    /**
     * Ruta base para los endpoints de usuarios.
     */
    const API_PATH = '/user';

    /**
     * Inicializa las rutas para la gestión de usuarios.
     *
     * Este método registra todas las rutas disponibles bajo la ruta base definida en `API_PATH`.
     * Las rutas incluyen operaciones como login, logout, y obtención de información del usuario.
     *
     * @return void
     */
    final public static function initRouters()
    {
        /**
         * Ruta para probar la funcionalidad de usuario.
         *
         * @method GET
         */
        Route::add(self::API_PATH . '/test', function () {
            $control = new AuthJWTController();
            $control->test2();
        }, ['get']);

        /**
         * Ruta para el inicio de sesión de usuarios.
         *
         * @method POST
         */
        Route::add(self::API_PATH . '/login', function () {
            $control = new AuthJWTController();
            $control->login();
        }, ['post']);

        /**
         * Ruta para cerrar la sesión de un usuario.
         *
         * @method GET
         */
        Route::add(self::API_PATH . '/logout', function () {
            $control = new AuthJWTController();
            $control->destruir();
        }, ['get']);

        /**
         * Ruta para obtener información del usuario autenticado.
         *
         * @method GET
         */
        Route::add(self::API_PATH . '/user', function () {
            $control = new AuthJWTController();
            $control->user();
        }, ['get']);

        // Ejecutar las rutas definidas
        Route::run(BASEPATH);
    }
}
