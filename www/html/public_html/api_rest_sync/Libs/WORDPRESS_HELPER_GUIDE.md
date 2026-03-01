# Guía Completa: Helpers de WordPress para API REST

Esta guía te ayudará a elegir entre las dos clases disponibles para trabajar con WordPress desde tu API REST.

---

## 📦 Clases Disponibles

### 1. **Wp.php** - Acceso Directo PDO
- **Archivo:** [Wp.php](Wp.php)
- **Documentación:** [WP_README.md](WP_README.md)
- **Ejemplos:** [WpExamples.php](WpExamples.php)

### 2. **WpCore.php** - WordPress Completo
- **Archivo:** [WpCore.php](WpCore.php)
- **Documentación:** [WPCORE_README.md](WPCORE_README.md)
- **Ejemplos:** [WpCoreExamples.php](WpCoreExamples.php)

---

## 🎯 Comparación Rápida

| Aspecto | Wp (PDO) | WpCore (wp-load) |
|---------|----------|------------------|
| **Velocidad** | ⚡⚡⚡⚡⚡ | ⚡⚡⚡ |
| **Memoria** | 2-5 MB | 40-60 MB |
| **Funciones WP** | ❌ No | ✅ Todas |
| **WooCommerce API** | ❌ Manual | ✅ WC_Product |
| **Hooks/Filters** | ❌ No | ✅ Sí |
| **Transacciones** | ✅ Manual | ⚠️ Depende WP |
| **Complejidad** | Simple | Compleja |
| **Inicialización** | ~1ms | ~100-200ms |

---

## 🤔 ¿Cuál Usar?

### Usa **Wp** cuando:

✅ Necesites **máxima velocidad**
```php
// Operaciones rápidas de CRUD
$wp = new Wp();
$productos = $wp->getProductsWithMeta(100, 0); // 1 query optimizada
```

✅ **Actualizaciones masivas** (muchos registros)
```php
// Actualizar 1000 productos
$wp->transaction(function($wp) use ($productos) {
    foreach ($productos as $p) {
        $wp->updateProductStock($p['id'], $p['stock']);
    }
}); // ~5-10 segundos
```

✅ **API REST de alta frecuencia**
```php
// Endpoint llamado miles de veces/minuto
public function getStockBySku() {
    $wp = new Wp();
    $producto = $wp->getProductBySku($_GET['sku']);
    return $wp->getProductStock($producto->ID);
}
```

✅ **Control total de transacciones**
```php
$wp->transaction(function($wp) {
    $wp->updateProductStock(123, 50);
    $wp->updateProductPrice(123, 99.99);
    // Si falla cualquiera, ROLLBACK automático
});
```

### Usa **WpCore** cuando:

✅ Necesites **funciones específicas de WordPress**
```php
$wpcore = new WpCore();

// Usar funciones nativas
$permalink = $wpcore->call('get_permalink', [123]);
$sanitized = $wpcore->call('sanitize_text_field', [$input]);
```

✅ **WooCommerce API completa** (WC_Product)
```php
$producto = $wpcore->getProductBySku('ABC123');

// WC_Product tiene métodos muy útiles
$producto->get_name();
$producto->get_price();
$producto->is_on_sale();
$producto->get_permalink();
$producto->set_category_ids([15, 16]);
```

✅ **Hooks y Filters de WordPress**
```php
// Ejecutar hooks personalizados
$wpcore->doAction('mi_hook_personalizado', $data);

// Aplicar filters
$precio_filtrado = $wpcore->applyFilters('custom_price_filter', $precio);
```

✅ **Operaciones complejas con lógica de negocio**
```php
// Crear producto con todas las validaciones de WooCommerce
$wpcore->createProduct([
    'name' => 'Laptop',
    'sku' => 'LAP-001',
    'price' => 2499.99,
    'stock' => 10
]);
// Ejecuta hooks, validaciones, actualiza caché, etc.
```

---

## 📚 Guías de Inicio Rápido

### Quick Start: Wp (PDO)

