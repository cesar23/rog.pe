# Funcionalidades de WooCommerce - Solu Admin Utils

## 📋 Descripción

Este módulo añade funcionalidades avanzadas al panel de administración de WooCommerce, incluyendo filtros personalizados y columnas adicionales en el listado de productos.

## ✨ Características

### 🔍 Filtros Avanzados

- **Filtro por Destacados**: Permite filtrar productos por estado destacado (Sí/No/Todos)
- **Filtro por Stock**: Permite filtrar productos por estado de stock (En stock/Sin stock/Todos)

### 📊 Columnas Personalizadas

- **Stock (Cantidad)**: Muestra la cantidad exacta de stock con indicadores visuales
- **Destacado**: Indica si el producto está marcado como destacado
- **Precio (HTML)**: Muestra el precio formateado con HTML

## 🚀 Instalación y Configuración

### 1. Activación Automática

Las funcionalidades se activan automáticamente cuando:
- WooCommerce está instalado y activo
- El plugin Solu Admin Utils está activo

### 2. Configuración de Columnas

Puedes controlar las columnas personalizadas desde:
**Admin Utils → Ayuda → Configuración**

- ✅ **Habilitar columnas personalizadas**: Activa/desactiva las columnas adicionales
- ⚠️ **Si el listado se deforma**: Desactiva esta opción

## 🎨 Personalización Visual

### Estilos de Stock

- **En Stock**: Fondo verde con texto oscuro
- **Sin Stock**: Fondo rojo con texto claro
- **Sin Cantidad**: Texto gris e itálico

### Estilos de Destacados

- **Destacado**: ✅ Verde con "Sí"
- **No Destacado**: ❌ Gris con "No"

## 🔧 Uso

### Filtros

1. Ve a **Productos** en el admin de WordPress
2. En la parte superior verás dos filtros:
   - **Destacados**: Selecciona el estado de destacado
   - **Stock**: Selecciona el estado de stock
3. Haz clic en **Filtrar** para aplicar los filtros

### Columnas

Las columnas aparecen automáticamente en el listado de productos. Puedes:
- **Ocultar columnas**: Usa "Opciones de pantalla" en la parte superior
- **Ordenar por columnas**: Haz clic en los encabezados de columna

## 🛠️ Solución de Problemas

### El listado se deforma

Si las columnas personalizadas causan problemas de diseño:

1. Ve a **Admin Utils → Ayuda**
2. En la sección **Configuración**
3. Desactiva **"Habilitar columnas personalizadas"**
4. Guarda los cambios

### Los filtros no funcionan

Verifica que:
- WooCommerce esté activo
- Tengas permisos de administrador
- No haya conflictos con otros plugins

### Las columnas no aparecen

1. Verifica que las columnas estén habilitadas en la configuración
2. Usa "Opciones de pantalla" para mostrar/ocultar columnas
3. Limpia la caché del navegador

## 📝 Código de Ejemplo

### Agregar filtro personalizado

```php
// En tu tema o plugin
add_action('restrict_manage_posts', function($post_type) {
    if ($post_type !== 'product') return;
    
    // Tu filtro personalizado aquí
    echo '<select name="mi_filtro">';
    echo '<option value="">Mi Filtro</option>';
    echo '</select>';
});
```

### Aplicar filtro a la consulta

```php
add_action('pre_get_posts', function($query) {
    if (!is_admin() || $query->get('post_type') !== 'product') return;
    
    $mi_filtro = $_GET['mi_filtro'] ?? '';
    if ($mi_filtro) {
        // Aplicar tu lógica de filtro
    }
});
```

## 🔒 Seguridad

- Todos los filtros están sanitizados
- Las consultas usan prepared statements
- Se verifica la capacidad del usuario
- Se valida el contexto de administración

## 📊 Rendimiento

- Las consultas están optimizadas
- Se usa caché cuando es posible
- Los estilos se cargan solo cuando es necesario
- Compatible con consultas existentes

## 🤝 Compatibilidad

### Plugins Compatibles

- ✅ WooCommerce (requerido)
- ✅ WooCommerce Subscriptions
- ✅ WooCommerce Bookings
- ✅ Product Add-ons
- ✅ Advanced Custom Fields

### Temas Compatibles

- ✅ Temas estándar de WordPress
- ✅ Temas de WooCommerce
- ✅ Temas personalizados
- ✅ Modo oscuro (si está disponible)

## 📞 Soporte

Para problemas específicos con las funcionalidades de WooCommerce:

- **Email**: perucaos@gmail.com
- **Sitio Web**: https://solucionessystem.com
- **Documentación**: Este archivo

---

**Nota**: Estas funcionalidades requieren WooCommerce para funcionar correctamente.
