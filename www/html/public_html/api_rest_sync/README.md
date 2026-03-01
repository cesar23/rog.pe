# API REST Sync - Sistema de Sincronización WooCommerce

API REST para sincronización de datos entre sistemas ERP (SoftLink) y WooCommerce. Proporciona endpoints para gestión de productos, precios, stock, usuarios y opciones de configuración.

## Tabla de Contenidos

- [Características Principales](#características-principales)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Autenticación](#autenticación)
- [Endpoints](#endpoints)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Sistema de Logs](#sistema-de-logs)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Base de Datos](#base-de-datos)

---

## Características Principales

- **Autenticación JWT**: Sistema seguro de tokens con expiración configurable
- **Gestión de Productos**: CRUD completo para productos WooCommerce
- **Sincronización en Tiempo Real**: Actualización masiva de stock, precios y descripciones
- **Sistema de Logs Dual**: Registro completo de peticiones y respuestas con rotación automática
- **Arquitectura MVC**: Código organizado y mantenible
- **CORS Habilitado**: Acceso desde múltiples orígenes
- **Manejo de Errores**: Respuestas HTTP estandarizadas

---

## Requisitos

### Software Requerido

- **PHP**: >= 7.4
- **Composer**: >= 2.0
- **MariaDB/MySQL**: >= 10.3
- **Apache/Nginx**: Con mod_rewrite habilitado

### Extensiones PHP

```bash
php-pdo
php-pdo_mysql
php-json
php-mbstring
```

---

## Instalación

### 1. Clonar el Repositorio

```bash
git clone <repository-url>
cd api_rest_sync
```

### 2. Instalar Dependencias

```bash
composer install
```

### 3. Configurar Variables de Entorno

```bash
cp .env.development.development.example .env.development.development
```

Editar `.env` con tus credenciales:

```env
# Database Configuration
DB_HOST=mariadb
DB_PORT=3306
DB_NAME=adco_adcomputer
DB_USERNAME=adco_adcomputer
DB_PASSWORD=tu_password

# JWT Configuration
SECRET_KEY=tu_clave_secreta_segura
EXPIRE_TOKEN=PT1H
EXPIRE_TOKEN_JWT=24

# Application Configuration
APP_TIMEZONE=America/Lima
APP_BASEPATH=/api_rest_sync/
```

### 4. Importar Base de Datos

```bash
mysql -u usuario -p base_datos < auth.sql
```

### 5. Configurar Servidor Web

#### Apache (.htaccess)

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx

```nginx
location /api_rest_sync/ {
    try_files $uri $uri/ /api_rest_sync/index.php?$query_string;
}
```

---

## Configuración

### Variables de Entorno

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| `DB_HOST` | Host de la base de datos | `mariadb` o `localhost` |
| `DB_PORT` | Puerto de MySQL | `3306` |
| `DB_NAME` | Nombre de la base de datos | `adco_adcomputer` |
| `DB_USERNAME` | Usuario de la base de datos | `root` |
| `DB_PASSWORD` | Contraseña de la base de datos | `password123` |
| `SECRET_KEY` | Clave secreta para JWT | `asdawdsd8ws.6@` |
| `EXPIRE_TOKEN` | Duración del token (formato DateInterval) | `PT1H` (1 hora) |
| `EXPIRE_TOKEN_JWT` | Duración del JWT en horas | `24` |
| `APP_TIMEZONE` | Zona horaria de la aplicación | `America/Lima` |
| `APP_BASEPATH` | Ruta base de la API | `/api_rest_sync/` |

---

## Autenticación

### Sistema de Autenticación JWT

La API utiliza **JSON Web Tokens (JWT)** para autenticación y autorización.

#### Flujo de Autenticación

```
1. Login → Obtener JWT Token
2. Usar Token en todas las peticiones protegidas
3. Token expira después de 24 horas (configurable)
```

### Endpoint de Login

**POST** `/user/login`

**Request:**
```json
{
  "user": "cesar",
  "password": "tu_password"
}
```

**Response (200 OK):**
```json
{
  "status": "ok",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expiration_token": "2025-11-03 13:38:45",
  "application_date": "2025-11-02 13:38:45"
}
```

**Response (401 Unauthorized):**
```json
{
  "status": "error",
  "result": [],
  "error_id": "401",
  "error_msg": "Usuario o contraseña incorrectos"
}
```

### Usar el Token

Una vez obtenido el token, inclúyelo en el query string de todas las peticiones:

```bash
curl 'https://api.example.com/api_rest_sync/product/get-poducts-id-softlink?token=YOUR_JWT_TOKEN'
```

### Payload del JWT

El token JWT contiene:

```json
{
  "iss": "adcomputers.local",
  "exp": 1730000000,
  "aud": "secret_key_hostname",
  "data_user": {
    "id": 1,
    "user": "cesar",
    "email": "usuario@example.com",
    "role": "admin"
  }
}
```

---

## Endpoints

### Health Check

| Método | Endpoint | Autenticación | Descripción |
|--------|----------|---------------|-------------|
| GET | `/health-check` | No | Verificar estado de la API |

**Ejemplo:**
```bash
curl 'https://api.example.com/api_rest_sync/health-check'
```

**Response:**
```json
{"status": "ok"}
```

---

### Usuarios

#### Login

| Método | Endpoint | Autenticación | Descripción |
|--------|----------|---------------|-------------|
| POST | `/user/login` | No | Autenticación de usuario |

**Request Body:**
```json
{
  "user": "cesar",
  "password": "password"
}
```

#### Obtener Datos del Usuario

| Método | Endpoint | Autenticación | Descripción |
|--------|----------|---------------|-------------|
| GET | `/user/user?token=JWT` | Sí | Obtener datos del usuario autenticado |

**Ejemplo:**
```bash
curl 'https://api.example.com/api_rest_sync/user/user?token=eyJhbGc...'
```

**Response:**
```json
{
  "status": "ok",
  "result": {
    "id": 1,
    "user": "cesar",
    "email": "usuario@example.com",
    "role": "admin"
  }
}
```

---

### Productos

#### Obtener IDs de Productos SoftLink

| Método | Endpoint | Autenticación | Descripción |
|--------|----------|---------------|-------------|
| GET | `/product/get-poducts-id-softlink?token=JWT` | Sí | Lista de productos con SKU |

**Response:**
```json
{
  "status": "ok",
  "result": [
    {
      "ID": "123",
      "post_title": "Producto Ejemplo",
      "cod_prod": "SKU001"
    }
  ]
}
```

#### Actualizar Stock de Productos

| Método | Endpoint | Autenticación | Descripción |
|--------|----------|---------------|-------------|
| POST | `/product/up-product-stock?token=JWT` | Sí | Actualizar stock de múltiples productos |

**Request Body:**
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
  }
]
```

**Response:**
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
  ]
}
```

