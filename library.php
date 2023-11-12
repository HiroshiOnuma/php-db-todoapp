<?php
// htmlspecialcharsを短くする
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES);
}

// DB接続
function dbconnect()
{
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $db_host = $_ENV['DB_HOST'];
    $db_user = $_ENV['DB_USER'];
    $db_pass = $_ENV['DB_PASS'];
    $db_name = $_ENV['DB_NAME'];
    $db = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (!$db) {
        die($db->error);
    }
    return $db;
}