```php
<?php
use Libs\Wp;

// 1. Inicializar
$wp = new Wp();

// 2. Obtener producto por SKU
$producto = $wp->getProductBySku('ABC123');

// 3. Actualizar stock (CON TRANSACCIÓN - 3 tablas)
$wp->updateProductStock($producto->ID, 50);

// 4. Actualizar precio (CON TRANSACCIÓN - 3 tablas)
$wp->updateProductPrice($producto->ID, 99.99);

// 5. Query personalizada
$resultados = $wp->query(
    "SELECT * FROM wp_posts WHERE post_type = ? LIMIT ?",
    ['product', 10]
);
```

### Quick Start: WpCore (WordPress)

```php
<?php
use Libs\WpCore;

// 1. Inicializar (carga WordPress)
$wpcore = new WpCore();

// 2. Obtener producto con WooCommerce API
$producto = $wpcore->getProductBySku('ABC123');

// 3. Usar métodos de WC_Product
echo $producto->get_name();
echo $producto->get_price();
echo $producto->is_on_sale() ? 'En oferta' : 'Precio normal';

// 4. Actualizar con WooCommerce
$wpcore->updateProductStock($producto->get_id(), 50);

// 5. Usar funciones nativas de WordPress
$permalink = $wpcore->call('get_permalink', [$producto->get_id()]);
```

---

## 💡 Casos de Uso Reales

### Caso 1: Sincronización Masiva desde ERP

**Mejor opción: Wp (PDO)** ⭐

**¿Por qué?**
- 1000+ productos a sincronizar
- Necesitas velocidad
- Operaciones simples (stock, precio)

```php
use Libs\Wp;

function sincronizarDesdeERP($productos_erp) {
    $wp = new Wp();

    return $wp->transaction(function($wp) use ($productos_erp) {
        $actualizados = 0;

        foreach ($productos_erp as $p) {
            $producto = $wp->getProductBySku($p['sku']);

            if ($producto) {
                $wp->updateProductStock($producto->ID, $p['stock']);
                $wp->updateProductPrice($producto->ID, $p['precio']);
                $actualizados++;
            }
        }

        return $actualizados;
    });
}

// Procesar 1000 productos en ~10-15 segundos
```

### Caso 2: Crear Producto con Categorías y Metadatos

**Mejor opción: WpCore (WordPress)** ⭐

**¿Por qué?**
- Necesitas asignar categorías
- Validaciones de WooCommerce
- Hooks automáticos

```php
use Libs\WpCore;

function crearProductoCompleto($datos) {
    $wpcore = new WpCore();

    // Crear producto
    $product_id = $wpcore->createProduct([
        'name' => $datos['nombre'],
        'sku' => $datos['sku'],
        'price' => $datos['precio'],
        'stock' => $datos['stock']
    ]);

    // Configurar producto
    $producto = $wpcore->getProduct($product_id);
    $producto->set_category_ids($datos['categorias']);
    $producto->set_weight($datos['peso']);
    $producto->save();

    return $product_id;
}
```

### Caso 3: Consultar Stock en Tiempo Real

**Mejor opción: Wp (PDO)** ⭐

**¿Por qué?**
- Llamado miles de veces/minuto
- Solo necesitas stock
- Máxima velocidad

```php
use Libs\Wp;

class StockController {
    private $wp;

    public function __construct() {
        $this->wp = new Wp();
    }

    public function getStock() {
        $sku = $_GET['sku'];
        $producto = $this->wp->getProductBySku($sku);

        return [
            'sku' => $sku,
            'stock' => $this->wp->getProductStock($producto->ID)
        ];
    }
}

// Respuesta en ~5-10ms
```

### Caso 4: Aplicar Descuento Dinámico con Filtros

**Mejor opción: WpCore (WordPress)** ⭐

**¿Por qué?**
- Necesitas hooks/filters
- Lógica de negocio compleja
- Integración con plugins

