jQuery( document ).ready(function() {
    // esto pondra en el formulario de contacto del producto la url del producto y la  imagen
  if ( jQuery("#input_1_10") ) {
      var ulr_imagen=jQuery(".woocommerce-product-gallery__image").attr( "data-thumb" );
      console.log(ulr_imagen);
    //  jQuery("#url_producto").val(window.location.href);
      jQuery("#input_1_10").val(ulr_imagen);
  
    }
});