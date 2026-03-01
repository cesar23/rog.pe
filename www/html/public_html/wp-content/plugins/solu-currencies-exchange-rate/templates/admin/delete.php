<?php
// Template para eliminar un registro de la tabla SOLU_CURRENCIES_EXCHANGE_TABLE
?>
<div class="wrap">
    <h1>Eliminar Moneda</h1>
    <?php
    if (isset($currency['currency'])) {

        ?>

        <p class="alert alert-danger">⚠️¿Estás seguro de que quieres eliminar la moneda <?php echo esc_attr($currency['currency']); ?>
            (<?php echo esc_attr($currency['code']); ?>) del país <?php echo esc_attr($currency['country']); ?>?</p>


        <form method="post" action="">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo esc_attr($currency['id']); ?>">
            <input type="submit" value="Eliminar Moneda" class="button button-primary">
        </form>
        <?php

    }

    ?>


</div>