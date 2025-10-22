<?php

namespace App\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\FileUploader;
use App\Models\TipoDocumentoModel;
use App\Models\DocumentoModel;

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


    //OPERACOES COM DOCUMENTOS
    public static function listarDocumentos(string $gabinete_id = '', ?string $ano = null, ?string $tipo = null, ?string $busca = null, ?string $orgao = null): array {
        try {
            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $query = DocumentoModel::where('gabinete_id', $gabinete_id);

            if (!empty($busca)) {
                $query->where('titulo', 'LIKE', '%' . $busca . '%');
            } else {
                if (!empty($ano)) {
                    $query->where('ano', $ano);
                }

                if (!empty($tipo)) {
                    $query->where('tipo_id', $tipo);
                }

                if (!empty($orgao)) {
                    $query->where('orgao_id', $tipo);
                }
            }

            $documentos = $query->get();

            if ($documentos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum documento encontrado.'];
            }

            return ['status' => 'success', 'data' => $documentos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarDocumento(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = DocumentoModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Documento não encontrado'];
            }

            return ['status' => 'success', 'data' => $tipo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarDocumento(string $id = ''): array {
        try {

            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $documento = DocumentoModel::find($id);

            if (!$documento) {
                return ['status' => 'not_found', 'message' => 'Documento não encontrado'];
            }

            if (file_exists($documento->arquivo)) {
                FileUploader::deleteFile($documento->arquivo);
            }

            $documento->delete();
            return ['status' => 'success', 'message' => 'Documento apagado.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Este documento não pode ser apagado.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novoDocumento(array $dados): array {
        try {

            $tipo = DocumentoModel::where('titulo', $dados['titulo'])->first();

            if ($tipo) {
                return ['status' => 'conflict', 'message' => 'Documento já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();

            $tiposPermitidos = ['image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/pdf'];

            $result = FileUploader::uploadFile('arquivos/documentos/', $dados['arquivo'], $tiposPermitidos, 20);
            if ($result['status'] == 'success') {
                $dados['arquivo'] = $result['file_path'];
            } else {
                return $result;
            }

            $result = DocumentoModel::create($dados);

            return ['status' => 'success', 'message' => 'Documento criado com sucesso.', 'data' => $result->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarDocumento(string $id, array $dados): array {
        try {


            $documento = DocumentoModel::find($id);

            if (!$documento) {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado.'];
            }

            if (isset($dados['nome'])) {
                $tipoExistente = DocumentoModel::where('titulo', $dados['titulo'])
                    ->where('id', '<>', $id)
                    ->first();

                if ($tipoExistente) {
                    return ['status' => 'conflict', 'message' => 'Documento já cadastrado.'];
                }
            }

            $tiposPermitidos = ['image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/pdf'];

            if (!empty($dados['arquivo'])) {
                $result = FileUploader::uploadFile('arquivos/documentos/', $dados['arquivo'], $tiposPermitidos, 20);
                if ($result['status'] == 'success') {
                    if (file_exists($documento->arquivo)) {
                        FileUploader::deleteFile($documento->arquivo);
                    }
                    $dados['arquivo'] = $result['file_path'];
                } else {
                    return $result;
                }
            } else {
                $dados['arquivo'] = $documento->arquivo;
            }

            $documento->update($dados);

            return ['status' => 'success', 'message' => 'Tipo atualizado.', 'data' => $documento->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
