<?php


if (!function_exists('renderTablePrice')) {
    function renderTablePrice(
        $discount,
        $currency_symbol_local,
        $currency_alternative_symbol,
        $formatted_price_local_regular,
        $formatted_price_alternative_regular,
        $formatted_price_local_sale,
        $formatted_price_alternative_sale
    ) {

        if ($discount == "regular_price") {
            $price_local_regular_display = $currency_symbol_local . ' ' . $formatted_price_local_regular;
            $price_alternative_regular_display = $currency_alternative_symbol . ' ' . $formatted_price_alternative_regular;
            $price_regular_final = $price_local_regular_display . ' (' . $price_alternative_regular_display . ')';
        }

        $price_local_sale_display = $currency_symbol_local . ' ' . $formatted_price_local_sale;
        $price_alternative_sale_display = $currency_alternative_symbol . ' ' . $formatted_price_alternative_sale;
        $price_sale_final = $price_local_sale_display . ' (' . $price_alternative_sale_display . ')';

        //price increased by 5%
        // Convertir los strings formateados de vuelta a números usando función robusta
        try {
            $price_local_sale_numeric = solu_parse_formatted_number($formatted_price_local_sale);
            $price_alternative_sale_numeric = solu_parse_formatted_number($formatted_price_alternative_sale);

            $price_local_sale_increased = solu_increase_amount_by_percentage($price_local_sale_numeric, 5);
            $price_alternative_sale_increased = solu_increase_amount_by_percentage($price_alternative_sale_numeric, 5);

            $price_local_sale_increased_display = $currency_symbol_local . ' ' . number_format($price_local_sale_increased, 2);
            $price_local_alternative_increased_display = $currency_alternative_symbol . ' ' . number_format($price_alternative_sale_increased, 2);
            $price_sale_increased_final = $price_local_sale_increased_display . ' (' . $price_local_alternative_increased_display . ')';
        } catch (Exception $e) {
            // Si hay error en el cálculo, usar los precios originales
            solu_log("Error calculating increased prices: " . $e->getMessage(), 'error');
            $price_sale_increased_final = $price_sale_final;
        }



        $html_table_content = '
                <div class="tabla_monedas">
                    <div class="cash-current-price">
                        <span class="price_sale">' . $price_sale_final . '</span>';
        if ($discount == "regular_price") {
            $html_table_content .= '<span class="regular-price">' . $price_regular_final . '</span>';
        }
        $html_table_content .= '
                    </div>
                    <div class="cash-info-type">
                        <span>💵 Ahorre Pagando Efectivo, Transferencia, Deposito Bancario</span>
                    </div>
                    <div class="creditCard-current-price">
                        <span class="price_sale">' . $price_sale_increased_final . '</span>
                    </div>
                    <div class="creditCard-info-type">
                        <span>💳5% adicional pagando con T. Debito/Credito</span>
                    </div>
                    <div class="tax-label">* El precio incluye IGV.</div>
                </div>
                ';

        return $html_table_content;
    }
}

/**
 * Muestra el precio alternativo de venta.
 *
 * @param double $priceProduct Precio del producto.
 * @param SoluCurrenciesExchange $currency_storage_local El objeto del producto.
 * @param SoluCurrenciesExchange $currency_storage_alternative El objeto del producto.
 * @return double precio modificado.
 */
/**
 * Obtiene el precio alternativo basado en el precio original
 */
function getAlternativeCurrencyPrice($priceProduct, SoluCurrenciesExchange $currency_storage_local, SoluCurrenciesExchange $currency_storage_alternative)
{
    $price_alternative_sale = floatval(0.00);

    // Convertir a float para evitar errores de tipo
    $price = floatval($priceProduct);
    $currency_value = floatval($currency_storage_alternative->currency_value);

    // Validar que el valor de la moneda no sea cero para evitar división por cero
    if ($currency_value == 0) {
        solu_log("Error: currency_value is zero for currency code: " . $currency_storage_alternative->currency_code, 'error');
        return $price;
    }

    if ($currency_storage_local->currency_code === "PEN") {
        $price_alternative_sale = $price / $currency_value;
    } else {
        $price_alternative_sale = $price * $currency_value;
    }

    return $price_alternative_sale;
}

/**
 * Modifica el HTML del precio para mostrarlo en la moneda PEN.
 *
 * @param string $price El HTML del precio.
 * @param WC_Product $product El objeto del producto.
 * @return string El HTML del precio modificado.
 */
/**
 * Muestra el precio para la página de un solo producto.
 */

