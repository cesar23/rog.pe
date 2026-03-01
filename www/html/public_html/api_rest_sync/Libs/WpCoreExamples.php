<?php

/**
 * EJEMPLOS DE USO DE LA CLASE WpCore
 *
 * Esta clase usa wp-load.php para cargar el core de WordPress
 * y tener acceso a TODAS las funciones nativas.
 *
 * NO EJECUTAR ESTE ARCHIVO DIRECTAMENTE - Solo para referencia
 */

namespace Libs;

use Libs\WpCore;
use Exception;

class WpCoreExamples
{
    private $wpcore;

    public function __construct()
    {
        try {
            // Opción 1: Detección automática de wp-load.php
            $this->wpcore = new WpCore();

            // Opción 2: Especificar ruta manualmente
            // $this->wpcore = new WpCore('D:/repos/project_sync_sistema/www/adcomputers.local/wp-load.php');

        } catch (Exception $e) {
            die("Error al cargar WordPress: " . $e->getMessage());
        }
    }

    // ==========================================
    // EJEMPLO 1: POSTS BÁSICOS
    // ==========================================

    /**
     * Ejemplo: Obtener un post con get_post()
     */
    public function ejemplo1_obtenerPost()
    {
        $post = $this->wpcore->getPost(123);

        if ($post) {
            echo "Título: {$post->post_title}\n";
            echo "Contenido: {$post->post_content}\n";
            echo "Autor: {$post->post_author}\n";
            echo "Fecha: {$post->post_date}\n";
        }

        return $post;
    }

