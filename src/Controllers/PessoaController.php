<?php

namespace App\Controllers;


use JairoJeffersont\EasyLogger\Logger;
use JairoJeffersont\FileUploader;
use App\Models\TipoPessoaModel;
use App\Models\ProfissaoModel;
use App\Models\PessoaModel;

use Ramsey\Uuid\Uuid;

class PessoaController {

    //CRUD TIPOS
    public static function listarTiposPessoas(string $gabinete_id = ''): array {
        try {

            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $tiposPessoas = TipoPessoaModel::where('gabinete_id', $gabinete_id)->get();

            if ($tiposPessoas->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de pessoa registrado.'];
            }

            return ['status' => 'success', 'data' => $tiposPessoas->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarTipoPessoa(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $tipo = TipoPessoaModel::where($coluna, $valor)->where('id', '<>', '1')->first();

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

            $tipo = TipoPessoaModel::find($id);

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

    public static function novoTipodePessoa(array $dados): array {
        try {

            $tipo = TipoPessoaModel::where('nome', $dados['nome'])->first();

            if ($tipo) {
                return ['status' => 'conflict', 'message' => 'Tipo já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $result = TipoPessoaModel::create($dados);

            return ['status' => 'success', 'message' => 'Tipo criado com sucesso.', 'data' => $result->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarTipo(string $id, array $dados): array {
        try {

            $tipo = TipoPessoaModel::find($id);

            if (!$tipo) {
                return ['status' => 'not_found', 'message' => 'Tipo não encontrado.'];
            }

            if (isset($dados['nome'])) {
                $tipoExistente = TipoPessoaModel::where('nome', $dados['nome'])
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

    //CRUD PROFISSOES
    public static function listarProfissoes(string $gabinete_id = ''): array {
        try {

            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $profissoes = ProfissaoModel::where('gabinete_id', $gabinete_id)->get();

            if ($profissoes->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhuma profissão registrada.'];
            }

            return ['status' => 'success', 'data' => $profissoes->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function buscarProfissao(string $valor = '', string $coluna = 'id'): array {
        try {
            if (empty($valor)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $profissao = ProfissaoModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$profissao) {
                return ['status' => 'not_found', 'message' => 'Profissão não encontrada'];
            }

            return ['status' => 'success', 'data' => $profissao->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function apagarProfissao(string $id = ''): array {
        try {

            if (empty($id)) {
                return ['status' => 'bad_request', 'message' => 'ID do tipo não enviado'];
            }

            $profissao = ProfissaoModel::find($id);

            if (!$profissao) {
                return ['status' => 'not_found', 'message' => 'Profissão não encontrada'];
            }

            $profissao->delete();
            return ['status' => 'success', 'message' => 'Profissão apagada.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Essa profissão não pode ser apagada.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novaProfissao(array $dados): array {
        try {

            $profissao = ProfissaoModel::where('nome', $dados['nome'])->first();

            if ($profissao) {
                return ['status' => 'conflict', 'message' => 'Profissão já cadastrada.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $result = ProfissaoModel::create($dados);

            return ['status' => 'success', 'message' => 'Profissão criada com sucesso.', 'data' => $result->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function atualizarProfissao(string $id, array $dados): array {
        try {

            $profissao = ProfissaoModel::find($id);

            if (!$profissao) {
                return ['status' => 'not_found', 'message' => 'Profissão não encontrada.'];
            }

            if (isset($dados['nome'])) {
                $profissaoExistente = ProfissaoModel::where('nome', $dados['nome'])
                    ->where('id', '<>', $id)
                    ->first();

                if ($profissaoExistente) {
                    return ['status' => 'conflict', 'message' => 'Profissão já cadastrada.'];
                }
            }

            $profissao->update($dados);

            return ['status' => 'success', 'message' => 'Profissão atualizada.', 'data' => $profissao->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function listarPessoas(string $gabinete_id = '', string $ordem = 'ASC', string $ordernarPor = 'nome', int $itens = 10, int $pagina = 1, string $estado = '', string $cidade = '', string $tipo = '', string $orgao = '',  string $busca = ''): array {
        try {

            if (empty($gabinete_id)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $offset = ($pagina - 1) * $itens;

            $query = PessoaModel::where('gabinete_id', $gabinete_id);

            if (!empty($estado)) {
                $query->where('estado', $estado);
            }

            if (!empty($cidade)) {
                $query->where('cidade', $cidade);
            }

            if (!empty($tipo)) {
                $query->where('tipo_id', $tipo);
            }

            if (!empty($orgao)) {
                $query->where('orgao_id', $orgao);
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
                return ['status' => 'empty', 'message' => 'Nenhuma pessoa registrada.'];
            }

            $totalPaginas = ceil($total / $itens);

            return ['status' => 'success', 'total_registros' => $total, 'total_pagina' => $totalPaginas, 'data' => $orgaos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novaPessoa(array $dados): array {
        try {          

            $tipo = PessoaModel::where('nome', $dados['nome'])->first();

            if ($tipo) {
                return ['status' => 'conflict', 'message' => 'Pessoa já cadastrada.'];
            }

            if (isset($dados['foto'])) {
                $result = FileUploader::uploadFile('arquivos/pessoas', $dados['foto'], ['image/jpeg', 'image/png'], 5);
                if ($result['status'] == 'success') {
                    $dados['foto'] = $result['file_path'];
                } else {
                    return $result;
                }
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $result = PessoaModel::create($dados);

            return ['status' => 'success', 'message' => 'Pessoa criada com sucesso.', 'data' => $result->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
