<?php

use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

if ($_SESSION['usuario']['tipo_usuario_id'] != '1') {
    header('Location: ?secao=home');
}

$buscaGabinetes = GabineteController::listarGabinetes();


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
                    Gabinetes
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível adicionar e editar os do sistema.</p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">TIpo</th>
                                    <th scope="col">Município/UF</th>
                                    <th scope="col">Ativo?</th>
                                    <th scope="col">Criado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($buscaGabinetes['status'] == 'success') {
                                    foreach ($buscaGabinetes['data'] as $gabinete) {
                                        $dataFormatada = date('d/m - H:i', strtotime($gabinete['created_at']));
                                        $ativo = $gabinete['ativo'] == 1 ? '<b>Sim</b>' : 'Não';
                                        $tipoNome = GabineteController::buscarTipoGabinete($gabinete['tipo_gabinete_id'])['data']['nome'];

                                        $local = !empty($gabinete['cidade'])
                                            ? "{$gabinete['cidade']}/{$gabinete['estado']}"
                                            : "{$gabinete['estado']}";

                                        echo "
                                                <tr>
                                                    <td>
                                                        <a href='?secao=gabinete&id={$gabinete['id']}'>
                                                            {$gabinete['nome']}
                                                        </a>
                                                    </td>
                                                    <td>{$tipoNome}</td>
                                                    <td>{$local}</td>
                                                    <td>{$ativo}</td>
                                                    <td>{$dataFormatada}</td>
                                                </tr>
                                            ";
                                    }
                                } else if ($buscaGabinetes['status'] == 'empty') {
                                    echo '<tr><td colspan="4">' . $buscaGabinetes['message'] . '</td></tr>';
                                } else if ($buscaGabinetes['status'] == 'server_error') {
                                    echo '<tr><td colspan="4">' . $buscaGabinetes['message'] . ' | ' . $buscaGabinetes['error_id'] . '</td></tr>';
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