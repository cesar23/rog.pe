<?php
// Template para mostrar información del plugin
?>
<div class="wrap">
    <h1>Información del Plugin - Solu Generate HTML</h1>
    
    <div class="card">
        <h2>Descripción</h2>
        <p>Plugin para generar y gestionar códigos HTML desde el backend de WordPress. Permite crear, editar y eliminar fragmentos de código HTML que pueden ser utilizados en diferentes partes del sitio web.</p>
    </div>

    <div class="card">
        <h2>Características</h2>
        <ul>
            <li><strong>Gestión de Códigos HTML:</strong> Crear, editar y eliminar códigos HTML</li>
            <li><strong>Organización por Grupos:</strong> Organizar códigos por nombres de grupo</li>
            <li><strong>Editor con Resaltado:</strong> Editor de código con resaltado de sintaxis</li>
            <li><strong>Almacenamiento JSON:</strong> Cache de datos en archivos JSON</li>
            <li><strong>Sistema de Logs:</strong> Registro de todas las operaciones</li>
        </ul>
    </div>

    <div class="card">
        <h2>Uso</h2>
        <ol>
            <li><strong>Crear Código HTML:</strong> Ve a "Códigos HTML" → "Agregar Nuevo Código HTML"</li>
            <li><strong>Editar Código:</strong> Haz clic en "Editar" en la lista de códigos</li>
            <li><strong>Eliminar Código:</strong> Haz clic en "Borrar" en la lista de códigos</li>
            <li><strong>Organizar:</strong> Usa nombres de grupo descriptivos para organizar tus códigos</li>
        </ol>
    </div>

    <div class="card">
        <h2>Información Técnica</h2>
        <table class="form-table">
            <tr>
                <th>Versión del Plugin:</th>
                <td><?php echo SOLU_GENERATE_HTML_VERSION; ?></td>
            </tr>
            <tr>
                <th>Tabla Principal:</th>
                <td><?php echo SOLU_GENERATE_HTML_TABLE; ?></td>
            </tr>
            <tr>
                <th>Tabla de Logs:</th>
                <td><?php echo SOLU_GENERATE_HTML_TABLE_LOG; ?></td>
            </tr>
            <tr>
                <th>Archivo JSON:</th>
                <td><?php echo SOLU_GENERATE_HTML_STORAGE_JSON; ?></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>Autor</h2>
        <p><strong>César Auris</strong> - <a href="mailto:perucaos@gmail.com">perucaos@gmail.com</a></p>
        <p><strong>Sitio Web:</strong> <a href="https://solucionessystem.com" target="_blank">https://solucionessystem.com</a></p>
    </div>
</div>