<?php
/**
 * ================================
 * 📦 Optimización de indexación en FiboSearch (AJAX Search for WooCommerce)
 * ================================
 *
 * Este bloque de filtros controla la cantidad de elementos que se procesan 
 * por lote durante la indexación del contenido para el plugin FiboSearch.
 * Es útil si deseas evitar errores de memoria o hacer más estable la indexación 
 * en servidores compartidos o con recursos limitados.
 * 
 * Puedes ajustar los valores dependiendo del rendimiento de tu servidor.
 */


/**
 * 🔎 Filtro: Cantidad de productos buscables por lote
 *
 * Controla cuántos productos "buscables" se indexan a la vez.
 * Valor más bajo = menos carga por ciclo, más estable pero más lento.
 */
add_filter('dgwt/wcas/indexer/searchable_set_items_count', function ($count) {
    return 10; // Procesa 10 productos por lote
});


/**
 * 📘 Filtro: Cantidad de elementos legibles por lote
 *
 * Incluye datos como atributos, metadatos o contenido adicional del producto.
 */
add_filter('dgwt/wcas/indexer/readable_set_items_count', function ($count) {
    return 5; // Procesa 5 elementos legibles por lote
});


/**
 * 🏷️ Filtro: Cantidad de taxonomías por lote
 *
 * Controla cuántos términos de taxonomía (categorías, etiquetas, etc.) se procesan a la vez.
 */
add_filter('dgwt/wcas/indexer/taxonomy_set_items_count', function ($count) {
    return 10; // Procesa 10 taxonomías por lote
});


/**
 * 🧩 Filtro: Cantidad de variaciones de productos por lote
 *
 * Solo aplica a productos variables. Útil para tiendas con muchas variaciones.
 */
add_filter('dgwt/wcas/indexer/variations_set_items_count', function ($count) {
    return 5; // Procesa 5 variaciones por lote
});
