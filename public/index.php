<?php
ob_start();
define('LOG_FOLDER', dirname(__DIR__, 1) . '/logs');
define('PUBLIC_FOLDER', dirname(__DIR__, 2) . '/public');
define('BASE_URL', '/');

use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => $_ENV['DB_DRIVER'],
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_DATABASE'],
    'username'  => $_ENV['DB_USERNAME'],
    'password'  => $_ENV['DB_PASSWORD'],
    'charset'   => $_ENV['DB_CHARSET'],
    'collation' => $_ENV['DB_COLLATION']
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

include('../src/Views/base/base_layout.php');
