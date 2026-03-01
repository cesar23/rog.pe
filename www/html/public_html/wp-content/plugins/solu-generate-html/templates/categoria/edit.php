<?php
// Template para editar un código HTML existente
?>
<div class="wrap">
    <h1>Editar Código HTML</h1>



    <form method="post" id="codeForm" action="">
        <?php wp_nonce_field('solu_generate_html_update', 'solu_generate_html_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="name_group">Nombre del Grupo <span class="description">(obligatorio)</span></label></th>
                <td>
                    <input type="text" name="name_group" id="name_group" value="<?php echo esc_attr($html_code['name_group']); ?>" class="regular-text" required="required" placeholder="Ej: category_electronica, brand_nike, etc.">
                    <p class="description">
                        <strong>Prefijos válidos:</strong> category_, brand_, label_, products_, other<br>
                        <small style="color: #666;">Ejemplos: category_electronica, brand_nike, label_oferta, products_destacados, other_personalizado</small>
                    </p>
                    <div id="name_group_error" style="display:none; color: #dc3232; font-weight: bold; margin-top: 5px;"></div>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="name_code">Nombre del Código <span class="description">(obligatorio)</span></label></th>
                <td><input type="text" name="name_code" id="name_code" value="<?php echo esc_attr($html_code['name_code']); ?>" class="regular-text" required="required" placeholder="Ej: category_header, brand_footer, etc."></td>
            </tr>
            <tr>
                <th scope="row"><label for="code">Código HTML <span class="description">(obligatorio)</span></label></th>
                <td>
                    <textarea name="code" id="code" rows="20" cols="80" class="large-text code" required="required" placeholder="Ingresa aquí tu código HTML..." style="position: absolute; left: -9999px; width: 1px; height: 1px; opacity: 0;"><?php echo esc_textarea($html_code['code']); ?></textarea>
                    <p class="description">Puedes incluir código PHP y HTML. Usa variables como $category, $brand, etc. según el contexto.</p>
                </td>
            </tr>
        </table>
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo esc_attr($html_code['id']); ?>">
        <?php submit_button('Actualizar Código HTML'); ?>
    </form>
</div>

<script>
    var editor;
    // Resaltado de sintaxis para el editor de código
    document.addEventListener('DOMContentLoaded', function() {
        // ==========================================
        // Para actualizar (PRISMA)
        if (typeof Prism !== 'undefined') {
            Prism.highlightAll();
        }


        // =============================================
        // Inicializa (CodeMirror) en el textarea
        const mixedMode = {
            name: "htmlmixed",
            scriptTypes: [{
                    matches: /\/x-handlebars-template|\/x-mustache/i,
                    mode: null
                },
                {
                    matches: /(text|application)\/(x-)?vb(a|script)/i,
                    mode: "vbscript"
                },
                {
                    matches: /application\/x-httpd-php/i,
                    mode: "php"
                },
                {
                    matches: /text\/javascript|application\/javascript/i,
                    mode: "javascript"
                },
                {
                    matches: /text\/css/i,
                    mode: "css"
                }
            ]
        };

        editor = CodeMirror.fromTextArea(document.getElementById("code"), {
            lineNumbers: true,
            matchBrackets: true, // Resalta los corchetes coincidentes
            theme: "dracula",
            mode: mixedMode, // Modo mixto para HTML + PHP + JS + CSS
            styleActiveLine: true, // Resalta la línea activa
            autoCloseBrackets: true, // Cierra automáticamente los corchetes
            autoCloseTags: true, // Cierra automáticamente las etiquetas HTML
            foldGutter: true, // Permite plegar y desplegar bloques de código
            gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"], // Gutter para números de línea y plegado
            extraKeys: {
                "Ctrl-Space": "autocomplete", // Autocompletado con Ctrl+Space
                "Ctrl-/": "toggleComment", // Comentar/descomentar con Ctrl+/
                "Tab": function(cm) {
                    if (cm.somethingSelected()) {
                        cm.indentSelection("add");
                    } else {
                        cm.replaceSelection("  ", "end");
                    }
                }
            },
            indentUnit: 2, // Indentación de 2 espacios
            tabSize: 2, // Tamaño de tab de 2 espacios
            lineWrapping: true, // Envolver líneas largas
            lint: true // Habilitar linting si está disponible
        });

    });

    document.querySelector('form').addEventListener('submit', function(e) {

        /*
                e.preventDefault(); // Previene el envío por defecto del formulario
                // Guardar el contenido del editor en el textarea
                var codigo = editor.getValue();

                // Aquí puedes procesar el código como necesites
                console.log("Código enviado:", codigo);

        */

        // editor.save();
        //
        // // Verificar que el textarea tenga contenido
        // var textarea = document.getElementById('code');
        // console.log('textarea.value:', textarea.value);
        // if (!textarea.value.trim()) {
        //     e.preventDefault();
        //     alert('El campo Código HTML es obligatorio.');
        //     return false;
        // }
    });

    // Validación de prefijos para el nombre del grupo
    document.addEventListener('DOMContentLoaded', function() {
        var prefijosValidos = ['category_', 'brand_', 'label_', 'products_', 'other'];

        // Función para validar el nombre del grupo
        function validarNombreGrupo(nombre) {
            if (!nombre || nombre.trim() === '') {
                return {
                    valido: false,
                    mensaje: 'El nombre del grupo no puede estar vacío.'
                };
            }

            // Verificar si comienza con uno de los prefijos válidos
            var tienePrefijoValido = false;
            for (var i = 0; i < prefijosValidos.length; i++) {
                if (nombre.toLowerCase().startsWith(prefijosValidos[i])) {
                    tienePrefijoValido = true;
                    break;
                }
            }

            if (!tienePrefijoValido) {
                return {
                    valido: false,
                    mensaje: 'El nombre del grupo debe comenzar con uno de estos prefijos: ' + prefijosValidos.join(', ')
                };
            }

            // Verificar que tenga al menos un carácter después del prefijo
            var nombreSinPrefijo = nombre.substring(nombre.indexOf('_') + 1);
            if (nombreSinPrefijo.trim() === '') {
                return {
                    valido: false,
                    mensaje: 'El nombre del grupo debe tener contenido después del prefijo.'
                };
            }

            return {
                valido: true,
                mensaje: ''
            };
        }

        // Validar el nombre del grupo en tiempo real
        document.getElementById('name_group').addEventListener('input', function() {
            var nombre = this.value;
            var validacion = validarNombreGrupo(nombre);

            if (!validacion.valido) {
                document.getElementById('name_group_error').textContent = validacion.mensaje;
                document.getElementById('name_group_error').style.display = 'block';
                this.style.borderColor = '#dc3232';
            } else {
                document.getElementById('name_group_error').style.display = 'none';
                this.style.borderColor = '#7ad03a';
            }
        });

        // Validar antes de enviar el formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            var nombre = document.getElementById('name_group').value;
            var validacion = validarNombreGrupo(nombre);

            if (!validacion.valido) {
                e.preventDefault();
                document.getElementById('name_group_error').textContent = validacion.mensaje;
                document.getElementById('name_group_error').style.display = 'block';
                document.getElementById('name_group').focus();
                return false;
            }
        });
    });
</script>