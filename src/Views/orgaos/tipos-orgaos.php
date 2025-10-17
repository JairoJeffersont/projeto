<?php

use App\Controllers\OrgaoController;
use App\Controllers\UsuarioController;

include('../src/Views/includes/verificaLogado.php'); ?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-header custom-card-header text-white">
                    Tipos de órgãos
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível adicionar e editar os tipos de órgãos e entidades, garantindo a organização correta dessas informações no sistema.</p>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'nome' => $_POST['nome'],
                            'gabinete_id' => $_SESSION['usuario']['gabinete_id'],
                            'usuario_id' => $_SESSION['usuario']['id'],
                        ];

                        $result = OrgaoController::novoTipodeOrgao($dados);

                        if ($result['status'] == 'conflict') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }


                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome do Tipo" required>
                        </div>
                        <div class="col-md-1 col-12">
                            <button type="submit" class="btn btn-success btn-sm confirm-action" data-message="Tem certeza que deseja inserir esse tipo de órgão?" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
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
                                    <th scope="col">Nome</th>
                                    <th scope="col">Criado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $buscaTipo = OrgaoController::listarTiposOrgaos($_SESSION['usuario']['gabinete_id']);

                                if ($buscaTipo['status'] == 'success') {
                                    foreach ($buscaTipo['data'] as $tipo) {
                                        $usuario = UsuarioController::buscarUsuario($tipo['usuario_id'])['data']['nome'];
                                        echo '<tr><td><a href="?secao=tipo-orgao&id='.$tipo['id'].'">' . $tipo['nome'] . '</a></td><td>' . $usuario . ' | ' . date('d/m - H:i', strtotime($tipo['created_at'])) . '</td></tr>';
                                    }
                                } else if ($buscaTipo['status'] == 'empty') {
                                    echo '<tr><td colspan="2">' . $buscaTipo['message'] . '</td></tr>';
                                } else if ($buscaTipo['status'] == 'server_error') {
                                    echo '<tr><td colspan="2">' . $buscaTipo['message'] . ' | ' . $buscaTipo['error_id'] . '</td></tr>';
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