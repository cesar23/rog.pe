<?php
// Template para eliminar un código HTML
?>
<div class="wrap">
    <h1>Eliminar Código HTML</h1>
    <form method="post" action="">
        <?php wp_nonce_field('solu_generate_html_delete', 'solu_generate_html_nonce'); ?>
        <div class="notice notice-warning">
            <p><strong>⚠️ ¿Estás seguro de que quieres eliminar el código HTML "<?php echo esc_attr($html_code['name_group']); ?>"?</strong></p>
            <p>Esta acción no se puede deshacer.</p>
        </div>

        <div class="card">
            <h3>Detalles del código HTML:</h3>
            <p><strong>ID:</strong> <?php echo esc_html($html_code['id']); ?></p>
            <p><strong>Nombre del Grupo:</strong> <?php echo esc_html($html_code['name_group']); ?></p>
            <p><strong>Nombre del Codigo:</strong> <?php echo esc_html($html_code['name_code']); ?></p>
            <p><strong>Fecha de Creación:</strong> <?php echo esc_html($html_code['created_at']); ?></p>
            <p><strong>Última Actualización:</strong> <?php echo esc_html($html_code['update_at']); ?></p>
        </div>

        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?php echo esc_attr($html_code['id']); ?>">

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Eliminar Código HTML" style="background-color: #dc3232; border-color: #dc3232;">
            <a href="?page=solu-generate-html" class="button">Cancelar</a>
        </p>
    </form>
</div>