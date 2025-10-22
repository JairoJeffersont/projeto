<?php

namespace App\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use App\Models\TipoDocumentoModel;

use Ramsey\Uuid\Uuid;


class DocumentoController {

    //OPERACOES COM TIPO
    public static function listarTiposDocumentos(string $gabinete_id = ''): array {
        try {

            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $tiposOrgaos = TipoDocumentoModel::where('gabinete_id', $gabinete_id)->get();

            if ($tiposOrgaos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de documentos registrado.'];
            }

            return ['status' => 'success', 'data' => $tiposOrgaos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarTipoDocumento(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = TipoDocumentoModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado'];
            }

            return ['status' => 'success', 'data' => $tipo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarTipodeDocumento(string $id = ''): array {
        try {

            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = TipoDocumentoModel::find($id);

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado'];
            }

            $tipo->delete();
            return ['status' => 'success', 'message' => 'Tipo apagado.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Este tipo não pode ser apagado.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novoTipodeDocumento(array $dados): array {
        try {

            $tipo = TipoDocumentoModel::where('nome', $dados['nome'])->first();

            if ($tipo) {
                return ['status' => 'conflict', 'message' => 'Tipo já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $result = TipoDocumentoModel::create($dados);

            return ['status' => 'success', 'message' => 'Tipo criado com sucesso.', 'data' => $result->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarTipo(string $id, array $dados): array {
        try {

            $tipo = TipoDocumentoModel::find($id);

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado.'];
            }

            if (isset($dados['nome'])) {
                $tipoExistente = TipoDocumentoModel::where('nome', $dados['nome'])
                    ->where('id', '<>', $id)
                    ->first();

                if ($tipoExistente) {
                    return ['status' => 'conflict', 'message' => 'Tipo já cadastrado.'];
                }
            }

            $tipo->update($dados);

            return ['status' => 'success', 'message' => 'Tipo atualizado.', 'data' => $tipo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
