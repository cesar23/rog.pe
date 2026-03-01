<?php

/**
 * Script de prueba para verificar la función clean_html_code
 * 
 * Este archivo es temporal y debe ser eliminado después de las pruebas
 */

// Simular la función clean_html_code
function clean_html_code($code)
{
  // Si las comillas están escapadas, las limpiamos
  if (strpos($code, '\\"') !== false) {
    $code = stripslashes($code);
  }
  return $code;
}

// Código de prueba con comillas escapadas
$test_code = '<?php
// Template para crear un nuevo código HTML
?>
<div class=\"wrap\">
    <h1>Crear Nuevo Código HTML</h1>

    <form id=\"codeForm\" onsubmit=\"handleSubmit(event)\">
      <?php wp_nonce_field(\'solu_generate_html_create\', \'solu_generate_html_nonce\'); ?>
        <table class=\"form-table\">
            <tr>
                <th scope=\"row\"><label for=\"name_group\">Nombre del Grupo <span class=\"description\">(obligatorio)</span></label></th>
                <td><input type=\"text\" name=\"name_group\" id=\"name_group\" value=\"\" class=\"regular-text\" required=\"required\" placeholder=\"Ej: category_header, brand_footer, etc.\"></td>
            </tr>
            <tr>
                <th scope=\"row\"><label for=\"name_group\">Nombre de Codigo<span class=\"description\">(obligatorio)</span></label></th>
                <td><input type=\"text\" name=\"name_code\" id=\"name_code\" value=\"\" class=\"regular-text\" required=\"required\" placeholder=\"Ej: category_header, brand_footer, etc.\"></td>
            </tr>
            <tr>
                <th scope=\"row\"><label for=\"code\">Código HTML <span class=\"description\">(obligatorio)</span></label></th>
                <td>
                    <textarea name=\"code_html\" id=\"code_html\" rows=\"20\" cols=\"80\" class=\"large-text code\" required=\"required\" placeholder=\"Ingresa aquí tu código HTML...\" style=\"\"></textarea>
                    <p class=\"description\">Puedes incluir código PHP y HTML. Usa variables como $category, $brand, etc. según el contexto.</p>
                </td>
            </tr>
        </table>
        <input type=\"hidden\" name=\"action\" value=\"create\">
        <button type=\"submit\">Enviar Formulario</button>
    </form>
</div>';

echo "=== CÓDIGO ORIGINAL (con comillas escapadas) ===\n";
echo $test_code . "\n\n";

echo "=== CÓDIGO LIMPIO (sin comillas escapadas) ===\n";
echo clean_html_code($test_code) . "\n\n";

echo "=== VERIFICACIÓN ===\n";
$cleaned_code = clean_html_code($test_code);
if (strpos($cleaned_code, '\\"') === false) {
  echo "✅ SUCCESS: Las comillas escapadas han sido eliminadas correctamente.\n";
} else {
  echo "❌ ERROR: Aún hay comillas escapadas en el código.\n";
}

// Verificar que las comillas normales se mantienen
if (strpos($cleaned_code, '"') !== false) {
  echo "✅ SUCCESS: Las comillas normales se mantienen correctamente.\n";
} else {
  echo "❌ ERROR: Las comillas normales han sido eliminadas incorrectamente.\n";
}
