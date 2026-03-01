<?php
// Template para crear un nuevo código HTML
?>
<div class="wrap">
  <h1>Crear Nuevo Código HTML</h1>




  <form id="codeForm" onsubmit="handleSubmit(event)">
    <!-- Textarea para CodeMirror -->
    <textarea id="code_html" name="code_html">
<html style="color: green">
  <!-- this is a comment -->
  <head>
    <title>Mixed HTML Example</title>
    <style>
      h1 {font-family: comic sans; color: #f0f;}
      div {background: yellow !important;}
      body {
        max-width: 50em;
        margin: 1em 2em 1em 5em;
      }
    </style>
  </head>
  <body>
    <h1>Mixed HTML Example</h1>
    <script>
      function jsFunc(arg1, arg2) {
        if (arg1 && arg2) document.body.innerHTML = "achoo";
      }
    </script>
  </body>
</html>
</textarea>

    <div class="form-buttons">
      <button type="button" onclick="cargaCodigo()">Carga codigo HTML</button>
      <button type="submit">Enviar Formulario</button>
    </div>
  </form>
</div>


<script>
    // Resaltado de sintaxis para el editor de código
    document.addEventListener('DOMContentLoaded', function() {
        // =============================================
        // Inicializa CodeMirror en el textarea
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
                }
            ]
        };

        editor = CodeMirror.fromTextArea(document.getElementById("code_html"), {
            lineNumbers: true,
            matchBrackets: true, // Resalta los corchetes coincidentes
            theme: "dracula",
            mode: 'javascript', // Modo mixto para HTML + PHP + JS + CSS


        });

    });




    // Sincronizar el contenido del editor con el textarea antes del envío del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        // Guardar el contenido del editor en el textarea
        console.log('Guardando contenido del editor en el textarea...');
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


    function handleSubmit(event) {
        event.preventDefault(); // Previene el envío por defecto del formulario

        // Obtiene el código del editor
        var codigo = editor.getValue();

        // Aquí puedes procesar el código como necesites
        console.log("Código enviado:", codigo);

        // Ejemplo: mostrar el código en un alert (puedes cambiarlo por lo que necesites)
        alert("Código enviado:\n\n" + codigo);

        // También puedes enviar el código a un servidor aquí
        // fetch('/api/save-code', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //     },
        //     body: JSON.stringify({ code: codigo })
        // });
    }
</script>