<?php

use App\Helpers\GetData;
use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

$buscaGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id']);

$estado = $_GET['estado'] ?? $buscaGabinete['data']['estado'];
$partido = $_GET['partido'] ?? $buscaGabinete['data']['partido'];

$url = 'https://www.camara.leg.br/sitcamaraws/deputados.asmx/ObterDeputados';

$buscaDep = GetData::getXml($url);

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
                    <p class="card-text mb-0">Nesta seção, é possível ver todos os deputados federais.</p>
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
                    <?php
                    if (!empty($buscaDep['data']['deputado'])) {

                        // Filtra conforme estado e/ou partido
                        $deputados = array_filter($buscaDep['data']['deputado'], function ($dep) use ($estado, $partido) {
                            if ($estado === '' && $partido === '') return true;
                            if ($estado !== '' && $partido === '') return $dep['uf'] === $estado;
                            if ($estado === '' && $partido !== '') return $dep['partido'] === $partido;
                            return $dep['uf'] === $estado && $dep['partido'] === $partido;
                        });

                        // Ordena por nome
                        usort($deputados, function ($a, $b) {
                            return strcmp($a['nomeParlamentar'], $b['nomeParlamentar']);
                        });
                    }
                    ?>

                    <?php if (!empty($deputados)) : ?>
                        <ul class="list-group">
                            <?php foreach ($deputados as $dep) : ?>
                                <li class="list-group-item d-flex align-items-center">
                                    <img src="<?= $dep['urlFoto'] ?>"
                                        alt="Foto de <?= $dep['nomeParlamentar'] ?>"
                                        class="me-3 rounded border shadow-sm" width="50">
                                    <div>
                                        <a href="?secao=deputado&id=<?= $dep['ideCadastro'] ?>" class="fw-semibold text-decoration-none loading-modal">
                                            <?= $dep['nomeParlamentar'] ?>
                                        </a><br>
                                        <small class="text-muted">
                                            <b><?= $dep['partido'] ?>/<?= $dep['uf'] ?> — <?= $dep['condicao'] ?></b><br>
                                            <b><?= $dep['email'] ?></b>
                                        </small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <div class="alert alert-info px-2 py-1 custom-alert mb-0">Nenhum deputado encontrado.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>