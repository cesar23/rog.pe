<?php

namespace Routers;

use Controllers\AuthJWTController;
use Controllers\OptionController;
use Libs\Route;

/**
 * Class OptionRouter
 *
 * Esta clase se encarga de definir y registrar las rutas relacionadas con las opciones del sistema.
 */
class OptionRouter
{
    /**
     * Ruta base para los endpoints de opciones.
     */
    const API_PATH = '/option';

    /**
     * Inicializa las rutas para la gestión de opciones.
     *
     * Este método registra todas las rutas disponibles bajo la ruta base definida en `API_PATH`.
     * Actualmente, maneja la actualización del tipo de cambio en la web.
     *
     * @return void
     */
    public static function initRouters()
    {
        /**
         * Ruta para  actualizar el tpo de cambio con  el plugin antiguo
         *
         * @method POST
         */
        Route::add(self::API_PATH . '/up-tipo-cambio-web', function () {
            $control = new OptionController();
            $control->updateOptionTipoCambio(); // Método para actualizar el tipo de cambio
        }, ['post']);

        /**
         * Ruta para actualizar el tipo de cambio con el plugin nuevo 
         * Solu Currencies Exchange (Solu Exchange) creado por  mi
         *
         * @method POST
         */
        Route::add(self::API_PATH . '/up-tipo-cambio-web-v2', function () {
            $control = new OptionController();
            $control->updateOptionTipoCambioV2(); // Método para actualizar el tipo de cambio
        }, ['post']);

        // Ejecutar las rutas definidas
        Route::run(BASEPATH);
    }
}
