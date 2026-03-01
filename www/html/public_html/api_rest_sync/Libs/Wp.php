<?php

namespace Libs;

use Config\Database;
use PDO;
use PDOException;
use Exception;

/**
 * Clase Wp - Helper para ejecutar operaciones de WordPress/WooCommerce
 *
 * Proporciona métodos para interactuar con la base de datos de WordPress
 * sin necesidad de cargar todo el core de WordPress.
 *
 * @package Libs
 * @version 1.0.0
 */
class Wp
{
    /**
     * @var PDO Conexión a la base de datos
     */
    private $db;

    /**
     * @var string Prefijo de tablas de WordPress
     */
    private $table_prefix = 'wp_';

    /**
     * Constructor
     *
     * @param string|null $table_prefix Prefijo de tablas (por defecto 'wp_')
     */
    public function __construct($table_prefix = null)
    {
        $this->db = Database::connect();

        if ($table_prefix !== null) {
            $this->table_prefix = $table_prefix;
        }
    }

    /**
     * Obtiene la conexión PDO
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * Obtiene el prefijo de tablas
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->table_prefix;
    }

    // ==========================================
    // POSTS (Productos, Páginas, etc.)
    // ==========================================

    /**
     * Obtiene un post por ID
     *
     * @param int $post_id ID del post
     * @return object|null Post encontrado o null
     */
    public function getPost($post_id)
    {
        try {
            $sql = "SELECT * FROM {$this->table_prefix}posts WHERE ID = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$post_id]);

            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener post: " . $e->getMessage());
        }
    }

    /**
     * Obtiene posts por tipo (product, page, post, etc.)
     *
     * @param string $post_type Tipo de post
     * @param int $limit Límite de resultados (0 = sin límite)
     * @param int $offset Offset para paginación
     * @return array Array de posts
     */
    public function getPostsByType($post_type = 'post', $limit = 0, $offset = 0)
    {
        try {
            $sql = "SELECT * FROM {$this->table_prefix}posts
                    WHERE post_type = ?
                    AND post_status = 'publish'
                    ORDER BY post_date DESC";

            if ($limit > 0) {
                $sql .= " LIMIT ? OFFSET ?";
            }

            $stmt = $this->db->prepare($sql);

            if ($limit > 0) {
                $stmt->execute([$post_type, $limit, $offset]);
            } else {
                $stmt->execute([$post_type]);
            }

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener posts: " . $e->getMessage());
        }
    }

    /**
     * Actualiza el título de un post
     *
     * @param int $post_id ID del post
     * @param string $title Nuevo título
     * @return bool True si se actualizó
     */
    public function updatePostTitle($post_id, $title)
    {
        try {
            $sql = "UPDATE {$this->table_prefix}posts
                    SET post_title = ?
                    WHERE ID = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$title, $post_id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar título: " . $e->getMessage());
        }
    }

    /**
     * Actualiza el contenido de un post
     *
     * @param int $post_id ID del post
     * @param string $content Nuevo contenido
     * @return bool True si se actualizó
     */
    public function updatePostContent($post_id, $content)
    {
        try {
            $sql = "UPDATE {$this->table_prefix}posts
                    SET post_content = ?
                    WHERE ID = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$content, $post_id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar contenido: " . $e->getMessage());
        }
    }

    // ==========================================
    // POSTMETA (Metadatos de Posts)
    // ==========================================

    /**
     * Obtiene un metadato de un post
     *
     * @param int $post_id ID del post
     * @param string $meta_key Clave del metadato
     * @return mixed Valor del metadato o null
     */
    public function getPostMeta($post_id, $meta_key)
    {
        try {
            $sql = "SELECT meta_value FROM {$this->table_prefix}postmeta
                    WHERE post_id = ? AND meta_key = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$post_id, $meta_key]);

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ? $result->meta_value : null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener postmeta: " . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los metadatos de un post
     *
     * @param int $post_id ID del post
     * @return array Array asociativo [meta_key => meta_value]
     */
    public function getAllPostMeta($post_id)
    {
        try {
            $sql = "SELECT meta_key, meta_value FROM {$this->table_prefix}postmeta
                    WHERE post_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$post_id]);

