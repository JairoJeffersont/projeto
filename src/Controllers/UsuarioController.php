<?php

namespace App\Controllers;

use JairoJeffersont\EasyLogger\Logger;
use App\Models\TipoUsuarioModel;
use App\Models\UsuarioModel;
use App\Models\GabineteModel;
use Ramsey\Uuid\Uuid;

/**
 * Class UsuarioController
 *
 * Controlador responsável por gerenciar usuários e tipos de usuários.
 * Fornece funcionalidades para:
 * - Listar tipos de usuários
 * - Listar usuários de um gabinete específico
 * - Buscar, criar, atualizar e apagar usuários
 *
 * Os métodos utilizam tratamento de exceções e registram erros no log quando ocorrem falhas.
 *
 * @package App\Controllers
 */

class UsuarioController {
    
    /**
     * Retorna todos os tipos de usuários.
     *
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'empty', 'server_error'
     *     @type array|null 'data' (opcional) Lista de tipos de usuários
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function listarTiposUsuarios(): array {
        try {
            $tiposUsuarios = TipoUsuarioModel::get();

            if ($tiposUsuarios->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de usuário registrado.'];
            }

            return ['status' => 'success', 'data' => $tiposUsuarios->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    /**
     * Retorna todos os usuários de um gabinete específico.
     *
     * @param string $gabineteId ID do gabinete
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'empty', 'not_found', 'bad_request', 'server_error'
     *     @type array|null 'data' (opcional) Lista de usuários
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function listarUsuarios($gabineteId = ''): array {
        try {

            if (empty($gabineteId)) {
                return ['status' => 'bad_request', 'message' => 'ID do gabinete não enviado'];
            }

            $gabinete = GabineteModel::where('id', $gabineteId)->where('id', '<>', 1)->first();

            if (!$gabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            $usuarios = UsuarioModel::where('id', '!=', 1)->where('gabinete_id', $gabinete->id)->get();

            if ($usuarios->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Nenhum usuário registrado.'];
            }

            return ['status' => 'success', 'data' => $usuarios->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    /**
     * Busca um usuário baseado em um valor e coluna específica.
     *
     * @param string $valor Valor a ser buscado
     * @param string $coluna Coluna onde o valor será buscado. Ex.: 'id', 'email', 'nome'
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'server_error'
     *     @type array|null 'data' (opcional) Dados do usuário encontrado
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function buscarUsuario(string $valor, string $coluna = 'id'): array {

        try {
            $usuario = UsuarioModel::where($coluna, $valor)->where('id', '<>', '1')->first();

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            return ['status' => 'success', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    /**
     * Apaga um usuário pelo ID, exceto o usuário com ID 1.
     *
     * @param string $id ID do usuário a ser apagado
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'not_permitted', 'server_error'
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function apagarUsuario(string $id): array {
        try {

            if ($id === '1') {
                return ['status' => 'not_permitted', 'message' => 'Este usuário não pode ser apagado.'];
            }

            $usuario = UsuarioModel::find($id);

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $usuario->delete();
            return ['status' => 'success', 'message' => 'Usuário apagado.'];
        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'not_permitted', 'message' => 'Este usuário não pode ser apagado.'];
            }

            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    /**
     * Cria um novo usuário.
     *
     * @param array $dados Array contendo os dados do usuário. Deve incluir pelo menos 'email' e 'senha'
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'duplicated', 'server_error'
     *     @type array|null 'data' (opcional) Dados do usuário criado
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function novoUsuario(array $dados): array {
        try {

            $usuario = UsuarioModel::where('email', $dados['email'])->first();

            if ($usuario) {
                return ['status' => 'duplicated', 'message' => 'Usuário já cadastrado.'];
            }

            $dados['id'] = Uuid::uuid4()->toString();
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);

            $usuario = UsuarioModel::create($dados);

            return ['status' => 'success', 'message' => 'Gabinete criado.', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    /**
     * Atualiza um usuário existente.
     *
     * @param string $id ID do usuário a ser atualizado
     * @param array $dados Array contendo os dados a serem atualizados. Se incluir 'email', será verificada duplicidade
     * @return array {
     *     @type string 'status' Status da operação: 'success', 'not_found', 'duplicated', 'server_error'
     *     @type array|null 'data' (opcional) Dados do usuário atualizado
     *     @type string|null 'message' (opcional) Mensagem descritiva
     *     @type string|null 'error_id' (opcional) ID do log em caso de erro
     * }
     */
    public static function atualizarUsuario(string $id, array $dados): array {
        try {
            $usuario = UsuarioModel::find($id);

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
            }

            if (isset($dados['email'])) {
                $emailExistente = UsuarioModel::where('email', $dados['email'])->where('id', '<>', $id)->first();

                if ($emailExistente) {
                    return ['status' => 'duplicated', 'message' => 'Email já cadastrado.'];
                }
            }

            $usuario->update($dados);

            return ['status' => 'success', 'data' => $usuario->toArray()];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
