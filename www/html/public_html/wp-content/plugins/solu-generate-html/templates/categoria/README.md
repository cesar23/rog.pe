# Templates de Categorías - Solu Generate HTML

Esta carpeta contiene todos los templates utilizados para mostrar las vistas del plugin de generación de códigos HTML.

## Estructura de Archivos

### Templates Principales

- **`home.php`** - Dashboard principal de inicio con estadísticas y acciones rápidas
- **`list.php`** - Vista que muestra la lista completa de códigos HTML con estadísticas
- **`create.php`** - Formulario para crear nuevos códigos HTML
- **`edit.php`** - Formulario para editar códigos HTML existentes
- **`delete.php`** - Confirmación de eliminación de códigos HTML
- **`statistics.php`** - Componente de estadísticas (puede ser incluido en otros templates)
- **`info.php`** - Página de ayuda y documentación

### Variables Disponibles

#### En `edit.php` y `delete.php`:

- `$html_code` - Array con los datos del código HTML a editar/eliminar

#### En `list.php` y `home.php`:

- Las funciones `get_all_html_codes()` están disponibles para obtener los datos

## Tipos de Códigos

El sistema reconoce automáticamente los tipos de códigos basándose en el prefijo del nombre:

- **Categorías**: Prefijo `category_` (ej: `category_header`, `category_footer`)
- **Marcas**: Prefijo `brand_` (ej: `brand_header`, `brand_footer`)
- **Otros**: Sin prefijo específico

## Estilos CSS

Los badges de tipos utilizan las siguientes clases CSS:

- `.badge-category` - Para códigos de categorías (azul)
- `.badge-brand` - Para códigos de marcas (verde)
- `.badge-other` - Para otros códigos (gris)

## Funciones Disponibles

Los templates tienen acceso a las siguientes funciones del plugin:

- `get_all_html_codes()` - Obtener todos los códigos HTML
- `get_html_code($id)` - Obtener un código HTML específico
- `insert_html_code($data)` - Insertar nuevo código HTML
- `update_html_code($id, $data)` - Actualizar código HTML
- `delete_html_code($id)` - Eliminar código HTML

## Uso en la Clase Principal

La clase `Solu_Generate_HTML_Categoria` utiliza estos templates mediante `include`:

```php
private function display_home_dashboard()
{
    include SOLU_GENERATE_HTML_PATH . 'templates/categoria/home.php';
}

private function display_html_codes_list()
{
    include SOLU_GENERATE_HTML_PATH . 'templates/categoria/list.php';
}
```

## Estructura de Menús

El plugin ahora incluye los siguientes menús:

1. **Inicio** - Dashboard principal con estadísticas y acciones rápidas
2. **Ayuda** - Página de ayuda e información del plugin

## Ventajas de esta Estructura

1. **Separación de responsabilidades**: La lógica PHP está separada de la presentación HTML
2. **Mantenibilidad**: Es más fácil modificar las vistas sin tocar la lógica
3. **Reutilización**: Los templates pueden ser reutilizados en diferentes contextos
4. **Legibilidad**: El código es más limpio y fácil de entender
5. **Escalabilidad**: Fácil agregar nuevas vistas o modificar las existentes
6. **UX mejorada**: Dashboard de inicio con estadísticas visuales y acciones rápidas
