<?php
if (function_exists('curl_version')) {
    echo "cURL está habilitado.";
    $version = curl_version();
    echo " Versión de cURL: " . $version['version'];
} else {
    echo "cURL no está habilitado.";
}
?>
