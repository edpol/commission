<?php
foreach (get_loaded_extensions() as $extension) {
    if (substr($extension, 0, 4) == 'pdo_' || substr($extension, 0, 6) == 'mysqli') {
        echo $extension . "<br>" . PHP_EOL;
    }
}
phpinfo();
?>