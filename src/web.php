<?php

use App\Controllers\GabineteController;
use App\Controllers\UsuarioController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    // Função auxiliar para simplificar respostas JSON
    $responder = function (Response $response, $callback, $args = [], $request = null) {
        if ($request) {
            $body = $request->getBody()->getContents();
            $args[] = json_decode($body, true); // passa os dados do POST/PUT como último argumento
        }
        $data = $callback(...$args);
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($data['status_code'] ?? 200);
    };

    $app->get('/', function (Request $request, Response $response) use ($responder) {
        $payload = ['status_code' => 200, 'status' => 'success', 'message' => 'API em funcionamento. Consulte a documentação.'];
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    $app->group('/gabinetes', function ($group) use ($responder) {

        $group->get('/tipos', function (Request $request, Response $response) use ($responder) {
            return $responder($response, [GabineteController::class, 'listarTiposGabinetes']);
        });

        $group->get('', function (Request $request, Response $response) use ($responder) {
            return $responder($response, [GabineteController::class, 'listarGabinetes']);
        });

        $group->get('/{id}', function (Request $request, Response $response, array $args) use ($responder) {
            return $responder($response, [GabineteController::class, 'buscarGabinete'], [$args['id']]);
        });

        $group->delete('/{id}', function (Request $request, Response $response, array $args) use ($responder) {
            return $responder($response, [GabineteController::class, 'apagarGabinete'], [$args['id']]);
        });

        $group->post('', function (Request $request, Response $response) use ($responder) {
            return $responder($response, [GabineteController::class, 'novoGabinete'], [], $request);
        });

        $group->put('/{id}', function (Request $request, Response $response, array $args) use ($responder) {
            return $responder($response, [GabineteController::class, 'atualizarGabinete'], [$args['id']], $request);
        });
    });

    $app->group('/usuarios', function ($group) use ($responder) {

        // Listar tipos de usuário
        $group->get('/tipos', function (Request $request, Response $response) use ($responder) {
            return $responder($response, [UsuarioController::class, 'listarTiposUsuarios']);
        });

        // Listar usuários de um gabinete (passando id do gabinete como query param)
        $group->get('', function (Request $request, Response $response) use ($responder) {
            $gabineteId = $request->getQueryParams()['gabinete_id'] ?? '';
            return $responder($response, [UsuarioController::class, 'listarUsuarios'], [$gabineteId]);
        });

        // Buscar usuário por id ou outro campo
        $group->get('/{valor}', function (Request $request, Response $response, array $args) use ($responder) {
            $valor = $args['valor'];
            return $responder($response, [UsuarioController::class, 'buscarUsuario'], [$valor]);
        });

        // Deletar usuário
        $group->delete('/{id}', function (Request $request, Response $response, array $args) use ($responder) {
            return $responder($response, [UsuarioController::class, 'apagarUsuario'], [$args['id']]);
        });

        // Criar novo usuário
        $group->post('', function (Request $request, Response $response) use ($responder) {
            return $responder($response, [UsuarioController::class, 'novoUsuario'], [], $request);
        });

        // Atualizar usuário
        $group->put('/{id}', function (Request $request, Response $response, array $args) use ($responder) {
            return $responder($response, [UsuarioController::class, 'atualizarUsuario'], [$args['id']], $request);
        });
    });
};
