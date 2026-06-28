<?php

// Cargar variables del archivo .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $_ENV[trim($key)] = trim($value);
    }
}

define("DB_HOST",     $_ENV['DB_HOST']     ?? 'localhost');
define("DB_NAME",     $_ENV['DB_NAME']     ?? 'simplepos');
define("DB_USERNAME", $_ENV['DB_USERNAME'] ?? 'root');
define("DB_PASSWORD", $_ENV['DB_PASSWORD'] ?? '');
define("DB_ENCODE",   $_ENV['DB_ENCODE']   ?? 'utf8');
define("PRO_NOMBRE",  $_ENV['PRO_NOMBRE']  ?? 'SimplePOS');
define("PRO_TIMEZONE",$_ENV['PRO_TIMEZONE']?? 'UTC');
