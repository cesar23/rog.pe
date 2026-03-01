<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form method="post" action="" class="">
        <div class="mb-3 form-check">
            <?php
            $option_value_shorten_title=get_option(SOLU_CURRENCIES_EXCHANGE_OPTION_SHORTEN_TITLE);
            $checked = checked('on',$option_value_shorten_title, false);
            ?>
            <input type="checkbox" class="" id="shorten_titles_option" name="shorten_titles_option" value="on" <?php echo $checked; ?>>
            <label class="form-check-label" for="shorten_titles_option">Habilitar acortar titulos de productos</label>
        </div>
        <input type="hidden" name="action" value="update_shorten_titles">
        <?php submit_button('Save Changes'); ?>
    </form>
</div>