#### Actualizar Precios de Productos

| Método | Endpoint | Autenticación | Descripción |
|--------|----------|---------------|-------------|
| POST | `/product/up-product-price?token=JWT` | Sí | Actualizar precios de múltiples productos |

**Request Body:**
```json
[
  {
    "precio_venta": 99.99,
    "cod_prod": "SKU001",
    "post_id": 123
  }
]
```

#### Actualizar Descripción de Producto

| Método | Endpoint | Autenticación | Descripción |
|--------|----------|---------------|-------------|
| POST | `/product/up-product-description?token=JWT` | Sí | Actualizar nombre de 1 producto |
| POST | `/product/up-product-description-v3?token=JWT` | Sí | Actualizar nombre de múltiples productos |

**Request Body (v3):**
```json
[
  {
    "nom_prod": "Nuevo Nombre Producto",
    "cod_prod": "SKU001",
    "post_id": 123
  }
]
```

---

### Opciones de Sistema

#### Actualizar Tipo de Cambio

| Método | Endpoint | Autenticación | Descripción |
|--------|----------|---------------|-------------|
| POST | `/option/up-tipo-cambio-web?token=JWT` | Sí | Actualizar tipo de cambio (plugin antiguo) |
| POST | `/option/up-tipo-cambio-web-v2?token=JWT` | Sí | Actualizar tipo de cambio (Solu Exchange) |

