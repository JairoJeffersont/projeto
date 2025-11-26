<?php

namespace App\Controllers;

use App\Helpers\GetData;

class ProposicaoController {

    public static function buscarProposicoesCD(String $deputado, String $ano, String $tipo = 'PL', String $itens = '10', String $pagina = '1'): array {

        $buscaProposicoes = GetData::getJson('https://dadosabertos.camara.leg.br/api/v2/proposicoes?siglaTipo=' . $tipo . '&autor=' . urlencode($deputado) . '&ordem=ASC&itens=' . $itens . '&pagina=' . $pagina . '&ano=' . $ano . '&ordenarPor=ano');

        $payload = [];

        if ($buscaProposicoes['status'] == 'success' && !empty($buscaProposicoes['data']['dados'])) {
            $payload = [
                'status' => 'success',
                'data' => $buscaProposicoes['data']['dados']
            ];
        } else if ($buscaProposicoes['status'] == 'success' && empty($buscaProposicoes['data']['dados'])) {
            $payload = [
                'status' => 'empty',
                'message' => 'Nenhuma proposição encontrada'
            ];
        } else {
            $payload = [
                'status' => 'server_error',
                'message' => 'Erro interno do servidor',
            ];
        }

        return $payload;
    }
}
