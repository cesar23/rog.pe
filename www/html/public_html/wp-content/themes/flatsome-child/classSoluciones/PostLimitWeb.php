<?php
/*
------------------------------
-------POST-------------------
------------------------------
$pagenow -  edit.php // listado de post
---creacion---
$pagenow = post-new.php
$typenow = post

------------------------------
-------MEDIOS-------------------
------------------------------
$pagenow -  upload.php // listado de medios (aqui poner estilo para  ocultar la  subidad del boton)
$pagenow -  media-new.php // agrega medio


------------------------------
-------PAGINAS-------------------
------------------------------

// --------------------listado de paginas
$pagenow = edit.php
$typenow = page
------------------------creacion---
$pagenow = post-new.php
$typenow = page
------------------------edicion---
$pagenow = post.php
$typenow = page

------------------------------
-------PRODUCTOS-------------------
------------------------------

// --------------------listado de paginas
$pagenow = edit.php
$typenow = product
------------------------creacion---
$pagenow = post-new.php
$typenow = product

------------------------edicion---
$pagenow = post.php
$typenow = product

 * )
 * */

//define('LIMIT_FILTER_POST',12);
//define('LIMIT_FILTER_MEDIA',12);
//define('LIMIT_FILTER_PRODUCT',12);
//define('LIMIT_FILTER_PAGE',12);

function getPostCount($post_type='product'){
    global $wpdb;
    $pstatus = "IN ('publish', 'pending', 'draft','inherit')";
    $wherePostType=" and post_type='". $post_type."' ";
    $num_post = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status ". $pstatus ."   ".$wherePostType." "  ));
    return  $num_post;

}
function limit_post_count(){

    global $pagenow ,$current_user,$typenow;

    if (is_admin() && in_array($pagenow,array('post-new.php','press-this.php')) ){

        if($typenow=='post' && getPostCount('post') >=LIMIT_FILTER_POST){
            messageLimitPost("Error en el servidor has superado el maximo de <strong>(".LIMIT_FILTER_POST.")</strong> publicaciones");
        }

        if($typenow=='product' && getPostCount('product') >=LIMIT_FILTER_PRODUCT){
            messageLimitPost("Error en el servidor has superado el maximo de <strong>(".LIMIT_FILTER_PRODUCT.")</strong> productos");
        }

        if($typenow=='page' && getPostCount('product') >=LIMIT_FILTER_PAGE){
            messageLimitPost("Error en el servidor has superado el maximo de <strong>(".LIMIT_FILTER_PAGE.")</strong> paginas");
        }



    }
}
// display error massage
function messageLimitPost($m=null){

    ?>
    <style>
        html {background: #f9f9f9;}
        #error-page {margin-top: 50px;}
        #error-page p {font-size: 14px;line-height: 1.5;margin: 25px 0 20px;}
        #error-page code {font-family: Consolas, Monaco, monospace;}
        body {background: #fff;color: #333;font-family: sans-serif;margin: 2em auto;padding: 1em 2em;-webkit-border-radius: 3px;border-radius: 3px;border: 1px solid #dfdfdf;max-width: 700px;height: auto;}
    </style>
    <div id="error-page">
        <div  style="text-align: center;"><img src="https://icons.iconarchive.com/icons/double-j-design/ravenna-3d/128/Alert-icon.png" alt=""></div>
        <div id="message" class="" style="padding: 10px;text-align: center; font-size: 15px"><?php echo $m ?></div>
    </div>



    <div id="" class="hidden notice is-dismissible " style="display: block; border-left-color: #dba617">
        <p class="local-restore">
            Este  servidor de Amazon se ha llenado de imagenes y archivos se sugiere que abone una cuota adicianal mensual.
        </p>
    </div>


    <?php
    add_action('admin_footer','hide_links');
    exit;

}
function hide_links(){
    global $typenow;

    ?>
    <script>
        function BuscarReemplazarEnlaces($textoBuscar,$urlReemplazar){
            document.querySelectorAll('a').forEach(ele => {
                if(ele.getAttribute('href').indexOf($textoBuscar) != -1 ) {
                    //console.log('se encontro',ele);
                    ele.setAttribute('href',$urlReemplazar)
                }

            });

        }
        jQuery(document).ready(function() {
            jQuery('.add-new-h2').remove();
            jQuery('[href$="post-new.php"]').remove();
            BuscarReemplazarEnlaces('post-new.php','#')
        });

    </script>
    <?php
}

function LimitMediaFile( $file ) {
    $msg='';
    if(getPostCount('attachment') >=LIMIT_FILTER_MEDIA){
        $msg="Error en el servidor has superado el maximo de (".LIMIT_FILTER_MEDIA.") imagenes";
        $file['error'] = $msg;
        return $file;
    }else{
        return $file;
    }


}

add_filter( 'wp_handle_upload_prefilter', 'LimitMediaFile' );