**Request Body:**
```json
{
  "tipo_cambio": "3.85"
}
```

**Response:**
```json
{
  "status": "ok",
  "result": {
    "rowAffect": 1,
    "error": false
  }
}
```

---

## Estructura del Proyecto

```
api_rest_sync/
├── Config/
│   ├── Config.php              # Configuración general
│   └── Database.php            # Clase de conexión PDO
├── Controllers/
│   ├── AuthController.php      # Autenticación manual (deprecado)
│   ├── AuthJWTController.php   # Autenticación JWT (actual)
│   ├── ProductController.php   # Controlador de productos
│   ├── UserController.php      # Controlador de usuarios
│   └── OptionController.php    # Controlador de opciones
├── Models/
│   ├── ProductModel.php        # Modelo de productos
│   ├── UserModel.php           # Modelo de usuarios
│   └── OptionModel.php         # Modelo de opciones
├── Routers/
│   ├── InitRouter.php          # Router principal
│   ├── ProductRouter.php       # Rutas de productos
│   ├── UserRouter.php          # Rutas de usuarios
│   └── OptionRouter.php        # Rutas de opciones
├── Libs/
│   ├── Route.php               # Motor de enrutamiento
│   ├── Auth.php                # Autenticación con BD
│   ├── AuthJWT.php             # Autenticación JWT
│   ├── IAuth.php               # Interfaz de autenticación
│   ├── UtilHelper.php          # Utilidades y helpers
│   ├── Solulog.php             # Sistema de logging
│   └── ConsoleColor.php        # Colores en consola
├── Logs/
│   ├── access_log.log          # Log de peticiones HTTP
│   ├── response_log.log        # Log de respuestas HTTP
│   ├── error_YYYY.log          # Log de errores por año
│   └── README.md               # Documentación de logs
├── vendor/                     # Dependencias Composer
├── index.php                   # Punto de entrada
├── .env                        # Variables de entorno
├── .env.example                # Plantilla de variables
├── composer.json               # Dependencias PHP
├── composer.lock               # Versiones bloqueadas
├── auth.sql                    # Script SQL tabla users_auth
└── README.md                   # Este archivo
```

### Arquitectura MVC

- **Models**: Interactúan con la base de datos (PDO)
- **Controllers**: Lógica de negocio y validación
- **Routers**: Definición de rutas y endpoints
- **Libs**: Utilidades, autenticación, logging
- **Config**: Configuración y conexión a BD

---

## Sistema de Logs

La API implementa un sistema dual de logging con rotación automática.

### Access Log (Peticiones)

**Archivo**: `Logs/access_log.log`

**Formato**:
```
[TIMESTAMP] [REQUEST_ID] METHOD URI - IP: X.X.X.X - User-Agent: ... - Host: ...
```

**Ejemplo**:
```
[2025-11-02 13:40:32] [req_6907a5a036eb75.23645529] GET /api_rest_sync/product/get-poducts-id-softlink?token=null - IP: 192.168.0.52 - User-Agent: curl/8.9.0 - Host: adcomputers.local
```

### Response Log (Respuestas)

**Archivo**: `Logs/response_log.log`

**Formato**:
```
[TIMESTAMP] [REQUEST_ID] Status: HTTP_CODE - Response: JSON_BODY
```

**Ejemplo**:
```
[2025-11-02 13:40:32] [req_6907a5a036eb75.23645529] Status: 401 - Response: {"status":"error","result":[],"application_date":"2025-11-02 13:40:32","error_id":"401","error_msg":"Su Token:null  es invalido"}
```

