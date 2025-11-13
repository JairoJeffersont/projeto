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
                    Pessoas
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível ver todos os deputados federais.</p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Partido/Estado</th>
                                    <th scope="col">Situação</th>
                                </tr>
                            </thead>
                            <?php

                            if (!empty($buscaDep['data']['deputado'])) {

                                // Filtra os deputados conforme o estado e/ou partido
                                $deputados = array_filter($buscaDep['data']['deputado'], function ($dep) use ($estado, $partido) {
                                    // Se ambos estiverem vazios, mostra todos
                                    if ($estado === '' && $partido === '') {
                                        return true;
                                    }

                                    // Se só estado estiver definido
                                    if ($estado !== '' && $partido === '') {
                                        return $dep['uf'] === $estado;
                                    }

                                    // Se só partido estiver definido
                                    if ($estado === '' && $partido !== '') {
                                        return $dep['partido'] === $partido;
                                    }

                                    // Se ambos estiverem definidos
                                    return $dep['uf'] === $estado && $dep['partido'] === $partido;
                                });

                                // Ordena por nome
                                usort($deputados, function ($a, $b) {
                                    return strcmp($a['nomeParlamentar'], $b['nomeParlamentar']);
                                });
                            }
                            ?>

                            <tbody>
                                <?php
                                if (!empty($deputados)) {
                                    foreach ($deputados as $dep) {
                                        echo '<tr>
                                                <td><a href="?secao=deputado&id=' . $dep['ideCadastro'] . '">' . $dep['nomeParlamentar'] . '</a></td>
                                                <td>' . $dep['partido'] . '/' . $dep['uf'] . '</td>
                                                <td>' . $dep['condicao'] . '</td>
                                            </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="3">Nenhum deputado encontrado.</td></tr>';
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