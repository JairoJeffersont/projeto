<?php

namespace App\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use App\Models\OrgaoModel;
use App\Models\TipoOrgaoModel;
use Ramsey\Uuid\Uuid;

/**
 * Class OrgaoController
 *
 * Controlador responsável por gerenciar órgãos e tipos de órgãos.
 * Fornece funcionalidades para listar, buscar, criar, atualizar e apagar registros,
 * além de registrar erros no log quando ocorrem exceções.
 *
 * @package App\Controllers
 */
class OrgaoController {

    /**
     * Lista todos os tipos de órgãos associados a um gabinete específico ou ao gabinete padrão (1).
     *
     * @param string $gabinete ID do gabinete
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'empty', 'server_error'
     *     @type array|null 'data' (opcional) Lista de tipos de órgãos
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function listarTiposOrgaos(string $gabinete): array {
        try {
            $tipos = TipoOrgaoModel::where(function ($query) use ($gabinete) {
                $query->where('gabinete_id', '1')->orWhere('gabinete_id', $gabinete);
            })->get();

            if ($tipos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de órgão encontrado.'];
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
     * Lista todos os órgãos de um gabinete específico, exceto o órgão com ID 1.
     *
     * @param string $gabinete ID do gabinete
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'empty', 'server_error'
     *     @type array|null 'data' (opcional) Lista de órgãos
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function listarOrgaos(string $gabinete): array {
        try {
            $orgaos = OrgaoModel::where(function ($query) use ($gabinete) {
                $query->where('gabinete_id', '1')->orWhere('gabinete_id', $gabinete);
            })->get();

            if ($orgaos->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum órgão encontrado.'];
            }

            return ['status' => 'success', 'data' => $orgaos->toArray()];
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
     * Busca um órgão baseado em um valor e coluna específica, exceto o órgão com ID 1.
     *
     * @param string $valor Valor a ser buscado
     * @param string $coluna Coluna onde o valor será buscado. Valores permitidos: 'id', 'nome', 'email', etc.
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'server_error'
     *     @type array|null 'data' (opcional) Dados do órgão encontrado
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function buscarOrgao(string $valor, string $coluna = 'id'): array {
        try {
            $orgao = OrgaoModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$orgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado.'];
            }

            return ['status' => 'success', 'data' => $orgao->toArray()];
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
     * Cria um novo órgão.
     *
     * @param array $dados Dados do órgão. Deve incluir pelo menos a chave 'nome'.
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'duplicated', 'server_error'
     *     @type array|null 'data' (opcional) Dados do órgão criado
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function novoOrgao(array $dados): array {
        try {

            $existe = OrgaoModel::where('nome', $dados['nome'])->first();
            if ($existe) {
                return ['status' => 'duplicated', 'message' => 'Órgão já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $orgao = OrgaoModel::create($dados);

            return [
                'status' => 'success',
                'message' => 'Órgão criado com sucesso.',
                'data' => $orgao->toArray()
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
     * Atualiza um órgão existente.
     *
     * @param string $id ID do órgão a ser atualizado
     * @param array $dados Dados a serem atualizados. Se incluir 'nome', será verificada duplicidade
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'duplicated', 'server_error'
     *     @type array|null 'data' (opcional) Dados do órgão atualizado
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function atualizarOrgao(string $id, array $dados): array {
        try {
            $orgao = OrgaoModel::find($id);

            if (!$orgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado.'];
            }

            if (isset($dados['nome'])) {
                $duplicado = OrgaoModel::where('nome', $dados['nome'])
                    ->where('id', '<>', $id)
                    ->first();

                if ($duplicado) {
                    return ['status' => 'duplicated', 'message' => 'Já existe um órgão com este e-mail.'];
                }
            }

            $orgao->update($dados);

            return [
                'status' => 'success',
                'message' => 'Órgão atualizado com sucesso.',
                'data' => $orgao->toArray()
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
     * Apaga um órgão pelo ID, exceto o órgão com ID 1.
     *
     * @param string $id ID do órgão a ser apagado
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'not_permitted', 'server_error'
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function apagarOrgao(string $id): array {
        try {
            if ($id === '1') {
                return ['status' => 'not_permitted', 'message' => 'Ação não permitida.'];
            }

            $orgao = OrgaoModel::find($id);

            if (!$orgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado.'];
            }

            $orgao->delete();

            return ['status' => 'success', 'message' => 'Órgão apagado com sucesso.'];
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Não é possível apagar este órgão.'];
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
