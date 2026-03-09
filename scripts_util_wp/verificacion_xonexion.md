
Verificar conexion de  php db

```shell
php -r "
try {
    \$pdo = new PDO('mysql:host=mariadb;dbname=rog_web', 'rog_web', 'cesar203');
    echo '✅ Conexión OK' . PHP_EOL;
} catch (PDOException \$e) {
    echo '❌ ' . \$e->getMessage() . PHP_EOL;
}
"
```

```phpregexp
<?php
try {
    $pdo = new PDO('mysql:host=mariadb;dbname=rog_web', 'rog_web', 'cesar203');
    echo '✅ Conexión OK' . PHP_EOL;
} catch (PDOException \$e) {
    echo '❌ ' . \$e->getMessage() . PHP_EOL;
}

```