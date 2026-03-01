<div class="wrap">
    <h1><?php esc_html_e( 'Información del Plugin', 'solu-currencies-exchange' ); ?></h1>

    <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e( 'Nombre', 'solu-currencies-exchange' ); ?></th>
            <td><?php echo esc_html( 'Solu Currencies Exchange' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Descripción', 'solu-currencies-exchange' ); ?></th>
            <td><?php echo esc_html( 'manejo de monedas par aecomerce' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Versión', 'solu-currencies-exchange' ); ?></th>
            <td><?php echo esc_html( '1.0.0' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Autor', 'solu-currencies-exchange' ); ?></th>
            <td><?php echo esc_html( 'César Auris [perucaos@gmail.com]' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Sitio Web', 'solu-currencies-exchange' ); ?></th>
            <td><?php echo esc_html( '' ); ?></td>
        </tr>
    </table>

    <h2>Implementación en Flatsome</h2>
    <p>Este plugin fue diseñado para ser utilizado con el tema <code>Flatsome</code> y se implementa en el tema hijo de <code>flatsome-child</code>.</p>
    <p>Para que el plugin funcione correctamente, debes crear el archivo<code>wp-content/themes/flatsome-child/woocommerce/single-product/price.php</code>con el siguiente contenido:</p>


</div>

<?php
$php_to_html = file_get_contents(SOLU_CURRENCIES_EXCHANGE_PATH."storage/template_price.php.txt");
$html_encoded = htmlentities($php_to_html);
?>
<pre class="line-numbers"><code class="language-php">

<?php echo  $html_encoded;?>

</code></pre>