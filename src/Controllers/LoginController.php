<?php

namespace App\Controllers;

use App\Helpers\EmailService;
use App\Helpers\SessionHelper;
use App\Helpers\Slugfy;
use App\Models\UsuarioModel;
use JairoJeffersont\EasyLogger\Logger;
use Ramsey\Uuid\Uuid;

class LoginController {

    public static function logar(array $dados): array {

        try {
            if (filter_var($dados['login'], FILTER_VALIDATE_EMAIL)) {
                $usuario = UsuarioModel::where('email', $dados['login'])->first();
            } else {
                $usuario = UsuarioModel::where('telefone', preg_replace('/\D/', '', $dados['login']))->first();
            }

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            if (!$usuario->ativo) {
                return ['status' => 'deactived', 'message' => 'Usuário desativado'];
            }

            if (!password_verify($dados['senha'], $usuario->senha)) {
                return ['status' => 'wrong_password', 'message' => 'Senha incorreta'];
            }

            if (SessionHelper::iniciarSessao($usuario->toArray())) {
                return ['status' => 'success', 'message' => 'Login feito com sucesso!'];
            } else {
                return ['status' => 'login_failed', 'message' => 'Falha ao iniciar sessão.'];
            }
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function recuperarSenha(string $email): array {

        try {

            $usuario = UsuarioModel::where('email', $email)->first();

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $token = Uuid::uuid4()->toString();

            $dados = ['token' => $token];
            $usuario->update($dados);

            //TERMINAR ISSO AQUI
            $emailService = new EmailService();
            $emailService->sendMail($usuario->email, 'EMAIL DE RECUPERAÇÃO', $token);

            return ['status' => 'success', 'message' => 'Email de recuperação enviado com sucesso!'];
        } catch (\RuntimeException $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'email_error', 'message' => 'Falha ao enviar email.', 'error_id' => $errorId];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }

    public static function novaSenha(string $token, string $senha): array {

        try {

            $usuario = UsuarioModel::where('token', $token)->first();

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Token inválido.'];
            }

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $dados = ['token' => null, 'senha' => $senhaHash];
            $usuario->update($dados);

            return ['status' => 'success', 'message' => 'Senha atualizada com sucesso.'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