### Correlación de Logs

El `REQUEST_ID` permite correlacionar cada petición con su respuesta:

```bash
# Buscar una petición específica
grep "req_6907a5a036eb75.23645529" Logs/access_log.log
grep "req_6907a5a036eb75.23645529" Logs/response_log.log
```

### Rotación Automática

- **Tamaño máximo**: 5MB por archivo
- **Archivos históricos**: 5 backups (`.log.1` a `.log.5`)
- **Total de espacio**: ~60MB (12 archivos × 5MB)

### Ver Logs en Tiempo Real

```bash
# Ver peticiones en tiempo real
tail -f Logs/access_log.log

# Ver respuestas en tiempo real
tail -f Logs/response_log.log

# Ver ambos simultáneamente
tail -f Logs/access_log.log Logs/response_log.log
```

---

## Ejemplos de Uso

### 1. Autenticación y Obtener Token

```bash
curl -X POST 'https://adcomputers.local/api_rest_sync/user/login' \
  -H 'Content-Type: application/json' \
  -d '{
    "user": "cesar",
    "password": "password"
  }'
```

**Respuesta**:
```json
{
  "status": "ok",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhZGNvbXB1dGVycy5sb2NhbCIsImV4cCI6MTczMDU4MjMyNSwiYXVkIjoiYXNkYXdkc2Q4d3MuNkBteXN5c3RlbSIsImRhdGFfdXNlciI6eyJpZCI6MSwidXNlciI6ImNlc2FyIiwiZW1haWwiOiJwZXJ1Y2Fvc0BnbWFpbC5jb20iLCJyb2xlIjoiYWRtaW4ifX0.signature",
  "expiration_token": "2025-11-03 13:38:45",
  "application_date": "2025-11-02 13:38:45"
}
```

### 2. Obtener Productos con SKU

```bash
TOKEN="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."

curl -X GET "https://adcomputers.local/api_rest_sync/product/get-poducts-id-softlink?token=$TOKEN"
```

### 3. Actualizar Stock Masivo

```bash
TOKEN="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."

curl -X POST "https://adcomputers.local/api_rest_sync/product/up-product-stock?token=$TOKEN" \
  -H 'Content-Type: application/json' \
  -d '[
    {
      "stock_act": 50,
      "cod_prod": "SKU001",
      "post_id": 123
    },
    {
      "stock_act": 30,
      "cod_prod": "SKU002",
      "post_id": 124
    }
  ]'
```

### 4. Actualizar Precios Masivo

```bash
TOKEN="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."

curl -X POST "https://adcomputers.local/api_rest_sync/product/up-product-price?token=$TOKEN" \
  -H 'Content-Type: application/json' \
  -d '[
    {
      "precio_venta": 99.99,
      "cod_prod": "SKU001",
      "post_id": 123
    }
  ]'
```

### 5. Actualizar Tipo de Cambio

```bash
TOKEN="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."

curl -X POST "https://adcomputers.local/api_rest_sync/option/up-tipo-cambio-web-v2?token=$TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{
    "tipo_cambio": "3.85"
  }'
```

---

## Base de Datos

### Tabla: `users_auth`

