<?php

use App\Controllers\GabineteController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write("COLOCAR A DOCUMENTAÇÃO");
        return $response;
    });
    
};
