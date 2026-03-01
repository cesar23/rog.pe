# Solu Admin Utils

Herramientas administrativas para WordPress/WooCommerce - Generador HTML, filtros avanzados y utilidades.

## 📋 Descripción

Plugin de WordPress que proporciona herramientas administrativas avanzadas para sitios web con WooCommerce. Incluye funcionalidades para generación de HTML, filtros avanzados, sistema de logging y utilidades para fechas, precios y strings.

## ✨ Características

- **Generador de HTML**: Crea y gestiona códigos HTML para categorías, marcas y productos
- **Filtros de WooCommerce**: Filtros avanzados en el admin de productos
- **Sistema de Logging**: Registro de eventos con rotación automática
- **Utilidades**: Herramientas para fechas, precios y strings
- **Seguridad**: Sistema de permisos basado en emails autorizados
- **Interfaz Moderna**: UI con Bootstrap 5 y Prism.js para resaltado de sintaxis

## 🚀 Instalación

1. Sube el plugin a la carpeta `/wp-content/plugins/`
2. Activa el plugin a través del menú 'Plugins' en WordPress
3. Asegúrate de que WooCommerce esté instalado y activado
4. Accede a las herramientas desde el menú "Admin Utils" en el admin

## 📁 Estructura del Plugin

```
solu-admin-utils/
├── assets/                 # Assets CSS, JS e imágenes
│   └── admin/             # Assets del backend
├── includes/              # Archivos principales del plugin
│   ├── admin/             # Funcionalidades del backend
│   │   └── woocommerce/   # Integración con WooCommerce
│   └── config/            # Configuraciones globales
├── templates/             # Plantillas de interfaz
├── utils/                 # Clases utilitarias
│   ├── DateUtils.php      # Manejo de fechas
│   ├── PriceUtils.php     # Manejo de precios
│   ├── LogUtils.php       # Sistema de logging
│   └── StringUtils.php    # Utilidades de strings
├── solu-admin-utils.php   # Archivo principal
└── README.md             # Este archivo
```

## 🔧 Configuración

### Permisos de Usuario

El plugin utiliza un sistema de permisos basado en emails autorizados. Los usuarios autorizados por defecto son:

- `perucaos@gmail.com`
- `editor2@solucionesssystem.com`
- `juan@gmail.com`
- `ventas@pcbyte.com.pe`

Para modificar la lista de usuarios autorizados, edita la opción `solu_admin_utils_allowed_emails` en la base de datos.

### Sistema de Logging

Los logs se almacenan en:
- Archivo principal: `ABSPATH/solu_log.log`
- Archivo de errores: `ABSPATH/solu_log_error.log`

El sistema incluye rotación automática cuando los archivos superan 5MB.

## 📖 Uso

### Funciones de Conveniencia

```php
// Obtener instancia de DateUtils
$dateUtils = solu_date_utils('America/Lima');
$fecha_actual = $dateUtils->getCurrentDateTime();

// Obtener instancia de StringUtils
$stringUtils = solu_string_utils();

// Logging específico del plugin
solu_admin_utils_log('Mensaje de prueba', 'info');
```

### Zonas Horarias Soportadas

El plugin soporta múltiples zonas horarias:

```php
// América del Sur
$dateUtilsLima = solu_date_utils('America/Lima');
$dateUtilsBogota = solu_date_utils('America/Bogota');

// América del Norte
$dateUtilsNY = solu_date_utils('America/New_York');
$dateUtilsLA = solu_date_utils('America/Los_Angeles');

// Europa
$dateUtilsMadrid = solu_date_utils('Europe/Madrid');
$dateUtilsLondon = solu_date_utils('Europe/London');
```

## 🛠️ Desarrollo

### Requisitos

- WordPress 6.7+
- PHP 8.0+
- WooCommerce (activo)

### Estándares de Código

El plugin sigue las mejores prácticas de WordPress y Clean Code:

- Documentación completa con PHPDoc
- Nombres de funciones y variables en inglés
- Comentarios en español
- Arquitectura modular y escalable

### Herramientas de Desarrollo

- **ESLint**: Para linting de JavaScript
- **Prettier**: Para formateo de código
- **PHP_CodeSniffer**: Para estándares PHP (recomendado)

## 🔒 Seguridad

- Verificación de permisos de usuario
- Sanitización de datos de entrada
- Escape de datos de salida
- Logging de eventos importantes
- Validación de nonces en formularios

## 📝 Changelog

### Versión 1.2.0
- ✅ Corrección de inconsistencias en nombres de funciones
- ✅ Mejora en la documentación del código
- ✅ Agregado sistema de configuración ESLint/Prettier
- ✅ Corrección de nombres de archivos
- ✅ Función de notificación faltante agregada

### Versión 1.0.4
- 🎉 Lanzamiento inicial del plugin

## 🤝 Soporte

Para soporte técnico o reportar problemas:

- **Email**: perucaos@gmail.com
- **Sitio Web**: https://solucionessystem.com

## 📄 Licencia

Este plugin está licenciado bajo GPL v2 o posterior.

## 👨‍💻 Autor

**César Auris**
- Email: perucaos@gmail.com
- Sitio Web: https://solucionessystem.com

---

**Nota**: Este plugin requiere WooCommerce para funcionar correctamente.
