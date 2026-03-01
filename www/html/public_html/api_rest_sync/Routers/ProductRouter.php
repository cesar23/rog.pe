<?php

namespace Routers;

use Controllers\AuthJWTController;
use Controllers\ProductController;
use Libs\Route;

/**
 * Class ProductRouter
 *
 * Esta clase se encarga de definir y registrar las rutas relacionadas con la gestión de productos.
 */
class ProductRouter
{
    /**
     * Ruta base para los endpoints de productos.
     */
    const API_PATH = '/product';

    /**
     * Inicializa las rutas para la gestión de productos.
     *
     * Este método registra todas las rutas disponibles bajo la ruta base definida en `API_PATH`.
     * Las rutas incluyen operaciones como la actualización de descripciones, stock, y precios de productos.
     *
     * @return void
     */
    final public static function initRouters()
    {
        /**
         * Ruta para probar la funcionalidad de productos.
         *
         * @method GET
         */
        Route::add(self::API_PATH . '/test', function () {
            $control = new ProductController();
            $control->test();
        }, ['get']);

        /**
         * Ruta para obtener productos por ID desde SoftLink.
         *
         * @method GET
         */
        Route::add(self::API_PATH . '/get-poducts-id-softlink', function () {
            $control = new ProductController();
            $control->getProductsIDSoftLink();
        }, ['get']);

        /**
         * Ruta para actualizar la descripción de un producto.
         *
         * @method POST
         */
        Route::add(self::API_PATH . '/up-product-description', function () {
            $control = new ProductController();
            $control->updateProductV2(); // Método para actualizar la descripción del producto
        }, ['post']);

        /**
         * Ruta para actualizar la descripción de varios productos.
         *
         * @method POST
         */
        Route::add(self::API_PATH . '/up-product-description-v3', function () {
            $control = new ProductController();
            $control->updateProductV3(); // Método para actualizar la descripción de múltiples productos
        }, ['post']);

        /**
         * Ruta para actualizar el stock de un producto.
         *
         * @method POST
         */
        Route::add(self::API_PATH . '/up-product-stock', function () {
            $control = new ProductController();
            $control->updateProductStock(); // Método para actualizar el stock del producto
        }, ['post']);

        /**
         * Ruta para actualizar el precio de un producto.
         *
         * @method POST
         */
        Route::add(self::API_PATH . '/up-product-price', function () {
            $control = new ProductController();
            $control->updateProductPrice(); // Método para actualizar el precio del producto
        }, ['post']);

        // Ejecutar las rutas definidas
        Route::run(BASEPATH);
    }
}
