<?php

namespace App\Controllers;

use App\Models\SituacaoEmendaModel;
use App\Models\TipoEmendaModel;
use App\Models\AreaEmendaModel;
use App\Models\EmendaModel;
use JairoJeffersont\EasyLogger\Logger;


use Ramsey\Uuid\Uuid;

class EmendaController {
    //CRUD TIPOS
    public static function listarTiposdeEmendas(string $gabinete_id = ''): array {
        try {

            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $tiposPessoas = TipoEmendaModel::where('gabinete_id', $gabinete_id)
                ->orWhere('gabinete_id', '1')
                ->get();


            if ($tiposPessoas->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de emenda registrado.'];
            }

            return ['status' => 'success', 'data' => $tiposPessoas->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarTipodeEmenda(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = TipoEmendaModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado'];
            }

            return ['status' => 'success', 'data' => $tipo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarTipodeEmenda(string $id = ''): array {
        try {

            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = TipoEmendaModel::find($id);

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

    public static function novoTipodeEmenda(array $dados): array {
        try {

            $tipo = TipoEmendaModel::where('nome', $dados['nome'])->first();

            if ($tipo) {
                return ['status' => 'conflict', 'message' => 'Tipo já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $result = TipoEmendaModel::create($dados);

            return ['status' => 'success', 'message' => 'Tipo criado com sucesso.', 'data' => $result->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarTipo(string $id, array $dados): array {
        try {

            $tipo = TipoEmendaModel::find($id);

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado.'];
            }

            if (isset($dados['nome'])) {
                $tipoExistente = TipoEmendaModel::where('nome', $dados['nome'])
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

    //CRUD SITUACOES
    public static function listarSituacoesdeEmendas(string $gabinete_id = ''): array {
        try {

            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $tiposPessoas = SituacaoEmendaModel::where('gabinete_id', $gabinete_id)->orWhere('gabinete_id', '1')->get();

            if ($tiposPessoas->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhuma situacao de emenda registrado.'];
            }

            return ['status' => 'success', 'data' => $tiposPessoas->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarSituacaodeEmenda(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = SituacaoEmendaModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Situação não encontrada'];
            }

            return ['status' => 'success', 'data' => $tipo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarSituacaodeEmenda(string $id = ''): array {
        try {

            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = SituacaoEmendaModel::find($id);

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Situação não encontrada'];
            }

            $tipo->delete();
            return ['status' => 'success', 'message' => 'Situaç˜zo apagado.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Esta situação não pode ser apagado.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novaSituacaodeEmenda(array $dados): array {
        try {

            $tipo = SituacaoEmendaModel::where('nome', $dados['nome'])->first();

            if ($tipo) {
                return ['status' => 'conflict', 'message' => 'Situacao já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $result = SituacaoEmendaModel::create($dados);

            return ['status' => 'success', 'message' => 'Situacao criada com sucesso.', 'data' => $result->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarSituacaodeEmenda(string $id, array $dados): array {
        try {

            $tipo = SituacaoEmendaModel::find($id);

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Situação não encontrada.'];
            }

            if (isset($dados['nome'])) {
                $tipoExistente = TipoEmendaModel::where('nome', $dados['nome'])
                    ->where('id', '<>', $id)
                    ->first();

                if ($tipoExistente) {
                    return ['status' => 'conflict', 'message' => 'Situação já cadastrada.'];
                }
            }

            $tipo->update($dados);

            return ['status' => 'success', 'message' => 'Situação atualizado.', 'data' => $tipo->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    // CRUD ÁREAS DE EMENDAS
    public static function listarAreasDeEmendas(string $gabinete_id = ''): array {
        try {
            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $areas = AreaEmendaModel::where('gabinete_id', $gabinete_id)->orWhere('gabinete_id', '1')->get();

            if ($areas->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhuma área de emenda registrada.'];
            }

            return ['status' => 'success', 'data' => $areas->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarAreaDeEmenda(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'Valor de busca não informado'];
            }

            $area = AreaEmendaModel::where($coluna, $valor)->first();
            

            if (!$area) {
                return ['status' => 'not_found', 'message' => 'Área de emenda não encontrada'];
            }

            return ['status' => 'success', 'data' => $area->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarAreaDeEmenda(string $id = ''): array {
        try {
            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID não informado'];
            }

            $area = AreaEmendaModel::find($id);

            if (!$area) {
                return ['status' => 'not_found', 'message' => 'Área de emenda não encontrada'];
            }

            $area->delete();
            return ['status' => 'success', 'message' => 'Área de emenda apagada com sucesso.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Esta área não pode ser apagada pois está vinculada a outros registros.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novaAreaDeEmenda(array $dados): array {
        try {
            $areaExistente = AreaEmendaModel::where('nome', $dados['nome'])
                ->where('gabinete_id', $dados['gabinete_id'])
                ->first();

            if ($areaExistente) {
                return ['status' => 'conflict', 'message' => 'Área de emenda já cadastrada para este gabinete.'];
            }

            $dados['id'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $novaArea = AreaEmendaModel::create($dados);

            return ['status' => 'success', 'message' => 'Área de emenda criada com sucesso.', 'data' => $novaArea->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarAreaDeEmenda(string $id, array $dados): array {
        try {
            $area = AreaEmendaModel::find($id);

            if (!$area) {
                return ['status' => 'not_found', 'message' => 'Área de emenda não encontrada.'];
            }

            if (isset($dados['nome'])) {
                $areaExistente = AreaEmendaModel::where('nome', $dados['nome'])
                    ->where('gabinete_id', $area->gabinete_id)
                    ->where('id', '<>', $id)
                    ->first();

                if ($areaExistente) {
                    return ['status' => 'conflict', 'message' => 'Já existe uma área de emenda com esse nome neste gabinete.'];
                }
            }

            $area->update($dados);

            return ['status' => 'success', 'message' => 'Área de emenda atualizada com sucesso.', 'data' => $area->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    //CRUD EMENDAS
    public static function listarEmendas(string $gabinete_id = '', string $ordem = 'ASC', string $ordenarPor = 'nome', int $itens = 10, int $pagina = 1, string $ano = '',  string $estado = '', string $cidade = '', string $tipo = '', string $ares = '', string $situacao = '',  string $busca = ''): array {
        try {

            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $query = EmendaModel::where('gabinete_id', $gabinete_id);

            // Filtros opcionais
            if (!empty($ano)) {
                $query->where('ano', $ano);
            }

            if (!empty($estado)) {
                $query->where('estado', $estado);
            }

            if (!empty($cidade)) {
                $query->where('cidade', $cidade);
            }

            if (!empty($tipo)) {
                $query->where('tipo_id', $tipo);
            }

            if (!empty($area)) {
                $query->where('area_id', $area);
            }

            if (!empty($situacao)) {
                $query->where('situacao_id', $situacao);
            }

            if (!empty($busca)) {
                $query->where(function ($q) use ($busca) {
                    $q->where('descricao', 'like', "%{$busca}%");
                });
            }

            // Ordenação
            $query->orderBy($ordenarPor, $ordem);

            // Paginação manual (caso não use paginate do Laravel)
            $offset = ($pagina - 1) * $itens;
            $emendas = $query->skip($offset)->take($itens)->get();

            if ($emendas->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhuma emenda encontrada.'];
            }

            // Total para controle de páginas
            $total = $query->count();

            return ['status' => 'success', 'total' => $total, 'total_pagina' => ceil($total / $itens), 'data' => $emendas->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarEmenda(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID da emenda não enviado'];
            }

            $emenda = EmendaModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$emenda) {
                return ['status' => 'not_found', 'message' => 'Emenda não encontrada'];
            }

            return ['status' => 'success', 'data' => $emenda->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarEmenda(string $id = ''): array {
        try {
            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID não informado'];
            }

            $emenda = EmendaModel::find($id);

            if (!$emenda) {
                return ['status' => 'not_found', 'message' => 'Emenda não encontrada'];
            }

            $emenda->delete();
            return ['status' => 'success', 'message' => 'Emenda apagada com sucesso.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Esta emenda não pode ser apagada pois está vinculada a outros registros.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novaEmenda(array $dados): array {
        try {
            $dados['id'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $novaEmenda = EmendaModel::create($dados);
            return ['status' => 'success', 'message' => 'Emenda inserida com sucesso.', 'data' => $novaEmenda->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarEmenda(string $id, array $dados): array {
        try {
            $emenda = AreaEmendaModel::find($id);

            if (!$emenda) {
                return ['status' => 'not_found', 'message' => 'Emenda não encontrada.'];
            }

            $emenda->update($dados);

            return ['status' => 'success', 'message' => 'Emenda atualizada com sucesso.', 'data' => $emenda->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
