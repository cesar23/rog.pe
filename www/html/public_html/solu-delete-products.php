<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Eliminar Productos - Modo Black</title>

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
      body {
          background-color: #000;
          color: white;
          font-family: Arial, sans-serif;
      }

      span,
      p,
      a,
      div,
      li,
      h1,
      h2,
      h3,
      h4,
      h5,
      h6,
      label {
          color: white !important;
      }

      .btn-dark-custom {
          background-color: #222;
          border: none;
          color: #fff;
      }

      .btn-dark-custom:hover {
          background-color: #444;
      }

      .btn-danger-custom {
          background-color: #dc3545;
          border: none;
          color: #fff;
      }

      .btn-danger-custom:hover {
          background-color: #c82333;
      }

      .btn-primary-custom {
          background-color: #007bff;
          border: none;
          color: #fff;
      }

      .btn-primary-custom:hover {
          background-color: #0056b3;
      }

      .alert-danger-custom {
          background-color: #721c24;
          border-color: #f5c6cb;
          color: #f8d7da;
      }

      .alert-success-custom {
          background-color: #155724;
          border-color: #c3e6cb;
          color: #d4edda;
      }

      .alert-warning-custom {
          background-color: #856404;
          border-color: #ffeaa7;
          color: #fff3cd;
      }

      .card-custom {
          background-color: #1a1a1a;
          border: 1px solid #333;
      }

      .table-dark-custom {
          background-color: #1a1a1a;
          color: white !important;
          border-collapse: separate;
          border-spacing: 0;
      }

      .table-dark-custom thead th {
          background-color: #0d0d0d;
          border: 1px solid #333;
          color: white !important;
          font-weight: bold;
          padding: 12px;
          text-align: center;
          vertical-align: middle;
          position: sticky;
          top: 0;
          z-index: 10;
      }

      .table-dark-custom tbody td {
          background-color: #1a1a1a;
          border: 1px solid #333;
          color: white !important;
          padding: 12px;
          vertical-align: middle;
      }

      .table-dark-custom tbody tr {
          transition: background-color 0.2s;
      }

      .table-dark-custom tbody tr:hover {
          background-color: #2a2a2a;
      }

      .table-dark-custom tbody tr:hover td {
          background-color: #2a2a2a;
      }

      /* Estilos para la imagen del producto */
      .product-image-cell {
          text-align: center;
          padding: 8px !important;
      }

      .product-image-wrapper {
          display: inline-block;
          width: 100px;
          height: 100px;
          border: 2px solid #444;
          border-radius: 8px;
          overflow: hidden;
          background-color: #2a2a2a;
      }

      .product-image-wrapper img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          display: block;
      }

      .no-image-placeholder {
          width: 100px;
          height: 100px;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #2a2a2a;
          border: 2px dashed #555;
          border-radius: 8px;
          color: #666;
          font-size: 12px;
          text-align: center;
      }

      .pagination-dark .page-link {
          background-color: #222;
          border-color: #444;
          color: white;
      }

      .pagination-dark .page-link:hover {
          background-color: #444;
          color: white;
      }

      .pagination-dark .page-item.active .page-link {
          background-color: #007bff;
          border-color: #007bff;
      }

      .pagination-dark .page-item.disabled .page-link {
          background-color: #1a1a1a;
          border-color: #333;
          color: #666;
      }

      .log-container {
          background-color: #111;
          border: 1px solid #333;
          border-radius: 8px;
          padding: 20px;
          margin: 20px 0;
          max-height: 500px;
          overflow-y: auto;
      }

      /* Estilos para checkboxes */
      .table-dark-custom input[type="checkbox"] {
          width: 20px;
          height: 20px;
          cursor: pointer;
      }

      /* Estilos para el nombre del producto */
      .product-name {
          font-weight: bold;
          color: white !important;
          margin-bottom: 4px;
      }

      .product-id {
          color: #888 !important;
          font-size: 12px;
      }

      /* Estilos para categorías */
      .category-list {
          color: #4CAF50 !important;
          font-size: 13px;
      }

      .category-list a {
          color: #4CAF50 !important;
          text-decoration: none;
      }

      .category-list a:hover {
          color: #66BB6A !important;
          text-decoration: underline;
      }

      /* Ajuste de anchos de columna */
      .col-checkbox {
          width: 50px;
          text-align: center;
      }

      .col-image {
          width: 120px;
          text-align: center;
      }

      .col-sku {
          width: 100px;
      }

      .col-name {
          min-width: 200px;
      }

      .col-price {
          width: 100px;
          text-align: right;
      }

      .col-stock {
          width: 80px;
          text-align: center;
      }

      .col-categories {
          width: 150px;
      }

      .col-gallery {
          width: 100px;
          text-align: center;
      }

      /* Badge para galería */
      .gallery-badge {
          background-color: #007bff;
          color: white;
          padding: 4px 8px;
          border-radius: 4px;
          font-size: 12px;
          display: inline-block;
      }

      /* Stock status */
      .stock-status {
          padding: 4px 8px;
          border-radius: 4px;
          font-size: 13px;
          display: inline-block;
      }

      .stock-in {
          background-color: #155724;
          color: #d4edda;
      }

      .stock-out {
          background-color: #721c24;
          color: #f8d7da;
      }

      /* Precio */
      .product-price {
          color: #4CAF50 !important;
          font-weight: bold;
          font-size: 14px;
      }
  </style>