if (!function_exists('solu_show_product_price_single')) {
    function solu_show_product_price_single(WC_Product $product, SoluCurrenciesExchange $currency_storage_local, SoluCurrenciesExchange $currency_storage_alternative)
    {

        $currency_alternative_symbol = $currency_storage_alternative->currency_symbol;
        $currency_symbol_local = $currency_storage_local->currency_symbol;



        // Verificar si el producto tiene descuento y un precio regular
        if ($product->is_on_sale() && $product->get_regular_price()) {
            // Obtener precios regular y de oferta
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();

            $price_alternative_regular = getAlternativeCurrencyPrice($regular_price, $currency_storage_local, $currency_storage_alternative);
            $price_alternative_sale = getAlternativeCurrencyPrice($sale_price, $currency_storage_local, $currency_storage_alternative);


            // Formatear el precio regular con el símbolo de la moneda local y alternativa
            $formatted_price_local_regular = sprintf(
                ' %s',
                number_format(floatval($regular_price), 2)
            );
            $formatted_price_alternative_regular = sprintf(
                '%s',
                number_format(floatval($price_alternative_regular), 2)
            );

            // Formatear el precio de oferta con el símbolo de la moneda local y alternativa
            $formatted_price_local_sale = sprintf(
                '%s',
                number_format(floatval($sale_price), 2)
            );
            $formatted_price_alternative_sale = sprintf(
                '%s',
                number_format(floatval($price_alternative_sale), 2)
            );


            $price_html = renderTablePrice(
                'regular_price',
                $currency_symbol_local,
                $currency_alternative_symbol,
                $formatted_price_local_regular,
                $formatted_price_alternative_regular,
                $formatted_price_local_sale,
                $formatted_price_alternative_sale
            );


            return $price_html;
        } else {



            // Obtener el precio del producto (será el precio regular si no está en oferta)
            $product_price = $product->get_price();
            // 2. Validamso si la moneda es peruana
            $price_alternative_sale = getAlternativeCurrencyPrice($product_price, $currency_storage_local, $currency_storage_alternative);



            // Formatear el precio de oferta con el símbolo de la moneda local y alternativa
            $formatted_price_local_sale = sprintf(
                '%s',
                number_format(floatval($product_price), 2)
            );
            $formatted_price_alternative_sale = sprintf(
                '%s',
                number_format(floatval($price_alternative_sale), 2)
            );


            $price_html = renderTablePrice(
                'sale_price',
                $currency_symbol_local,
                $currency_alternative_symbol,
                '',
                '',
                $formatted_price_local_sale,
                $formatted_price_alternative_sale
            );


            // Retornar el HTML del precio modificado
            return $price_html;
        }
    }
}

/**
 * Muestra el precio para las páginas de listado de productos.
 */
