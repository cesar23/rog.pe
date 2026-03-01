<?php
// Template para mostrar estadísticas de códigos HTML usando la clase Singleton
$backend = Solu_Generate_HTML_Backend_Functions::getInstance();
$total_codes = $backend->get_all_html_codes();
$total_count = count($total_codes);

$categories_count = 0;
$brands_count = 0;
$others_count = 0;

foreach ($total_codes as $code) {
    if (strpos($code['name_group'], 'category_') === 0) {
        $categories_count++;
    } elseif (strpos($code['name_group'], 'brand_') === 0) {
        $brands_count++;
    } else {
        $others_count++;
    }
}
?>

<div class="card">
    <h2>Estadísticas</h2>
    <div style="display: flex; gap: 20px; margin-top: 10px;">
        <div><strong>Total:</strong> <?php echo $total_count; ?> códigos</div>
        <div><strong>Categorías:</strong> <?php echo $categories_count; ?> códigos</div>
        <div><strong>Marcas:</strong> <?php echo $brands_count; ?> códigos</div>
        <div><strong>Otros:</strong> <?php echo $others_count; ?> códigos</div>
    </div>
</div> 