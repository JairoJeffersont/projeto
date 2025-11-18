<?php

use App\Helpers\GetData;
use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

$buscaGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id']);

$estado = $_GET['estado'] ?? $buscaGabinete['data']['estado'];
$partido = $_GET['partido'] ?? '';

$url = 'https://legis.senado.leg.br/dadosabertos/senador/lista/atual';
$buscaSen = GetData::getXml($url);

$senadores = $buscaSen['data']['Parlamentares']['Parlamentar'] ?? [];

$lista = $senadores;

if (!empty($estado)) {
    $lista = array_filter($lista, function ($sen) use ($estado) {
        return strtoupper($sen['IdentificacaoParlamentar']['UfParlamentar']) === strtoupper($estado);
    });
}

if (!empty($partido)) {
    $lista = array_filter($lista, function ($sen) use ($partido) {
        return strtoupper($sen['IdentificacaoParlamentar']['SiglaPartidoParlamentar']) === strtoupper($partido);
    });
}

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">

            <div class="card mb-2 ">
                <div class="card-body custom-card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button">
                        <i class="bi bi-house-door-fill"></i> Início
                    </a>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header custom-card-header px-2 py-1 text-white">
                    Senadores
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-1">Nesta seção, é possível ver todos os senadores.</p>
                    <p class="card-text mb-0"><b><a href="https://www.senado.leg.br/transparencia/LAI/secrh/parla_inter.pdf" target="_blank">Baixar lista completa</a></p>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <form class="row g-2 form_custom mb-0" method="GET">

                        <div class="col-md-1 col-5">
                            <input type="hidden" name="secao" value="senadores" />
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
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <ul class="list-group">

                        <?php
                        if (!empty($lista)) {

                            foreach ($lista as $senador) {

                                echo '
                                <li class="list-group-item d-flex align-items-center">
                                    <img src="' . $senador['IdentificacaoParlamentar']['UrlFotoParlamentar'] . '" 
                                        alt="Foto de ' . $senador['IdentificacaoParlamentar']['NomeParlamentar'] . '" 
                                        class="me-3 rounded-3 shadow-sm border"
                                        style="width:60px; height:80px; object-fit:cover;">

                                    <div>
                                        <p class="mb-0">
                                            <a href="https://www25.senado.leg.br/web/senadores/senador/-/perfil/' .
                                    $senador['IdentificacaoParlamentar']['CodigoParlamentar'] .
                                    '" target="_blank">
                                                ' . $senador['IdentificacaoParlamentar']['NomeParlamentar'] . ' - ' .
                                    $senador['IdentificacaoParlamentar']['SiglaPartidoParlamentar'] . '/' .
                                    $senador['IdentificacaoParlamentar']['UfParlamentar'] . '
                                            </a>
                                        </p>

                                        <p class="mb-0 text-muted">
                                            <small>' . $senador['IdentificacaoParlamentar']['EmailParlamentar'] . '</small>
                                        </p>
                                    </div>
                                </li>';
                            }
                        } else {
                            echo '<li class="list-group-item"><b>Nenhum senador encontrado</b></li>';
                        }
                        ?>

                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>