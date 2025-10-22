<?php

use App\Controllers\DocumentoController;
use App\Controllers\UsuarioController;
use App\Controllers\OrgaoController;

include('../src/Views/includes/verificaLogado.php');

$buscaOrgao = OrgaoController::listarOrgaos($_SESSION['usuario']['gabinete_id'], 'ASC', 'nome', 1000);
$buscaTipo = DocumentoController::listarTiposDocumentos($_SESSION['usuario']['gabinete_id']);

?>


<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-header custom-card-header px-2 py-1 text-white">
                    Documentos
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível arquivar e pesquisar documentos do gabinete, garantindo a organização correta dessas informações no sistema.</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body custom-card-body p-2">

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="titulo" placeholder="Titulo" required>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="number" class="form-control form-control-sm" name="ano" data-mask=0000 value="<?php echo date('Y') ?>">

                        </div>
                        <div class="col-md-2 col-12">
                            <div class="input-group input-group-sm">
                                <select class="form-select" name="orgao" required>
                                    <option value="1">Órgão não informado</option>
                                    <?php
                                    if ($buscaOrgao['status'] == 'success') {
                                        foreach ($buscaOrgao['data'] as $o) {
                                            echo '<option value="' . $o['id'] . '">' . $o['nome'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <a href="?secao=orgaos" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir um novo órgão?" title="Gerenciar Tipos de Órgãos">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2 col-12">
                            <div class="input-group input-group-sm">
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="1">Sem tipo definido</option>
                                    <?php
                                    if ($buscaTipo['status'] == 'success') {
                                        foreach ($buscaTipo['data'] as $t) {
                                            echo '<option value="' . $t['id'] . '">' . $t['nome'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <a href="?secao=tipos-documentos" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir um novo tipo de órgão?" title="Gerenciar Tipos de Órgãos">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="file" class="form-control form-control-sm" name="arquivo" required>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="resumo" rows="10" placeholder="Resumo do documento"></textarea>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>