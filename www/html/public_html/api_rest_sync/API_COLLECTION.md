# API REST Sync - Colección de Endpoints

Colección completa de endpoints para importar en Postman, Insomnia o Thunder Client.

---

## Variables de Entorno

```
BASE_URL = https://adcomputers.local/api_rest_sync
TOKEN = eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9... (obtenido del login)
```

---

## 📁 Collection: API REST Sync

### 🟢 Health Check

**GET** `{{BASE_URL}}/health-check`

**Headers:**
```
Content-Type: application/json
```

**Response:**
```json
{
  "status": "ok"
}
```

---

## 📁 Autenticación

### 🔐 Login

**POST** `{{BASE_URL}}/user/login`

**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
  "user": "cesar",
  "password": "password"
}
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhZGNvbXB1dGVycy5sb2NhbCIsImV4cCI6MTczMDU4MjMyNSwiYXVkIjoiYXNkYXdkc2Q4d3MuNkBteXN5c3RlbSIsImRhdGFfdXNlciI6eyJpZCI6MSwidXNlciI6ImNlc2FyIiwiZW1haWwiOiJwZXJ1Y2Fvc0BnbWFpbC5jb20iLCJyb2xlIjoiYWRtaW4ifX0.signature",
  "expiration_token": "2025-11-03 13:38:45",
  "application_date": "2025-11-02 13:38:45"
}
```

**Test Script (Postman):**
```javascript
// Guardar token automáticamente
const response = pm.response.json();
if (response.status === 'ok') {
    pm.environment.set('TOKEN', response.token);
}
```

---

### 👤 Obtener Usuario Actual

**GET** `{{BASE_URL}}/user/user?token={{TOKEN}}`

**Headers:**
```
Content-Type: application/json
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "result": {
    "id": 1,
    "user": "cesar",
    "email": "perucaos@gmail.com",
    "role": "admin"
  },
  "application_date": "2025-11-02 13:38:45"
}
```

**Response (401 Unauthorized):**
```json
{
  "status": "error",
  "result": [],
  "application_date": "2025-11-02 13:38:45",
  "error_id": "401",
  "error_msg": "Su Token:null es invalido"
}
```

---

## 📁 Productos

### 📦 Obtener Productos con SKU

**GET** `{{BASE_URL}}/product/get-poducts-id-softlink?token={{TOKEN}}`

**Headers:**
```
Content-Type: application/json
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "result": [
    {
      "ID": "123",
      "post_title": "Producto Ejemplo 1",
      "cod_prod": "SKU001"
    },
    {
      "ID": "124",
      "post_title": "Producto Ejemplo 2",
      "cod_prod": "SKU002"
    }
  ],
  "application_date": "2025-11-02 13:38:45"
}
```

---

### 📊 Actualizar Stock (Múltiples Productos)

**POST** `{{BASE_URL}}/product/up-product-stock?token={{TOKEN}}`

**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
[
  {
    "stock_act": 50,
    "cod_prod": "SKU001",
    "post_id": 123
  },
  {
    "stock_act": 30,
    "cod_prod": "SKU002",
    "post_id": 124
  },
  {
    "stock_act": 0,
    "cod_prod": "SKU003",
    "post_id": 125
  }
]
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "result": [
    {
      "rowAffect": 1,
      "error": false,
      "post_id": 123
    },
    {
      "rowAffect": 1,
      "error": false,
      "post_id": 124
    },
    {
      "rowAffect": 1,
      "error": false,
      "post_id": 125
    }
  ],
  "application_date": "2025-11-02 13:38:45"
}
```

**Notas:**
- `stock_act`: Stock actual del producto
- `cod_prod`: SKU del producto (identificador único)
- `post_id`: ID del post en WooCommerce
- Si `stock_act = 0`, el producto se marca como "out of stock"
- Si `stock_act > 0`, el producto se marca como "in stock"

---

### 💰 Actualizar Precios (Múltiples Productos)

**POST** `{{BASE_URL}}/product/up-product-price?token={{TOKEN}}`

