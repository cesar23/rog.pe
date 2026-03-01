<?php
// Template para crear un nuevo registro en la tabla SOLU_CURRENCIES_EXCHANGE_TABLE

global $solu_currencies;
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Crear Nueva Moneda</h1>
    <hr class="wp-header-end">
    <form method="post" action="" class="validate" novalidate="novalidate">
        <table class="form-table" role="presentation">

            <tr class="form-field">
                <th scope="row"><label for="currency_name">Nombre de la moneda <span class="description">(obligatorio)</span></label></th>
                <td>
                    <select name="currency_name" id="currency_name" class="regular-text" required="required">
                        <option value="">Seleccionar</option>
                        <?php foreach ($solu_currencies as $code => $data): ?>
                            <option value="<?php echo esc_attr($data['currency_name']); ?>"><?php echo esc_html($data['currency_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>


             <tr class="form-field">
                <th scope="row"><label for="currency_description">Descripción</label></th>
                <td><input type="text" name="currency_description" id="currency_description" value="" class="regular-text"></td>
            </tr>

            <tr class="form-field">
                <th scope="row"><label for="currency_code">Codigo Moneda <span class="description">(obligatorio)</span></label></th>
                <td>
                    <select name="currency_code" id="currency_code" class="regular-text" required="required">
                        <option value="">Seleccionar</option>
                        <?php foreach ($solu_currencies as $code => $data): ?>
                            <option value="<?php echo esc_attr($code); ?>" data-symbol="<?php echo esc_attr($data['currency_symbol']); ?>" data-name="<?php echo esc_attr($data['currency_name']); ?>" data-image="<?php echo esc_url($data['image']); ?>"><?php echo esc_html($code); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <img id="currency-icon" src="" alt="Icono Moneda" width="20" style="vertical-align:middle; margin-left:8px; display:none;">
                </td>
            </tr>

           
            <tr class="form-field">
                <th scope="row"><label for="currency_symbol">Símbolo <span class="description">(obligatorio)</span></label></th>
                <td>
                    <select name="currency_symbol" id="currency_symbol" class="regular-text" required="required">
                        <option value="">Seleccionar</option>
                        <?php foreach ($solu_currencies as $code => $data): ?>
                            <option value="<?php echo esc_attr($data['currency_symbol']); ?>"><?php echo esc_html($data['currency_symbol']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="currency_value">Valor <span class="description">(obligatorio)</span></label></th>
                <td><input type="number" step="0.01" name="currency_value" id="currency_value" value="" class="regular-text" required="required"></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="local">Local <span style="font-size: 0.7em">(Moneda principal Ecommerce)</span></label></th>
                <td><input type="checkbox" name="local" id="local" value="1"></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="currency_order">Orden</label></th>
                <td><input type="number" name="currency_order" id="currency_order" value="0" class="regular-text"></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="active">Activo</label></th>
                <td><input type="checkbox" name="active" id="active" value="1" checked></td>
            </tr>
        </table>
        <input type="hidden" name="action" value="create">
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Agregar Nueva Moneda">
        </p>
    </form>
</div>
<script>
    document.getElementById('currency_code').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var icon = document.getElementById('currency-icon');
        var imageUrl = selectedOption.getAttribute('data-image');

        document.getElementById('currency_name').value = selectedOption.getAttribute('data-name');
        document.getElementById('currency_symbol').value = selectedOption.getAttribute('data-symbol');

        if (imageUrl) {
            icon.src = imageUrl;
            icon.style.display = 'inline-block';
        } else {
            icon.src = '';
            icon.style.display = 'none';
        }
    });
</script>