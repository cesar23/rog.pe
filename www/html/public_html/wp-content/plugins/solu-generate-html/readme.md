# Solu Generate HTML

## Descripción

Plugin simple y estándar para generar y gestionar códigos HTML desde el backend de WordPress. Permite crear, editar y eliminar fragmentos de código HTML organizados por tipos (categorías, marcas, etc.) que pueden ser utilizados en diferentes partes del sitio web.

## Características

- **Estructura Simple**: Código fácil de entender y mantener
- **Gestión Unificada**: Todos los códigos HTML en una sola interfaz
- **Organización por Tipos**: Categorías, marcas y otros tipos de códigos
- **Editor con Resaltado**: Editor de código con resaltado de sintaxis usando Prism.js
- **Base de Datos Directa**: Acceso directo a la base de datos sin cache complejo
- **Sistema de Logs**: Registro de todas las operaciones realizadas
- **Interfaz Bootstrap**: Interfaz moderna usando Bootstrap 5.3.7
- **Estadísticas**: Vista general de todos los códigos organizados por tipo

## Requisitos

- WordPress 6.7 o superior
- PHP 8.0 o superior
- WooCommerce (activo)

## Instalación

1. Sube el plugin a la carpeta `/wp-content/plugins/`
2. Activa el plugin desde el panel de administración
3. Accede al menú "Códigos HTML" en el panel de administración

## Estructura del Plugin

```
solu-generate-html/
├── assets/
│   └── admin/           # Assets del backend (CSS, JS, Bootstrap, Prism.js)
├── includes/
│   ├── admin/           # Clases y funciones del backend
│   ├── config/          # Configuración global
│   ├── utils/           # Utilidades (fechas, precios, logs)
│   ├── entities/        # Entidades de datos (SoluHtmlCode)
│   ├── functions.php    # Funciones principales y CRUD
│   └── storage_functions.php # Funciones de almacenamiento JSON
├── storage/             # Archivos de almacenamiento
├── templates/
│   └── admin/           # Plantillas del backend
├── uninstall/           # Archivos de desinstalación
├── solu-generate-html.php # Archivo principal
└── uninstall.php        # Script de desinstalación
```

## Funcionalidades del Backend

### 1. Gestión Unificada
- **Interfaz Única**: Todos los códigos HTML en una sola página
- **Organización por Tipos**: Categorías, marcas y otros tipos
- **Estadísticas**: Vista general de todos los códigos

### 2. Tipos de Códigos
- **Categorías**: Usa prefijo "category_" para códigos de categorías
- **Marcas**: Usa prefijo "brand_" para códigos de marcas
- **Otros**: Cualquier otro nombre sin prefijo específico

### 3. Editor de Código
- Editor de texto con resaltado de sintaxis (Prism.js)
- Soporte para HTML, CSS, JavaScript y PHP
- Validación de campos obligatorios
- Variables disponibles según el contexto

### 4. Sistema de Logs
- Registro de todas las operaciones CRUD
- Información de usuario y timestamp
- Almacenamiento en base de datos

### 5. Base de Datos Directa
- Acceso directo a la base de datos
- Sin cache complejo
- Operaciones más simples y rápidas

## Configuración

### Tablas de Base de Datos

El plugin crea automáticamente las siguientes tablas:

- `wp_solu_generate_html` - Tabla principal de códigos HTML
- `wp_solu_generate_html_log` - Tabla de logs

### Base de Datos

- `wp_solu_generate_html` - Tabla principal de códigos HTML
- `wp_solu_generate_html_log` - Tabla de logs

## Uso

### Acceso Principal
1. **Acceder al Plugin**: Ve a "Generar HTML" en el menú de WordPress
2. **Ver Estadísticas**: En la página principal verás un resumen de todos los códigos
3. **Gestionar Códigos**: Usa la tabla para ver, editar o eliminar códigos existentes

### Crear Códigos HTML
1. **Agregar Nuevo**: Haz clic en "Agregar Nuevo Código HTML"
2. **Tipos de Códigos**:
   - **Categorías**: Usa prefijo "category_" (ej: category_header, category_footer)
   - **Marcas**: Usa prefijo "brand_" (ej: brand_header, brand_footer)
   - **Otros**: Cualquier otro nombre sin prefijo específico
3. **Variables Disponibles**:
   - Para categorías: `$category->name`, `$category->description`, etc.
   - Para marcas: `$brand->name`, `$brand->description`, etc.

### Operaciones CRUD
- **Crear**: Usa el botón "Agregar Nuevo Código HTML"
- **Editar**: Haz clic en "Editar" en la lista de códigos
- **Eliminar**: Haz clic en "Borrar" en la lista de códigos
- **Organizar**: Usa nombres de grupo descriptivos para organizar tus códigos

## API de Funciones

### Funciones CRUD

```php
// Crear un nuevo código HTML
insert_html_code($data);

// Actualizar un código HTML existente
update_html_code($id, $data);

// Eliminar un código HTML
delete_html_code($id);

// Obtener un código HTML por ID
get_html_code($id);

// Obtener todos los códigos HTML
get_all_html_codes();
```

### Funciones de Base de Datos

```php
// Obtener todos los códigos HTML
get_all_html_codes();

// Obtener un código HTML por ID
get_html_code($id);

// Crear un nuevo código HTML
insert_html_code($data);

// Actualizar un código HTML
update_html_code($id, $data);

// Eliminar un código HTML
delete_html_code($id);
```

## Desinstalación

El plugin incluye un script de desinstalación que:

- Elimina las tablas de base de datos
- Elimina las opciones de WordPress
- Limpia los archivos JSON de cache

## Autor

**César Auris** - [perucaos@gmail.com](mailto:perucaos@gmail.com)
**Sitio Web**: [https://solucionessystem.com](https://solucionessystem.com)

## Versión

1.0.4 - Sin Cache JSON

## Licencia

GPLv2 o posterior