<?php




function solu_currencies_exchange_woocommerce_price_format($price, WC_Product $product)
{

  $currency_storage_local = getStorageTableJsonRowLocal();

  // Si no existe la moneda PEN, retornar el precio original
  if (! $currency_storage_local) {
    return $price;
  }

    // Obtener la información de la moneda PEN desde la base de datos
  $soluCurrencyAlternative = getStorageCurrencyAlternative();
  $currency_storage_alternative = getStorageTableJsonRow($soluCurrencyAlternative->currency_alternative_code);
    // la moneda local es la que se usa en el sitio web, por lo que no es necesario pasarle el código de moneda


    // Obtener el símbolo y la tasa de cambio de la moneda PEN
    $currency_alternative_symbol = $currency_storage_alternative->currency_symbol; // Símbolo del sol peruano
    $currency_alternative_price = $currency_storage_alternative->currency_value;

    $currency_symbol_local = $currency_storage_local->currency_symbol;

        // Para las páginas de listado, llamar a la otra función
        return solu_show_product_price_list($product, $currency_storage_local, $currency_storage_alternative);

}

