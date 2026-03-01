<?php
// Verificar que no se acceda directamente
if (!defined('ABSPATH')) {
    exit;
}


// Extraer datos del array $template_data
$product_categories = $template_data['product_categories'];
$total_categories = $template_data['total_categories'];
$total_products = $template_data['total_products'];
$instance_backend_function = $template_data['instance_backend_function'];
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-category" style="margin-right: 10px;"></span>
        Categorías de Productos
    </h1>
    <a href="?page=solu-generate-html" class="page-title-action">← Volver a Códigos HTML</a>
    
    <hr class="wp-header-end">

    <?php
    // Verificar si hay categorías problemáticas usando la clase Singleton
//    $backend = Solu_Generate_HTML_Backend_Functions::getInstance();
    $problematic_categories = $instance_backend_function->detect_problematic_categories('product_cat');
    if (!empty($problematic_categories)): ?>
        <div class="notice notice-warning">
            <p>
                <strong>⚠️ Se detectaron <?php echo count($problematic_categories); ?> categoría<?php echo count($problematic_categories) !== 1 ? 's' : ''; ?> con problemas:</strong>
                <br>
                <?php foreach ($problematic_categories as $problem): ?>
                    • <?php echo esc_html($problem['name']); ?> (<?php echo esc_html($problem['description']); ?>)
                    <?php if (isset($problem['parent_id'])): ?>
                        - Padre ID: <?php echo esc_html($problem['parent_id']); ?>
                    <?php endif; ?>
                    <br>
                <?php endforeach; ?>
                <small>Estas categorías han sido excluidas de la lista para evitar errores.</small>
            </p>
        </div>
    <?php endif; ?>

    <!-- Formulario de selección -->
    <form method="post" action="<?php echo admin_url('admin.php?page=solu-generate-html&action=selected_categories'); ?>">
        <?php wp_nonce_field('solu_generate_html_select_categories', 'solu_generate_html_nonce'); ?>
        <input type="hidden" name="action" value="select_categories">
        
        <div class="tablenav top">
            <div class="alignleft actions">
                <input type="submit" name="submit_selected_categories" class="button button-primary" value="Enviar Categorías Seleccionadas">
            </div>
            <div class="alignright">
                <span class="displaying-num">
                    <?php echo $total_categories; ?> categoría<?php echo $total_categories !== 1 ? 's' : ''; ?> padre
                </span>
            </div>
        </div>

    <!-- Tabla de categorías -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-cb check-column">
                    <input type="checkbox" id="cb-select-all-1">
                </th>
                <th scope="col" class="manage-column column-name">
                    <strong>Nombre</strong>
                </th>
                <th scope="col" class="manage-column column-slug">
                    <strong>Slug</strong>
                </th>
                <th scope="col" class="manage-column column-description">
                    <strong>Descripción</strong>
                </th>
                <th scope="col" class="manage-column column-count">
                    <strong>Productos</strong>
                </th>
                <th scope="col" class="manage-column column-parent">
                    <strong>Categoría Padre</strong>
                </th>
            </tr>
        </thead>
        
        <tbody>
            <?php if (empty($product_categories)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        <p style="color: #666;">
                            No hay categorías padre de productos disponibles
                        </p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($product_categories as $category): ?>
                    <tr>
                        <td class="check-column">
                            <input type="checkbox" name="selected_categories[]" value="<?php echo esc_attr($category['term_id']); ?>">
                        </td>
                        <td class="column-name">
                            <strong>
                                <?php 
                                $admin_link = admin_url('edit.php?post_type=product&product_cat=' . urlencode($category['slug']));
                                if (!empty($category['slug']) && $category['slug'] !== '') {
                                    echo '<a href="' . esc_url($admin_link) . '" target="_blank">';
                                    echo esc_html($category['name']);
                                    echo '</a>';
                                } else {
                                    echo esc_html($category['name']);
                                }
                                ?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                    <a href="<?php echo esc_url(admin_url('edit-tags.php?action=edit&taxonomy=product_cat&tag_ID=' . $category['term_id'])); ?>" 
                                       target="_blank">Editar</a> |
                                </span>
                                <span class="view">
                                    <?php 
                                    $term_link = $instance_backend_function->get_safe_term_link($category['term_id'], 'product_cat');
                                    if ($term_link) {
                                        echo '<a href="' . esc_url($term_link) . '" target="_blank">Ver</a>';
                                    } else {
                                        echo '<span style="color: #999;">Enlace no disponible</span>';
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                        
                        <td class="column-slug">
                            <?php if (!empty($category['slug']) && $category['slug'] !== ''): ?>
                                <code><?php echo esc_html($category['slug']); ?></code>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;">Slug no disponible</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="column-description">
                            <?php if (!empty($category['description'])): ?>
                                <?php echo esc_html(wp_trim_words($category['description'], 10)); ?>
                            <?php else: ?>
                                <span style="color: #999;">Sin descripción</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="column-count">
                            <span class="count-bubble <?php echo $category['count'] > 0 ? 'has-products' : 'no-products'; ?>">
                                <?php echo esc_html($category['count']); ?>
                            </span>
                        </td>
                        
                        <td class="column-parent">
                            <?php 
                                                            $parent_info = $instance_backend_function->get_safe_parent_category($category['parent'], 'product_cat');
                            if ($parent_info) {
                                echo esc_html($parent_info['name']);
                            } elseif ($category['parent'] > 0) {
                                echo '<span style="color: #999; font-style: italic;">Categoría padre eliminada</span>';
                            } else {
                                echo '<span style="color: #999;">—</span>';
                            }
                            ?>
                        </td>
                        

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Botón de envío -->
    <div class="tablenav bottom">
        <div class="alignleft actions">
            <input type="submit" name="submit_selected_categories" class="button button-primary" value="Enviar Categorías Seleccionadas">
        </div>
        <div class="alignright">
            <p class="description">
                <strong>Total categorías padre:</strong> <?php echo $total_categories; ?> categoría<?php echo $total_categories !== 1 ? 's' : ''; ?>
                <?php if ($total_products > 0): ?>
                    | <strong>Total productos:</strong> <?php echo number_format($total_products); ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    </form>
</div>




<style>
.count-bubble {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    min-width: 20px;
}

.count-bubble.has-products {
    background-color: #0073aa;
    color: white;
}

.count-bubble.no-products {
    background-color: #f1f1f1;
    color: #666;
}

.column-actions .row-actions {
    margin-top: 5px;
}

.column-actions .button {
    margin-right: 5px;
}

.column-name strong a {
    color: #0073aa;
    text-decoration: none;
}

.column-name strong a:hover {
    color: #005a87;
}

.column-slug code {
    background: #f1f1f1;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 12px;
}

.check-column {
    width: 30px;
    text-align: center;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Funcionalidad para "Seleccionar todo"
    $('#cb-select-all-1').on('change', function() {
        $('input[name="selected_categories[]"]').prop('checked', this.checked);
    });
    
    // Actualizar "Seleccionar todo" cuando se cambian checkboxes individuales
    $('input[name="selected_categories[]"]').on('change', function() {
        var totalCheckboxes = $('input[name="selected_categories[]"]').length;
        var checkedCheckboxes = $('input[name="selected_categories[]"]:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#cb-select-all-1').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#cb-select-all-1').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#cb-select-all-1').prop('indeterminate', true);
        }
    });
});
</script>