```php
use Libs\WpCore;

function aplicarDescuentoPersonalizado($product_id, $porcentaje) {
    $wpcore = new WpCore();

    $producto = $wpcore->getProduct($product_id);
    $precio_regular = $producto->get_regular_price();

    // Aplicar filter para descuentos personalizados
    $precio_final = $wpcore->applyFilters(
        'custom_discount_filter',
        $precio_regular * (1 - $porcentaje / 100),
        $product_id,
        $porcentaje
    );

    $wpcore->updateProductPrice($product_id, $precio_regular, $precio_final);

    // Ejecutar hook personalizado
    $wpcore->doAction('after_discount_applied', $product_id, $precio_final);
}
```

---

## 🔧 Mejores Prácticas Generales

### 1. Manejo de Errores

```php
// ✅ BUENO: Try-catch
try {
    $wp = new Wp();
    $wp->updateProductStock(123, 50);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    return ['error' => $e->getMessage()];
}

// ❌ MALO: Sin manejo
$wp = new Wp();
$wp->updateProductStock(123, 50); // Puede fallar silenciosamente
```

### 2. Validación de Entrada

```php
// ✅ BUENO: Validar antes de procesar
if (!isset($data->sku) || !is_numeric($data->stock)) {
    return ['error' => 'Datos inválidos'];
}

$wp = new Wp();
$producto = $wp->getProductBySku($data->sku);

// ❌ MALO: Sin validación
$producto = $wp->getProductBySku($data->sku); // $data->sku puede no existir
```

### 3. Uso de Transacciones

```php
// ✅ BUENO: Con transacción (Wp)
$wp->transaction(function($wp) {
    $wp->updateProductStock(123, 50);
    $wp->updateProductPrice(123, 99.99);
});

// ⚠️ ACEPTABLE: WC se encarga de integridad (WpCore)
$wpcore->updateProductStock(123, 50);
$wpcore->updateProductPrice(123, 99.99);

// ❌ MALO: Sin transacción (Wp)
$wp->updateProductStock(123, 50);
$wp->updateProductPrice(123, 99.99); // Si falla, stock ya cambió
```

### 4. Reutilización de Instancia

```php
// ✅ BUENO: Una instancia
$wp = new Wp();
for ($i = 0; $i < 100; $i++) {
    $wp->updateProductStock($productos[$i]['id'], $productos[$i]['stock']);
}

// ❌ MALO: Múltiples instancias
for ($i = 0; $i < 100; $i++) {
    $wp = new Wp(); // Sobrecarga innecesaria
    $wp->updateProductStock($productos[$i]['id'], $productos[$i]['stock']);
}
```

---

## 📊 Tabla de Decisión

| Necesitas... | Usa | Razón |
|-------------|-----|-------|
| Máxima velocidad | Wp | PDO directo es más rápido |
| Funciones de WordPress | WpCore | Carga core completo |
| WC_Product API | WpCore | Métodos nativos de WooCommerce |
| Transacciones manuales | Wp | Control total con PDO |
| Hooks/Filters | WpCore | Requiere WordPress cargado |
| Batch updates (1000+) | Wp | Más eficiente para volumen |
| Crear productos complejos | WpCore | Validaciones automáticas |
| API alta frecuencia | Wp | Menor overhead |
| Integraciones con plugins | WpCore | Plugins requieren WP cargado |
| Consultas personalizadas | Wp | SQL directo más flexible |

---

## 📖 Recursos

### Documentación Completa

1. **Wp (PDO Directo)**
   - [WP_README.md](WP_README.md) - Documentación completa
   - [WpExamples.php](WpExamples.php) - 20+ ejemplos

2. **WpCore (WordPress Completo)**
   - [WPCORE_README.md](WPCORE_README.md) - Documentación completa
   - [WpCoreExamples.php](WpCoreExamples.php) - 18+ ejemplos

### API Reference Rápida

