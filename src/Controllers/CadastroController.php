<?php

namespace App\Controllers;

use App\Models\GabineteModel;
use App\Models\UsuarioModel;
use App\Helpers\Slugfy;
use Ramsey\Uuid\Uuid;
use JairoJeffersont\EasyLogger\Logger;

class CadastroController {

    public static function novoCadastro(array $dadosGabinete, array $dadosUsuario): array {
        try {
            // Verifica se o gabinete já existe
            if (GabineteModel::where('nome', $dadosGabinete['nome'])->exists()) {
                return ['status' => 'duplicated', 'type' => 'gabinete'];
            }

            // Cria o gabinete
            $dadosGabinete['id'] = Uuid::uuid4()->toString();
            $dadosGabinete['nome_slug'] = Slugfy::slug($dadosGabinete['nome']);

            $resultGabinete = GabineteModel::create($dadosGabinete);

            $idGabinete = $resultGabinete->id;

            // Verifica se o usuário já existe pelo email ou telefone
            if (UsuarioModel::where('email', $dadosUsuario['email'])
                ->orWhere('telefone', $dadosUsuario['telefone'])
                ->exists()
            ) {
                // Remove o gabinete criado por rollback
                GabineteModel::where('id', $idGabinete)->delete();
                return ['status' => 'duplicated', 'type' => 'usuario'];
            }


            // Cria o usuário
            $dadosUsuario['id'] = Uuid::uuid4()->toString();
            $dadosUsuario['gabinete_id'] = $idGabinete;
            $dadosUsuario['senha'] = password_hash($dadosUsuario['senha'], PASSWORD_DEFAULT);
            $dadosUsuario['tipo_usuario_id'] = '1';
            $dadosUsuario['telefone'] = preg_replace('/\D/', '', $dadosUsuario['telefone']);

            UsuarioModel::create($dadosUsuario);

            return ['status' => 'success'];
        } catch (\Exception $e) {
            $errorId = Logger::newLog(LOG_FOLDER, 'error', $e->getMessage(), 'ERROR');
            return ['status' => 'server_error', 'message' => 'Erro interno do servidor.', 'error_id' => $errorId];
        }
    }
}
