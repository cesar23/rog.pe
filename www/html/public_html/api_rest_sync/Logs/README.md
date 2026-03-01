# Sistema de Logs - API REST Sync

## Access Log

El sistema registra automáticamente todas las peticiones HTTP en el archivo `access_log.log`.

### Formato del Log

Cada entrada de log contiene la siguiente información:

```
[TIMESTAMP] [REQUEST_ID] METHOD URI - IP: CLIENT_IP - User-Agent: USER_AGENT - Host: HOST
```

**Ejemplo:**
```
[2025-11-02 13:32:22] [req_6907a3b6063bb6.64708254] GET /api_rest_sync/health-check - IP: 192.168.0.52 - User-Agent: curl/8.9.0 - Host: adcomputers.local
```

### Campos del Log

- **TIMESTAMP**: Fecha y hora de la petición (formato: Y-m-d H:i:s)
- **REQUEST_ID**: Identificador único de la petición (también enviado en header `X-Request-ID`)
- **METHOD**: Método HTTP (GET, POST, PUT, DELETE, etc.)
- **URI**: Ruta completa de la petición
- **CLIENT_IP**: Dirección IP del cliente
- **USER_AGENT**: Cliente o navegador que realizó la petición
- **HOST**: Host de la petición

## Response Log

El sistema registra automáticamente todas las respuestas HTTP en el archivo `response_log.log`.

### Formato del Log

Cada entrada de log contiene la siguiente información:

```
[TIMESTAMP] [REQUEST_ID] Status: HTTP_STATUS - Response: RESPONSE_BODY
```

**Ejemplo:**
```
[2025-11-02 13:40:32] [req_6907a5a036eb75.23645529] Status: 401 - Response: {"status":"error","result":[],"application_date":"2025-11-02 13:40:32","error_id":"401","error_msg":"Su Token:null  es invalido"}
```

### Campos del Log

- **TIMESTAMP**: Fecha y hora de la respuesta (formato: Y-m-d H:i:s)
- **REQUEST_ID**: Identificador único que correlaciona con el access log
- **HTTP_STATUS**: Código de estado HTTP (200, 401, 404, 500, etc.)
- **RESPONSE_BODY**: Cuerpo completo de la respuesta enviada al cliente

### Correlación de Logs

El `REQUEST_ID` permite correlacionar cada petición con su respuesta:

**Access Log:**
```
[2025-11-02 13:40:32] [req_6907a5a036eb75.23645529] GET /api_rest_sync/product/get-poducts-id-softlink?token=null - IP: 192.168.0.52 - User-Agent: curl/8.9.0 - Host: adcomputers.local
```

**Response Log:**
```
[2025-11-02 13:40:32] [req_6907a5a036eb75.23645529] Status: 401 - Response: {"status":"error","result":[],"application_date":"2025-11-02 13:40:32","error_id":"401","error_msg":"Su Token:null  es invalido"}
```

## Rotación de Logs

El sistema implementa rotación automática basada en tamaño para **ambos** tipos de logs (access y response):

### Parámetros de Rotación

- **Tamaño máximo**: 5MB por archivo
- **Archivos históricos**: 5 archivos rotados por cada tipo de log
- **Total de espacio**: ~60MB máximo (12 archivos × 5MB)

### Funcionamiento

Cuando `access_log.log` o `response_log.log` alcanza 5MB:

1. Se verifica si existe el archivo `.5` (el más antiguo) y se elimina
2. Los archivos se rotan:
   - `*.log.4` → `*.log.5`
   - `*.log.3` → `*.log.4`
   - `*.log.2` → `*.log.3`
   - `*.log.1` → `*.log.2`
3. El archivo actual `*.log` se renombra a `*.log.1`
4. Se crea un nuevo `*.log` vacío

### Estructura de Archivos

```
Logs/
├── access_log.log        # Archivo actual de peticiones (activo)
├── access_log.log.1      # Primera rotación (más reciente)
├── access_log.log.2      # Segunda rotación
├── access_log.log.3      # Tercera rotación
├── access_log.log.4      # Cuarta rotación
├── access_log.log.5      # Quinta rotación (más antigua)
├── response_log.log      # Archivo actual de respuestas (activo)
├── response_log.log.1    # Primera rotación (más reciente)
├── response_log.log.2    # Segunda rotación
├── response_log.log.3    # Tercera rotación
├── response_log.log.4    # Cuarta rotación
└── response_log.log.5    # Quinta rotación (más antigua)
```

### Configuración

La configuración se encuentra en `index.php`:

```php
$maxFileSize = 5 * 1024 * 1024; // 5MB en bytes
$maxBackups = 5; // Mantener 5 archivos históricos
```

## Visualización de Logs

### Ver logs en tiempo real

```bash
# Ver peticiones en tiempo real
tail -f Logs/access_log.log

# Ver respuestas en tiempo real
tail -f Logs/response_log.log

# Ver ambos simultáneamente
tail -f Logs/access_log.log Logs/response_log.log
```

### Ver todos los logs (incluidos rotados)

```bash
# Ver todos los access logs
cat Logs/access_log.log*

# Ver todos los response logs
cat Logs/response_log.log*
```

### Buscar por REQUEST_ID (correlacionar petición y respuesta)

```bash
# Buscar en ambos logs para ver el ciclo completo
grep "req_6907a3b6063bb6.64708254" Logs/access_log.log*
grep "req_6907a3b6063bb6.64708254" Logs/response_log.log*
```

### Búsquedas específicas

```bash
# Ver logs por método HTTP
grep "POST" Logs/access_log.log*

# Ver logs por IP
grep "IP: 192.168.0.52" Logs/access_log.log*

# Ver respuestas por código de estado
grep "Status: 401" Logs/response_log.log*
grep "Status: 200" Logs/response_log.log*

# Ver errores en respuestas
grep "error" Logs/response_log.log*

# Ver respuestas exitosas
grep "\"status\":\"ok\"" Logs/response_log.log*
```

### Análisis de tráfico

```bash
# Contar peticiones por endpoint
grep -oP '/api_rest_sync/\S+' Logs/access_log.log | sort | uniq -c | sort -rn

# Contar respuestas por código de estado
grep -oP 'Status: \d+' Logs/response_log.log | sort | uniq -c

# Ver últimas 10 peticiones
tail -10 Logs/access_log.log

# Ver últimas 10 respuestas
tail -10 Logs/response_log.log
```

## Mantenimiento

El sistema de rotación es automático y no requiere intervención manual. Los logs antiguos se eliminan automáticamente cuando se alcanza el límite de 5 archivos rotados.

### Limpieza Manual (si es necesaria)

```bash
# Eliminar todos los access logs rotados
rm -f Logs/access_log.log.*

# Eliminar todos los response logs rotados
rm -f Logs/response_log.log.*

# Vaciar logs actuales
> Logs/access_log.log
> Logs/response_log.log

# Eliminar todos los logs completamente
rm -f Logs/access_log.log* Logs/response_log.log*
```

## Consideraciones de Seguridad

- Los archivos de log tienen permisos `0755` para el directorio y se crean con permisos estándar
- No se registran datos sensibles como contraseñas o tokens
- Los logs están excluidos del control de versiones vía `.gitignore`
