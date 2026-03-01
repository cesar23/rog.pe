<?php
// Template para crear un nuevo registro en la tabla SOLU_CURRENCIES_EXCHANGE_TABLE


?>
<div class="wrap">
    <h1 class="wp-heading-inline">Lista de Monedas</h1>
    <a href="?page=solu-currencies-exchange&action=create" class="page-title-action">Agregar Nueva Moneda</a>
    <hr class="wp-header-end">
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Símbolo</th>
                <th>Código</th>
                <th>Valor</th>
                <th>Local <br> <span style="font-size: 0.7em">(Moneda principal Ecommerce)</span></th>
                <th>Orden</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $currencies = get_currencies();
            if (empty($currencies)) {
                echo '<tr><td colspan="9">No se encontraron monedas.</td></tr>';
            } else {
                global $solu_currencies;
                foreach ($currencies as $currency) {
                    $img='<img src="'.$solu_currencies[$currency['currency_code']]['image'].'" alt="'.$currency['currency_code'].'" width="20">';
                    echo '<tr>';
                    echo '<td>' . esc_html($currency['id']) . '</td>';
                    echo '<td>' . esc_html($currency['currency_name']) . '</td>';
                    echo '<td>' . esc_html($currency['currency_description']) . '</td>';
                    echo '<td>' . esc_html($currency['currency_symbol']) . '</td>';
                    echo '<td>' . esc_html($currency['currency_code']).' '.$img . '</td>';
                    echo '<td>' . esc_html($currency['currency_value']) . '</td>';
                    echo '<td>' . ($currency['currency_local'] ? '✅' : '-') . '</td>';
                    echo '<td>' . esc_html($currency['currency_order']) . '</td>';
                    echo '<td>' . ($currency['active'] ? '✅' : '-') . '</td>';
                    echo '<td>
                            <a href="?page=solu-currencies-exchange&action=edit&id=' . esc_attr($currency['id']) . '" class="button action">Editar</a>
                            <a href="?page=solu-currencies-exchange&action=delete&id=' . esc_attr($currency['id']) . '" class="button action">Borrar</a>
                          </td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Símbolo</th>
                <th>Código</th>
                <th>Valor</th>
                <th>Local <br> <span style="font-size: 0.7em">(Moneda principal Ecommerce)</span></th>
                <th>Orden</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </tfoot>
    </table>
</div>