**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
[
  {
    "precio_venta": 99.99,
    "cod_prod": "SKU001",
    "post_id": 123
  },
  {
    "precio_venta": 149.50,
    "cod_prod": "SKU002",
    "post_id": 124
  }
]
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "result": [
    {
      "rowAffect": 1,
      "error": false,
      "post_id": 123
    },
    {
      "rowAffect": 1,
      "error": false,
      "post_id": 124
    }
  ],
  "application_date": "2025-11-02 13:38:45"
}
```

**Notas:**
- `precio_venta`: Precio regular del producto
- Actualiza `_price` y `_regular_price` en WooCommerce

---

### 📝 Actualizar Descripción (1 Producto)

**POST** `{{BASE_URL}}/product/up-product-description?token={{TOKEN}}`

**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
  "nom_prod": "Nuevo Nombre del Producto",
  "cod_prod": "SKU001",
  "post_id": 123
}
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "result": {
    "rowAffect": 1,
    "error": false,
    "post_id": 123
  },
  "application_date": "2025-11-02 13:38:45"
}
```

---

### 📝 Actualizar Descripción (Múltiples Productos)

**POST** `{{BASE_URL}}/product/up-product-description-v3?token={{TOKEN}}`

**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
[
  {
    "nom_prod": "Producto A - Nueva Descripción",
    "cod_prod": "SKU001",
    "post_id": 123
  },
  {
    "nom_prod": "Producto B - Nueva Descripción",
    "cod_prod": "SKU002",
    "post_id": 124
  }
]
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "result": [
    {
      "rowAffect": 1,
      "error": false,
      "post_id": 123
    },
    {
      "rowAffect": 1,
      "error": false,
      "post_id": 124
    }
  ],
  "application_date": "2025-11-02 13:38:45"
}
```

---

## 📁 Opciones de Sistema

### 💱 Actualizar Tipo de Cambio (v1 - Plugin Antiguo)

**POST** `{{BASE_URL}}/option/up-tipo-cambio-web?token={{TOKEN}}`

**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
  "tipo_cambio": "3.85"
}
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "result": {
    "rowAffect": 1,
    "error": false
  },
  "application_date": "2025-11-02 13:38:45"
}
```

**Notas:**
- Actualiza tabla `wp_options` con `option_name = 'woocs'`
- Serializa datos con USD y PEN
- Usado por plugin de cambio de moneda antiguo

---

### 💱 Actualizar Tipo de Cambio (v2 - Solu Exchange)

**POST** `{{BASE_URL}}/option/up-tipo-cambio-web-v2?token={{TOKEN}}`

**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
  "tipo_cambio": "3.85"
}
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "result": {
    "rowAffect": 1,
    "error": false
  },
  "application_date": "2025-11-02 13:38:45"
}
```

**Notas:**
- Actualiza tabla `wp_solu_currencies_exchange`
- Solo actualiza registros con `currency_code = 'PEN'`
- Usado por plugin Solu Exchange (recomendado)

---

## 🔴 Respuestas de Error

### Error 400 - Bad Request

```json
{
  "status": "error",
  "result": [],
  "application_date": "2025-11-02 13:38:45",
  "error_id": "400",
  "error_msg": "Datos incompletos o inválidos"
}
```

**Causa**: Falta un campo requerido en el JSON o formato incorrecto

---

### Error 401 - Unauthorized

```json
{
  "status": "error",
  "result": [],
  "application_date": "2025-11-02 13:38:45",
  "error_id": "401",
  "error_msg": "Su Token:eyJhbGc... es invalido"
}
```

**Causas comunes**:
- Token expirado (más de 24 horas)
- Token inválido o malformado
- Token no proporcionado en query string
- SECRET_KEY incorrecta

**Solución**: Hacer login nuevamente para obtener un token nuevo

---

### Error 404 - Not Found

```json
{
  "Error": "404 Recurso no encontrado"
}
```

**Causa**: El endpoint no existe

---

### Error 405 - Method Not Allowed

```json
{
  "status": "error",
  "error_id": "405",
  "error_msg": "Método no permitido"
}
```

**Causa**: Método HTTP incorrecto (ej: POST en vez de GET)

---

### Error 500 - Internal Server Error

```json
{
  "status": "error",
  "result": [],
  "application_date": "2025-11-02 13:38:45",
  "error_id": "500",
  "error_msg": "Error interno del servidor"
}
```

**Causa**: Error en la base de datos o lógica del servidor

---

## 🧪 Tests Automáticos (Postman)

### Pre-request Script (Collection Level)

```javascript
// Verificar si el token existe, si no hacer login automáticamente
const token = pm.environment.get('TOKEN');
if (!token) {
    pm.sendRequest({
        url: pm.environment.get('BASE_URL') + '/user/login',
        method: 'POST',
        header: 'Content-Type: application/json',
        body: {
            mode: 'raw',
            raw: JSON.stringify({
                user: 'cesar',
                password: 'password'
            })
        }
    }, (err, response) => {
        if (!err && response.code === 200) {
            const jsonData = response.json();
            pm.environment.set('TOKEN', jsonData.token);
        }
    });
}
```

### Test Script (Global)

```javascript
// Verificar que la respuesta sea JSON
pm.test("Content-Type is JSON", () => {
    pm.response.to.have.header("Content-Type", "application/json; charset=UTF-8");
});

