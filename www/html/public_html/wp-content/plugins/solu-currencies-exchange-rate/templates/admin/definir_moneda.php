<div class="wrap">
    <h1>Definir Moneda tipo Cambio</h1>
    <p>Aquí puedes definir la moneda y el tipo de cambio.</p>
</div>



<form method="post" class="row g-3">
    <div class="col-auto">
        <label for="currency" class="form-label">Moneda alternativa que se mostrar para el tipo de cambio:</label>
    </div>
    <div class="col-auto">
        <select name="currency_alternative_code" id="currency" class="form-select">
            <option value="">Seleccione una moneda</option>
            <?php
            $get_storage_currency_alternative = getStorageCurrencyAlternative();
            if (!empty($get_currencies_no_locals)) : ?>
                <?php foreach ($get_currencies_no_locals as $currency) : ?>
                    <option value="<?php echo $currency->currency_code; ?>" <?php echo ($get_storage_currency_alternative && $get_storage_currency_alternative->currency_alternative_code == $currency->currency_code) ? 'selected' : ''; ?>><?php echo $currency->currency_code; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div class="col-auto">
        <input type="hidden" name="action" value="create">
        <button type="submit" class="btn btn-primary mb-3">Guardar</button>
    </div>
</form>