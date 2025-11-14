<?php

use App\Helpers\GetData;
use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

$buscaGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id']);

$estado = $_GET['estado'] ?? $buscaGabinete['data']['estado'];
$partido = $_GET['partido'] ?? $buscaGabinete['data']['partido'];

$params = [
    'siglaUf' => $estado,
    'siglaPartido' => $partido
];

$url = 'https://dadosabertos.camara.leg.br/api/v2/deputados?' . http_build_query($params);

$buscaDep = GetData::getJson($url);

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
                    Deputados federais
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-1">Nesta seção, é possível ver todos os deputados federais.</p>
                    <p class="card-text mb-0"><b><a href="https://www.camara.leg.br/internet/infdoc/novoconteudo/Acervo/CELEG/Carometro/carometro_legislatura57.pdf" target="_blank">Imprimir carômetro</a> | </b>
                    <b><a href="https://www.camara.leg.br/internet/deputado/deputado.xls" target="_blank">Baixar lista completa</a>

                    </p>

                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <form class="row g-2 form_custom mb-0" id="form_busca" method="GET" enctype="application/x-www-form-urlencoded">

                        <div class="col-md-1 col-5">
                            <input type="hidden" name="secao" value="deputados" />
                            <select class="form-select form-select-sm estado" name="estado" data-selected="<?= $estado ?>">
                                <option value="" <?= ($estado == '') ? 'selected' : '' ?>>Todos os estados</option>
                            </select>
                        </div>

                        <div class="col-md-1 col-5">
                            <select class="form-select form-select-sm partidos" name="partido" data-selected="<?= $partido ?>">
                                <option value="" <?= ($partido == '') ? 'selected' : '' ?>>Todos os partidos</option>
                            </select>
                        </div>

                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <ul class="list-group">
                        <?php
                        if (!empty($buscaDep['data']['dados'])) {
                            foreach ($buscaDep['data']['dados'] as $dep) {
                                echo '
                                        <li class="list-group-item d-flex align-items-center">
                                            <img src="' . $dep['urlFoto'] . '" 
                                                alt="Foto de ' . $dep['nome'] . '" 
                                                class="me-3 rounded-3 shadow-sm border"
                                                style="width:60px; height:80px; object-fit:cover;">

                                            <div>
                                                <p class="mb-0"><a href="https://www.camara.leg.br/deputados/' . $dep['id'] . '" target="_blank">' . $dep['nome'] . ' - ' . $dep['siglaPartido'] . '/' . $dep['siglaUf'] . '</a></p>
                                                <p class="mb-0 text-muted"><small>' . $dep['email'] . '</small></p>
                                            </div>
                                        </li>';
                            }
                        } else {
                            echo '<li class="list-group-item d-flex align-items-center"><b>Nenhum deputado encontrado</b></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>