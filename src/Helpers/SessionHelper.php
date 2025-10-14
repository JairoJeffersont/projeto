<?php

namespace App\Helpers;

class SessionHelper {

    public static function iniciarSessao(array $usuario): bool {
        $expiracaoMinutos = 1440; // 1 dia

        // Evita warnings caso headers já tenham sido enviados
        if (headers_sent()) {
            return false;
        }

        // Inicia a sessão se ainda não estiver ativa
        if (session_status() === PHP_SESSION_NONE) {
            if (!session_start([
                'cookie_httponly' => true,
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'cookie_samesite' => 'Strict'
            ])) {
                return false; // falha ao iniciar a sessão
            }
        }

        // Salva o ID antigo da sessão
        $idAntigo = session_id();

        // Regenera ID da sessão para prevenir fixação
        if (!session_regenerate_id(true)) {
            return false; // falha ao regenerar ID
        }

        // Verifica se o ID realmente mudou
        $idNovo = session_id();
        if ($idNovo === $idAntigo) {
            return false; // regeneração falhou
        }

        // Armazena dados do usuário na sessão
        $_SESSION['usuario'] = $usuario;

        // Define tempo de expiração da sessão
        $_SESSION['ultimo_acesso'] = time();
        $_SESSION['expiracao'] = $expiracaoMinutos * 60;

        // Verifica se os dados foram salvos corretamente
        return isset($_SESSION['usuario']) && session_status() === PHP_SESSION_ACTIVE;
    }


    /**
     * Deve ser chamado em cada página que requer sessão ativa
     */
    public static function validarSessao(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            return false; // não logado
        }

        // Verifica expiração
        if (isset($_SESSION['ultimo_acesso'], $_SESSION['expiracao'])) {
            if (time() - $_SESSION['ultimo_acesso'] > $_SESSION['expiracao']) {
                self::destruirSessao();
                return false; // sessão expirada
            }
            // Atualiza tempo de último acesso
            $_SESSION['ultimo_acesso'] = time();
        }

        return true; // sessão válida
    }

    public static function destruirSessao(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
        return true;
    }
}
