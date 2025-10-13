<?php

define('LOG_FOLDER', dirname(__DIR__, 1) . '/logs');
define('PUBLIC_FOLDER', dirname(__DIR__, 1) . '/public');

use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Dotenv\Dotenv;
use JairoJeffersont\EasyLogger\Logger;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Configura banco de dados
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

// Cria app Slim
$app = AppFactory::create();

// Importa rotas
(require __DIR__ . '/../src/web.php')($app);

// === Tratamento de rota não encontrada (404) ===
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setErrorHandler(HttpNotFoundException::class, function (Request $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    $errorId = Logger::newLog(LOG_FOLDER, 'error', 'route_not_found | ' . $request->getUri()->getPath(), 'ERROR');

    $response->getBody()->write(json_encode([
        'status_code' => 404,
        'status' => 'route_not_found',
        'message' => 'Rota não encontrada',
        'error_id' => $errorId
    ]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
});

// === Tratamento de método não permitido (405) ===
$errorMiddleware->setErrorHandler(HttpMethodNotAllowedException::class, function (Request $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    $errorId = Logger::newLog(LOG_FOLDER, 'error', 'method_not_allowed | ' . $request->getUri()->getPath() . ' | ' . $request->getMethod(), 'ERROR');

    $response->getBody()->write(json_encode([
        'status_code' => 405,
        'status' => 'method_not_allowed',
        'message' => 'Método HTTP não permitido',
        'error_id' => $errorId
    ]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(405);
});

// === Tratamento de erro genérico (500) ===
$errorMiddleware->setDefaultErrorHandler(function (Request $request, Throwable $exception, bool $displayErrorDetails,  bool $logErrors, bool $logErrorDetails) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    $errorId = Logger::newLog(LOG_FOLDER, 'error', 'server_error | ' . $request->getUri()->getPath() . ' | ' . $request->getMethod() . ' | ' . $exception->getMessage(), 'ERROR');

    $response->getBody()->write(json_encode([
        'status_code' => 500,
        'status' => 'server_error',
        'message' => 'Erro interno do servidor.',
        'error_id' => $errorId
    ]));

    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
});


// Executa app
$app->run();
