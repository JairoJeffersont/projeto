<?php

namespace App\Controllers;


use JairoJeffersont\EasyLogger\Logger;
use App\Models\TipoOrgaoModel;
use App\Models\OrgaoModel;

use Ramsey\Uuid\Uuid;

class OrgaoController {

    //OPERACOES COM TIPO
    public static function listarTiposOrgaos(string $gabinete_id = ''): array {
        try {

            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $tiposOrgaos = TipoOrgaoModel::where('gabinete_id', $gabinete_id)->get();

            if ($tiposOrgaos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de órgão registrado.'];
            }

            return ['status' => 'success', 'data' => $tiposOrgaos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarTipoOrgao(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = TipoOrgaoModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado'];
            }

            return ['status' => 'success', 'data' => $tipo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarTipodeOrgao(string $id = ''): array {
        try {

            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = TipoOrgaoModel::find($id);

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

    public static function novoTipodeOrgao(array $dados): array {
        try {

            $tipo = TipoOrgaoModel::where('nome', $dados['nome'])->first();

            if ($tipo) {
                return ['status' => 'conflict', 'message' => 'Tipo já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $result = TipoOrgaoModel::create($dados);

            return ['status' => 'success', 'message' => 'Tipo criado com sucesso.', 'data' => $result->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarTipo(string $id, array $dados): array {
        try {

            $tipo = TipoOrgaoModel::find($id);

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado.'];
            }

            if (isset($dados['nome'])) {
                $tipoExistente = TipoOrgaoModel::where('nome', $dados['nome'])
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


    //OPERACOES COM ORGAOS
    public static function listarOrgaos(string $gabinete_id = '', string $ordem = 'ASC', string $ordernarPor = 'nome', int $itens = 10, int $pagina = 1, string $estado = '', string $cidade = '', string $tipo = '', string $busca = ''): array {
        try {

            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $offset = ($pagina - 1) * $itens;

            $query = OrgaoModel::where('gabinete_id', $gabinete_id);

            if (!empty($estado)) {
                $query->where('estado', $estado);
            }

            if (!empty($cidade)) {
                $query->where('cidade', $cidade);
            }

            if (!empty($tipo)) {
                $query->where('tipo_id', $tipo);
            }

            if (!empty($busca)) {
                $query->where('nome', 'like', '%' . $busca . '%');
            }

            $total = $query->count();

            $orgaos = $query->orderBy($ordernarPor, $ordem)
                ->offset($offset)
                ->limit($itens)
                ->get();

            if ($orgaos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum órgão registrado.'];
            }

            $totalPaginas = ceil($total / $itens);

            return ['status' => 'success', 'total_registros' => $total, 'total_pagina' => $totalPaginas, 'data' => $orgaos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarOrgao(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID do órgão não enviado'];
            }

            $orgao = OrgaoModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$orgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado'];
            }

            return ['status' => 'success', 'data' => $orgao->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarOrgao(string $id = ''): array {
        try {

            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID do órgão não enviado'];
            }

            $orgao = OrgaoModel::find($id);

            if (!$orgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado'];
            }

            $orgao->delete();
            return ['status' => 'success', 'message' => 'Órgão apagado.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Este órgão não pode ser apagado.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novoOrgao(array $dados): array {
        try {

            $tipo = OrgaoModel::where('nome', $dados['nome'])->first();

            if ($tipo) {
                return ['status' => 'conflict', 'message' => 'Órgão já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $result = OrgaoModel::create($dados);

            return ['status' => 'success', 'message' => 'Órgão criado com sucesso.', 'data' => $result->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarOrgao(string $id, array $dados): array {
        try {

            $orgao = OrgaoModel::find($id);

            if (!$orgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado.'];
            }

            if (isset($dados['nome'])) {
                $orgaoExistente = OrgaoModel::where('nome', $dados['nome'])
                    ->where('id', '<>', $id)
                    ->first();

                if ($orgaoExistente) {
                    return ['status' => 'conflict', 'message' => 'Órgão já cadastrado.'];
                }
            }

            $orgao->update($dados);

            return ['status' => 'success', 'message' => 'Órgão atualizado.', 'data' => $orgao->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
