<?php

use App\Controllers\GabineteController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    $app->get('/', function (Request $request, Response $response) {

        $payload = ['status_code' => 200, 'status' => 'success', 'message' => 'API em funcionamento. Consulte a documentação.'];

        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });
};