</head>

<body class="container py-5">

<?php
// =============================================================================
// 🔥 SCRIPT: Eliminar productos de WooCommerce con paginación
// =============================================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

$saltoLinea = PHP_EOL;
$is_cli = php_sapi_name() === 'cli';
if (!$is_cli) {
  $saltoLinea = "<br>";
}

// Función para colorear texto según el entorno
function colorize($text, $color_code)
{
  global $is_cli;
  if ($is_cli) {
    return $color_code . $text . '\033[0m';
  } else {
    $html_colors = [
        '\033[0;30m' => '<span style="color: black;">',
        '\033[0;31m' => '<span style="color: red;">',
        '\033[0;32m' => '<span style="color: green;">',
        '\033[0;33m' => '<span style="color: orange;">',
        '\033[0;34m' => '<span style="color: blue;">',
        '\033[0;35m' => '<span style="color: purple;">',
        '\033[0;36m' => '<span style="color: cyan;">',
        '\033[0;37m' => '<span style="color: white;">',
        '\033[0;90m' => '<span style="color: gray;">',
        '\033[1;30m' => '<span style="color: black; font-weight: bold;">',
        '\033[1;31m' => '<span style="color: red; font-weight: bold;">',
        '\033[1;32m' => '<span style="color: green; font-weight: bold;">',
        '\033[1;33m' => '<span style="color: orange; font-weight: bold;">',
        '\033[1;34m' => '<span style="color: blue; font-weight: bold;">',
        '\033[1;35m' => '<span style="color: purple; font-weight: bold;">',
        '\033[1;36m' => '<span style="color: cyan; font-weight: bold;">',
        '\033[1;37m' => '<span style="color: white; font-weight: bold;">',
        '\033[1;90m' => '<span style="color: gray; font-weight: bold;">',
        '\033[0m' => '</span>'
    ];
    return str_replace(array_keys($html_colors), array_values($html_colors), $color_code . $text . '\033[0m');
  }
}

// =============================================================================
// 🎨 SECTION: Colores
// =============================================================================
$Color_Off = '\033[0m';
$Red = '\033[0;31m';
$Green = '\033[0;32m';
$Yellow = '\033[0;33m';
$Blue = '\033[0;34m';
$Purple = '\033[0;35m';
$Cyan = '\033[0;36m';
$White = '\033[0;37m';
$Gray = '\033[0;90m';

$BRed = '\033[1;31m';
$BGreen = '\033[1;32m';
$BYellow = '\033[1;33m';
$BBlue = '\033[1;34m';
$BCyan = '\033[1;36m';

// =============================================================================
// 🔥 FUNCIONES
// =============================================================================

require_once 'wp-load.php';

// Función para eliminar las imágenes de un producto
function delete_product_images($product_id)
{
  global $saltoLinea, $Red, $Green, $Yellow;

  $deleted_images = 0;
  $product = wc_get_product($product_id);

  if (!$product) {
    echo colorize("  ✗ Error: No se pudo obtener el producto ID: $product_id", $Red) . $saltoLinea;
    return 0;
  }

  // Eliminar imagen principal
  $image_id = $product->get_image_id();
  if ($image_id) {
    $image_path = get_attached_file($image_id);
    $delete_result = wp_delete_attachment($image_id, true);

    if ($delete_result) {
      echo colorize("    ✓ Imagen principal eliminada: " . basename($image_path), $Green) . $saltoLinea;
      $deleted_images++;
    } else {
      echo colorize("    ✗ Error al eliminar imagen principal: " . basename($image_path), $Red) . $saltoLinea;
    }
  }

  // Eliminar imágenes de galería
  $gallery_image_ids = $product->get_gallery_image_ids();
  if (!empty($gallery_image_ids)) {
    echo colorize("    → Eliminando " . count($gallery_image_ids) . " imágenes de galería...", $Yellow) . $saltoLinea;

    foreach ($gallery_image_ids as $gallery_image_id) {
      $gallery_image_path = get_attached_file($gallery_image_id);
      $delete_result = wp_delete_attachment($gallery_image_id, true);

      if ($delete_result) {
        echo colorize("      ✓ Imagen de galería eliminada: " . basename($gallery_image_path), $Green) . $saltoLinea;
        $deleted_images++;
      } else {
        echo colorize("      ✗ Error al eliminar imagen de galería: " . basename($gallery_image_path), $Red) . $saltoLinea;
      }
    }
  }

  return $deleted_images;
}

