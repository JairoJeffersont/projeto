<?php

use App\Controllers\EmendaController;
use App\Controllers\UsuarioController;

include('../src/Views/includes/verificaLogado.php');

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
                        <div class="col-md-2 col-9">
                            <input type="text" class="form-control form-control-sm" name="valor" placeholder="Valor (R$)" required>
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
                            <div class="input-group input-group-sm">
                                <select class="form-select form-select-sm" name="tipo">
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
                                <select class="form-select form-select-sm" name="area">
                                    <option>Escolha a área</option>
                                    <?php
                                    $buscaTipo = EmendaController::listarAreasDeEmendas($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaTipo['status'] == 'success') {
                                        foreach ($buscaTipo['data'] as $tipo) {
                                            echo '<option value="' . $tipo['id'] . '">' . $tipo['nome'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <a href="?secao=areas-emendas" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir uma nova área de emenda??" title="Gerenciar Áreas de Emendas">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
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