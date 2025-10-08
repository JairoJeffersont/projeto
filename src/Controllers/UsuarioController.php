<?php

namespace App\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use App\Models\TipoUsuarioModel;
use App\Models\UsuarioModel;
use App\Models\GabineteModel;
use Ramsey\Uuid\Uuid;

class UsuarioController {

    public static function listarTiposUsuarios(): array {
        try {
            $tiposUsuarios = TipoUsuarioModel::get();

            if ($tiposUsuarios->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de usuário registrado.'];
            }

            return ['status' => 'success', 'data' => $tiposUsuarios->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function listarUsuarios($gabineteId = ''): array {
        try {

            if (empty($gabineteId)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $gabinete = GabineteModel::where('id', $gabineteId)->where('id', '<>', 1)->first();

            if (!$gabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            $usuarios = UsuarioModel::where('id', '!=', 1)->where('gabinete_id', $gabinete->id)->get();

            if ($usuarios->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum usuário registrado.'];
            }

            return ['status' => 'success', 'data' => $usuarios->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarUsuario(string $valor, string $coluna = 'id'): array {

        try {
            $usuario = UsuarioModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            return ['status' => 'success', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarUsuario(string $id): array {
        try {

            if ($id === '1') {
                return ['status' => 'not_permitted', 'message' => 'Este usuário não pode ser apagado.'];
            }

            $usuario = UsuarioModel::find($id);

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $usuario->delete();
            return ['status' => 'success', 'message' => 'Usuário apagado.'];
        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Este usuário não pode ser apagado.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novoUsuario(array $dados): array {
        try {

            $usuario = UsuarioModel::where('email', $dados['email'])->first();

            if ($usuario) {
                return ['status' => 'duplicated', 'message' => 'Usuário já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);

            $usuario = UsuarioModel::create($dados);

            return ['status' => 'success', 'message' => 'Gabinete criado.', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