    /**
     * Ejemplo: Obtener posts con filtros avanzados
     */
    public function ejemplo2_obtenerProductosConFiltros()
    {
        // Argumentos avanzados de get_posts
        $args = [
            'post_type' => 'product',
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => '_stock',
                    'value' => 0,
                    'compare' => '>'
                ]
            ]
        ];

        $productos = $this->wpcore->getPosts($args);

        foreach ($productos as $producto) {
            echo "ID: {$producto->ID} - {$producto->post_title}\n";
        }

        return $productos;
    }

    /**
     * Ejemplo: Crear un nuevo producto
     */
    public function ejemplo3_crearProducto()
    {
        $post_data = [
            'post_title' => 'Nuevo Producto',
            'post_content' => 'Descripción del producto',
            'post_status' => 'publish',
            'post_type' => 'product',
            'post_author' => 1
        ];

        $post_id = $this->wpcore->insertPost($post_data);

        if (is_wp_error($post_id)) {
            return [
                'status' => 'error',
                'message' => $post_id->get_error_message()
            ];
        }

        return [
            'status' => 'ok',
            'post_id' => $post_id,
            'message' => 'Producto creado correctamente'
        ];
    }

    /**
     * Ejemplo: Actualizar un post
     */
    public function ejemplo4_actualizarPost()
    {
        $post_data = [
            'ID' => 123,
            'post_title' => 'Título Actualizado',
            'post_content' => 'Contenido actualizado'
        ];

        $post_id = $this->wpcore->updatePost($post_data);

        if (is_wp_error($post_id)) {
            return [
                'status' => 'error',
                'message' => $post_id->get_error_message()
            ];
        }

        return [
            'status' => 'ok',
            'post_id' => $post_id
        ];
    }

    // ==========================================
    // EJEMPLO 2: WOOCOMMERCE
    // ==========================================

    /**
     * Ejemplo: Obtener producto por SKU (método WooCommerce)
     */
    public function ejemplo5_productoPorSKU()
    {
        $sku = 'ABC123';
        $producto = $this->wpcore->getProductBySku($sku);

        if ($producto) {
            echo "ID: {$producto->get_id()}\n";
            echo "Nombre: {$producto->get_name()}\n";
            echo "SKU: {$producto->get_sku()}\n";
            echo "Precio: {$producto->get_price()}\n";
            echo "Stock: {$producto->get_stock_quantity()}\n";
            echo "Estado Stock: {$producto->get_stock_status()}\n";
        }

        return $producto;
    }

    /**
     * Ejemplo: Actualizar stock usando WooCommerce
     */
    public function ejemplo6_actualizarStockWC()
    {
        $product_id = 123;

        // Opción 1: Establecer stock exacto
        $nuevo_stock = $this->wpcore->updateProductStock($product_id, 50, 'set');

        // Opción 2: Incrementar stock
        // $nuevo_stock = $this->wpcore->updateProductStock($product_id, 10, 'increase');

        // Opción 3: Decrementar stock
        // $nuevo_stock = $this->wpcore->updateProductStock($product_id, 5, 'decrease');

        return [
            'status' => 'ok',
            'product_id' => $product_id,
            'nuevo_stock' => $nuevo_stock
        ];
    }

    /**
     * Ejemplo: Actualizar precio con precio de oferta
     */
    public function ejemplo7_actualizarPrecioConOferta()
    {
        $product_id = 123;
        $precio_regular = 100.00;
        $precio_oferta = 79.99;

        $actualizado = $this->wpcore->updateProductPrice(
            $product_id,
            $precio_regular,
            $precio_oferta
        );

        return [
            'status' => 'ok',
            'actualizado' => $actualizado,
            'precio_regular' => $precio_regular,
            'precio_oferta' => $precio_oferta
        ];
    }

    /**
     * Ejemplo: Crear producto completo con WooCommerce
     */
    public function ejemplo8_crearProductoCompleto()
    {
        $product_data = [
            'name' => 'Laptop HP 15',
            'sku' => 'LAPTOP-HP-15',
            'price' => 2499.99,
            'stock' => 10,
            'description' => 'Laptop HP de 15 pulgadas con procesador Intel i7',
            'short_description' => 'Laptop HP 15" Intel i7'
        ];

        $product_id = $this->wpcore->createProduct($product_data);

        if (is_wp_error($product_id)) {
            return [
                'status' => 'error',
                'message' => $product_id->get_error_message()
            ];
        }

        return [
            'status' => 'ok',
            'product_id' => $product_id,
            'message' => 'Producto creado correctamente'
        ];
    }

    /**
     * Ejemplo: Obtener producto con todos los datos
     */
    public function ejemplo9_obtenerProductoCompleto()
    {
        $product_id = 123;
        $producto = $this->wpcore->getProduct($product_id);

        if (!$producto) {
            return ['error' => 'Producto no encontrado'];
        }

        // WC_Product tiene muchos métodos útiles
        return [
            'id' => $producto->get_id(),
            'name' => $producto->get_name(),
            'sku' => $producto->get_sku(),
            'price' => $producto->get_price(),
            'regular_price' => $producto->get_regular_price(),
            'sale_price' => $producto->get_sale_price(),
            'stock' => $producto->get_stock_quantity(),
            'stock_status' => $producto->get_stock_status(),
            'description' => $producto->get_description(),
            'short_description' => $producto->get_short_description(),
            'weight' => $producto->get_weight(),
            'dimensions' => $producto->get_dimensions(false),
            'is_on_sale' => $producto->is_on_sale(),
            'is_purchasable' => $producto->is_purchasable(),
            'permalink' => $producto->get_permalink()
        ];
    }

    // ==========================================
    // EJEMPLO 3: METADATOS AVANZADOS
    // ==========================================

    /**
     * Ejemplo: Trabajar con metadatos complejos
     */
    public function ejemplo10_metadatosComplejos()
    {
        $post_id = 123;

        // Obtener todos los metas de un post
        $all_meta = $this->wpcore->getPostMeta($post_id, '', false);

        // Actualizar múltiples metas
        $this->wpcore->updatePostMeta($post_id, '_custom_field_1', 'Valor 1');
        $this->wpcore->updatePostMeta($post_id, '_custom_field_2', 'Valor 2');
        $this->wpcore->updatePostMeta($post_id, '_custom_array', ['item1', 'item2', 'item3']);

        // Añadir meta sin reemplazar existente
        $this->wpcore->addPostMeta($post_id, '_custom_multiple', 'Valor A', false);
        $this->wpcore->addPostMeta($post_id, '_custom_multiple', 'Valor B', false);

        return [
            'status' => 'ok',
            'all_meta' => $all_meta
        ];
    }

    // ==========================================
    // EJEMPLO 4: CATEGORÍAS Y TAXONOMÍAS
    // ==========================================

    /**
     * Ejemplo: Obtener categorías de producto
     */
    public function ejemplo11_categorias()
    {
        $categorias = $this->wpcore->getProductCategories();

        foreach ($categorias as $categoria) {
            echo "ID: {$categoria->term_id}\n";
            echo "Nombre: {$categoria->name}\n";
            echo "Slug: {$categoria->slug}\n";
            echo "Productos: {$categoria->count}\n";
            echo "---\n";
        }

        return $categorias;
    }

    /**
     * Ejemplo: Asignar categorías a un producto
     */
    public function ejemplo12_asignarCategorias()
    {
        $product_id = 123;

        // Obtener producto
        $producto = $this->wpcore->getProduct($product_id);

        if ($producto) {
            // Asignar categorías (IDs de términos)
            $producto->set_category_ids([15, 16, 17]);
            $producto->save();

            return [
                'status' => 'ok',
                'categorias_asignadas' => $producto->get_category_ids()
            ];
        }

        return ['error' => 'Producto no encontrado'];
    }

    // ==========================================
    // EJEMPLO 5: USUARIOS
    // ==========================================

    /**
     * Ejemplo: Obtener información completa de usuario
     */
    public function ejemplo13_usuario()
    {
        $user_id = 1;
        $usuario = $this->wpcore->getUser($user_id);

        if ($usuario) {
            return [
                'ID' => $usuario->ID,
                'login' => $usuario->user_login,
                'email' => $usuario->user_email,
                'display_name' => $usuario->display_name,
                'roles' => $usuario->roles,
                'first_name' => $this->wpcore->getUserMeta($user_id, 'first_name'),
                'last_name' => $this->wpcore->getUserMeta($user_id, 'last_name')
            ];
        }

        return null;
    }

    // ==========================================
    // EJEMPLO 6: FUNCIONES PERSONALIZADAS
    // ==========================================

    /**
     * Ejemplo: Ejecutar cualquier función de WordPress
     */
    public function ejemplo14_funcionesPersonalizadas()
    {
        // Ejemplo 1: Sanitizar texto
        $texto_limpio = $this->wpcore->call('sanitize_text_field', ['<script>alert("xss")</script>']);

        // Ejemplo 2: Generar URL
        $url = $this->wpcore->call('home_url', ['/products']);

        // Ejemplo 3: Formatear fecha
        $fecha = $this->wpcore->call('date_i18n', ['F j, Y', strtotime('2025-11-10')]);

        return [
            'texto_limpio' => $texto_limpio,
            'url' => $url,
            'fecha' => $fecha
        ];
    }

    // ==========================================
    // EJEMPLO 7: HOOKS Y FILTERS
    // ==========================================

    /**
     * Ejemplo: Ejecutar hooks y filters
     */
    public function ejemplo15_hooksFilters()
    {
        // Ejecutar un action
        $this->wpcore->doAction('wp_trash_post', 123);

        // Aplicar un filter
        $contenido = "Este es un contenido con HTML <script>alert('test')</script>";
        $contenido_filtrado = $this->wpcore->applyFilters('the_content', $contenido);

        return [
            'contenido_original' => $contenido,
            'contenido_filtrado' => $contenido_filtrado
        ];
    }

    // ==========================================
    // EJEMPLO 8: SINCRONIZACIÓN MASIVA CON WC
    // ==========================================

    /**
     * Ejemplo: Sincronizar múltiples productos desde ERP
     */
    public function ejemplo16_sincronizacionMasiva()
    {
        // Datos del ERP SoftLink
        $productos_erp = [
            ['sku' => 'ABC123', 'stock' => 50, 'precio' => 99.99],
            ['sku' => 'DEF456', 'stock' => 30, 'precio' => 79.99],
            ['sku' => 'GHI789', 'stock' => 0, 'precio' => 59.99]
        ];

        $resultados = [];

        foreach ($productos_erp as $producto_erp) {
            try {
                // Buscar producto por SKU
                $producto = $this->wpcore->getProductBySku($producto_erp['sku']);

                if ($producto) {
                    // Actualizar stock
                    $this->wpcore->updateProductStock(
                        $producto->get_id(),
                        $producto_erp['stock']
                    );

                    // Actualizar precio
                    $this->wpcore->updateProductPrice(
                        $producto->get_id(),
                        $producto_erp['precio']
                    );

                    $resultados[] = [
                        'sku' => $producto_erp['sku'],
                        'status' => 'ok',
                        'product_id' => $producto->get_id()
                    ];
                } else {
                    $resultados[] = [
                        'sku' => $producto_erp['sku'],
                        'status' => 'not_found'
                    ];
                }
            } catch (Exception $e) {
                $resultados[] = [
                    'sku' => $producto_erp['sku'],
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        return [
            'status' => 'ok',
            'total_procesados' => count($productos_erp),
            'resultados' => $resultados
        ];
    }

    // ==========================================
    // EJEMPLO 9: INFORMACIÓN DEL SISTEMA
    // ==========================================

    /**
     * Ejemplo: Obtener información del sistema
     */
    public function ejemplo17_infoSistema()
    {
        return [
            'wordpress_loaded' => $this->wpcore->isLoaded(),
            'wordpress_version' => $this->wpcore->getWordPressVersion(),
            'woocommerce_active' => $this->wpcore->isWooCommerceActive(),
            'woocommerce_version' => $this->wpcore->getWooCommerceVersion()
        ];
    }

    // ==========================================
    // EJEMPLO 10: BÚSQUEDAS AVANZADAS
    // ==========================================

    /**
     * Ejemplo: Búsqueda avanzada de productos
     */
    public function ejemplo18_busquedaAvanzada()
    {
        // Productos en oferta con stock > 0
        $args = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => '_sale_price',
                    'value' => '',
                    'compare' => '!='
                ],
                [
                    'key' => '_stock',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                ]
            ]
        ];

        $productos_oferta = $this->wpcore->getPosts($args);

        $resultado = [];
        foreach ($productos_oferta as $post) {
            $producto = $this->wpcore->getProduct($post->ID);
            $resultado[] = [
                'id' => $producto->get_id(),
                'name' => $producto->get_name(),
                'regular_price' => $producto->get_regular_price(),
                'sale_price' => $producto->get_sale_price(),
                'stock' => $producto->get_stock_quantity()
            ];
        }

        return $resultado;
    }
}

