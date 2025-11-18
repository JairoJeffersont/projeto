<?php

include('../src/Views/includes/verificaLogado.php');

use App\Controllers\ProposicaoController;
use App\Helpers\GetData;

$ano = $_GET['ano'] ?? date('Y');
$tipoGet = $_GET['tipo'] ?? 'PL';
$itens = $_GET['itens'] ?? 10;
$pagina = $_GET['pagina'] ?? 1;

$buscaProposicoes = ProposicaoController::buscarProposicoesCD($buscaGabinete['nome'], $ano, $tipoGet, $itens, $pagina);

$buscaTipos = GetData::getJson('https://dadosabertos.camara.leg.br/api/v2/referencias/tiposProposicao');
?>


<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body custom-card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header custom-card-header px-2 py-1 text-white">
                    Proposições do gabinete
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-1">Nesta seção, você pode consultar todas as proposições legislativas relacionadas ao gabinete.</p>
                    <p class="card-text mb-0">As informações da Câmara e do Senado são obtidas automaticamente dos servidores de cada Casa.</p>
                </div>

            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <form class="row g-2 form_custom mb-0" id="form_busca" method="GET" enctype="application/x-www-form-urlencoded">

                        <input type="hidden" name="secao" value="proposicoes" />
                        <div class="col-md-1 col-2">
                            <input type="text" class="form-control form-control-sm" name="ano" data-mask=0000 value="<?= $ano ?>">
                        </div>

                        <div class="col-md-1 col-5">
                            <select class="form-select form-select-sm" name="tipo">
                                <?php

                                if (isset($buscaTipos['data']) && !empty($buscaTipos['data'])) {
                                    foreach ($buscaTipos['data']['dados'] as $tipo) {
                                        if ($tipo['sigla'] == $tipoGet) {
                                            echo '<option value="' . $tipo['sigla'] . '" selected>' . $tipo['sigla'] . ' - ' . $tipo['nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['sigla'] . '">' . $tipo['sigla'] . ' - ' . $tipo['nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>

                        <div class="col-md-1 col-3">
                            <select class="form-select form-select-sm" name="itens" required>
                                <option value="5" <?= ($itens == 5) ? 'selected' : '' ?>>5 itens</option>
                                <option value="10" <?= ($itens == 10) ? 'selected' : '' ?>>10 itens</option>
                                <option value="25" <?= ($itens == 25) ? 'selected' : '' ?>>25 itens</option>
                                <option value="50" <?= ($itens == 50) ? 'selected' : '' ?>>50 itens</option>
                                <option value="100" <?= ($itens == 100) ? 'selected' : '' ?>>100 itens</option>
                            </select>
                        </div>

                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-0">
                <div class="card-body custom-card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Título</th>
                                    <th scope="col">Ementa</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php

                                if (isset($buscaProposicoes['data']) && !empty($buscaProposicoes['data'])) {

                                    foreach ($buscaProposicoes['data'] as $proposicao) {
                                        echo '<tr>
                                                <td style="white-space: nowrap;"><b><a href="?secao=proposicaoCD&id=' . $proposicao['id'] . '">' . $proposicao['siglaTipo'] . ' ' . $proposicao['numero'] . '/' . $proposicao['ano'] . '</a></b></td>
                                                <td>' . $proposicao['ementa'] . '</td>
                                            </tr>';
                                    }
                                } else if (isset($buscaProposicoes['data']) && empty($buscaProposicoes['data'])) {
                                    echo '<tr><td colspan="2">Nenhuma proposição encontrada</td></tr>';
                                } else {
                                    echo '<tr><td colspan="2">' . $buscaProposicoes['message'] . '</td></tr>';
                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>