```sql
CREATE TABLE users_auth (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super', 'admin', 'user') NOT NULL,
    token VARCHAR(200) NULL,
    expiration_token DATETIME NULL,
    active TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Notas**:
- La contraseña debe estar hasheada con SHA1
- El campo `token` se usa en autenticación manual (deprecado)
- Para JWT, el token se genera dinámicamente y no se almacena

### Tablas WooCommerce Utilizadas

La API interactúa con las siguientes tablas de WordPress/WooCommerce:

- `wp_posts`: Productos (custom post type)
- `wp_postmeta`: Metadatos de productos (precio, stock, SKU)
- `wp_wc_product_meta_lookup`: Tabla lookup de WooCommerce para performance
- `wp_options`: Opciones de configuración del sitio
- `wp_solu_currencies_exchange`: Tipo de cambio (plugin Solu Exchange)

---

## Códigos de Estado HTTP

| Código | Significado | Descripción |
|--------|-------------|-------------|
| 200 | OK | Petición exitosa |
| 400 | Bad Request | Datos incompletos o inválidos |
| 401 | Unauthorized | Token inválido, expirado o no autorizado |
| 404 | Not Found | Endpoint no encontrado |
| 405 | Method Not Allowed | Método HTTP no permitido para esta ruta |
| 500 | Internal Server Error | Error interno del servidor |

### Estructura de Respuestas de Error

```json
{
  "status": "error",
  "result": [],
  "application_date": "2025-11-02 13:38:45",
  "error_id": "401",
  "error_msg": "Descripción del error"
}
```

---

## Seguridad

### Recomendaciones de Producción

1. **Cambiar SECRET_KEY**: Usar una clave fuerte y única
   ```env
   SECRET_KEY=$(openssl rand -base64 32)
   ```

2. **HTTPS Obligatorio**: Configurar SSL/TLS en el servidor

3. **Restricción CORS**: Limitar orígenes permitidos
   ```php
   header("Access-Control-Allow-Origin: https://tudominio.com");
   ```

4. **Rate Limiting**: Implementar límite de peticiones por IP

5. **Validación de Input**: Ya implementado en controladores

6. **Logs Seguros**: No registrar contraseñas ni tokens completos

7. **Permisos de Archivos**:
   ```bash
   chmod 600 .env.development.development
   chmod 755 Logs/
   ```

8. **Actualizar Dependencias**:
   ```bash
   composer update
   ```

---

## Troubleshooting

### Error: "Token inválido"

**Causa**: Token expirado o SECRET_KEY incorrecta

**Solución**:
1. Verificar que el token no haya expirado (24h por defecto)
2. Solicitar un nuevo token con `/user/login`
3. Verificar que `SECRET_KEY` coincida en .env

### Error: "Connection refused"

**Causa**: Base de datos no accesible

**Solución**:
1. Verificar que el servidor MySQL esté corriendo
2. Comprobar credenciales en `.env`
3. Verificar firewall y permisos de red

### Error 404 en todas las rutas

**Causa**: Mod_rewrite no configurado o .htaccess no leído

**Solución**:
1. Habilitar `mod_rewrite` en Apache
2. Verificar que `.htaccess` esté en la raíz del proyecto
3. Confirmar que `AllowOverride All` esté configurado

### Logs no se crean

**Causa**: Permisos insuficientes en directorio Logs/

**Solución**:
```bash
chmod 755 Logs/
chown www-data:www-data Logs/
```

---

## Dependencias

### PHP Packages

```json
{
  "firebase/php-jwt": "^6.0",
  "vlucas/phpdotenv": "^5.5"
}
```

- **firebase/php-jwt**: Encoding y decoding de JWT tokens
- **vlucas/phpdotenv**: Carga de variables de entorno desde .env

---

## Versionado

**Versión actual**: 1.0.0

### Changelog

- **1.0.0** (2025-11-02)
  - Sistema de autenticación JWT
  - Endpoints de productos, usuarios y opciones
  - Sistema dual de logging con rotación automática
  - Actualización masiva de stock, precios y nombres
  - Integración con WooCommerce

---

## Licencia

Este proyecto es privado y propietario. Todos los derechos reservados.

---

## Soporte

Para reportar bugs o solicitar nuevas características, contactar al equipo de desarrollo.

---

## Autores

- **Desarrollador Principal**: Cesar
- **Email**: perucaos@gmail.com

---

## Referencias

- [PHP JWT Library](https://github.com/firebase/php-jwt)
- [WooCommerce REST API](https://woocommerce.github.io/woocommerce-rest-api-docs/)
- [WordPress Database Schema](https://codex.wordpress.org/Database_Description)