#### Wp - Métodos Principales
```php
$wp = new Wp();

// Posts
$wp->getPost($id);
$wp->getPostsByType('product', 50, 0);
$wp->updatePostTitle($id, 'Título');

// Postmeta
$wp->getPostMeta($id, '_sku');
$wp->updatePostMeta($id, '_sku', 'ABC123');

// WooCommerce
$wp->getProductBySku('ABC123');
$wp->updateProductStock($id, 50);
$wp->updateProductPrice($id, 99.99);
$wp->getProductsWithMeta(100, 0);

// Queries
$wp->query($sql, $params);
$wp->execute($sql, $params);
$wp->transaction(function($wp) { ... });
```

#### WpCore - Métodos Principales
```php
$wpcore = new WpCore();

// Posts (funciones nativas)
$wpcore->getPost($id);
$wpcore->getPosts($args);
$wpcore->insertPost($data);
$wpcore->updatePost($data);

// WooCommerce (API completa)
$wpcore->getProduct($id);
$wpcore->getProductBySku('ABC123');
$wpcore->updateProductStock($id, 50, 'set');
$wpcore->updateProductPrice($id, 99.99, 79.99);
$wpcore->createProduct($data);

// WordPress
$wpcore->call('function_name', $args);
$wpcore->doAction('hook_name', $data);
$wpcore->applyFilters('filter_name', $value);
$wpcore->isWooCommerceActive();
```

---

## 🎓 Ejemplos de Integración en Controllers

### Controller con Wp

```php
<?php
namespace Controllers;

use Libs\Wp;
use Libs\UtilHelper;

class ProductSyncController {
    private $wp;

    public function __construct() {
        $this->wp = new Wp();
    }

    public function syncFromERP() {
        $postBody = file_get_contents("php://input");
        $productos = json_decode($postBody);

        try {
            $resultados = $this->wp->transaction(function($wp) use ($productos) {
                $actualizados = [];

                foreach ($productos as $p) {
                    $producto = $wp->getProductBySku($p->sku);

                    if ($producto) {
                        $wp->updateProductStock($producto->ID, $p->stock);
                        $wp->updateProductPrice($producto->ID, $p->precio);
                        $actualizados[] = $p->sku;
                    }
                }

                return $actualizados;
            });

            http_response_code(200);
            echo json_encode(UtilHelper::ok([
                'actualizados' => count($resultados),
                'skus' => $resultados
            ]));

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(UtilHelper::error_500($e->getMessage()));
        }
    }
}
```

### Controller con WpCore

```php
<?php
namespace Controllers;

use Libs\WpCore;
use Libs\UtilHelper;

class ProductManagementController {
    private $wpcore;

    public function __construct() {
        $this->wpcore = new WpCore();
    }

    public function createProductWithCategories() {
        $postBody = file_get_contents("php://input");
        $data = json_decode($postBody);

        try {
            // Crear producto
            $product_id = $this->wpcore->createProduct([
                'name' => $data->name,
                'sku' => $data->sku,
                'price' => $data->price,
                'stock' => $data->stock
            ]);

            // Configurar categorías y extras
            $producto = $this->wpcore->getProduct($product_id);
            $producto->set_category_ids($data->categories);
            $producto->save();

            http_response_code(201);
            echo json_encode(UtilHelper::ok([
                'product_id' => $product_id,
                'permalink' => $producto->get_permalink()
            ]));

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(UtilHelper::error_500($e->getMessage()));
        }
    }
}
```

---

## ⚡ Conclusión

### Resumen Ejecutivo

- **Wp (PDO)**: Rápido, eficiente, ideal para operaciones masivas y APIs de alta frecuencia
- **WpCore (WordPress)**: Completo, funcional, ideal para operaciones complejas y uso de WooCommerce API

### Recomendación General

**Usa Wp** como tu herramienta principal para el 80% de las operaciones (CRUD básico, sincronización, consultas).

**Usa WpCore** para el 20% de operaciones especiales que requieren:
- WooCommerce API completa
- Hooks y filters de WordPress
- Funciones específicas de plugins
- Validaciones automáticas de WooCommerce

---

**Versión:** 1.0.0
**Fecha:** 2025-11-10
**Autor:** Claude (Anthropic AI)
