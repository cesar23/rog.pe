# Clase WpPlugin - Usar Funciones de Plugins de WordPress

Biblioteca para usar funciones de plugins de WordPress desde tu API REST, específicamente diseñada para trabajar con **Solu Currencies Exchange**.

## Tabla de Contenidos

- [Instalación](#instalación)
- [Uso Básico](#uso-básico)
- [API Reference](#api-reference)
- [Ejemplos con Solu Currencies Exchange](#ejemplos-con-solu-currencies-exchange)
- [Endpoints Listos para Usar](#endpoints-listos-para-usar)
- [Integración Completa](#integración-completa)

---

## Instalación

La clase ya está incluida. Solo importa:

```php
use Libs\WpPlugin;
```

---

## Uso Básico

```php
<?php
use Libs\WpPlugin;

try {
    // 1. Inicializar (carga WordPress automáticamente)
    $wpPlugin = new WpPlugin();

    // 2. Cargar el plugin Solu Currencies Exchange
    $wpPlugin->loadSoluCurrenciesExchange();

    // 3. Usar funciones del plugin
    $monedas = $wpPlugin->saveCurrenciesStorage();

    echo "¡Archivo JSON actualizado!";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

---

## API Reference

### Métodos Generales

#### `loadPluginFile($plugin_dir, $file_path)`
Carga un archivo de un plugin.

```php
$wpPlugin->loadPluginFile(
    'solu-currencies-exchange-rate',
    'includes/storage_functions.php'
);
```

#### `callPluginFunction($function_name, $args)`
Ejecuta una función de un plugin.

```php
$result = $wpPlugin->callPluginFunction('saveStorageTable', []);
```

#### `getPluginConstant($constant_name)`
Obtiene una constante de un plugin.

```php
$path = $wpPlugin->getPluginConstant('SOLU_CURRENCIES_EXCHANGE_STORAGE_JSON');
```

---

### Métodos para Solu Currencies Exchange

#### `loadSoluCurrenciesExchange()`
Carga el plugin Solu Currencies Exchange.

```php
$wpPlugin->loadSoluCurrenciesExchange();
```

#### `saveCurrenciesStorage()` ⭐
**Llama a `saveStorageTable()` del plugin.**

Guarda todas las monedas activas en el archivo JSON.

```php
$monedas = $wpPlugin->saveCurrenciesStorage();

// Resultado: Array de objetos SoluCurrenciesExchange
foreach ($monedas as $moneda) {
    echo "{$moneda->currency_name}: {$moneda->currency_value}\n";
}
```

#### `getCurrenciesStorage()`
Obtiene todas las monedas desde el archivo JSON.

```php
$monedas = $wpPlugin->getCurrenciesStorage();
```

#### `getCurrencyByCode($currency_code)`
Obtiene una moneda específica por código.

```php
// Obtener dólar
$usd = $wpPlugin->getCurrencyByCode('USD');

echo "Valor del dólar: {$usd->currency_value}\n";
```

#### `getLocalCurrency()`
Obtiene la moneda local configurada.

```php
$moneda_local = $wpPlugin->getLocalCurrency();

echo "Moneda local: {$moneda_local->currency_code}\n";
```

#### `updateCurrency($id, $data)`
Actualiza una moneda en la base de datos.

```php
$wpPlugin->updateCurrency(2, [
    'currency_value' => 3.87,
    'update_at' => date('Y-m-d H:i:s')
]);
```

#### `saveCurrencyAlternative($currency_code)`
Establece la moneda alternativa.

```php
$wpPlugin->saveCurrencyAlternative('USD');
```

#### `getCurrencyAlternative()`
Obtiene la moneda alternativa configurada.

```php
$alternative = $wpPlugin->getCurrencyAlternative();

echo "Moneda alternativa: {$alternative->currency_alternative_code}\n";
```

---

## Ejemplos con Solu Currencies Exchange

### Ejemplo 1: Actualizar Archivo JSON (saveStorageTable)

**Este es el uso principal que querías:**

```php
use Libs\WpPlugin;

try {
    $wpPlugin = new WpPlugin();
    $wpPlugin->loadSoluCurrenciesExchange();

    // Llamar a saveStorageTable() del plugin
    $monedas = $wpPlugin->saveCurrenciesStorage();

    echo "✅ Archivo JSON actualizado\n";
    echo "📦 Total monedas: " . count($monedas) . "\n";

    foreach ($monedas as $moneda) {
        echo "- {$moneda->currency_name} ({$moneda->currency_code}): ";
        echo "{$moneda->currency_symbol}{$moneda->currency_value}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
```

**Salida esperada:**
```
✅ Archivo JSON actualizado
📦 Total monedas: 3
- Soles (PEN): S/.1
- Dolares (USD): $3.85
- Euros (EUR): €4.20
```

### Ejemplo 2: Obtener Tipo de Cambio Actual

```php
use Libs\WpPlugin;

function getTipoCambio($currency_code = 'USD') {
    $wpPlugin = new WpPlugin();
    $wpPlugin->loadSoluCurrenciesExchange();

    $moneda = $wpPlugin->getCurrencyByCode($currency_code);

    if ($moneda) {
        return [
            'codigo' => $moneda->currency_code,
            'nombre' => $moneda->currency_name,
            'simbolo' => $moneda->currency_symbol,
            'valor' => (float)$moneda->currency_value
        ];
    }

    return null;
}

// Uso
$usd = getTipoCambio('USD');
echo "Tipo de cambio USD: {$usd['valor']}\n";
```

### Ejemplo 3: Actualizar Tipo de Cambio y Regenerar JSON

```php
use Libs\WpPlugin;

function actualizarTipoCambio($currency_id, $nuevo_valor) {
    $wpPlugin = new WpPlugin();
    $wpPlugin->loadSoluCurrenciesExchange();

    // 1. Actualizar en la base de datos
    $wpPlugin->updateCurrency($currency_id, [
        'currency_value' => $nuevo_valor,
        'update_at' => date('Y-m-d H:i:s')
    ]);

    // 2. Regenerar archivo JSON
    $wpPlugin->saveCurrenciesStorage();

    return [
        'status' => 'ok',
        'message' => 'Tipo de cambio actualizado y JSON regenerado',
        'nuevo_valor' => $nuevo_valor
    ];
}

// Uso: Actualizar USD (ID: 2) a 3.87
$resultado = actualizarTipoCambio(2, 3.87);
```

### Ejemplo 4: Sincronizar desde API Externa

```php
use Libs\WpPlugin;

function sincronizarTipoCambioDesdeAPI() {
    // 1. Obtener tipo de cambio desde API externa (ej: SUNAT, BCR)
    $tipo_cambio_externo = 3.87; // Esto vendría de una API real

    // 2. Actualizar en WordPress
    $wpPlugin = new WpPlugin();
    $wpPlugin->loadSoluCurrenciesExchange();

    // Obtener moneda USD actual
    $usd = $wpPlugin->getCurrencyByCode('USD');

    if ($usd && $usd->currency_value != $tipo_cambio_externo) {
        // Actualizar solo si cambió
        $wpPlugin->updateCurrency($usd->id, [
            'currency_value' => $tipo_cambio_externo,
            'update_at' => date('Y-m-d H:i:s')
        ]);

        // Regenerar JSON
        $wpPlugin->saveCurrenciesStorage();

        return [
            'actualizado' => true,
            'valor_anterior' => $usd->currency_value,
            'valor_nuevo' => $tipo_cambio_externo
        ];
    }

    return [
        'actualizado' => false,
        'mensaje' => 'Tipo de cambio sin cambios'
    ];
}
```

---

## Endpoints Listos para Usar

### Endpoint 1: Actualizar JSON

**POST** `/currency/update-storage`

```php
use Libs\WpPlugin;

class CurrencyController {
    public function updateStorage() {
        try {
            $wpPlugin = new WpPlugin();
            $wpPlugin->loadSoluCurrenciesExchange();

            $monedas = $wpPlugin->saveCurrenciesStorage();

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'message' => 'Archivo JSON actualizado',
                'total_monedas' => count($monedas),
                'archivo' => $wpPlugin->getCurrenciesJsonPath()
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

**Ejemplo de llamada:**
```bash
curl -X POST 'https://adcomputers.local/api_rest_sync/currency/update-storage?token=JWT_TOKEN'
```

**Respuesta:**
```json
{
  "status": "ok",
  "message": "Archivo JSON actualizado",
  "total_monedas": 3,
  "archivo": "/path/to/wp-content/uploads/solu-currencies-storage/currencies.json"
}
```

### Endpoint 2: Obtener Todas las Monedas

**GET** `/currency/all`

```php
public function getAllCurrencies() {
    try {
        $wpPlugin = new WpPlugin();
        $wpPlugin->loadSoluCurrenciesExchange();

        $monedas = $wpPlugin->getCurrenciesStorage();

        http_response_code(200);
        echo json_encode([
            'status' => 'ok',
            'total' => count($monedas),
            'monedas' => $monedas
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
```

### Endpoint 3: Obtener Moneda por Código

**GET** `/currency/by-code?code=USD`

```php
public function getCurrencyByCode() {
    try {
        $code = $_GET['code'] ?? null;

        if (!$code) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'El parámetro code es requerido'
            ]);
            return;
        }

        $wpPlugin = new WpPlugin();
        $wpPlugin->loadSoluCurrenciesExchange();

        $moneda = $wpPlugin->getCurrencyByCode($code);

        if (!$moneda) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => "Moneda {$code} no encontrada"
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 'ok',
            'moneda' => [
                'id' => $moneda->id,
                'nombre' => $moneda->currency_name,
                'codigo' => $moneda->currency_code,
                'simbolo' => $moneda->currency_symbol,
                'valor' => $moneda->currency_value
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
```

### Endpoint 4: Actualizar Tipo de Cambio

**POST** `/currency/update-exchange-rate`

**Body:**
```json
{
  "currency_id": 2,
  "value": 3.87
}
```

```php
public function updateExchangeRate() {
    try {
        $postBody = file_get_contents("php://input");
        $data = json_decode($postBody);

        if (!isset($data->currency_id) || !isset($data->value)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'currency_id y value son requeridos'
            ]);
            return;
        }

        $wpPlugin = new WpPlugin();
        $wpPlugin->loadSoluCurrenciesExchange();

        // Obtener moneda actual
        $moneda = $wpPlugin->getCurrency($data->currency_id);

        if (!$moneda) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Moneda no encontrada'
            ]);
            return;
        }

        // Actualizar
        $wpPlugin->updateCurrency($data->currency_id, [
            'currency_value' => $data->value,
            'update_at' => date('Y-m-d H:i:s')
        ]);

        // Regenerar JSON
        $wpPlugin->saveCurrenciesStorage();

        http_response_code(200);
        echo json_encode([
            'status' => 'ok',
            'message' => 'Tipo de cambio actualizado',
            'moneda' => $moneda->currency_code,
            'valor_anterior' => $moneda->currency_value,
            'valor_nuevo' => $data->value
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
```

---

## Integración Completa

### Paso 1: Crear Controller

**Archivo:** `api_rest_sync/Controllers/CurrencyController.php`

```php
<?php
namespace Controllers;

use Libs\WpPlugin;
use Libs\UtilHelper;
use Libs\AuthJWT;

class CurrencyController {
    private $wpPlugin;
    private $_auth;

    public function __construct() {
        $this->_auth = new AuthJWT();
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        try {
            $this->wpPlugin = new WpPlugin();
            $this->wpPlugin->loadSoluCurrenciesExchange();
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al cargar plugin: ' . $e->getMessage()
            ]);
            exit();
        }
    }

    private function _auth() {
        $datosArray = $this->_auth->estaAutenticado();
        if (isset($datosArray['status']) && $datosArray['status'] == 'error') {
            http_response_code(401);
            echo json_encode($datosArray);
            exit();
        }
    }

    // Endpoint: POST /currency/update-storage?token=JWT
    public function updateStorage() {
        $this->_auth();

        try {
            $monedas = $this->wpPlugin->saveCurrenciesStorage();

            http_response_code(200);
            echo json_encode(UtilHelper::ok([
                'message' => 'Archivo JSON actualizado',
                'total_monedas' => count($monedas),
                'archivo' => $this->wpPlugin->getCurrenciesJsonPath()
            ]));

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(UtilHelper::error_500($e->getMessage()));
        }
    }

    // Endpoint: GET /currency/all?token=JWT
    public function getAllCurrencies() {
        $this->_auth();

        try {
            $monedas = $this->wpPlugin->getCurrenciesStorage();

            http_response_code(200);
            echo json_encode(UtilHelper::ok([
                'total' => count($monedas),
                'monedas' => $monedas
            ]));

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(UtilHelper::error_500($e->getMessage()));
        }
    }

    // Más métodos aquí...
}
```

### Paso 2: Crear Router

**Archivo:** `api_rest_sync/Routers/CurrencyRouter.php`

```php
<?php
namespace Routers;

use Libs\Route;
use Controllers\CurrencyController;

$currencyController = new CurrencyController();

Route::add('/currency/update-storage', function() use ($currencyController) {
    $currencyController->updateStorage();
}, 'post');

Route::add('/currency/all', function() use ($currencyController) {
    $currencyController->getAllCurrencies();
}, 'get');

Route::add('/currency/by-code', function() use ($currencyController) {
    $currencyController->getCurrencyByCode();
}, 'get');

Route::add('/currency/update-exchange-rate', function() use ($currencyController) {
    $currencyController->updateExchangeRate();
}, 'post');
```

### Paso 3: Incluir Router en InitRouter

**Archivo:** `api_rest_sync/Routers/InitRouter.php`

```php
// Añadir al final
require_once __DIR__ . '/CurrencyRouter.php';
```

---

## Ejemplos de Uso desde Cliente

### JavaScript (Fetch)

```javascript
// Actualizar JSON
async function updateCurrenciesStorage(token) {
    const response = await fetch(
        `https://adcomputers.local/api_rest_sync/currency/update-storage?token=${token}`,
        {
            method: 'POST'
        }
    );

    return await response.json();
}

// Obtener tipo de cambio
async function getTipoCambio(token, code = 'USD') {
    const response = await fetch(
        `https://adcomputers.local/api_rest_sync/currency/by-code?token=${token}&code=${code}`
    );

    return await response.json();
}

// Actualizar tipo de cambio
async function updateTipoCambio(token, currencyId, value) {
    const response = await fetch(
        `https://adcomputers.local/api_rest_sync/currency/update-exchange-rate?token=${token}`,
        {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ currency_id: currencyId, value: value })
        }
    );

    return await response.json();
}
```

### Python (requests)

```python
import requests

BASE_URL = 'https://adcomputers.local/api_rest_sync'
TOKEN = 'tu_jwt_token'

# Actualizar JSON
response = requests.post(
    f'{BASE_URL}/currency/update-storage',
    params={'token': TOKEN}
)
print(response.json())

# Obtener tipo de cambio
response = requests.get(
    f'{BASE_URL}/currency/by-code',
    params={'token': TOKEN, 'code': 'USD'}
)
print(response.json())
```

---

## Resumen

### ✅ Lo que puedes hacer ahora:

1. ✅ Usar `saveStorageTable()` desde tu API REST
2. ✅ Obtener todas las monedas
3. ✅ Obtener moneda por código (USD, PEN, EUR)
4. ✅ Actualizar tipo de cambio
5. ✅ Regenerar archivo JSON automáticamente
6. ✅ Integrar con sistemas externos (ERP, APIs)

### 📁 Archivos Creados:

1. [WpPlugin.php](WpPlugin.php) - Clase principal
2. [WpPluginExamples.php](WpPluginExamples.php) - 10+ ejemplos
3. [WPPLUGIN_README.md](WPPLUGIN_README.md) - Esta documentación

---

**Versión:** 1.0.0
**Fecha:** 2025-11-10
