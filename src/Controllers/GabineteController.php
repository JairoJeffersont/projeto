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
                return ['status' => 'empty', 'message' => 'Nenhum tipo de gabinete registrado.'];
            }

            return ['status' => 'success', 'data' => $tiposGabinetes->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function listarGabinetes(): array {
        try {
            $gabinetes = GabineteModel::where('id', '!=', '1')->get();

            if ($gabinetes->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum gabinete registrado.'];
            }

            return ['status' => 'success', 'data' => $gabinetes->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarGabinete(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $gabinete = GabineteModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$gabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            return ['status' => 'success', 'data' => $gabinete->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarGabinete(string $id = ''): array {
        try {
            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            if ($id === '1') {
                return ['status' => 'not_permitted'];
            }

            $gabinete = GabineteModel::find($id);

            if (!$gabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            $gabinete->delete();
            return ['status' => 'success', 'message' => 'Gabinete apagado.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Este gabinete não pode ser apagado.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novoGabinete(array $dados): array {
        try {
            if (empty($dados)) {
                return ['status' => 'bad_request', 'message' => 'Nenhum dado foi enviado'];
            }
        
            $gabinete = GabineteModel::where('nome', $dados['nome'])->first();
            if ($gabinete) {
                return ['status' => 'conflict', 'message' => 'Gabinete já cadastrado.'];
            }

            $dados['id'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $dados['nome_slug'] = \App\Helpers\Slugfy::slug($dados['nome']);

            $gabinete = GabineteModel::create($dados);

            return ['status' => 'success', 'message' => 'Gabinete criado.', 'data' => $gabinete->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarGabinete(string $id, array $dados): array {
        try {
            if (empty($dados)) {
                return ['status' => 'bad_request', 'message' => 'Nenhum dado foi enviado'];
            }

            $gabinete = GabineteModel::find($id);

            if (!$gabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado.'];
            }

            if (isset($dados['email'])) {
                $emailExistente = GabineteModel::where('email', $dados['email'])
                    ->where('id', '<>', $id)
                    ->first();

                if ($emailExistente) {
                    return ['status' => 'conflict', 'message' => 'Gabinete já cadastrado.'];
                }
            }

            if (isset($dados['nome'])) {
                $dados['nome_slug'] = \App\Helpers\Slugfy::slug($dados['nome']);
            }

            $gabinete->update($dados);

            return ['status' => 'success', 'message' => 'Gabinete atualizado.', 'data' => $gabinete->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