// Función para eliminar un producto con sus imágenes
function delete_product_with_images($product_id)
{
  global $saltoLinea, $Red, $Green, $Yellow, $Cyan;

  $product = wc_get_product($product_id);

  if (!$product) {
    echo colorize("✗ Error: No se pudo obtener información del producto ID: $product_id", $Red) . $saltoLinea;
    return false;
  }

  $product_name = $product->get_name();
  $product_sku = $product->get_sku();

  echo colorize("Procesando producto: '$product_name' (ID: $product_id, SKU: $product_sku)", $Cyan) . $saltoLinea;

  // Eliminar imágenes del producto
  $deleted_images = delete_product_images($product_id);

  // Eliminar el producto
  $delete_result = $product->delete(true); // true = forzar eliminación permanente

  if ($delete_result) {
    echo colorize("  ✓ Producto eliminado: '$product_name' ($deleted_images imágenes eliminadas)", $Green) . $saltoLinea;
    return true;
  } else {
    echo colorize("  ✗ Error al eliminar producto: '$product_name'", $Red) . $saltoLinea;
    return false;
  }
}

// Función para eliminar productos seleccionados
function delete_selected_products($product_ids)
{
  global $saltoLinea, $Red, $Green, $Yellow, $BRed, $BGreen, $BBlue;

  echo colorize("=== INICIANDO ELIMINACIÓN DE PRODUCTOS SELECCIONADOS ===", $BRed) . $saltoLinea;
  echo colorize("Fecha y hora: " . date('Y-m-d H:i:s'), $Yellow) . $saltoLinea;
  echo "----------------------------------------" . $saltoLinea;

  $total_products = count($product_ids);
  echo colorize("Total de productos a eliminar: $total_products", $BBlue) . $saltoLinea;
  echo "----------------------------------------" . $saltoLinea;

  $deleted_count = 0;
  $error_count = 0;

  foreach ($product_ids as $product_id) {
    echo "----------------------------------------" . $saltoLinea;
    $result = delete_product_with_images($product_id);

    if ($result) {
      $deleted_count++;
    } else {
      $error_count++;
    }
  }

  echo "----------------------------------------" . $saltoLinea;
  echo colorize("=== RESUMEN DE ELIMINACIÓN ===", $BBlue) . $saltoLinea;
  echo colorize("Productos eliminados exitosamente: $deleted_count", $BGreen) . $saltoLinea;
  echo colorize("Errores durante la eliminación: $error_count", $BRed) . $saltoLinea;
  echo colorize("Total procesado: " . ($deleted_count + $error_count), $Yellow) . $saltoLinea;
}

// Función para obtener productos con paginación
function get_products_paginated($page = 1, $per_page = 20, $search = '')
{
  $args = [
      'status' => 'any',
      'limit' => $per_page,
      'page' => $page,
      'paginate' => true,
      'orderby' => 'date',
      'order' => 'DESC'
  ];

  if (!empty($search)) {
    $args['s'] = $search;
  }

  return wc_get_products($args);
}

// =============================================================================
// 🚀 EJECUCIÓN PRINCIPAL
// =============================================================================

