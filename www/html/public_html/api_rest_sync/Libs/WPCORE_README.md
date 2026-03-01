# Clase WpCore - WordPress con wp-load.php

Biblioteca para usar las funciones nativas de WordPress y WooCommerce mediante la carga del core completo de WordPress (`wp-load.php`).

## Tabla de Contenidos

- [Comparación: Wp vs WpCore](#comparación-wp-vs-wpcore)
- [Instalación](#instalación)
- [Uso Básico](#uso-básico)
- [API Reference](#api-reference)
  - [Posts](#posts)
  - [Postmeta](#postmeta)
  - [Options](#options)
  - [WooCommerce](#woocommerce)
  - [Usuarios](#usuarios)
  - [Taxonomías](#taxonomías)
  - [Utilidades](#utilidades)
- [Ejemplos Completos](#ejemplos-completos)
- [Mejores Prácticas](#mejores-prácticas)

---

## Comparación: Wp vs WpCore

| Característica | Wp (PDO directo) | WpCore (wp-load.php) |
|----------------|------------------|----------------------|
| **Velocidad** | ⭐⭐⭐⭐⭐ Muy rápido | ⭐⭐⭐☆☆ Más lento |
| **Memoria** | ⭐⭐⭐⭐⭐ Bajo consumo | ⭐⭐☆☆☆ Alto consumo |
| **Funciones WP** | ❌ No disponibles | ✅ Todas disponibles |
| **WooCommerce API** | ❌ Manual | ✅ Completa (WC_Product) |
| **Hooks/Filters** | ❌ No | ✅ Sí |
| **Plugins** | ❌ No | ✅ Sí |
| **Transacciones** | ✅ Manual | ⚠️ Depende de WP |
| **Complejidad** | Simple | Compleja |

### ¿Cuál Usar?

#### Usa **Wp** (PDO directo) cuando:
- ✅ Necesites máxima velocidad
- ✅ Operaciones simples (CRUD de posts/meta)
- ✅ API REST de alto tráfico
- ✅ Batch updates masivos
- ✅ Requieras control total de transacciones

#### Usa **WpCore** (wp-load.php) cuando:
- ✅ Necesites funciones específicas de WordPress
- ✅ Trabajes con WooCommerce API (WC_Product)
- ✅ Requieras hooks y filters
- ✅ Necesites funciones de plugins instalados
- ✅ Operaciones complejas con lógica de negocio

---

## Instalación

La clase ya está incluida. Solo importa:

```php
use Libs\WpCore;
```

---

## Uso Básico

```php
<?php
use Libs\WpCore;

try {
    // Opción 1: Detección automática
    $wpcore = new WpCore();

    // Opción 2: Ruta manual
    $wpcore = new WpCore('/ruta/absoluta/wp-load.php');

    // Verificar que WordPress se cargó
    if ($wpcore->isLoaded()) {
        echo "WordPress cargado correctamente";
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
```

---

## API Reference

### Posts

#### `getPost($post_id, $output)`
Obtiene un post usando `get_post()`.

```php
// Como objeto (por defecto)
$post = $wpcore->getPost(123);
echo $post->post_title;

// Como array asociativo
$post = $wpcore->getPost(123, ARRAY_A);
echo $post['post_title'];
```

#### `getPosts($args)`
Obtiene posts usando `get_posts()` con argumentos avanzados.

```php
$args = [
    'post_type' => 'product',
    'posts_per_page' => 10,
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => [
        [
            'key' => '_stock',
            'value' => 0,
            'compare' => '>'
        ]
    ]
];

$productos = $wpcore->getPosts($args);
```

#### `insertPost($post_data)`
Crea un post usando `wp_insert_post()`.

```php
$post_data = [
    'post_title' => 'Nuevo Producto',
    'post_content' => 'Descripción...',
    'post_status' => 'publish',
    'post_type' => 'product',
    'post_author' => 1
];

$post_id = $wpcore->insertPost($post_data);

if (is_wp_error($post_id)) {
    echo "Error: " . $post_id->get_error_message();
}
```

#### `updatePost($post_data)`
Actualiza un post usando `wp_update_post()`.

```php
$post_data = [
    'ID' => 123,
    'post_title' => 'Título Actualizado'
];

$wpcore->updatePost($post_data);
```

#### `deletePost($post_id, $force_delete)`
Elimina un post usando `wp_delete_post()`.

```php
// Mover a papelera
$wpcore->deletePost(123, false);

// Eliminar permanentemente
$wpcore->deletePost(123, true);
```

---

### Postmeta

#### `getPostMeta($post_id, $meta_key, $single)`
Obtiene metadatos usando `get_post_meta()`.

```php
// Obtener un meta específico (valor único)
$sku = $wpcore->getPostMeta(123, '_sku', true);

// Obtener todos los metas del post
$all_meta = $wpcore->getPostMeta(123, '', false);

// Obtener meta que puede tener múltiples valores
$imagenes = $wpcore->getPostMeta(123, '_product_images', false);
```

#### `updatePostMeta($post_id, $meta_key, $meta_value, $prev_value)`
Actualiza metadatos usando `update_post_meta()`.

```php
// Actualizar o crear
$wpcore->updatePostMeta(123, '_sku', 'ABC123');

// Actualizar solo si tiene valor anterior específico
$wpcore->updatePostMeta(123, '_stock', 50, 30);
```

#### `addPostMeta($post_id, $meta_key, $meta_value, $unique)`
Añade metadatos usando `add_post_meta()`.

```php
// Añadir sin reemplazar existente
$wpcore->addPostMeta(123, '_gallery_image', 'img1.jpg', false);
$wpcore->addPostMeta(123, '_gallery_image', 'img2.jpg', false);

// Añadir solo si no existe (unique)
$wpcore->addPostMeta(123, '_sku', 'ABC123', true);
```

---

### Options

#### `getOption($option_name, $default)`
Obtiene opciones usando `get_option()`.

```php
// Con valor por defecto
$nombre_sitio = $wpcore->getOption('blogname', 'Mi Sitio');

// Opción personalizada
$tipo_cambio = $wpcore->getOption('solu_exchange_rate', 3.80);
```

#### `updateOption($option_name, $option_value, $autoload)`
Actualiza opciones usando `update_option()`.

```php
// Actualizar opción
$wpcore->updateOption('solu_exchange_rate', 3.85);

// Con autoload especificado
$wpcore->updateOption('mi_opcion', 'valor', 'no');
```

---

### WooCommerce

#### `getProduct($product)`
Obtiene un producto usando `wc_get_product()`.

```php
$producto = $wpcore->getProduct(123);

// WC_Product tiene muchos métodos útiles
echo $producto->get_name();
echo $producto->get_sku();
echo $producto->get_price();
echo $producto->get_stock_quantity();
echo $producto->is_on_sale();
```

#### `getProductBySku($sku)`
Busca un producto por SKU.

```php
$producto = $wpcore->getProductBySku('ABC123');

if ($producto) {
    echo "Producto: {$producto->get_name()}";
    echo "Precio: {$producto->get_price()}";
    echo "Stock: {$producto->get_stock_quantity()}";
}
```

#### `updateProductStock($product_id, $stock_quantity, $operation)`
Actualiza el stock usando WooCommerce.

```php
// Establecer stock exacto
$wpcore->updateProductStock(123, 50, 'set');

// Incrementar stock
$wpcore->updateProductStock(123, 10, 'increase');

// Decrementar stock
$wpcore->updateProductStock(123, 5, 'decrease');
```

#### `updateProductPrice($product_id, $price, $sale_price)`
Actualiza el precio usando WooCommerce.

```php
// Solo precio regular
$wpcore->updateProductPrice(123, 99.99);

// Con precio de oferta
$wpcore->updateProductPrice(123, 99.99, 79.99);
```

#### `createProduct($product_data)`
Crea un producto nuevo.

```php
$product_data = [
    'name' => 'Laptop HP 15',
    'sku' => 'LAPTOP-HP-15',
    'price' => 2499.99,
    'stock' => 10,
    'description' => 'Descripción completa',
    'short_description' => 'Descripción corta'
];

$product_id = $wpcore->createProduct($product_data);
```

---

### Usuarios

#### `getUser($user_id)`
Obtiene un usuario usando `get_userdata()`.

```php
$usuario = $wpcore->getUser(1);

echo $usuario->user_login;
echo $usuario->user_email;
echo $usuario->display_name;
print_r($usuario->roles);
```

#### `getUserByEmail($email)`
Busca usuario por email usando `get_user_by()`.

```php
$usuario = $wpcore->getUserByEmail('usuario@example.com');

if ($usuario) {
    echo "Usuario encontrado: {$usuario->display_name}";
}
```

#### `getUserMeta($user_id, $meta_key, $single)`
Obtiene metadatos de usuario.

```php
$nombre = $wpcore->getUserMeta(1, 'first_name');
$apellido = $wpcore->getUserMeta(1, 'last_name');
$telefono = $wpcore->getUserMeta(1, 'billing_phone');
```

---

### Taxonomías

#### `getTerms($args)`
Obtiene términos usando `get_terms()`.

```php
$args = [
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    'orderby' => 'name'
];

$categorias = $wpcore->getTerms($args);

foreach ($categorias as $categoria) {
    echo "{$categoria->name} ({$categoria->count} productos)\n";
}
```

#### `getProductCategories($args)`
Obtiene categorías de producto.

```php
$categorias = $wpcore->getProductCategories([
    'hide_empty' => false
]);

foreach ($categorias as $cat) {
    echo "ID: {$cat->term_id} - {$cat->name}\n";
}
```

---

### Utilidades

#### `call($function_name, $args)`
Ejecuta cualquier función de WordPress.

```php
// Sanitizar texto
$limpio = $wpcore->call('sanitize_text_field', ['<script>test</script>']);

// Generar URL
$url = $wpcore->call('home_url', ['/productos']);

// Formatear fecha
$fecha = $wpcore->call('date_i18n', ['F j, Y', time()]);

// Obtener permalink
$permalink = $wpcore->call('get_permalink', [123]);
```

#### `doAction($hook_name, ...$args)`
Ejecuta un action/hook de WordPress.

```php
// Ejecutar hook personalizado
$wpcore->doAction('mi_hook_personalizado', $data);

// Hook de WordPress
$wpcore->doAction('wp_trash_post', 123);
```

#### `applyFilters($filter_name, $value, ...$args)`
Aplica un filter de WordPress.

```php
// Filtrar contenido
$contenido = "Texto con HTML";
$contenido_filtrado = $wpcore->applyFilters('the_content', $contenido);

// Filter personalizado
$precio = 99.99;
$precio_filtrado = $wpcore->applyFilters('mi_filtro_precio', $precio);
```

#### `isWooCommerceActive()`
Verifica si WooCommerce está activo.

```php
if ($wpcore->isWooCommerceActive()) {
    echo "WooCommerce versión: " . $wpcore->getWooCommerceVersion();
}
```

#### `getWordPressVersion()`
Obtiene la versión de WordPress.

```php
echo "WordPress: " . $wpcore->getWordPressVersion();
```

---

## Ejemplos Completos

### Ejemplo 1: Sincronización con WooCommerce API

```php
use Libs\WpCore;

function sincronizarProductoWC($sku, $datos_erp) {
    $wpcore = new WpCore();

    try {
        // Buscar producto por SKU
        $producto = $wpcore->getProductBySku($sku);

        if (!$producto) {
            return ['error' => 'Producto no encontrado'];
        }

        // Actualizar usando métodos de WooCommerce
        $wpcore->updateProductStock($producto->get_id(), $datos_erp['stock']);
        $wpcore->updateProductPrice($producto->get_id(), $datos_erp['precio']);

        // También podemos actualizar otros datos
        $producto_obj = $wpcore->getProduct($producto->get_id());
        $producto_obj->set_name($datos_erp['nombre']);
        $producto_obj->set_description($datos_erp['descripcion']);
        $producto_obj->save();

        return [
            'success' => true,
            'product_id' => $producto->get_id(),
            'stock' => $datos_erp['stock'],
            'precio' => $datos_erp['precio']
        ];

    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Uso
$resultado = sincronizarProductoWC('ABC123', [
    'stock' => 50,
    'precio' => 99.99,
    'nombre' => 'Laptop HP 15',
    'descripcion' => 'Laptop de última generación'
]);
```

### Ejemplo 2: Crear Producto Completo

```php
use Libs\WpCore;

function crearProductoCompleto($datos) {
    $wpcore = new WpCore();

    try {
        // Crear producto básico
        $product_data = [
            'name' => $datos['nombre'],
            'sku' => $datos['sku'],
            'price' => $datos['precio'],
            'stock' => $datos['stock'],
            'description' => $datos['descripcion'],
            'short_description' => $datos['descripcion_corta']
        ];

        $product_id = $wpcore->createProduct($product_data);

        if (is_wp_error($product_id)) {
            throw new Exception($product_id->get_error_message());
        }

        // Obtener producto para configuración avanzada
        $producto = $wpcore->getProduct($product_id);

        // Asignar categorías
        if (isset($datos['categorias'])) {
            $producto->set_category_ids($datos['categorias']);
        }

        // Configurar envío
        if (isset($datos['peso'])) {
            $producto->set_weight($datos['peso']);
        }

        if (isset($datos['dimensiones'])) {
            $producto->set_length($datos['dimensiones']['largo']);
            $producto->set_width($datos['dimensiones']['ancho']);
            $producto->set_height($datos['dimensiones']['alto']);
        }

        // Guardar cambios
        $producto->save();

        return [
            'success' => true,
            'product_id' => $product_id,
            'permalink' => $producto->get_permalink()
        ];

    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}
```

### Ejemplo 3: Búsqueda Avanzada con Meta Query

```php
use Libs\WpCore;

function buscarProductosEnOferta() {
    $wpcore = new WpCore();

    $args = [
        'post_type' => 'product',
        'posts_per_page' => 50,
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
        ],
        'orderby' => 'meta_value_num',
        'meta_key' => '_sale_price',
        'order' => 'ASC'
    ];

    $posts = $wpcore->getPosts($args);

    $productos_oferta = [];
    foreach ($posts as $post) {
        $producto = $wpcore->getProduct($post->ID);

        $productos_oferta[] = [
            'id' => $producto->get_id(),
            'name' => $producto->get_name(),
            'sku' => $producto->get_sku(),
            'regular_price' => $producto->get_regular_price(),
            'sale_price' => $producto->get_sale_price(),
            'descuento' => round((1 - ($producto->get_sale_price() / $producto->get_regular_price())) * 100, 2),
            'stock' => $producto->get_stock_quantity(),
            'permalink' => $producto->get_permalink()
        ];
    }

    return $productos_oferta;
}
```

---

## Mejores Prácticas

### ✅ DO (Hacer)

1. **Manejar excepciones al cargar WordPress**
   ```php
   try {
       $wpcore = new WpCore();
   } catch (Exception $e) {
       // Manejar error
   }
   ```

2. **Verificar que WooCommerce está activo antes de usarlo**
   ```php
   if ($wpcore->isWooCommerceActive()) {
       $producto = $wpcore->getProduct(123);
   }
   ```

3. **Verificar errores de WP_Error**
   ```php
   $post_id = $wpcore->insertPost($data);
   if (is_wp_error($post_id)) {
       echo $post_id->get_error_message();
   }
   ```

4. **Usar métodos de WC_Product para operaciones complejas**
   ```php
   $producto = $wpcore->getProduct(123);
   $producto->set_stock_quantity(50);
   $producto->set_price(99.99);
   $producto->save();
   ```

### ❌ DON'T (Evitar)

1. **NO usar WpCore para operaciones simples**
   ```php
   // ❌ Overhead innecesario
   $wpcore = new WpCore();
   $sku = $wpcore->getPostMeta(123, '_sku');

   // ✅ Mejor usar Wp (PDO directo)
   $wp = new Wp();
   $sku = $wp->getPostMeta(123, '_sku');
   ```

2. **NO cargar WordPress múltiples veces**
   ```php
   // ❌ Carga WordPress cada vez
   for ($i = 0; $i < 100; $i++) {
       $wpcore = new WpCore();
       // ...
   }

   // ✅ Cargar una sola vez
   $wpcore = new WpCore();
   for ($i = 0; $i < 100; $i++) {
       // ...
   }
   ```

3. **NO mezclar PDO directo con WpCore para mismo recurso**
   ```php
   // ❌ Mezclar puede causar inconsistencias
   $wp = new Wp();
   $wpcore = new WpCore();

   $wp->updateProductStock(123, 50);
   $wpcore->updateProductPrice(123, 99.99);

   // ✅ Usar uno solo
   $wpcore->updateProductStock(123, 50);
   $wpcore->updateProductPrice(123, 99.99);
   ```

---

## Performance

### Comparación de Tiempos

| Operación | Wp (PDO) | WpCore (wp-load) |
|-----------|----------|------------------|
| Inicialización | ~1ms | ~100-200ms |
| Get Post | ~2ms | ~5ms |
| Update Meta | ~3ms | ~8ms |
| Get Product | ~5ms | ~10ms |
| Update Stock | ~10ms | ~15ms |
| 100 productos | ~500ms | ~1500ms |

### Consumo de Memoria

| Método | Memoria |
|--------|---------|
| Wp (PDO) | ~2-5 MB |
| WpCore (wp-load) | ~40-60 MB |

### Recomendaciones

- ✅ **Usa WpCore** para: Operaciones complejas, hooks, WooCommerce API completa
- ✅ **Usa Wp** para: Operaciones masivas, alta frecuencia, máxima velocidad

---

## Diferencias con Wp

### Método: Actualizar Stock

#### Wp (PDO directo)
```php
$wp = new Wp();
$wp->updateProductStock(123, 50);
// 3 queries directas
// Sin hooks de WordPress
// Sin validaciones de WooCommerce
```

#### WpCore (WordPress completo)
```php
$wpcore = new WpCore();
$wpcore->updateProductStock(123, 50);
// Usa WC_Product->set_stock_quantity()
// Ejecuta hooks: woocommerce_product_set_stock
// Validaciones automáticas de WooCommerce
// Actualiza caché de WooCommerce
```

---

## Archivo de Ejemplos

Revisa [WpCoreExamples.php](WpCoreExamples.php) para ver 18+ ejemplos completos.

---

## Troubleshooting

### Error: "No se encontró wp-load.php"

**Solución 1:** Especifica la ruta manualmente
```php
$wpcore = new WpCore('/ruta/absoluta/wp-load.php');
```

**Solución 2:** Verifica que wp-load.php existe
```bash
ls /ruta/al/wordpress/wp-load.php
```

### Error: "Headers already sent"

**Causa:** WordPress intenta enviar headers pero ya se enviaron desde tu API.

**Solución:** WpCore usa output buffering automáticamente, pero verifica que no haya output antes.

### Error: "WooCommerce no está activo"

**Solución:** Verifica que WooCommerce está instalado y activo
```php
if ($wpcore->isWooCommerceActive()) {
    // Usar funciones de WooCommerce
}
```

---

## Soporte

¿Tienes preguntas? Contacta al equipo de desarrollo.

**Versión:** 1.0.0
**Fecha:** 2025-11-10
