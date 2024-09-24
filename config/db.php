<?php

use Dotenv\Dotenv;
use yii\base\InvalidConfigException;

// Carregar as variáveis do arquivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Função auxiliar para obter as variáveis de ambiente com validação
function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false) {
        if ($default !== null) {
            return $default;
        }
        throw new InvalidConfigException("Missing environment variable: $key");
    }

    return $value;
}

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=' . env('DB_HOST', 'localhost') . ';dbname=' . env('DB_NAME', 'teste'),
    'username' => env('DB_USERNAME', 'postgres'),
    'password' => env('DB_PASSWORD', '7812'),
    'charset' => 'utf8',
];
