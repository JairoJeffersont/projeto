<?php

namespace App\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use App\Models\GabineteModel;
use App\Models\TipoGabineteModel;


class GabineteController {


    public static function listarTiposGabinetes(): array {
        try {
            $tiposGabinetes = TipoGabineteModel::where('id', '!=', '1')->get();

            if ($tiposGabinetes->isEmpty()) {
                return ['status_code' => 200, 'status' => 'empty', 'message' => 'Nenhum tipo de gabinete registrado.'];
            }

            return ['status_code' => 200, 'status' => 'success', 'data' => $tiposGabinetes->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }


    public static function listarGabinetes(): array {
        try {
            $gabinetes = GabineteModel::where('id', '!=', '1')->get();

            if ($gabinetes->isEmpty()) {
                return ['status_code' => 200, 'status' => 'empty', 'message' => 'Nenhum gabinete registrado.'];
            }

            return ['status_code' => 200, 'status' => 'success', 'data' => $gabinetes->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }


    public static function buscarGabinete(string $valor = '', string $coluna = 'id'): array {

        try {

            if (empty($valor)) {
                return ['status_code' => 400, 'status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $gabinete = GabineteModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$gabinete) {
                return ['status_code' => 404, 'status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            return ['status_code' => 200, 'status' => 'success', 'data' => $gabinete->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }


    public static function apagarGabinete(string $id): array {
        try {

            if ($id === '1') {
                return ['status_code' => 403, 'status' => 'not_permitted'];
            }

            $gabinete = GabineteModel::find($id);

            if (!$gabinete) {
                return ['status_code' => 404, 'status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            $gabinete->delete();
            return ['status_code' => 200, 'status' => 'success', 'message' => 'Gabinete apagado.'];
        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status_code' => 403, 'status' => 'not_permitted', 'message' => 'Este gabinete não pode ser apagado.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }


    public static function novoGabinete(array $dados): array {
        try {

            // Define campos obrigatórios
            $camposObrigatorios = ['nome', 'email', 'estado', 'tipo_gabinete_id'];

            // Verifica campos obrigatórios
            $camposFaltando = array_filter($camposObrigatorios, fn($campo) => empty($dados[$campo]));

            if (!empty($camposFaltando)) {
                return ['status_code' => 400, 'status' => 'bad_request', 'message' => 'Campos obrigatórios não enviados: ' . implode(', ', $camposFaltando)];
            }

            // Verifica duplicidade pelo email
            $gabinete = GabineteModel::where('email', $dados['email'])->first();
            if ($gabinete) {
                return ['status_code' => 409, 'status' => 'conflict', 'message' => 'Gabinete já cadastrado.'];
            }

            // Adiciona ID e slug
            $dados['id'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $dados['nome_slug'] = \App\Helpers\Slugfy::slug($dados['nome']);

            // Cria o gabinete
            $gabinete = GabineteModel::create($dados);

            return ['status_code' => 201, 'status' => 'success', 'message' => 'Gabinete criado.', 'data' => $gabinete->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }


    public static function atualizarGabinete(string $id, array $dados): array {
        try {

            $gabinete = GabineteModel::find($id);

            if (!$gabinete) {
                return ['status_code' => 404, 'status' => 'not_found', 'message' => 'Gabinete não encontrado.'];
            }

            if (isset($dados['email'])) {

                $emailExistente = GabineteModel::where('email', $dados['email'])->where('id', '<>', $id)->first();

                if ($emailExistente) {
                    return ['status_code' => 409, 'status' => 'conflict', 'message' => 'Gabinete já cadastrado.'];
                }
            }

            $gabinete->update($dados);

            return ['status_code' => 200, 'status' => 'success', 'message' => 'Gabinete atualizado.', 'data' => $gabinete->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status_code' => 500, 'status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
