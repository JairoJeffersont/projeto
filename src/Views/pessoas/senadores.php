<?php

use App\Helpers\GetData;
use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

$buscaGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id']);

$estado = $_GET['estado'] ?? $buscaGabinete['data']['estado'];
$partido = $_GET['partido'] ?? $buscaGabinete['data']['partido'];

/*
 * API de Senadores do Senado Federal
 */
$url = 'https://legis.senado.leg.br/dadosabertos/senador/lista/atual?format=xml';
$buscaSen = GetData::getXml($url);

/**
 * Helper seguro para navegar em arrays aninhados
 */
function getNested(array $arr, string ...$keys) {
    $tmp = $arr;
    foreach ($keys as $k) {
        if (!is_array($tmp) || !array_key_exists($k, $tmp)) {
            return null;
        }
        $tmp = $tmp[$k];
    }
    return $tmp;
}

// Caminho dos senadores no XML do Senado
$senadores = $buscaSen['data']['Parlamentares']['Parlamentar'] ?? [];

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php' ?>
        <div class="container-fluid p-2">

            <div class="card mb-2">
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
                    <p class="card-text mb-0">Nesta seção, é possível ver todos os senadores em exercício.</p>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">

                    <form class="row g-2 form_custom mb-0" id="form_busca" method="GET">

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
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>

                </div>
            </div>

            <?php
            /*
             * FILTRO
             */
            if (!empty($senadores)) {

                $senadores = array_filter($senadores, function ($sen) use ($estado, $partido) {

                    $idf = $sen['IdentificacaoParlamentar'] ?? [];

                    // UF pode aparecer em caminhos diferentes dependendo do senador
                    $uf = $idf['UfParlamentar']
                        ?? getNested($sen, 'Mandato', 'UfParlamentar')
                        ?? '';

                    // Partido também varia em algumas estruturas
                    $sigla = $idf['SiglaPartidoParlamentar'] ?? '';

                    if ($estado === '' && $partido === '') return true;
                    if ($estado !== '' && $partido === '') return $uf === $estado;
                    if ($estado === '' && $partido !== '') return $sigla === $partido;

                    return $uf === $estado && $sigla === $partido;
                });

                // Ordenação por nome
                usort($senadores, function ($a, $b) {

                    $nomeA = getNested($a, 'IdentificacaoParlamentar', 'NomeParlamentar')
                        ?? getNested($a, 'IdentificacaoParlamentar', 'NomeCompletoParlamentar')
                        ?? '';

                    $nomeB = getNested($b, 'IdentificacaoParlamentar', 'NomeParlamentar')
                        ?? getNested($b, 'IdentificacaoParlamentar', 'NomeCompletoParlamentar')
                        ?? '';

                    return strcmp($nomeA, $nomeB);
                });
            }
            ?>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">

                    <?php if (!empty($senadores)) : ?>
                        <ul class="list-group">

                            <?php foreach ($senadores as $sen) :

                                $idf = $sen['IdentificacaoParlamentar'] ?? [];

                                $codigo = $idf['CodigoParlamentar'] ?? '';
                                $nome = $idf['NomeParlamentar']
                                    ?? $idf['NomeCompletoParlamentar']
                                    ?? '—';

                                $foto = $idf['UrlFotoParlamentar'] ?? '';
                                $email = $idf['EmailParlamentar'] ?? '';

                                // UF fallback
                                $uf = $idf['UfParlamentar']
                                    ?? getNested($sen, 'Mandato', 'UfParlamentar')
                                    ?? '';

                                // Partido fallback
                                $partido = $idf['SiglaPartidoParlamentar'] ?? '';

                            ?>

                                <li class="list-group-item d-flex align-items-center">

                                    <img src="<?= htmlspecialchars($foto) ?>"
                                        alt="Foto de <?= htmlspecialchars($nome) ?>"
                                        class="me-3 rounded border shadow-sm"
                                        width="50"
                                        onerror="this.src='/assets/images/placeholder.png'">

                                    <div>
                                        <a href="?secao=senador&id=<?= htmlspecialchars($codigo) ?>"
                                            class="fw-semibold text-decoration-none loading-modal">
                                            <?= htmlspecialchars($nome) ?>
                                        </a><br>
                                        <small class="text-muted">
                                            <b><?= htmlspecialchars($partido) ?>/<?= htmlspecialchars($uf) ?></b><br>
                                            <span><?= htmlspecialchars($email) ?></span>
                                        </small>
                                    </div>

                                </li>

                            <?php endforeach; ?>

                        </ul>

                    <?php else : ?>

                        <div class="alert alert-info px-2 py-1 custom-alert mb-0">
                            Nenhum senador encontrado.
                        </div>

                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>