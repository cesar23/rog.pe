# Solu Currencies Exchange

## Descripción

Este plugin permite manejar el tipo de cambio de monedas en un sitio web de WooCommerce.

## Estructura del Fichero

El plugin tiene la siguiente estructura de ficheros:

*   `solu-currencies-exchange-rate.php`: Archivo principal del plugin.
*   `includes/`: Directorio que contiene los archivos de inclusión del plugin.
    *   `admin/`: Directorio que contiene los archivos de administración del plugin.
        *   `class-solu-currencies-exchange-admin.php`: Clase responsable de manejar la administración del plugin.
        *   `menus_admin.php`: Archivo que define los menús de administración del plugin.
        *   `utils.php`: Archivo que contiene funciones de utilidad para el plugin.
    *   `front_filters.php`: Archivo que contiene los filtros para el frontend del plugin.
    *   `theme_functions.php`: Archivo que contiene funciones para el tema.
    *   `config/`: Directorio que contiene los archivos de configuración del plugin.
        *   `global_vars.php`: Archivo que define las variables globales del plugin.
    *   `templates/`: Directorio que contiene las plantillas del plugin.
        *   `admin/`: Directorio que contiene las plantillas para el panel de administración.
            *   `list.php`: Plantilla para listar las monedas.
            *   `create.php`: Plantilla para crear una nueva moneda.
            *   `edit.php`: Plantilla para editar una moneda existente.
            *   `delete.php`: Plantilla para eliminar una moneda existente.
            *   `info.php`: Plantilla para mostrar información del plugin.
*   `assets/`: Directorio que contiene los assets del plugin.
    *   `admin/`: Directorio que contiene los assets para el panel de administración.
        *   `css/`: Directorio que contiene los archivos CSS del plugin.
        *   `js/`: Directorio que contiene los archivos JavaScript del plugin.
        *   `bootstrap-5.3.7/`: Directorio que contiene los archivos de Bootstrap 5.3.7.
        *   `prismjs/`: Directorio que contiene los archivos de Prism.js.

## Instalación

1.  Sube el plugin al directorio `/wp-content/plugins/`
2.  Activa el plugin desde la página de 'Plugins' en WordPress.

## Uso

1.  Ve a la página de configuración del plugin en el menú de WordPress.
2.  Configura las opciones del plugin.
3.  Guarda los cambios.

Para que el plugin funcione correctamente, las monedas de US (Dólar estadounidense) y PEN (Sol peruano) deben estar habilitadas.

Si deseas editar el tipo de cambio para la moneda EUR (Euro), debes editar las siguientes líneas:

*   `$currency_storage = getStorageTableJsonRow('PEN');` en [wp-content/plugins/solu-currencies-exchange-rate/includes/front_filters.php:111](wp-content/plugins/solu-currencies-exchange-rate/includes/front_filters.php:111)
*   `$currency_storage = getStorageTableJsonRow('PEN');` en [wp-content/plugins/solu-currencies-exchange-rate/includes/theme_functions.php:261](wp-content/plugins/solu-currencies-exchange-rate/includes/theme_functions.php:261)

y cambiar `'PEN'` por `'EUR'`.

## Solución de problemas

Si tienes algún problema con el plugin, puedes probar lo siguiente:

*   Verifica que WooCommerce esté instalado y activado.
*   Verifica que las opciones del plugin estén configuradas correctamente.
*   Contacta al desarrollador si el problema persiste.

## Contacto

Si tienes alguna pregunta o sugerencia, puedes contactar al desarrollador a través de:

*   Correo electrónico: perucaos@gmail.com