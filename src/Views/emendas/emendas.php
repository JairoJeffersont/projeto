<?php

use App\Controllers\EmendaController;
use App\Controllers\UsuarioController;
use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

$buscaGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id'])['data']['estado'];

$anoGet = $_GET['ano'] ?? date('Y');
$ordenarPor = $_GET['ordenarPor'] ?? 'numero';
$ordem = $_GET['ordem'] ?? 'ASC';
$itens = $_GET['itens'] ?? 10;
$pagina = $_GET['pagina'] ?? 1;
$estado = $_GET['estado'] ?? $buscaGabinete;
$cidade = $_GET['cidade'] ?? '';
$tipoGet = $_GET['tipo'] ?? '1';





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
                    Emendas
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível adicionar e editar as emendas parlamentares, garantindo a organização correta dessas informações no sistema.</p>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {
                        $dadosEmenda = [
                            'ano' => $_POST['ano'],
                            'numero' => $_POST['numero'],
                            'descricao' => $_POST['descricao'],
                            'valor' => str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor']),
                            'estado' => $_POST['estado'],
                            'cidade' => $_POST['cidade'],
                            'tipo_id' => $_POST['tipo'],
                            'area_id' => $_POST['area'],
                            'situacao_id' => $_POST['situacao'],
                            'informacoes' => $_POST['informacoes'],
                            'gabinete_id' => $_SESSION['usuario']['gabinete_id'],
                            'usuario_id'  => $_SESSION['usuario']['id'],
                        ];

                        $result = EmendaController::novaEmenda($dadosEmenda);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }

                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-1 col-3">
                            <input type="text" class="form-control form-control-sm" name="ano" data-mask="0000" placeholder="Ano" value="<?php echo date('Y') ?>" required>
                        </div>
                        <div class="col-md-1 col-9">
                            <input type="text" class="form-control form-control-sm" name="numero" data-mask="000000000" placeholder="Número da emenda" required>
                        </div>
                        <div class="col-md-10 col-12">
                            <input type="text" class="form-control form-control-sm" name="descricao" placeholder="Descricao simplificada (objeto, projeto...)" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="valor" placeholder="Valor (R$)" required>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm estado" name="estado" data-selected="<?php echo $buscaGabinete ?>" required>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm municipio" name="cidade">
                                <option value="">Selecione o município</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="input-group input-group-sm">
                                <select class="form-select form-select-sm" name="tipo" required>
                                    <option value="1" selected>Emenda individual</option>
                                    <option value="2">Emenda de bancada</option>
                                    <?php
                                    $buscaTipo = EmendaController::listarTiposdeEmendas($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaTipo['status'] == 'success') {
                                        foreach ($buscaTipo['data'] as $tipo) {
                                            echo '<option value="' . $tipo['id'] . '">' . $tipo['nome'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <a href="?secao=tipos-emendas" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir um novo tipo de emenda??" title="Gerenciar Tipos de Emendas">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="input-group input-group-sm">
                                <select class="form-select form-select-sm" name="area" required>
                                    <option value="1" selected>Área não definida</option>
                                    <?php
                                    $buscaArea = EmendaController::listarAreasDeEmendas($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaArea['status'] == 'success') {
                                        foreach ($buscaArea['data'] as $area) {
                                            echo '<option value="' . $area['id'] . '">' . $area['nome'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <a href="?secao=areas-emendas" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir uma nova área de emenda??" title="Gerenciar Áreas de Emendas">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2 col-12">
                            <div class="input-group input-group-sm">
                                <select class="form-select form-select-sm" name="situacao" required>
                                    <option value="1" selected>Situação não definida</option>
                                    <?php
                                    $buscaSituacao = EmendaController::listarSituacoesdeEmendas($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaSituacao['status'] == 'success') {
                                        foreach ($buscaSituacao['data'] as $situacao) {
                                            echo '<option value="' . $situacao['id'] . '">' . $situacao['nome'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <a href="?secao=situacoes-emendas" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir uma nova área de emenda??" title="Gerenciar Áreas de Emendas">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" id="tinymce" name="informacoes" rows="5" placeholder="Informações importantes dessa emenda"></textarea>
                        </div>
                        <div class="col-md-1 col-12">
                            <button type="submit" class="btn btn-success btn-sm confirm-action" data-message="Tem certeza que deseja inserir essa emenda?" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>

                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <form class="row g-2 form_custom mb-0" id="form_busca" method="GET" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-1 col-2">
                            <input type="hidden" name="secao" value="emendas" />
                            <input type="number" class="form-control form-control-sm" name="ano" value="<?php echo $anoGet ?>">
                        </div>
                        <div class="col-md-2 col-10">
                            <select class="form-select form-select-sm" name="ordenarPor" required>
                                <option value="numero" <?= ($ordenarPor == 'numero') ? 'selected' : '' ?>>Ordenar por | Número</option>
                                <option value="valor" <?= ($ordenarPor == 'valor') ? 'selected' : '' ?>>Ordenar por | Valor</option>
                                <option value="area" <?= ($ordenarPor == 'area') ? 'selected' : '' ?>>Ordenar por | Área</option>
                                <option value="tipo" <?= ($ordenarPor == 'tipo') ? 'selected' : '' ?>>Ordenar por | Tipo</option>
                                <option value="created_at" <?= ($ordenarPor == 'created_at') ? 'selected' : '' ?>>Ordenar por | Criação</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="ordem" required>
                                <option value="ASC" <?= ($ordem == 'ASC') ? 'selected' : '' ?>>Ordem Crescente</option>
                                <option value="DESC" <?= ($ordem == 'DESC') ? 'selected' : '' ?>>Ordem Decrescente</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="itens" required>
                                <option value="5" <?= ($itens == 5) ? 'selected' : '' ?>>5 itens</option>
                                <option value="10" <?= ($itens == 10) ? 'selected' : '' ?>>10 itens</option>
                                <option value="25" <?= ($itens == 25) ? 'selected' : '' ?>>25 itens</option>
                                <option value="50" <?= ($itens == 50) ? 'selected' : '' ?>>50 itens</option>
                                <option value="100" <?= ($itens == 100) ? 'selected' : '' ?>>100 itens</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm estado" name="estado" data-selected="<?= $estado ?>">
                                <option value="" <?= ($estado == '') ? 'selected' : '' ?>>Todos os estados</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm municipio" name="cidade" data-selected="<?= $cidade ?>">
                                <option value="" <?= ($cidade == '') ? 'selected' : '' ?>>Todas as cidades</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="tipo" required>
                                <option value="1" <?= ($tipoGet == 1 ? 'selected' : '') ?>>Emenda individual</option>
                                <option value="2" <?= ($tipoGet == 2 ? 'selected' : '') ?>>Emenda de bancada</option>
                                <?php
                                if ($buscaTipo['status'] == 'success') {
                                    foreach ($buscaTipo['data'] as $tipo) {
                                        if ($tipoGet == $tipo['id']) {
                                            echo '<option value="' . $tipo['id'] . '" selected >' . $tipo['nome'] . '</option>';
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

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Descrição</th>
                                    <th scope="col">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaEmendas = EmendaController::listarEmendas($_SESSION['usuario']['gabinete_id'], $ordem, $ordenarPor, $itens, $pagina, $anoGet, $estado, $cidade, $tipoGet);
                                $soma = 0;
                                if ($buscaEmendas['status'] == 'success') {
                                    foreach ($buscaEmendas['data'] as $emenda) {
                                        $valorFormatado = 'R$ ' . number_format($emenda['valor'], 2, ',', '.');
                                        $soma += $emenda['valor'];
                                        echo '<tr>
                                                <td>' . $emenda['numero'] . '</td>
                                                <td><a href="?secao=emenda&id=' . $emenda['id'] . '" class="loading-modal">' . $emenda['descricao'] . '</a></td>
                                                <td>' . $valorFormatado . '</td>
                                            </tr>';
                                    }
                                } else if ($buscaEmendas['status'] == 'empty') {
                                    echo '<tr><td colspan="3">' . $buscaEmendas['message'] . '</td></tr>';
                                } else if ($buscaEmendas['status'] == 'server_error') {
                                    echo '<tr><td colspan="3">' . $buscaEmendas['message'] . ' | ' . $buscaEmendas['error_id'] . '</td></tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="3"><b>Total: <?php echo 'R$ ' . number_format($soma, 2, ',', '.'); ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>