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

        $colunasPermitidas = ['id', 'email', 'nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Permitidas: ' . implode(', ', $colunasPermitidas) . '.'];
        }

        try {
            $usuario = GabineteModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            return ['status' => 'success', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