// ==========================================
// EJEMPLO 11: USO EN UN CONTROLADOR REAL
// ==========================================

/**
 * Ejemplo de Controller usando WpCore
 */
class ProductControllerWpCoreExample
{
    private $wpcore;

    public function __construct()
    {
        try {
            $this->wpcore = new WpCore();
        } catch (Exception $e) {
            die(json_encode([
                'status' => 'error',
                'message' => 'Error al cargar WordPress: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * Endpoint: GET /product/by-sku-wc?sku=ABC123
     */
    public function getProductBySkuWC()
    {
        try {
            $sku = $_GET['sku'] ?? null;

            if (!$sku) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'SKU es requerido'
                ]);
                return;
            }

            // Buscar producto usando WooCommerce
            $producto = $this->wpcore->getProductBySku($sku);

            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => "Producto no encontrado con SKU: {$sku}"
                ]);
                return;
            }

            // Respuesta con datos completos de WooCommerce
            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'producto' => [
                    'id' => $producto->get_id(),
                    'name' => $producto->get_name(),
                    'sku' => $producto->get_sku(),
                    'price' => $producto->get_price(),
                    'regular_price' => $producto->get_regular_price(),
                    'sale_price' => $producto->get_sale_price(),
                    'stock' => $producto->get_stock_quantity(),
                    'stock_status' => $producto->get_stock_status(),
                    'is_on_sale' => $producto->is_on_sale(),
                    'permalink' => $producto->get_permalink(),
                    'description' => $producto->get_description()
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint: POST /product/create-wc
     * Body: {"name": "...", "sku": "...", "price": 99.99, "stock": 10}
     */
    public function createProductWC()
    {
        try {
            $postBody = file_get_contents("php://input");
            $data = json_decode($postBody);

            if (!isset($data->name) || !isset($data->sku)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'name y sku son requeridos'
                ]);
                return;
            }

            // Crear producto
            $product_data = [
                'name' => $data->name,
                'sku' => $data->sku,
                'price' => $data->price ?? 0,
                'stock' => $data->stock ?? 0,
                'description' => $data->description ?? '',
                'short_description' => $data->short_description ?? ''
            ];

            $product_id = $this->wpcore->createProduct($product_data);

            if (is_wp_error($product_id)) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => $product_id->get_error_message()
                ]);
                return;
            }

            http_response_code(201);
            echo json_encode([
                'status' => 'ok',
                'message' => 'Producto creado correctamente',
                'product_id' => $product_id
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
