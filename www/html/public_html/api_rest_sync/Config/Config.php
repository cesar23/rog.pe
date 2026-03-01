<?php
date_default_timezone_set('America/Lima');
header('X-Powered-By: Our company\'s development team');
if (function_exists('header_remove')) {
    header_remove('X-Powered-By'); // PHP 5.3+
} else {
    @ini_set('expose_php', 'off');

}
