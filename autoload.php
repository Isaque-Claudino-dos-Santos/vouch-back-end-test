<?php


spl_autoload_register(function ($classNamespace) {
    $path = str_replace('\\', '/', $classNamespace);
    $path = __DIR__ . "/$path.php";

    if (file_exists($path)) {
        require_once $path;
    }
});