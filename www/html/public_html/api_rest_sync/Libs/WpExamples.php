<?php

/**
 * EJEMPLOS DE USO DE LA CLASE Wp
 *
 * Este archivo contiene ejemplos prácticos de cómo usar la clase Wp
 * para ejecutar operaciones de WordPress/WooCommerce desde tu API REST.
 *
 * NO EJECUTAR ESTE ARCHIVO DIRECTAMENTE - Solo para referencia
 */

namespace Libs;

use Libs\Wp;
use Exception;

class WpExamples
{
    private $wp;

    public function __construct()
    {
        // Inicializar la clase Wp
        $this->wp = new Wp();

        // Si tu WordPress usa otro prefijo de tablas:
        // $this->wp = new Wp('custom_prefix_');
    }

    // ==========================================
    // EJEMPLO 1: TRABAJAR CON POSTS
    // ==========================================

    /**
     * Ejemplo: Obtener un post por ID
     */
    public function ejemplo1_obtenerPost()
    {
        try {
            $post_id = 123;
            $post = $this->wp->getPost($post_id);

            if ($post) {
                echo "Título: {$post->post_title}\n";
                echo "Contenido: {$post->post_content}\n";
                echo "Estado: {$post->post_status}\n";
            } else {
                echo "Post no encontrado";
            }

            return $post;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Ejemplo: Obtener todos los productos
     */
    public function ejemplo2_obtenerProductos()
    {
        try {
            // Obtener productos con paginación
            $productos = $this->wp->getPostsByType('product', 50, 0);

            foreach ($productos as $producto) {
                echo "ID: {$producto->ID} - {$producto->post_title}\n";
            }

            return [
                'status' => 'ok',
                'total' => count($productos),
                'productos' => $productos
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Actualizar título de un producto
     */
    public function ejemplo3_actualizarTitulo()
    {
        try {
            $post_id = 123;
            $nuevo_titulo = "Nuevo nombre del producto";

            $actualizado = $this->wp->updatePostTitle($post_id, $nuevo_titulo);

            return [
                'status' => 'ok',
                'actualizado' => $actualizado,
                'post_id' => $post_id
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 2: TRABAJAR CON METADATOS
    // ==========================================

    /**
     * Ejemplo: Obtener el SKU de un producto
     */
    public function ejemplo4_obtenerSKU()
    {
        try {
            $post_id = 123;
            $sku = $this->wp->getPostMeta($post_id, '_sku');

            echo "SKU del producto {$post_id}: {$sku}\n";

            return [
                'status' => 'ok',
                'post_id' => $post_id,
                'sku' => $sku
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Obtener TODOS los metadatos de un producto
     */
    public function ejemplo5_obtenerTodosLosMeta()
    {
        try {
            $post_id = 123;
            $meta = $this->wp->getAllPostMeta($post_id);

            // Resultado: ['_sku' => 'ABC123', '_price' => '99.99', ...]
            print_r($meta);

            return [
                'status' => 'ok',
                'post_id' => $post_id,
                'meta' => $meta
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Actualizar un metadato personalizado
     */
    public function ejemplo6_actualizarMeta()
    {
        try {
            $post_id = 123;
            $meta_key = '_custom_field';
            $meta_value = 'Valor personalizado';

            $actualizado = $this->wp->updatePostMeta($post_id, $meta_key, $meta_value);

            return [
                'status' => 'ok',
                'actualizado' => $actualizado,
                'post_id' => $post_id
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 3: TRABAJAR CON OPCIONES
    // ==========================================

    /**
     * Ejemplo: Obtener una opción de WordPress
     */
    public function ejemplo7_obtenerOpcion()
    {
        try {
            $option_name = 'blogname'; // Nombre del sitio
            $valor = $this->wp->getOption($option_name);

            echo "Nombre del sitio: {$valor}\n";

            return [
                'status' => 'ok',
                'option' => $option_name,
                'value' => $valor
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Actualizar el tipo de cambio (para tu plugin Solu Exchange)
     */
    public function ejemplo8_actualizarTipoCambio()
    {
        try {
            $tipo_cambio = 3.85;

            // Actualizar opción personalizada
            $this->wp->updateOption('solu_exchange_rate', $tipo_cambio);

            return [
                'status' => 'ok',
                'tipo_cambio' => $tipo_cambio,
                'message' => 'Tipo de cambio actualizado'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 4: WOOCOMMERCE - PRODUCTOS
    // ==========================================

    /**
     * Ejemplo: Buscar producto por SKU
     */
    public function ejemplo9_buscarPorSKU()
    {
        try {
            $sku = 'ABC123';
            $producto = $this->wp->getProductBySku($sku);

            if ($producto) {
                echo "Producto encontrado: {$producto->post_title}\n";
                echo "ID: {$producto->ID}\n";
            } else {
                echo "Producto no encontrado con SKU: {$sku}\n";
            }

            return [
                'status' => 'ok',
                'sku' => $sku,
                'producto' => $producto
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Actualizar stock de un producto (CON TRANSACCIÓN)
     */
    public function ejemplo10_actualizarStock()
    {
        try {
            $product_id = 123;
            $nuevo_stock = 50;

            // Esta función actualiza 3 tablas en una transacción
            $actualizado = $this->wp->updateProductStock($product_id, $nuevo_stock);

            return [
                'status' => 'ok',
                'product_id' => $product_id,
                'stock' => $nuevo_stock,
                'actualizado' => $actualizado
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Actualizar precio de un producto (CON TRANSACCIÓN)
     */
    public function ejemplo11_actualizarPrecio()
    {
        try {
            $product_id = 123;
            $nuevo_precio = 99.99;

            // Esta función actualiza 3 tablas en una transacción
            $actualizado = $this->wp->updateProductPrice($product_id, $nuevo_precio);

            return [
                'status' => 'ok',
                'product_id' => $product_id,
                'precio' => $nuevo_precio,
                'actualizado' => $actualizado
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Obtener stock actual de un producto
     */
    public function ejemplo12_obtenerStock()
    {
        try {
            $product_id = 123;
            $stock = $this->wp->getProductStock($product_id);

            return [
                'status' => 'ok',
                'product_id' => $product_id,
                'stock' => $stock
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Obtener precio actual de un producto
     */
    public function ejemplo13_obtenerPrecio()
    {
        try {
            $product_id = 123;
            $precio = $this->wp->getProductPrice($product_id);

            return [
                'status' => 'ok',
                'product_id' => $product_id,
                'precio' => $precio
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Obtener productos con metadatos (SKU, precio, stock)
     */
    public function ejemplo14_productosCompletos()
    {
        try {
            // Obtener 100 productos con paginación
            $productos = $this->wp->getProductsWithMeta(100, 0);

            foreach ($productos as $producto) {
                echo "ID: {$producto->ID}\n";
                echo "Título: {$producto->post_title}\n";
                echo "SKU: {$producto->sku}\n";
                echo "Precio: {$producto->price}\n";
                echo "Stock: {$producto->stock}\n";
                echo "Estado Stock: {$producto->stock_status}\n";
                echo "---\n";
            }

            return [
                'status' => 'ok',
                'total' => count($productos),
                'productos' => $productos
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 5: USUARIOS
    // ==========================================

    /**
     * Ejemplo: Obtener usuario por email
     */
    public function ejemplo15_obtenerUsuario()
    {
        try {
            $email = 'usuario@example.com';
            $usuario = $this->wp->getUserByEmail($email);

            if ($usuario) {
                echo "Usuario: {$usuario->user_login}\n";
                echo "Email: {$usuario->user_email}\n";
                echo "ID: {$usuario->ID}\n";
            }

            return [
                'status' => 'ok',
                'usuario' => $usuario
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 6: QUERIES PERSONALIZADAS
    // ==========================================

    /**
     * Ejemplo: Query SELECT personalizada
     */
    public function ejemplo16_queryPersonalizada()
    {
        try {
            // Query personalizada con prepared statements
            $sql = "SELECT p.ID, p.post_title, pm.meta_value as sku
                    FROM wp_posts p
                    INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
                    WHERE p.post_type = ?
                    AND pm.meta_key = ?
                    LIMIT ?";

            $params = ['product', '_sku', 10];

            $resultados = $this->wp->query($sql, $params);

            foreach ($resultados as $row) {
                echo "ID: {$row->ID}, Título: {$row->post_title}, SKU: {$row->sku}\n";
            }

            return [
                'status' => 'ok',
                'resultados' => $resultados
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Ejemplo: Query de modificación (UPDATE, INSERT, DELETE)
     */
    public function ejemplo17_queryModificacion()
    {
        try {
            $sql = "UPDATE wp_postmeta
                    SET meta_value = ?
                    WHERE post_id = ? AND meta_key = ?";

            $params = ['Nuevo valor', 123, '_custom_field'];

            $filas_afectadas = $this->wp->execute($sql, $params);

            return [
                'status' => 'ok',
                'filas_afectadas' => $filas_afectadas
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 7: TRANSACCIONES PERSONALIZADAS
    // ==========================================

    /**
     * Ejemplo: Actualizar múltiples productos en una transacción
     */
    public function ejemplo18_transaccion()
    {
        try {
            $productos = [
                ['id' => 123, 'stock' => 50, 'precio' => 99.99],
                ['id' => 124, 'stock' => 30, 'precio' => 79.99],
                ['id' => 125, 'stock' => 20, 'precio' => 59.99]
            ];

            // Ejecutar todo en una transacción
            $resultado = $this->wp->transaction(function($wp) use ($productos) {
                $actualizados = 0;

                foreach ($productos as $producto) {
                    // Actualizar stock
                    $wp->updateProductStock($producto['id'], $producto['stock']);

                    // Actualizar precio
                    $wp->updateProductPrice($producto['id'], $producto['precio']);

                    $actualizados++;
                }

                return $actualizados;
            });

            return [
                'status' => 'ok',
                'productos_actualizados' => $resultado,
                'message' => 'Todos los productos actualizados correctamente'
            ];

        } catch (Exception $e) {
            // Si hay error, todo se revierte automáticamente
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'note' => 'Ningún cambio fue aplicado (rollback automático)'
            ];
        }
    }

    // ==========================================
    // EJEMPLO 8: CONTAR REGISTROS
    // ==========================================

    /**
     * Ejemplo: Contar productos publicados
     */
    public function ejemplo19_contar()
    {
        try {
            // Contar todos los productos publicados
            $total = $this->wp->count('posts', "post_type = ? AND post_status = ?", ['product', 'publish']);

            echo "Total de productos publicados: {$total}\n";

            return [
                'status' => 'ok',
                'total_productos' => $total
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 9: SINCRONIZACIÓN MASIVA
    // ==========================================

    /**
     * Ejemplo: Sincronizar múltiples productos desde ERP
     * (Este sería un caso de uso real en tu sistema)
     */
    public function ejemplo20_sincronizacionMasiva()
    {
        try {
            // Datos que vienen del ERP SoftLink
            $productos_erp = [
                ['sku' => 'ABC123', 'stock' => 50, 'precio' => 99.99],
                ['sku' => 'DEF456', 'stock' => 30, 'precio' => 79.99],
                ['sku' => 'GHI789', 'stock' => 0, 'precio' => 59.99]
            ];

            $resultados = [];

            // Procesar cada producto en una transacción
            foreach ($productos_erp as $producto_erp) {
                try {
                    $this->wp->transaction(function($wp) use ($producto_erp) {
                        // 1. Buscar producto por SKU
                        $producto = $wp->getProductBySku($producto_erp['sku']);

                        if ($producto) {
                            // 2. Actualizar stock
                            $wp->updateProductStock($producto->ID, $producto_erp['stock']);

                            // 3. Actualizar precio
                            $wp->updateProductPrice($producto->ID, $producto_erp['precio']);

                            return [
                                'success' => true,
                                'sku' => $producto_erp['sku'],
                                'product_id' => $producto->ID
                            ];
                        } else {
                            return [
                                'success' => false,
                                'sku' => $producto_erp['sku'],
                                'error' => 'Producto no encontrado'
                            ];
                        }
                    });

                    $resultados[] = ['sku' => $producto_erp['sku'], 'status' => 'ok'];

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

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}

// ==========================================
// EJEMPLO 10: USO EN UN CONTROLADOR
// ==========================================

/**
 * Ejemplo de cómo usar Wp en un Controller real
 */
class ProductControllerExample
{
    private $wp;

    public function __construct()
    {
        $this->wp = new Wp();
    }

    /**
     * Endpoint: GET /product/by-sku?sku=ABC123
     */
    public function getProductBySku()
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

            // Buscar producto
            $producto = $this->wp->getProductBySku($sku);

            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => "Producto no encontrado con SKU: {$sku}"
                ]);
                return;
            }

            // Obtener metadatos
            $stock = $this->wp->getProductStock($producto->ID);
            $precio = $this->wp->getProductPrice($producto->ID);

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'producto' => [
                    'id' => $producto->ID,
                    'titulo' => $producto->post_title,
                    'sku' => $sku,
                    'stock' => $stock,
                    'precio' => $precio,
                    'estado' => $producto->post_status
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
     * Endpoint: POST /product/update-stock
     * Body: {"product_id": 123, "stock": 50}
     */
    public function updateStock()
    {
        try {
            $postBody = file_get_contents("php://input");
            $data = json_decode($postBody);

            if (!isset($data->product_id) || !isset($data->stock)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'product_id y stock son requeridos'
                ]);
                return;
            }

            // Actualizar stock (con transacción automática)
            $this->wp->updateProductStock($data->product_id, $data->stock);

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'message' => 'Stock actualizado correctamente',
                'product_id' => $data->product_id,
                'nuevo_stock' => $data->stock
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
