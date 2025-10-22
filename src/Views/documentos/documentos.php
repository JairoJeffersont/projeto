<?php

use App\Controllers\DocumentoController;
use App\Controllers\UsuarioController;
use App\Controllers\OrgaoController;

include('../src/Views/includes/verificaLogado.php');

$buscaOrgao = OrgaoController::listarOrgaos($_SESSION['usuario']['gabinete_id'], 'ASC', 'nome', 1000);
$buscaTipo = DocumentoController::listarTiposDocumentos($_SESSION['usuario']['gabinete_id']);

$anoGet = $_GET['ano'] ?? date('Y');
$buscaGet = $_GET['busca'] ?? '';
$buscaTipoGet = $_GET['tipo'] ?? '';

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
                            'resumo' => $_POST['resumo'],
                            'gabinete_id' => $_SESSION['usuario']['gabinete_id'],
                            'usuario_id' => $_SESSION['usuario']['id'],
                        ];

                        if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
                            $dados['arquivo'] = $_FILES['arquivo'];
                        }

                        $result = DocumentoController::novoDocumento($dados);

                        if ($result['status'] == 'conflict') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        } else if ($result['status'] == 'tamanho_maximo_excedido' || $result['status'] == 'formato_nao_permitido') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">Tamanho da foto excedido ou formato não permitido.</div>';
                        }
                    }

                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-10">
                            <input type="text" class="form-control form-control-sm" name="titulo" placeholder="Titulo" required>
                        </div>
                        <div class="col-md-1 col-2">
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
                            <button type="submit" class="btn btn-success btn-sm confirm-action" data-message="Tem certeza que deseja inserir esse documento?" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12">
                <div class="card mb-2">
                    <div class="card-body custom-card-body p-2">
                        <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                            <div class="col-md-1 col-6">
                                <input type="hidden" name="secao" value="documentos" />
                                <input type="text" class="form-control form-control-sm" name="ano" data-mask=0000 value="<?php echo $anoGet ?>">
                            </div>
                            <div class="col-md-2 col-6">
                                <select class="form-select form-select-sm" name="tipo" id="tipo">
                                    <option value="">Todos os documentos</option>
                                    <?php
                                    if ($buscaTipo['status'] == 'success') {
                                        foreach ($buscaTipo['data'] as $t) {
                                            if ($buscaTipoGet == $t['id']) {
                                                echo '<option value="' . $t['id'] . '" selected>' . $t['nome'] . '</option>';
                                            } else {
                                                echo '<option value="' . $t['id'] . '">' . $t['nome'] . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-10">
                                <input type="text" class="form-control form-control-sm" name="busca" value="<?php echo $buscaGet ?>" placeholder="Buscar...">
                            </div>
                            <div class="col-md-1 col-2">
                                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Resumo</th>
                                    <th scope="col">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $buscaDocumentos = DocumentoController::listarDocumentos($_SESSION['usuario']['gabinete_id'], $anoGet, $buscaTipoGet, $buscaGet);

                                if ($buscaDocumentos['status'] == 'success') {
                                    foreach ($buscaDocumentos['data'] as $documento) {
                                        $usuario = UsuarioController::buscarUsuario($documento['usuario_id'])['data']['nome'];
                                        echo '
                                            <tr>
                                                <td><a href="?secao=documento&id=' . $documento['id'] . '">' . $documento['titulo'] . '</a></td>
                                                <td>' . $documento['resumo'] . '</td>
                                                <td>' . $usuario . ' | ' . date('d/m - H:i', strtotime($documento['created_at'])) . '</td>
                                            </tr>
                                        ';
                                    }
                                } else if ($buscaDocumentos['status'] == 'empty') {
                                    echo '<tr><td colspan="5">' . $buscaDocumentos['message'] . '</td></tr>';
                                } else if ($buscaDocumentos['status'] == 'server_error') {
                                    echo '<tr><td colspan="5">' . $buscaDocumentos['message'] . ' | ' . $buscaDocumentos['error_id'] . '</td></tr>';
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