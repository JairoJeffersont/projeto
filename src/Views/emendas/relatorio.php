<?php

use App\Controllers\EmendaController;

include('../src/Views/includes/verificaLogado.php');

$anoGet = $_GET['ano'] ?? date('Y');
$tipoGet = $_GET['tipo'] ?? '1';

$buscaEmendas = EmendaController::listarEmendas($_SESSION['usuario']['gabinete_id'], 'asc', 'valor', 10000000, 1, $anoGet, '', '', $tipoGet);

if ($buscaEmendas['status'] != 'success') {
    $buscaEmendas['data'] = [];
}

?>


<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body custom-card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=emendas" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header custom-card-header px-2 py-1 text-white">
                    Relatório de emendas
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível gerar um relatório com as emendas parlamentares.</p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <form class="row g-2 form_custom mb-0" id="form_busca" method="GET" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-1 col-4">
                            <input type="hidden" name="secao" value="relatorio-emendas" />
                            <input type="number" class="form-control form-control-sm" name="ano" placeholder="Ano" value="<?php echo $anoGet ?>">
                        </div>
                        <div class="col-md-1 col-5">
                            <select class="form-select form-select-sm" name="tipo">
                                <?php
                                $buscaTipo = EmendaController::listarTiposdeEmendas($_SESSION['usuario']['gabinete_id']);
                                if ($buscaTipo['status'] == 'success') {
                                    foreach ($buscaTipo['data'] as $tipo) {
                                        if ($tipo['id'] == $tipoGet) {
                                            echo '<option value="' . $tipo['id'] . '" selected>' . $tipo['nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['id'] . '">' . $tipo['nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="card mb-2">
                        <div class="card-header custom-card-header-no-bg px-2 py-1 bg-primary text-center text-white">
                            Total de emendas <b><?php echo $anoGet ?></b>
                        </div>
                        <div class="card-body custom-card-body text-center p-4">
                            <h3>
                                <?php
                                $somaTodas = 0;
                                foreach ($buscaEmendas['data'] as $emenda) {
                                    $somaTodas += (float) $emenda['valor'];
                                }
                                echo 'R$ ' . number_format($somaTodas, 2, ',', '.');
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="card mb-2">
                        <div class="card-header custom-card-header-no-bg px-2 py-1 text-center bg-success text-white">
                            Total de emendas pagas <b><?php echo $anoGet ?></b>
                        </div>
                        <div class="card-body custom-card-body text-center p-4">
                            <h3>
                                <?php
                                $somaPagas = 0;
                                foreach ($buscaEmendas['data'] as $emenda) {
                                    if ($emenda['situacao_id'] == '2') {
                                        $somaPagas += (float) $emenda['valor'];
                                    }
                                }
                                echo 'R$ ' . number_format($somaPagas, 2, ',', '.');
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header custom-card-header-no-bg bg-secondary px-2 py-1 text-center text-white">
                    Distribuição por área
                </div>
                <div class="card-body custom-card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Área</th>
                                    <th scope="col">Valor Total</th>
                                    <th scope="col">Valor Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totaisPorArea = [];

                                // Agrupa os valores por área
                                foreach ($buscaEmendas['data'] as $emenda) {
                                    $areaId = $emenda['area_id'];

                                    $nomeArea = EmendaController::buscarAreaDeEmenda($areaId)['data']['nome'];

                                    if (!isset($totaisPorArea[$nomeArea])) {
                                        $totaisPorArea[$nomeArea] = [
                                            'total' => 0,
                                            'pago' => 0
                                        ];
                                    }

                                    $valor = (float)$emenda['valor'];
                                    $totaisPorArea[$nomeArea]['total'] += $valor;

                                    if ($emenda['situacao_id'] == '2') {
                                        $totaisPorArea[$nomeArea]['pago'] += $valor;
                                    }
                                }

                                // Exibe os totais por área
                                foreach ($totaisPorArea as $area => $valores) {
                                    echo '<tr>
                                            <td>' . htmlspecialchars($area) . '</td>
                                            <td>R$ ' . number_format($valores['total'], 2, ',', '.') . '</td>
                                            <td>R$ ' . number_format($valores['pago'], 2, ',', '.') . '</td>
                                        </tr>';
                                }

                                // Caso não haja emendas
                                if (empty($totaisPorArea)) {
                                    echo '<tr><td colspan="3" class="text-center">Nenhuma emenda encontrada.</td></tr>';
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