<?php

namespace App\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\FileUploader;
use App\Models\TipoUsuarioModel;
use App\Models\UsuarioModel;
use App\Models\GabineteModel;
use Ramsey\Uuid\Uuid;

class UsuarioController {

    public static function listarTiposUsuarios(): array {
        try {
            $tiposUsuarios = TipoUsuarioModel::get();

            if ($tiposUsuarios->isEmpty()) {
                return ['status_code' => 200, 'status' => 'empty', 'message' => 'Nenhum tipo de usuário registrado.'];
            }

            return ['status_code' => 200, 'status' => 'success', 'data' => $tiposUsuarios->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function listarUsuarios($gabineteId = ''): array {
        try {
            if (empty($gabineteId)) {
                return ['status_code' => 400, 'status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $gabinete = GabineteModel::where('id', $gabineteId)->where('id', '<>', 1)->first();

            if (!$gabinete) {
                return ['status_code' => 404, 'status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            $usuarios = UsuarioModel::where('id', '!=', 1)->where('gabinete_id', $gabinete->id)->get();

            if ($usuarios->isEmpty()) {
                return ['status_code' => 200, 'status' => 'empty', 'message' => 'Nenhum usuário registrado.'];
            }

            return ['status_code' => 200, 'status' => 'success', 'data' => $usuarios->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarUsuario(string $valor, string $coluna = 'id'): array {
        try {
            $usuario = UsuarioModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$usuario) {
                return ['status_code' => 404, 'status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            return ['status_code' => 200, 'status' => 'success', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarUsuario(string $id): array {
        try {
            if ($id === '1') {
                return ['status_code' => 403, 'status' => 'not_permitted', 'message' => 'Este usuário não pode ser apagado.'];
            }

            $usuario = UsuarioModel::find($id);

            if (!$usuario) {
                return ['status_code' => 404, 'status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $usuario->delete();
            return ['status_code' => 200, 'status' => 'success', 'message' => 'Usuário apagado.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status_code' => 403, 'status' => 'not_permitted', 'message' => 'Este usuário não pode ser apagado.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novoUsuario(array $dados): array {
        try {

            // Define campos obrigatórios
            $camposObrigatorios = ['nome', 'email', 'senha', 'telefone', 'data_nascimento', 'tipo_usuario_id', 'gabinete_id'];

            // Verifica campos obrigatórios
            $camposFaltando = array_filter($camposObrigatorios, fn($campo) => empty($dados[$campo]));

            if (!empty($camposFaltando)) {
                return ['status_code' => 400, 'status' => 'bad_request', 'message' => 'Campos obrigatórios não enviados: ' . implode(', ', $camposFaltando)];
            }

            $usuario = UsuarioModel::where('email', $dados['email'])->first();

            if ($usuario) {
                return ['status_code' => 409, 'status' => 'conflict', 'message' => 'Usuário já cadastrado.'];
            }

            if (isset($dados['foto'])) {
                $result = FileUploader::uploadFile(PUBLIC_FOLDER . '/arquivos/usuarios', $dados['foto'], ['image/jpeg', 'image/png'], 5);
                if ($result['status'] == 'success') {
                    $dados['foto'] = $result['file_path'];
                } else {
                    return $result;
                }
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);

            $usuario = UsuarioModel::create($dados);

            return ['status_code' => 201, 'status' => 'success', 'message' => 'Usuário criado.', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarUsuario(string $id, array $dados): array {
        try {
            $usuario = UsuarioModel::find($id);

            if (!$usuario) {
                return ['status_code' => 404, 'status' => 'not_found', 'message' => 'Usuário não encontrado.'];
            }

            if (isset($dados['email'])) {
                $emailExistente = UsuarioModel::where('email', $dados['email'])->where('id', '<>', $id)->first();

                if ($emailExistente) {
                    return ['status_code' => 409, 'status' => 'conflict', 'message' => 'Email já cadastrado.'];
                }
            }

            if (isset($dados['foto'])) {
                $result = FileUploader::uploadFile(PUBLIC_FOLDER . '/arquivos/usuarios', $dados['foto'], ['image/jpeg', 'image/png'], 5);
                if ($result['status'] == 'success') {
                    $dados['foto'] = $result['file_path'];
                } else {
                    return $result;
                }
            }

            $usuario->update($dados);

            return ['status_code' => 200, 'status' => 'success', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