if (!function_exists('solu_show_product_price_list')) {
    //    function solu_show_product_price_list(WC_Product $product, $currency_alternative_symbol, $currency_alternative_price, $currency_symbol_local)
    function solu_show_product_price_list(WC_Product $product, SoluCurrenciesExchange $currency_storage_local, SoluCurrenciesExchange $currency_storage_alternative)
    {
        $currency_alternative_symbol = $currency_storage_alternative->currency_symbol;
        $currency_symbol_local = $currency_storage_local->currency_symbol;


        // Verificar si el producto tiene descuento y un precio regular
        if ($product->is_on_sale() && $product->get_regular_price()) {
            // Obtener precios regular y de oferta
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();

            // Convertir precios a la moneda alternativa
            $price_alternative_regular =  getAlternativeCurrencyPrice($regular_price, $currency_storage_local, $currency_storage_alternative);
            $price_alternative_sale = getAlternativeCurrencyPrice($sale_price, $currency_storage_local, $currency_storage_alternative);

            // Formatear el precio regular con el símbolo de la moneda local y alternativa
            $formatted_price_local_regular = sprintf(
                ' %s',
                number_format(floatval($regular_price), 2)
            );
            $formatted_price_alternative_regular = sprintf(
                '%s',
                number_format(floatval($price_alternative_regular), 2)
            );

            // Formatear el precio de oferta con el símbolo de la moneda local y alternativa
            $formatted_price_local_sale = sprintf(
                '%s',
                number_format(floatval($sale_price), 2)
            );
            $formatted_price_alternative_sale = sprintf(
                '%s',
                number_format(floatval($price_alternative_sale), 2)
            );

            // Retornar el HTML del precio con el formato de oferta de WooCommerce
            $price_html = '<del aria-hidden="true">';
            $price_html .= '<span class="woocommerce-Price-amount amount solu-currencies-list-price-regular-del">';
            $price_html .= '<bdi><span class="woocommerce-Price-currencySymbol">' . $currency_symbol_local . '</span>' . $formatted_price_local_regular . '</bdi>';
            $price_html .= '</span></del>';
            $price_html .= '<span class="screen-reader-text">Original price was: ' . $formatted_price_local_regular . '.</span>';
            $price_html .= '<span class="line-separator"> - </span>';
            $price_html .= '<del aria-hidden="true">';
            $price_html .= '<span class="woocommerce-Price-amount amount solu-currencies-list-price-alternative-del">';
            $price_html .= '<bdi><span class="woocommerce-Price-currencySymbol">' . $currency_alternative_symbol . '</span>' . $formatted_price_alternative_regular . '</bdi>';
            $price_html .= '</span></del>';
            $price_html .= '<span class="screen-reader-text">Current price is: ' . $formatted_price_alternative_regular . '.</span>';

            $price_html .= '<br>';

            $price_html .= '<ins aria-hidden="true">';
            $price_html .= '<span class="woocommerce-Price-amount amount solu-currencies-list-price-local">';
            $price_html .= '<bdi><span class="woocommerce-Price-currencySymbol">' . $currency_symbol_local . '</span>' . $formatted_price_local_sale . '</bdi>';
            $price_html .= '</span></ins>';
            $price_html .= '<span class="screen-reader-text">Original price was: ' . $formatted_price_local_sale . '.</span>';
            $price_html .= '<span class="line-separator"> - </span>';
            $price_html .= '<ins aria-hidden="true">';
            $price_html .= '<span class="woocommerce-Price-amount amount solu-currencies-list-price-alternative">';
            $price_html .= '<bdi><span class="woocommerce-Price-currencySymbol">' . $currency_alternative_symbol . '</span>' . $formatted_price_alternative_sale . '</bdi>';
            $price_html .= '</span></ins>';
            $price_html .= '<span class="screen-reader-text">Current price is: ' . $formatted_price_alternative_sale . '.</span>';

            return $price_html;
        } else {
            // Obtener el precio del producto (será el precio regular si no está en oferta)
            $product_price = $product->get_price();

            // Convertir el precio a la moneda PEN
            $price_alternative_sale = getAlternativeCurrencyPrice($product_price, $currency_storage_local, $currency_storage_alternative);

            // Formatear el precio de oferta con el símbolo de la moneda local y alternativa
            $formatted_price_local_sale = sprintf(
                '%s',
                number_format(floatval($product_price), 2)
            );
            $formatted_price_alternative_sale = sprintf(
                '%s',
                number_format(floatval($price_alternative_sale), 2)
            );


            $price_html = '<ins aria-hidden="true">';
            $price_html .= '<span class="woocommerce-Price-amount amount solu-currencies-list-price-local">';
            $price_html .= '<bdi><span class="woocommerce-Price-currencySymbol">' . $currency_symbol_local . '</span>' . $formatted_price_local_sale . '</bdi>';
            $price_html .= '</span></ins>';
            $price_html .= '<span class="screen-reader-text">Original price was: ' . $formatted_price_local_sale . '.</span>';
            $price_html .= '<span class="line-separator"> - </span>';
            $price_html .= '<ins aria-hidden="true">';
            $price_html .= '<span class="woocommerce-Price-amount amount solu-currencies-list-price-alternative">';
            $price_html .= '<bdi><span class="woocommerce-Price-currencySymbol">' . $currency_alternative_symbol . '</span>' . $formatted_price_alternative_sale . '</bdi>';
            $price_html .= '</span></ins>';
            $price_html .= '<span class="screen-reader-text">Current price is: ' . $formatted_price_alternative_sale . '.</span>';


            // Retornar el HTML del precio modificado
            return $price_html;
        }
    }
}


if (!function_exists('solu_currencies_exchange_woocommerce_price_single')) {
    function solu_currencies_exchange_woocommerce_price_single(WC_Product $product)
    {

        // la moneda local es la que se usa en el sitio web, por lo que no es necesario pasarle el código de moneda
        $currency_storage_local = getStorageTableJsonRowLocal();

        // Si no existe la moneda PEN, retornar el precio original
        if (! $currency_storage_local) {
            return $product;
        }


        // Obtener la información de la moneda PEN desde la base de datos
        $soluCurrencyAlternative = getStorageCurrencyAlternative();
        $currency_storage_alternative = getStorageTableJsonRow($soluCurrencyAlternative->currency_alternative_code);


        return solu_show_product_price_single($product, $currency_storage_local, $currency_storage_alternative);
    }
}
