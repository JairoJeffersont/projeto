<?php

namespace App\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use App\Models\PessoaModel;
use App\Models\TipoPessoaModel;
use Ramsey\Uuid\Uuid;

/**
 * Class PessoaController
 *
 * Controlador responsável por gerenciar pessoas e tipos de pessoas.
 * Fornece funcionalidades para listar, buscar, criar, atualizar e apagar registros,
 * além de registrar erros no log quando ocorrem exceções.
 *
 * @package App\Controllers
 */
class PessoaController {

    /**
     * Lista todos os tipos de pessoas de um gabinete específico.
     *
     * @param string $gabinete ID do gabinete
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'empty', 'server_error'
     *     @type array|null 'data' (opcional) Lista de tipos de pessoas
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function listarTiposPessoas(string $gabinete): array {
        try {
            $tipos = TipoPessoaModel::where(function ($query) use ($gabinete) {
                $query->where('gabinete_id', '1')->orWhere('gabinete_id', $gabinete);
            })->get();
            
            if ($tipos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de pessoa encontrado.'];
            }

            return ['status' => 'success', 'data' => $tipos->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status' => 'server_error',
                'message' => 'Erro interno do servidor.',
                'error_id' => $errorId
            ];
        }
    }

    /**
     * Lista todas as pessoas de um gabinete específico.
     *
     * @param string $gabinete ID do gabinete
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'empty', 'server_error'
     *     @type array|null 'data' (opcional) Lista de pessoas
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function listarPessoas(string $gabinete): array {
        try {
            $pessoas = PessoaModel::where('gabinete_id', $gabinete)->get();

            if ($pessoas->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhuma pessoa encontrada.'];
            }

            return ['status' => 'success', 'data' => $pessoas->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status' => 'server_error',
                'message' => 'Erro interno do servidor.',
                'error_id' => $errorId
            ];
        }
    }

    /**
     * Busca uma pessoa por uma coluna específica.
     *
     * @param string $valor Valor a ser buscado
     * @param string $coluna Coluna onde será buscado (ex: 'id', 'nome', 'email')
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'server_error'
     *     @type array|null 'data' (opcional) Dados da pessoa encontrada
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function buscarPessoa(string $valor, string $coluna = 'id'): array {
        try {
            $pessoa = PessoaModel::where($coluna, $valor)->first();

            if (!$pessoa) {
                return ['status' => 'not_found', 'message' => 'Pessoa não encontrada.'];
            }

            return ['status' => 'success', 'data' => $pessoa->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status' => 'server_error',
                'message' => 'Erro interno do servidor.',
                'error_id' => $errorId
            ];
        }
    }

    /**
     * Cria uma nova pessoa.
     *
     * @param array $dados Dados da pessoa. Deve incluir pelo menos 'nome'.
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'duplicated', 'server_error'
     *     @type array|null 'data' (opcional) Dados da pessoa criada
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function novaPessoa(array $dados): array {
        try {
            $existe = PessoaModel::where('nome', $dados['nome'])->first();
            if ($existe) {
                return ['status' => 'duplicated', 'message' => 'Pessoa já cadastrada.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $pessoa = PessoaModel::create($dados);

            return [
                'status' => 'success',
                'message' => 'Pessoa criada com sucesso.',
                'data' => $pessoa->toArray()
            ];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status' => 'server_error',
                'message' => 'Erro interno do servidor.',
                'error_id' => $errorId
            ];
        }
    }

    /**
     * Atualiza uma pessoa existente.
     *
     * @param string $id ID da pessoa a ser atualizada
     * @param array $dados Dados a serem atualizados
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'duplicated', 'server_error'
     *     @type array|null 'data' (opcional) Dados da pessoa atualizada
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function atualizarPessoa(string $id, array $dados): array {
        try {
            $pessoa = PessoaModel::find($id);

            if (!$pessoa) {
                return ['status' => 'not_found', 'message' => 'Pessoa não encontrada.'];
            }

            if (isset($dados['nome'])) {
                $duplicado = PessoaModel::where('nome', $dados['nome'])
                    ->where('id', '<>', $id)
                    ->first();

                if ($duplicado) {
                    return ['status' => 'duplicated', 'message' => 'Já existe uma pessoa com este nome.'];
                }
            }

            $pessoa->update($dados);

            return [
                'status' => 'success',
                'message' => 'Pessoa atualizada com sucesso.',
                'data' => $pessoa->toArray()
            ];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status' => 'server_error',
                'message' => 'Erro interno do servidor.',
                'error_id' => $errorId
            ];
        }
    }

    /**
     * Apaga uma pessoa pelo ID.
     *
     * @param string $id ID da pessoa a ser apagada
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'not_permitted', 'server_error'
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function apagarPessoa(string $id): array {
        try {
            $pessoa = PessoaModel::find($id);

            if (!$pessoa) {
                return ['status' => 'not_found', 'message' => 'Pessoa não encontrada.'];
            }

            $pessoa->delete();

            return ['status' => 'success', 'message' => 'Pessoa apagada com sucesso.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Não é possível apagar esta pessoa.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return [
                'status' => 'server_error',
                'message' => 'Erro interno do servidor.',
                'error_id' => $errorId
            ];
        }
    }
}
