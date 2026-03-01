<?php

/**
 * Muestra la información de la moneda en la descripción del producto.
 */
function display_currency_info() {
    global $product;
    $currency_row = getStorageTableJsonRow('PEN');

    if ( isset( $currency_row->currency_name) ) {


        echo '<div class="currency-info">';
        echo '<p><strong>Moneda:</strong> ' . esc_html( $currency_row->currency_name ) . ' (' . esc_html( $currency_row->currency_code ) . ')</p>';
        echo '<p><strong>Descripción:</strong> ' . esc_html( $currency_row->currency_description ) . '</p>';
        echo '</div>';
    } else {
        echo '<div class="currency-info">';
        echo '<p><strong>Moneda no soportada</strong></p>';
        echo '</div>';
    }
}


function display_currencies_list() {
        global $solu_currencies;

        echo '<div class="currencies-list">';
        echo '<h3>Monedas Disponibles</h3>';
        echo '<ul>';
        foreach ( $solu_currencies as $code => $currency ) {
            echo '<li>' . esc_html( $currency['name'] ) . ' (' . esc_html( $code ) . ')</li>';
        }
        echo '</ul>';
        echo '</div>';
}