// Verificar código de respuesta
pm.test("Status code is 200", () => {
    pm.response.to.have.status(200);
});

// Verificar estructura de respuesta
pm.test("Response has status field", () => {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('status');
});

// Si la respuesta tiene token, guardarlo
const jsonData = pm.response.json();
if (jsonData.token) {
    pm.environment.set('TOKEN', jsonData.token);
    console.log('Token guardado:', jsonData.token);
}
```

---

## 📋 Casos de Uso Completos

### Caso 1: Sincronización de Stock desde ERP

```
1. POST /user/login
   → Obtener TOKEN

2. GET /product/get-poducts-id-softlink?token=TOKEN
   → Obtener lista de productos con SKU

3. POST /product/up-product-stock?token=TOKEN
   → Enviar array con stock actualizado
   Body: [
     {"stock_act": 50, "cod_prod": "SKU001", "post_id": 123},
     {"stock_act": 30, "cod_prod": "SKU002", "post_id": 124}
   ]
```

### Caso 2: Actualización Masiva de Precios

```
1. POST /user/login
   → Obtener TOKEN

2. POST /product/up-product-price?token=TOKEN
   → Enviar array con precios actualizados
   Body: [
     {"precio_venta": 99.99, "cod_prod": "SKU001", "post_id": 123},
     {"precio_venta": 149.50, "cod_prod": "SKU002", "post_id": 124}
   ]
```

### Caso 3: Actualización de Tipo de Cambio Diario

```
1. POST /user/login
   → Obtener TOKEN

2. POST /option/up-tipo-cambio-web-v2?token=TOKEN
   → Actualizar tipo de cambio del día
   Body: {"tipo_cambio": "3.85"}
```

---

## 🔧 Configuración de Postman/Insomnia

### Variables de Entorno

**Development**
```json
{
  "BASE_URL": "https://adcomputers.local/api_rest_sync",
  "TOKEN": ""
}
```

**Production**
```json
{
  "BASE_URL": "https://api.tudominio.com/api_rest_sync",
  "TOKEN": ""
}
```

### Headers Globales

```
Content-Type: application/json
Accept: application/json
```

---

## 📊 Monitoreo y Logs

### REQUEST_ID

Cada petición recibe un header de respuesta `X-Request-ID`:

```
X-Request-ID: req_6907a5a036eb75.23645529
```

Este ID permite rastrear la petición en los logs:

```bash
# Buscar en access log
grep "req_6907a5a036eb75.23645529" Logs/access_log.log

# Buscar en response log
grep "req_6907a5a036eb75.23645529" Logs/response_log.log
```

---

## 🐛 Debugging

### Habilitar Logs Detallados

Ver logs en tiempo real:

```bash
tail -f Logs/access_log.log Logs/response_log.log
```

### Verificar Token JWT

Decodificar token en [jwt.io](https://jwt.io):

```json
{
  "iss": "adcomputers.local",
  "exp": 1730582325,
  "aud": "asdawdsd8ws.6@hostname",
  "data_user": {
    "id": 1,
    "user": "cesar",
    "email": "perucaos@gmail.com",
    "role": "admin"
  }
}
```

---

**Documentación Completa**: Ver [README.md](README.md)
