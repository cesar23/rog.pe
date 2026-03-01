# Quick Start - API REST Sync

Guía rápida para comenzar a usar la API en menos de 5 minutos.

---

## 1. Instalación Rápida

```bash
# Clonar e instalar dependencias
composer install

# Configurar entorno
cp .env.development.development.example .env.development.development
# Editar .env.development.development con tus credenciales
```

---

## 2. Configuración Mínima

Editar `.env`:

```env
DB_HOST=localhost
DB_NAME=tu_base_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
SECRET_KEY=tu_clave_secreta_jwt
```

---

## 3. Obtener Token JWT

```bash
curl -X POST 'https://tu-dominio.com/api_rest_sync/user/login' \
  -H 'Content-Type: application/json' \
  -d '{"user":"cesar","password":"tu_password"}'
```

**Respuesta**:
```json
{
  "status": "ok",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expiration_token": "2025-11-03 13:38:45"
}
```

**Guardar el token**:
```bash
TOKEN="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

---

## 4. Endpoints Principales

### Health Check
```bash
curl 'https://tu-dominio.com/api_rest_sync/health-check'
```

### Obtener Productos
```bash
curl "https://tu-dominio.com/api_rest_sync/product/get-poducts-id-softlink?token=$TOKEN"
```

### Actualizar Stock
```bash
curl -X POST "https://tu-dominio.com/api_rest_sync/product/up-product-stock?token=$TOKEN" \
  -H 'Content-Type: application/json' \
  -d '[{"stock_act":50,"cod_prod":"SKU001","post_id":123}]'
```

### Actualizar Precios
```bash
curl -X POST "https://tu-dominio.com/api_rest_sync/product/up-product-price?token=$TOKEN" \
  -H 'Content-Type: application/json' \
  -d '[{"precio_venta":99.99,"cod_prod":"SKU001","post_id":123}]'
```

### Actualizar Tipo de Cambio
```bash
curl -X POST "https://tu-dominio.com/api_rest_sync/option/up-tipo-cambio-web-v2?token=$TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"tipo_cambio":"3.85"}'
```

---

## 5. Tabla de Endpoints

| Endpoint | Método | Auth | Descripción |
|----------|--------|------|-------------|
| `/health-check` | GET | No | Estado de la API |
| `/user/login` | POST | No | Autenticación |
| `/user/user` | GET | Sí | Datos del usuario |
| `/product/get-poducts-id-softlink` | GET | Sí | Listar productos |
| `/product/up-product-stock` | POST | Sí | Actualizar stock |
| `/product/up-product-price` | POST | Sí | Actualizar precios |
| `/product/up-product-description` | POST | Sí | Actualizar 1 nombre |
| `/product/up-product-description-v3` | POST | Sí | Actualizar N nombres |
| `/option/up-tipo-cambio-web` | POST | Sí | Tipo cambio (v1) |
| `/option/up-tipo-cambio-web-v2` | POST | Sí | Tipo cambio (v2) |

---

## 6. Estructura de Respuestas

### Exitosa
```json
{
  "status": "ok",
  "result": { ... }
}
```

### Error
```json
{
  "status": "error",
  "error_id": "401",
  "error_msg": "Token inválido"
}
```

---

## 7. Códigos HTTP

| Código | Significado |
|--------|-------------|
| 200 | OK - Petición exitosa |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - Token inválido |
| 404 | Not Found - Endpoint no existe |
| 500 | Server Error - Error interno |

---

## 8. Ver Logs

```bash
# Logs en tiempo real
tail -f Logs/access_log.log
tail -f Logs/response_log.log

# Buscar por REQUEST_ID
grep "req_6907a5a036eb75.23645529" Logs/*.log
```

---

## 9. Troubleshooting

### Token expirado
**Solución**: Hacer login nuevamente para obtener un token nuevo

### Error 404
**Solución**: Verificar que mod_rewrite esté habilitado y .htaccess configurado

### Error de conexión BD
**Solución**: Verificar credenciales en .env y que MySQL esté corriendo

---

## 10. Documentación Completa

Ver [README.md](README.md) para documentación completa.

---

## Ejemplos de Uso en Diferentes Lenguajes

### JavaScript (Fetch)
```javascript
// Login
const response = await fetch('https://api.example.com/api_rest_sync/user/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ user: 'cesar', password: 'pass' })
});
const { token } = await response.json();

// Actualizar stock
await fetch(`https://api.example.com/api_rest_sync/product/up-product-stock?token=${token}`, {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify([{ stock_act: 50, cod_prod: 'SKU001', post_id: 123 }])
});
```

### Python (requests)
```python
import requests

# Login
response = requests.post(
    'https://api.example.com/api_rest_sync/user/login',
    json={'user': 'cesar', 'password': 'pass'}
)
token = response.json()['token']

# Actualizar stock
requests.post(
    f'https://api.example.com/api_rest_sync/product/up-product-stock?token={token}',
    json=[{'stock_act': 50, 'cod_prod': 'SKU001', 'post_id': 123}]
)
```

### PHP (cURL)
```php
// Login
$ch = curl_init('https://api.example.com/api_rest_sync/user/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'user' => 'cesar',
    'password' => 'pass'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($ch), true);
$token = $response['token'];

// Actualizar stock
$ch = curl_init("https://api.example.com/api_rest_sync/product/up-product-stock?token=$token");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    ['stock_act' => 50, 'cod_prod' => 'SKU001', 'post_id' => 123]
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
```

---

**¿Necesitas más ayuda?** Consulta el [README.md](README.md) completo.
