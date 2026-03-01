# Clase Wp - Helper para WordPress/WooCommerce

Biblioteca para ejecutar operaciones de base de datos de WordPress/WooCommerce desde tu API REST sin cargar el core de WordPress.

## Tabla de Contenidos

- [Instalación](#instalación)
- [Uso Básico](#uso-básico)
- [API Reference](#api-reference)
  - [Posts](#posts)
  - [Postmeta](#postmeta)
  - [Options](#options)
  - [Productos WooCommerce](#productos-woocommerce)
  - [Usuarios](#usuarios)
  - [Queries Personalizadas](#queries-personalizadas)
  - [Transacciones](#transacciones)
- [Ejemplos Completos](#ejemplos-completos)
- [Mejores Prácticas](#mejores-prácticas)

---

## Instalación

La clase ya está incluida en tu proyecto. Solo necesitas importarla:

```php
use Libs\Wp;
```

---

## Uso Básico

```php
<?php
use Libs\Wp;

// Inicializar
$wp = new Wp();

// Si usas un prefijo personalizado de tablas
$wp = new Wp('custom_prefix_');

// Ejemplo: Obtener un post
$post = $wp->getPost(123);
echo $post->post_title;
```

---

## API Reference

### Posts

#### `getPost($post_id)`
Obtiene un post por ID.

```php
$post = $wp->getPost(123);

// Resultado:
// object {
//   ID: 123,
//   post_title: "Título del post",
//   post_content: "Contenido...",
//   post_type: "product",
//   post_status: "publish"
// }
```

#### `getPostsByType($post_type, $limit, $offset)`
Obtiene posts por tipo (product, page, post, etc.).

```php
// Obtener 50 productos desde el inicio
$productos = $wp->getPostsByType('product', 50, 0);

// Obtener todos los posts (sin límite)
$posts = $wp->getPostsByType('post', 0, 0);

// Paginación: página 2, 20 por página
$productos_p2 = $wp->getPostsByType('product', 20, 20);
```

#### `updatePostTitle($post_id, $title)`
Actualiza el título de un post.

```php
$actualizado = $wp->updatePostTitle(123, "Nuevo título");
// Retorna: true si se actualizó
```

#### `updatePostContent($post_id, $content)`
Actualiza el contenido de un post.

```php
$actualizado = $wp->updatePostContent(123, "Nuevo contenido HTML");
```

---

### Postmeta

#### `getPostMeta($post_id, $meta_key)`
Obtiene un metadato específico.

```php
$sku = $wp->getPostMeta(123, '_sku');
// Retorna: "ABC123" o null si no existe
```

#### `getAllPostMeta($post_id)`
Obtiene todos los metadatos de un post.

```php
$meta = $wp->getAllPostMeta(123);

// Resultado:
// [
//   '_sku' => 'ABC123',
//   '_price' => '99.99',
//   '_stock' => '50',
//   '_stock_status' => 'instock'
// ]
```

#### `updatePostMeta($post_id, $meta_key, $meta_value)`
Actualiza o crea un metadato.

```php
// Si no existe, lo crea (INSERT)
// Si existe, lo actualiza (UPDATE)
$wp->updatePostMeta(123, '_custom_field', 'Valor personalizado');
```

#### `deletePostMeta($post_id, $meta_key)`
Elimina un metadato.

```php
$eliminado = $wp->deletePostMeta(123, '_custom_field');
```

---

### Options

#### `getOption($option_name)`
Obtiene una opción de WordPress.

```php
$nombre_sitio = $wp->getOption('blogname');
$tipo_cambio = $wp->getOption('solu_exchange_rate');
```

#### `updateOption($option_name, $option_value, $autoload)`
Actualiza o crea una opción.

```php
// Actualizar tipo de cambio
$wp->updateOption('solu_exchange_rate', 3.85);

// Crear opción que no se carga automáticamente
$wp->updateOption('mi_opcion', 'valor', 'no');
```

#### `deleteOption($option_name)`
Elimina una opción.

```php
$eliminado = $wp->deleteOption('opcion_temporal');
```

---

### Productos WooCommerce

#### `getProductBySku($sku)`
Busca un producto por SKU.

```php
$producto = $wp->getProductBySku('ABC123');

if ($producto) {
    echo "ID: {$producto->ID}";
    echo "Título: {$producto->post_title}";
}
```

#### `getProductStock($product_id)`
Obtiene el stock actual de un producto.

```php
$stock = $wp->getProductStock(123);
// Retorna: int (ej: 50)
```

#### `getProductPrice($product_id)`
Obtiene el precio actual de un producto.

```php
$precio = $wp->getProductPrice(123);
// Retorna: float (ej: 99.99)
```

#### `updateProductStock($product_id, $stock)` ⭐
Actualiza el stock de un producto (CON TRANSACCIÓN).

**Actualiza 3 tablas automáticamente:**
1. `wp_postmeta` → `_stock`
2. `wp_postmeta` → `_stock_status`
3. `wp_wc_product_meta_lookup` → `stock_quantity` + `stock_status`

```php
try {
    // Si stock > 0: stock_status = 'instock'
    // Si stock = 0: stock_status = 'outofstock'
    $wp->updateProductStock(123, 50);

    echo "Stock actualizado correctamente";

} catch (Exception $e) {
    // Si falla, ROLLBACK automático
    echo "Error: " . $e->getMessage();
}
```

#### `updateProductPrice($product_id, $price)` ⭐
Actualiza el precio de un producto (CON TRANSACCIÓN).

**Actualiza 3 tablas automáticamente:**
1. `wp_postmeta` → `_price`
2. `wp_postmeta` → `_regular_price`
3. `wp_wc_product_meta_lookup` → `min_price` + `max_price`

```php
try {
    $wp->updateProductPrice(123, 99.99);
    echo "Precio actualizado correctamente";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

#### `getProductsWithMeta($limit, $offset)`
Obtiene productos con sus metadatos principales (optimizado).

```php
$productos = $wp->getProductsWithMeta(100, 0);

foreach ($productos as $producto) {
    echo "ID: {$producto->ID}\n";
    echo "Título: {$producto->post_title}\n";
    echo "SKU: {$producto->sku}\n";
    echo "Precio: {$producto->price}\n";
    echo "Stock: {$producto->stock}\n";
    echo "Estado: {$producto->stock_status}\n";
}
```

---

### Usuarios

#### `getUser($user_id)`
Obtiene un usuario por ID.

```php
$usuario = $wp->getUser(1);
echo $usuario->user_login;
echo $usuario->user_email;
```

#### `getUserByEmail($email)`
Obtiene un usuario por email.

```php
$usuario = $wp->getUserByEmail('usuario@example.com');
```

#### `getUserMeta($user_id, $meta_key)`
Obtiene un metadato de usuario.

```php
$nombre = $wp->getUserMeta(1, 'first_name');
$apellido = $wp->getUserMeta(1, 'last_name');
```

---

### Queries Personalizadas

#### `query($sql, $params, $fetch_mode)`
Ejecuta una query SELECT personalizada.

```php
$sql = "SELECT p.ID, p.post_title, pm.meta_value as sku
        FROM wp_posts p
        INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
        WHERE p.post_type = ?
        AND pm.meta_key = ?
        LIMIT ?";

$params = ['product', '_sku', 10];

$resultados = $wp->query($sql, $params);

foreach ($resultados as $row) {
    echo "{$row->ID}: {$row->post_title} ({$row->sku})\n";
}
```

**Fetch modes disponibles:**
- `PDO::FETCH_OBJ` (por defecto) - Objetos
- `PDO::FETCH_ASSOC` - Arrays asociativos
- `PDO::FETCH_ARRAY` - Arrays numéricos

#### `execute($sql, $params)`
Ejecuta una query de modificación (INSERT, UPDATE, DELETE).

```php
$sql = "UPDATE wp_postmeta
        SET meta_value = ?
        WHERE post_id = ? AND meta_key = ?";

$params = ['Nuevo valor', 123, '_custom_field'];

$filas_afectadas = $wp->execute($sql, $params);
echo "Filas afectadas: {$filas_afectadas}";
```

---

### Transacciones

#### `transaction(callable $callback)`
Ejecuta múltiples operaciones en una transacción.

**Si cualquier operación falla, ROLLBACK automático.**

```php
try {
    $resultado = $wp->transaction(function($wp) {
        // Actualizar múltiples productos
        $wp->updateProductStock(123, 50);
        $wp->updateProductPrice(123, 99.99);

        $wp->updateProductStock(124, 30);
        $wp->updateProductPrice(124, 79.99);

        return "2 productos actualizados";
    });

    echo $resultado; // "2 productos actualizados"

} catch (Exception $e) {
    // Si falla cualquier UPDATE, ninguno se aplica
    echo "Error: " . $e->getMessage();
}
```

**Ejemplo avanzado:**

```php
$productos = [
    ['id' => 123, 'stock' => 50, 'precio' => 99.99],
    ['id' => 124, 'stock' => 30, 'precio' => 79.99]
];

$wp->transaction(function($wp) use ($productos) {
    foreach ($productos as $producto) {
        $wp->updateProductStock($producto['id'], $producto['stock']);
        $wp->updateProductPrice($producto['id'], $producto['precio']);
    }
});
```

---

### Utilidades

#### `count($table, $where, $params)`
Cuenta registros de una tabla.

```php
// Contar productos publicados
$total = $wp->count('posts', "post_type = ? AND post_status = ?", ['product', 'publish']);

// Contar todos los posts
$total = $wp->count('posts');
```

#### `lastInsertId()`
Obtiene el último ID insertado.

```php
$wp->execute("INSERT INTO wp_posts ...");
$nuevo_id = $wp->lastInsertId();
```

#### `escape($value)`
Sanitiza un valor (usar solo si no puedes usar prepared statements).

```php
$valor_escapado = $wp->escape($valor_usuario);
```

---

## Ejemplos Completos

### Ejemplo 1: Sincronización desde ERP

```php
use Libs\Wp;

function sincronizarProducto($sku, $stock, $precio) {
    $wp = new Wp();

    try {
        // Buscar producto
        $producto = $wp->getProductBySku($sku);

        if (!$producto) {
            return ['error' => 'Producto no encontrado'];
        }

        // Actualizar en transacción
        $wp->transaction(function($wp) use ($producto, $stock, $precio) {
            $wp->updateProductStock($producto->ID, $stock);
            $wp->updateProductPrice($producto->ID, $precio);
        });

        return [
            'success' => true,
            'product_id' => $producto->ID,
            'stock' => $stock,
            'precio' => $precio
        ];

    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Uso
$resultado = sincronizarProducto('ABC123', 50, 99.99);
```

### Ejemplo 2: Endpoint de API

```php
use Libs\Wp;

class ProductController {
    private $wp;

    public function __construct() {
        $this->wp = new Wp();
    }

    public function updateMultipleProducts() {
        $postBody = file_get_contents("php://input");
        $productos = json_decode($postBody);

        try {
            $resultados = [];

            foreach ($productos as $producto) {
                // Buscar por SKU
                $prod = $this->wp->getProductBySku($producto->sku);

                if ($prod) {
                    // Actualizar
                    $this->wp->updateProductStock($prod->ID, $producto->stock);
                    $resultados[] = [
                        'sku' => $producto->sku,
                        'status' => 'ok'
                    ];
                } else {
                    $resultados[] = [
                        'sku' => $producto->sku,
                        'status' => 'not_found'
                    ];
                }
            }

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'resultados' => $resultados
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
```

### Ejemplo 3: Reportes

```php
use Libs\Wp;

function reporteProductosSinStock() {
    $wp = new Wp();

    $sql = "SELECT p.ID, p.post_title,
                   (SELECT meta_value FROM wp_postmeta WHERE post_id = p.ID AND meta_key = '_sku') as sku,
                   (SELECT meta_value FROM wp_postmeta WHERE post_id = p.ID AND meta_key = '_stock') as stock
            FROM wp_posts p
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            HAVING stock = 0 OR stock IS NULL";

    $productos = $wp->query($sql);

    return $productos;
}
```

---

## Mejores Prácticas

### ✅ DO (Hacer)

1. **Usar transacciones para operaciones múltiples**
   ```php
   $wp->transaction(function($wp) {
       $wp->updateProductStock(123, 50);
       $wp->updateProductPrice(123, 99.99);
   });
   ```

2. **Usar prepared statements**
   ```php
   $wp->query("SELECT * FROM wp_posts WHERE ID = ?", [123]);
   ```

3. **Manejar excepciones**
   ```php
   try {
       $wp->updateProductStock(123, 50);
   } catch (Exception $e) {
       // Manejar error
   }
   ```

4. **Usar métodos específicos cuando existan**
   ```php
   // ✅ Usar método específico
   $wp->updateProductStock(123, 50);

   // ❌ Evitar query manual
   $wp->execute("UPDATE wp_postmeta SET meta_value = ? ...", [50]);
   ```

### ❌ DON'T (Evitar)

1. **NO concatenar variables en SQL**
   ```php
   // ❌ NUNCA hacer esto (SQL Injection)
   $sql = "SELECT * FROM wp_posts WHERE ID = $id";

   // ✅ Usar prepared statements
   $sql = "SELECT * FROM wp_posts WHERE ID = ?";
   $wp->query($sql, [$id]);
   ```

2. **NO olvidar transacciones en operaciones múltiples**
   ```php
   // ❌ Sin transacción (puede quedar inconsistente)
   $wp->updateProductStock(123, 50);
   $wp->updateProductPrice(123, 99.99);

   // ✅ Con transacción
   $wp->transaction(function($wp) {
       $wp->updateProductStock(123, 50);
       $wp->updateProductPrice(123, 99.99);
   });
   ```

3. **NO usar escape() en lugar de prepared statements**
   ```php
   // ❌ Menos seguro
   $valor = $wp->escape($user_input);
   $sql = "SELECT * FROM wp_posts WHERE title = $valor";

   // ✅ Más seguro
   $sql = "SELECT * FROM wp_posts WHERE title = ?";
   $wp->query($sql, [$user_input]);
   ```

---

## Diferencias con ProductModel Actual

### ProductModel Antiguo ❌

```php
// Sin transacciones
public function updateProductStockModel($stock, $cod_prod, $post_id) {
    // UPDATE 1
    $stmt = $this->db->prepare("UPDATE ...");
    $stmt->execute([...]);

    // UPDATE 2
    $stmt = $this->db->prepare("UPDATE ...");
    $stmt->execute([...]);

    // UPDATE 3
    $stmt = $this->db->prepare("UPDATE ...");
    $stmt->execute([...]);

    // Si falla el UPDATE 3, los UPDATE 1 y 2 ya se aplicaron ❌
}
```

### Clase Wp Nueva ✅

```php
// CON transacciones
public function updateProductStock($product_id, $stock) {
    try {
        $this->db->beginTransaction();

        // UPDATE 1
        $this->updatePostMeta($product_id, '_stock', $stock);

        // UPDATE 2
        $this->updatePostMeta($product_id, '_stock_status', $stock_status);

        // UPDATE 3
        $stmt = $this->db->prepare("UPDATE wp_wc_product_meta_lookup ...");
        $stmt->execute([...]);

        $this->db->commit(); // ✅ Todo OK

    } catch (Exception $e) {
        $this->db->rollBack(); // ✅ Revertir TODO
        throw $e;
    }
}
```

---

## Performance

### Comparación de Métodos

| Método | Queries | Transacción | Performance |
|--------|---------|-------------|-------------|
| `updateProductStock()` | 3 | ✅ | ⭐⭐⭐⭐⭐ |
| `updateProductPrice()` | 3 | ✅ | ⭐⭐⭐⭐⭐ |
| `getProductsWithMeta()` | 1 | - | ⭐⭐⭐⭐⭐ |
| `getAllPostMeta()` | 1 | - | ⭐⭐⭐⭐☆ |
| `query()` | 1 | - | ⭐⭐⭐⭐⭐ |

### Tips de Performance

1. **Usar `getProductsWithMeta()` en lugar de loops**
   ```php
   // ❌ Lento (N+1 queries)
   $productos = $wp->getPostsByType('product', 100, 0);
   foreach ($productos as $producto) {
       $sku = $wp->getPostMeta($producto->ID, '_sku');
       $precio = $wp->getPostMeta($producto->ID, '_price');
   }

   // ✅ Rápido (1 query)
   $productos = $wp->getProductsWithMeta(100, 0);
   foreach ($productos as $producto) {
       echo $producto->sku;
       echo $producto->price;
   }
   ```

2. **Usar transacciones para batch updates**
   ```php
   $wp->transaction(function($wp) use ($productos) {
       foreach ($productos as $producto) {
           $wp->updateProductStock($producto->id, $producto->stock);
       }
   });
   ```

---

## Archivo de Ejemplos

Revisa [WpExamples.php](WpExamples.php) para ver 20+ ejemplos completos de uso.

---

## Soporte

¿Tienes preguntas? Contacta al equipo de desarrollo.

**Versión:** 1.0.0
**Fecha:** 2025-11-10
