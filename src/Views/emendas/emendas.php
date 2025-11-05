<?php

use App\Controllers\EmendaController;
use App\Controllers\UsuarioController;
use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

$buscaGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id'])['data']['estado'];


?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-header custom-card-header px-2 py-1 text-white">
                    Emendas mendas
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
                            <input type="text" class="form-control form-control-sm" name="descricao" placeholder="Descricao simplificada" required>
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
                                    <option>Escolha o tipo</option>
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
                                    <option>Escolha a área</option>
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
                                    <option>Escolha a situação</option>
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
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes dessa emenda"></textarea>
                        </div>
                        <div class="col-md-1 col-12">
                            <button type="submit" class="btn btn-success btn-sm confirm-action" data-message="Tem certeza que deseja inserir esse tipo de emenda?" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>