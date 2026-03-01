jQuery(document).ready(function($) {
    //por fecto valo de lima
  $('#billing_state').val('LIM');
  
  
   if ($('#billing_state').val().trim() === '') {
        alert('Debe seleccionar una opción');

    } else {
         $('#billing_city').val('Miraflores');
    }
  
  
  
  
  
  
});