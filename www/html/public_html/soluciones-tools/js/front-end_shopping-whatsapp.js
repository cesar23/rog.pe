
const _text_change='Cotización';
//::::::::::::::::::::::::::::::: Escritorio :::::::::::::::::::::::::
// (header) cambia el texto del boton del header
document.querySelector('.header-cart-title').innerHTML=document.querySelector('.header-cart-title').innerHTML.replace(/carrito/gi,_text_change)
// (header) botones de compra y carrito
for (const a of document.querySelectorAll('.woocommerce-mini-cart__buttons .wc-forward')) {
  if (a.textContent.includes("carrito")) {
    console.log(a)
    a.textContent=_text_change
    console.log(a.textContent)
  }
}


//::::::::::::::::::::::::::::::: Mobil :::::::::::::::::::::::::
// (header) cambia texto en el desplegable del carrito
jQuery('.mobile-nav .header-button').click(function() {
  setTimeout(() => {
    document.querySelector('.widget_shopping_cart_content .button.wc-forward').innerText=_text_change
  }, "500");
});
jQuery('.cart-popup-title h4').text(_text_change )
jQuery('.woocommerce-mini-cart__buttons .wc-forward').text( _text_change)


//::::::::::::::::::::::::::::::::::::::
//:::::::: producto single :::::::::::::
document.querySelector('.cart [name="add-to-cart"]').textContent=_text_change;

let text=document.querySelector('.message-container.success-color').textContent
text=text.replace(/carrito/gi,_text_change)
document.querySelector('.message-container.success-color').textContent=text.replace(/carrito/gi,_text_change)

//::::::::::::::::::::::::::::::::
//:::::::: page cart :::::::::::::

// cambiar cabecera
text=document.querySelector('.breadcrumbs.flex-row [class="current"]').textContent
text=text.replace(/carrito/gi,_text_change)
document.querySelector('.breadcrumbs.flex-row [class="current"]').textContent=text.replace(/carrito/gi,_text_change)

// cambiar boton
text=document.querySelector('.shop_table [name="update_cart"]').textContent
text=text.replace(/carrito/gi,_text_change)
document.querySelector('.shop_table [name="update_cart"]').textContent=text.replace(/carrito/gi,_text_change)

// tabla subtotal
text=document.querySelector('.cart-sidebar [class="product-name"]').textContent
text=text.replace(/carrito/gi,_text_change)
document.querySelector('.cart-sidebar [class="product-name"]').textContent=text.replace(/carrito/gi,_text_change)