if (!$is_cli) {
  echo '<div class="row justify-content-center">';
  echo '<div class="col-md-12">';
  echo '<div class="card card-custom">';
  echo '<div class="card-header text-center">';
  echo '<h2>🗑️ Eliminación de Productos de WooCommerce</h2>';
  echo '</div>';
  echo '<div class="card-body">';

  // Procesar eliminación si se envió el formulario
  if (isset($_POST['delete_products']) && isset($_POST['product_ids'])) {
    echo '<div class="alert alert-danger-custom">';
    echo '<strong>⚠️ Procesando eliminación...</strong>';
    echo '</div>';

    echo '<div class="log-container">';
    delete_selected_products($_POST['product_ids']);
    echo '</div>';

    echo '<div class="alert alert-success-custom mt-3">';
    echo '<strong>✅ Proceso completado</strong>';
    echo '</div>';

    echo '<div class="text-center mt-3">';
    echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn btn-primary-custom">← Volver al listado</a>';
    echo '</div>';
  } else {
    // Obtener parámetros de paginación y búsqueda
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

    // Obtener productos
    $products_data = get_products_paginated($current_page, $per_page, $search);
    $products = $products_data->products;
    $total_products = $products_data->total;
    $total_pages = $products_data->max_num_pages;

    // Formulario de búsqueda
    echo '<form method="get" class="mb-4">';
    echo '<div class="row">';
    echo '<div class="col-md-10">';
    echo '<input type="text" name="search" class="form-control" placeholder="Buscar por nombre, SKU..." value="' . esc_attr($search) . '">';
    echo '</div>';
    echo '<div class="col-md-2">';
    echo '<button type="submit" class="btn btn-primary-custom w-100">🔍 Buscar</button>';
    echo '</div>';
    echo '</div>';
    echo '</form>';

    // Mostrar estadísticas
    echo '<div class="alert alert-warning-custom mb-4">';
    echo '<strong>📊 Estadísticas:</strong> ';
    echo 'Total de productos: ' . $total_products;
    if (!empty($search)) {
      echo ' (Filtrados por: "' . esc_html($search) . '")';
    }
    echo '</div>';

    if (empty($products)) {
      echo '<div class="alert alert-warning-custom">';
      echo '<strong>ℹ️ No se encontraron productos</strong>';
      echo '</div>';
    } else {
      // Formulario de eliminación
      echo '<form method="post" id="deleteProductsForm" onsubmit="return confirmDelete()">';
      echo '<div class="table-responsive">';
      echo '<table class="table table-dark-custom">';
      echo '<thead>';
      echo '<tr>';
      echo '<th class="col-checkbox"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"><br><small>Todo</small></th>';
      echo '<th class="col-image">Imagen</th>';
      echo '<th class="col-sku">SKU</th>';
      echo '<th class="col-name">Nombre</th>';
      echo '<th class="col-price">Precio</th>';
      echo '<th class="col-stock">Stock</th>';
      echo '<th class="col-categories">Categorías</th>';
      echo '<th class="col-gallery">Galería</th>';
      echo '</tr>';
      echo '</thead>';
      echo '<tbody>';

      foreach ($products as $product) {
        $product_id = $product->get_id();
        $product_name = $product->get_name();
        $product_sku = $product->get_sku();
        $product_price = $product->get_price();
        $product_stock = $product->get_stock_quantity();
        $product_image_id = $product->get_image_id();
        $gallery_count = count($product->get_gallery_image_ids());
        $categories = wc_get_product_category_list($product_id, ', ');

        echo '<tr>';

        // Checkbox
        echo '<td class="col-checkbox"><input type="checkbox" name="product_ids[]" value="' . $product_id . '" class="product-checkbox"></td>';

        // Imagen
        echo '<td class="product-image-cell col-image">';
        if ($product_image_id) {
          $image_url = wp_get_attachment_image_url($product_image_id, 'thumbnail');
          echo '<div class="product-image-wrapper">';
          echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product_name) . '">';
          echo '</div>';
        } else {
          echo '<div class="no-image-placeholder">Sin imagen</div>';
        }
        echo '</td>';

        // SKU
        echo '<td class="col-sku">' . ($product_sku ? '<strong>' . esc_html($product_sku) . '</strong>' : '<em>Sin SKU</em>') . '</td>';

        // Nombre
        echo '<td class="col-name">';
        echo '<div class="product-name">' . esc_html($product_name) . '</div>';
        echo '<div class="product-id">ID: ' . $product_id . '</div>';
        echo '</td>';

        // Precio
        echo '<td class="col-price">';
        if ($product_price) {
          echo '<span class="product-price">' . wc_price($product_price) . '</span>';
        } else {
          echo '<em>Sin precio</em>';
        }
        echo '</td>';

        // Stock
        echo '<td class="col-stock">';
        if ($product_stock !== null) {
          $stock_class = $product_stock > 0 ? 'stock-in' : 'stock-out';
          echo '<span class="stock-status ' . $stock_class . '">' . $product_stock . '</span>';
        } else {
          echo '<em>N/A</em>';
        }
        echo '</td>';

        // Categorías
        echo '<td class="col-categories">';
        if ($categories) {
          echo '<div class="category-list">' . $categories . '</div>';
        } else {
          echo '<em>Sin categoría</em>';
        }
        echo '</td>';

        // Galería
        echo '<td class="col-gallery">';
        if ($gallery_count > 0) {
          echo '<span class="gallery-badge">' . $gallery_count . ' img</span>';
        } else {
          echo '<em>Sin galería</em>';
        }
        echo '</td>';

        echo '</tr>';
      }

      echo '</tbody>';
      echo '</table>';
      echo '</div>';

      // Botones de acción
      echo '<div class="row mt-3">';
      echo '<div class="col-md-6 mb-2">';
      echo '<button type="submit" name="delete_products" class="btn btn-danger-custom btn-lg w-100">🗑️ Eliminar Seleccionados</button>';
      echo '</div>';
      echo '<div class="col-md-6 mb-2">';
      echo '<button type="button" onclick="unselectAll()" class="btn btn-dark-custom btn-lg w-100">❌ Deseleccionar Todos</button>';
      echo '</div>';
      echo '</div>';
      echo '</form>';

      // Paginación
      if ($total_pages > 1) {
        echo '<nav aria-label="Paginación de productos" class="mt-4">';
        echo '<ul class="pagination pagination-dark justify-content-center">';

        // Botón anterior
        if ($current_page > 1) {
          $prev_url = add_query_arg(['paged' => $current_page - 1, 'search' => $search], $_SERVER['PHP_SELF']);
          echo '<li class="page-item"><a class="page-link" href="' . esc_url($prev_url) . '">← Anterior</a></li>';
        } else {
          echo '<li class="page-item disabled"><span class="page-link">← Anterior</span></li>';
        }

        // Números de página
        $range = 2;
        for ($i = max(1, $current_page - $range); $i <= min($total_pages, $current_page + $range); $i++) {
          $active_class = ($i == $current_page) ? ' active' : '';
          $page_url = add_query_arg(['paged' => $i, 'search' => $search], $_SERVER['PHP_SELF']);
          echo '<li class="page-item' . $active_class . '"><a class="page-link" href="' . esc_url($page_url) . '">' . $i . '</a></li>';
        }

        // Botón siguiente
        if ($current_page < $total_pages) {
          $next_url = add_query_arg(['paged' => $current_page + 1, 'search' => $search], $_SERVER['PHP_SELF']);
          echo '<li class="page-item"><a class="page-link" href="' . esc_url($next_url) . '">Siguiente →</a></li>';
        } else {
          echo '<li class="page-item disabled"><span class="page-link">Siguiente →</span></li>';
        }

        echo '</ul>';
        echo '</nav>';

        echo '<div class="text-center mt-2">';
        echo '<small>Página ' . $current_page . ' de ' . $total_pages . ' | Total: ' . $total_products . ' productos</small>';
        echo '</div>';
      }
    }
  }

  echo '</div>'; // card-body
  echo '</div>'; // card
  echo '</div>'; // col
  echo '</div>'; // row

  // JavaScript para selección
  echo '<script>';
  echo 'function toggleSelectAll(source) {';
  echo '  var checkboxes = document.getElementsByClassName("product-checkbox");';
  echo '  for(var i = 0; i < checkboxes.length; i++) {';
  echo '    checkboxes[i].checked = source.checked;';
  echo '  }';
  echo '}';
  echo 'function unselectAll() {';
  echo '  var checkboxes = document.getElementsByClassName("product-checkbox");';
  echo '  for(var i = 0; i < checkboxes.length; i++) {';
  echo '    checkboxes[i].checked = false;';
  echo '  }';
  echo '  document.getElementById("selectAll").checked = false;';
  echo '}';
  echo 'function confirmDelete() {';
  echo '  var checkedBoxes = document.querySelectorAll(".product-checkbox:checked");';
  echo '  if(checkedBoxes.length === 0) {';
  echo '    alert("⚠️ Por favor selecciona al menos un producto para eliminar");';
  echo '    return false;';
  echo '  }';
  echo '  var count = checkedBoxes.length;';
  echo '  return confirm("⚠️ ¿Estás seguro de que deseas eliminar " + count + " producto(s) y todas sus imágenes?\\n\\nEsta acción NO se puede deshacer.");';
  echo '}';
  echo '</script>';
} else {
  echo '<div class="alert alert-danger" role="alert">';
  echo '<h4>❌ Acceso Denegado</h4>';
  echo '<p>Este script solo puede ejecutarse desde un navegador web.</p>';
  echo '</div>';
}
?>

</body>

</html>