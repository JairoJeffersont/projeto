<?php

use App\Controllers\EmendaController;

include('../src/Views/includes/verificaLogado.php');

$id = $_GET['id'] ?? '';
$buscaArea = EmendaController::buscarAreaDeEmenda($id);

if ($buscaArea['status'] != 'success') {
    header('location: ?secao=areas-emendas');
    exit;
}

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body custom-card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button">
                        <i class="bi bi-house-door-fill"></i> Início
                    </a>
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=areas-emendas" role="button">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header custom-card-header px-2 py-1 text-white">
                    Editar Área de Emenda
                </div>

                <div class="card-body custom-card-body p-2">
                    <?php
                    // Atualizar registro
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {
                        $dados = [
                            'nome' => $_POST['nome']
                        ];

                        $result = EmendaController::atualizarAreaDeEmenda($id, $dados);

                        if ($result['status'] == 'conflict') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            $buscaArea = EmendaController::buscarAreaDeEmenda($id);
                            echo '<div class="alert alert-success px-2 py-1 custom-alert mb-2" data-timeout="3">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }

                    // Apagar registro
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_apagar'])) {
                        $result = EmendaController::apagarAreaDeEmenda($id);

                        if ($result['status'] == 'not_permitted') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            header('location: ?secao=areas-emendas');
                            exit;
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom" id="form_editar" method="POST">
                        <div class="col-md-3 col-12">
                            <input type="text"
                                class="form-control form-control-sm"
                                name="nome"
                                placeholder="Nome da Área"
                                value="<?php echo htmlspecialchars($buscaArea['data']['nome']); ?>"
                                required>
                        </div>
                        <div class="col-md-9 col-12">
                            <button type="submit"
                                class="btn btn-success btn-sm confirm-action"
                                data-message="Tem certeza que deseja atualizar esta área de emenda?"
                                name="btn_salvar">
                                <i class="bi bi-floppy-fill"></i> Salvar
                            </button>
                            <button type="submit"
                                class="btn btn-danger btn-sm confirm-action"
                                data-message="Tem certeza que deseja apagar esta área de emenda?"
                                name="btn_apagar">
                                <i class="bi bi-trash-fill"></i> Apagar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>