            $meta = [];
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $meta[$row->meta_key] = $row->meta_value;
            }

            return $meta;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener postmeta: " . $e->getMessage());
        }
    }

    /**
     * Actualiza o crea un metadato de post
     *
     * @param int $post_id ID del post
     * @param string $meta_key Clave del metadato
     * @param mixed $meta_value Valor del metadato
     * @return bool True si se actualizó/creó
     */
    public function updatePostMeta($post_id, $meta_key, $meta_value)
    {
        try {
            // Verificar si existe
            $exists = $this->getPostMeta($post_id, $meta_key);

            if ($exists !== null) {
                // UPDATE
                $sql = "UPDATE {$this->table_prefix}postmeta
                        SET meta_value = ?
                        WHERE post_id = ? AND meta_key = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$meta_value, $post_id, $meta_key]);
            } else {
                // INSERT
                $sql = "INSERT INTO {$this->table_prefix}postmeta
                        (post_id, meta_key, meta_value)
                        VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$post_id, $meta_key, $meta_value]);
            }

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar postmeta: " . $e->getMessage());
        }
    }

    /**
     * Elimina un metadato de post
     *
     * @param int $post_id ID del post
     * @param string $meta_key Clave del metadato
     * @return bool True si se eliminó
     */
    public function deletePostMeta($post_id, $meta_key)
    {
        try {
            $sql = "DELETE FROM {$this->table_prefix}postmeta
                    WHERE post_id = ? AND meta_key = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$post_id, $meta_key]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar postmeta: " . $e->getMessage());
        }
    }

    // ==========================================
    // OPTIONS (Opciones de WordPress)
    // ==========================================

    /**
     * Obtiene una opción de WordPress
     *
     * @param string $option_name Nombre de la opción
     * @return mixed Valor de la opción o null
     */
    public function getOption($option_name)
    {
        try {
            $sql = "SELECT option_value FROM {$this->table_prefix}options
                    WHERE option_name = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$option_name]);

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ? $result->option_value : null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener opción: " . $e->getMessage());
        }
    }

    /**
     * Actualiza o crea una opción de WordPress
     *
     * @param string $option_name Nombre de la opción
     * @param mixed $option_value Valor de la opción
     * @param string $autoload 'yes' o 'no'
     * @return bool True si se actualizó/creó
     */
    public function updateOption($option_name, $option_value, $autoload = 'yes')
    {
        try {
            // Verificar si existe
            $exists = $this->getOption($option_name);

            if ($exists !== null) {
                // UPDATE
                $sql = "UPDATE {$this->table_prefix}options
                        SET option_value = ?
                        WHERE option_name = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$option_value, $option_name]);
            } else {
                // INSERT
                $sql = "INSERT INTO {$this->table_prefix}options
                        (option_name, option_value, autoload)
                        VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$option_name, $option_value, $autoload]);
            }

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar opción: " . $e->getMessage());
        }
    }

    /**
     * Elimina una opción de WordPress
     *
     * @param string $option_name Nombre de la opción
     * @return bool True si se eliminó
     */
    public function deleteOption($option_name)
    {
        try {
            $sql = "DELETE FROM {$this->table_prefix}options
                    WHERE option_name = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$option_name]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar opción: " . $e->getMessage());
        }
    }

    // ==========================================
    // WOOCOMMERCE - Productos
    // ==========================================

    /**
     * Obtiene un producto por SKU
     *
     * @param string $sku SKU del producto
     * @return object|null Producto encontrado o null
     */
    public function getProductBySku($sku)
    {
        try {
            $sql = "SELECT p.* FROM {$this->table_prefix}posts p
                    INNER JOIN {$this->table_prefix}postmeta pm ON p.ID = pm.post_id
                    WHERE p.post_type = 'product'
                    AND pm.meta_key = '_sku'
                    AND pm.meta_value = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sku]);

            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener producto: " . $e->getMessage());
        }
    }

    /**
     * Obtiene el stock de un producto
     *
     * @param int $product_id ID del producto
     * @return int Stock del producto
     */
    public function getProductStock($product_id)
    {
        $stock = $this->getPostMeta($product_id, '_stock');
        return $stock !== null ? (int)$stock : 0;
    }

    /**
     * Obtiene el precio de un producto
     *
     * @param int $product_id ID del producto
     * @return float Precio del producto
     */
    public function getProductPrice($product_id)
    {
        $price = $this->getPostMeta($product_id, '_price');
        return $price !== null ? (float)$price : 0.0;
    }

    /**
     * Actualiza el stock de un producto (con transacción)
     *
     * @param int $product_id ID del producto
     * @param int $stock Cantidad de stock
     * @return bool True si se actualizó
     */
    public function updateProductStock($product_id, $stock)
    {
        try {
            $this->db->beginTransaction();

            // 1. Actualizar _stock
            $this->updatePostMeta($product_id, '_stock', $stock);

            // 2. Actualizar _stock_status
            $stock_status = $stock > 0 ? 'instock' : 'outofstock';
            $this->updatePostMeta($product_id, '_stock_status', $stock_status);

            // 3. Actualizar wp_wc_product_meta_lookup
            $sql = "UPDATE {$this->table_prefix}wc_product_meta_lookup
                    SET stock_quantity = ?, stock_status = ?
                    WHERE product_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$stock, $stock_status, $product_id]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Error al actualizar stock: " . $e->getMessage());
        }
    }

    /**
     * Actualiza el precio de un producto (con transacción)
     *
     * @param int $product_id ID del producto
     * @param float $price Precio
     * @return bool True si se actualizó
     */
    public function updateProductPrice($product_id, $price)
    {
        try {
            $this->db->beginTransaction();

            // 1. Actualizar _price
            $this->updatePostMeta($product_id, '_price', $price);

            // 2. Actualizar _regular_price
            $this->updatePostMeta($product_id, '_regular_price', $price);

            // 3. Actualizar wp_wc_product_meta_lookup
            $sql = "UPDATE {$this->table_prefix}wc_product_meta_lookup
                    SET min_price = ?, max_price = ?
                    WHERE product_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$price, $price, $product_id]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Error al actualizar precio: " . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los productos con sus metadatos principales
     *
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Array de productos con metadatos
     */
    public function getProductsWithMeta($limit = 100, $offset = 0)
    {
        try {
            $sql = "SELECT
                        p.ID,
                        p.post_title,
                        p.post_status,
                        (SELECT meta_value FROM {$this->table_prefix}postmeta WHERE post_id = p.ID AND meta_key = '_sku') as sku,
                        (SELECT meta_value FROM {$this->table_prefix}postmeta WHERE post_id = p.ID AND meta_key = '_price') as price,
                        (SELECT meta_value FROM {$this->table_prefix}postmeta WHERE post_id = p.ID AND meta_key = '_stock') as stock,
                        (SELECT meta_value FROM {$this->table_prefix}postmeta WHERE post_id = p.ID AND meta_key = '_stock_status') as stock_status
                    FROM {$this->table_prefix}posts p
                    WHERE p.post_type = 'product'
                    ORDER BY p.ID DESC
                    LIMIT ? OFFSET ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener productos: " . $e->getMessage());
        }
    }

    // ==========================================
    // USERS (Usuarios de WordPress)
    // ==========================================

    /**
     * Obtiene un usuario por ID
     *
     * @param int $user_id ID del usuario
     * @return object|null Usuario encontrado o null
     */
    public function getUser($user_id)
    {
        try {
            $sql = "SELECT * FROM {$this->table_prefix}users WHERE ID = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id]);

            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        }
    }

    /**
     * Obtiene un usuario por email
     *
     * @param string $email Email del usuario
     * @return object|null Usuario encontrado o null
     */
    public function getUserByEmail($email)
    {
        try {
            $sql = "SELECT * FROM {$this->table_prefix}users WHERE user_email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);

            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        }
    }

    /**
     * Obtiene metadatos de usuario
     *
     * @param int $user_id ID del usuario
     * @param string $meta_key Clave del metadato
     * @return mixed Valor del metadato o null
     */
    public function getUserMeta($user_id, $meta_key)
    {
        try {
            $sql = "SELECT meta_value FROM {$this->table_prefix}usermeta
                    WHERE user_id = ? AND meta_key = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $meta_key]);

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ? $result->meta_value : null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usermeta: " . $e->getMessage());
        }
    }

    // ==========================================
    // QUERIES PERSONALIZADAS
    // ==========================================

    /**
     * Ejecuta una query SELECT personalizada
     *
     * @param string $sql Query SQL
     * @param array $params Parámetros para prepared statement
     * @param int $fetch_mode PDO::FETCH_* (por defecto PDO::FETCH_OBJ)
     * @return array Resultados de la query
     */
    public function query($sql, $params = [], $fetch_mode = PDO::FETCH_OBJ)
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll($fetch_mode);
        } catch (PDOException $e) {
            throw new Exception("Error en query: " . $e->getMessage());
        }
    }

    /**
     * Ejecuta una query de modificación (INSERT, UPDATE, DELETE)
     *
     * @param string $sql Query SQL
     * @param array $params Parámetros para prepared statement
     * @return int Número de filas afectadas
     */
    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error al ejecutar query: " . $e->getMessage());
        }
    }

    /**
     * Ejecuta múltiples queries en una transacción
     *
     * @param callable $callback Función con las operaciones a ejecutar
     * @return mixed Resultado del callback
     * @throws Exception Si hay error, hace rollback
     */
    public function transaction(callable $callback)
    {
        try {
            $this->db->beginTransaction();

            $result = $callback($this);

            $this->db->commit();
            return $result;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ==========================================
    // UTILIDADES
    // ==========================================

    /**
     * Sanitiza un valor para usar en queries
     * (Nota: Usar prepared statements es mejor)
     *
     * @param string $value Valor a sanitizar
     * @return string Valor sanitizado
     */
    public function escape($value)
    {
        return $this->db->quote($value);
    }

    /**
     * Obtiene el último ID insertado
     *
     * @return int Último ID insertado
     */
    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * Cuenta registros de una tabla
     *
     * @param string $table Nombre de la tabla (sin prefijo)
     * @param string $where Condición WHERE (opcional)
     * @param array $params Parámetros para la condición
     * @return int Número de registros
     */
    public function count($table, $where = '1=1', $params = [])
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table_prefix}{$table} WHERE {$where}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ? (int)$result->total : 0;
        } catch (PDOException $e) {
            throw new Exception("Error al contar registros: " . $e->getMessage());
        }
    }
}
