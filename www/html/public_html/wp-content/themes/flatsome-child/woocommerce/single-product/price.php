<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://docs.woocommerce.com/document/template-structure/
 * @author           WooThemes
 * @package          WooCommerce/Templates
 * @version          3.0.0
 * @flatsome-version 3.16.0
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

$is_active_plugin_solu_currencies_exchange=0;
if (is_plugin_active('solu-currencies-exchange-rate/solu-currencies-exchange-rate.php')) {
    $is_active_plugin_solu_currencies_exchange= 1;
}

$classes = array();
if ($product->is_on_sale()) $classes[] = 'price-on-sale';
if (!$product->is_in_stock()) $classes[] = 'price-not-in-stock'; ?>
<div class="price-wrapper">
    <p class="price product-page-price <?php echo implode(' ', $classes); ?>">
        <?php
        if($is_active_plugin_solu_currencies_exchange==1){
            echo solu_currencies_exchange_woocommerce_price_single($product);
        }else{
            echo $product->get_price_html();
        }
        ?>

    </p>
</div>