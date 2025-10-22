<?php

use App\Controllers\DocumentoController;
use App\Controllers\OrgaoController;

include('../src/Views/includes/verificaLogado.php');

$buscaOrgao = OrgaoController::listarOrgaos($_SESSION['usuario']['gabinete_id'], 'ASC', 'nome', 1000);
$buscaTipo = DocumentoController::listarTiposDocumentos($_SESSION['usuario']['gabinete_id']);

$id = $_GET['id'] ?? '';

$buscaDocumento = DocumentoController::buscarDocumento($id);

if ($buscaDocumento['status'] != 'success') {
    header('location: ?secao=documentos');
}

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
                    <p class="card-text mb-2">Aqui você pode adicionar e editar os documentos, mantendo a organização das informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios. São permitidos arquivos <b>PDF</b>, <b>Word</b> e <b>Excel</b>. Tamanho máximo de <b>20MB</b></p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'titulo' => $_POST['titulo'],
                            'ano' => $_POST['ano'],
                            'orgao' => $_POST['orgao'],
                            'tipo_id' => $_POST['tipo'],
                            'arquivo' => $_FILES['arquivo']['name'],
                            'resumo' => $_POST['resumo']
                        ];

                        if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
                            $dados['arquivo'] = $_FILES['arquivo'];
                        }

                        $result = DocumentoController::atualizarDocumento($id, $dados);

                        if ($result['status'] == 'conflict') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            $buscaDocumento = DocumentoController::buscarDocumento($id);
                            echo '<div class="alert alert-success px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        } else if ($result['status'] == 'tamanho_maximo_excedido' || $result['status'] == 'formato_nao_permitido') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">Tamanho da foto excedido ou formato não permitido.</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_apagar'])) {

                        $result = DocumentoController::apagarDocumento($id);

                        if ($result['status'] == 'not_permitted') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            header('location: ?secao=documentos');
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }

                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-10">
                            <input type="text" class="form-control form-control-sm" name="titulo" placeholder="Titulo" value="<?php echo $buscaDocumento['data']['titulo'] ?>" required>
                        </div>
                        <div class="col-md-1 col-2">
                            <input type="number" class="form-control form-control-sm" name="ano" data-mask=0000 value="<?php echo $buscaDocumento['data']['ano'] ?>" readonly>
                        </div>
                        <div class="col-md-2 col-12">
                            <div class="input-group input-group-sm">
                                <select class="form-select" name="orgao" required>
                                    <option value="1">Órgão não informado</option>
                                    <?php
                                    if ($buscaOrgao['status'] == 'success') {
                                        foreach ($buscaOrgao['data'] as $o) {
                                            if ($buscaDocumento['data']['orgao_id'] == $o['id']) {
                                                echo '<option value="' . $o['id'] . '" selected>' . $o['nome'] . '</option>';
                                            } else {
                                                echo '<option value="' . $o['id'] . '">' . $o['nome'] . '</option>';
                                            }
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
                                            if ($buscaDocumento['data']['tipo_id'] == $t['id']) {
                                                echo '<option value="' . $t['id'] . '" selected>' . $t['nome'] . '</option>';
                                            } else {
                                                echo '<option value="' . $t['id'] . '">' . $t['nome'] . '</option>';
                                            }
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
                            <input type="file" class="form-control form-control-sm" name="arquivo">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="resumo" rows="10" placeholder="Resumo do documento"><?php echo $buscaDocumento['data']['resumo'] ?></textarea>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm confirm-action" data-message="Tem certeza que deseja inserir esse documento?" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm confirm-action" data-message="Tem certeza que deseja apagar esse documento?" name="btn_apagar"><i class="bi bi-floppy-fill"></i> Apagar</button>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>