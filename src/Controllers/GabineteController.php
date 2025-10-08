<?php

namespace App\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use App\Models\GabineteModel;
use App\Models\TipoGabineteModel;
use Ramsey\Uuid\Uuid;

/**
 * Class GabineteController
 *
 * Controlador responsável por gerenciar gabinetes e tipos de gabinetes.
 * Fornece funcionalidades para listar, buscar, criar, atualizar e apagar registros,
 * além de registrar erros no log quando ocorrem exceções.
 *
 * @package App\Controllers
 */

class GabineteController {

    /**
     * Retorna todos os tipos de gabinetes, exceto o tipo com ID 1.
     *
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'empty', 'server_error'
     *     @type array|null 'data' (opcional) Lista de tipos de gabinetes
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
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

    /**
     * Retorna todos os gabinetes, exceto o gabinete com ID 1.
     *
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'empty', 'server_error'
     *     @type array|null 'data' (opcional) Lista de gabinetes
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
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

    /**
     * Busca um gabinete baseado em um valor e coluna específica.
     *
     * @param string $valor Valor a ser buscado
     * @param string $coluna Coluna onde o valor será buscado. Valores permitidos: 'id', 'email', 'nome'
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'bad_request', 'server_error'
     *     @type array|null 'data' (opcional) Dados do gabinete encontrado
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function buscarGabinete(string $valor, string $coluna = 'id'): array {

        $colunasPermitidas = ['id', 'email', 'nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'message' => 'Coluna inválida. Permitidas: ' . implode(', ', $colunasPermitidas) . '.'];
        }

        try {
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

    /**
     * Apaga um gabinete pelo ID, exceto o gabinete com ID 1.
     *
     * @param string $id ID do gabinete a ser apagado
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'not_permitted', 'server_error'
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function apagarGabinete(string $id): array {
        try {

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

    /**
     * Cria um novo gabinete.
     *
     * @param array $dados Array contendo os dados do gabinete. Deve incluir pelo menos a chave 'email'
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'duplicated', 'server_error'
     *     @type array|null 'data' (opcional) Dados do gabinete criado
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function novoGabinete(array $dados): array {
        try {

            $gabinete = GabineteModel::where('email', $dados['email'])->first();

            if ($gabinete) {
                return ['status' => 'duplicated', 'message' => 'Gabinete já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();

            $gabinete = GabineteModel::create($dados);

            return ['status' => 'success', 'message' => 'Gabinete criado.', 'data' => $gabinete->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    /**
     * Atualiza um gabinete existente.
     *
     * @param string $id ID do gabinete a ser atualizado
     * @param array $dados Array contendo os dados a serem atualizados. Se incluir 'email', será verificada duplicidade
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'duplicated', 'server_error'
     *     @type array|null 'data' (opcional) Dados do gabinete atualizado
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function atualizarGabinete(string $id, array $dados): array {
        try {

            $gabinete = GabineteModel::find($id);

            if (!$gabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado.'];
            }

            if (isset($dados['email'])) {

                $emailExistente = GabineteModel::where('email', $dados['email'])->where('id', '<>', $id)->first();

                if ($emailExistente) {
                    return ['status' => 'duplicated', 'message' => 'Gabinete já cadastrado.'];
                }
            }

            $gabinete->update($dados);

            return ['status' => 'success', 'message' => 'Gabinete atualizado.', 'data' => $gabinete->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
