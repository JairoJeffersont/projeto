<?php

use App\Controllers\PessoaController;

include('../src/Views/includes/verificaLogado.php');

$id = $_GET['id'];
$buscaProfissao = PessoaController::buscarProfissao($id);

if ($buscaProfissao['status'] != 'success') {
    header('location: ?secao=profissoes');
}

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-header custom-card-header px-2 py-1 text-white">
                    Editar profissão
                </div>
                <div class="card-body custom-card-body p-2">
                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'nome' => $_POST['nome']
                        ];

                        $result = PessoaController::atualizarProfissao($id, $dados);

                        if ($result['status'] == 'conflict') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            $buscaProfissao = PessoaController::buscarProfissao($id);
                            echo '<div class="alert alert-success px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_apagar'])) {

                        $result = PessoaController::apagarProfissao($id);

                        if ($result['status'] == 'not_permitted') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            header('location: ?secao=profissoes');
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }

                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Profissão" value="<?php echo $buscaProfissao['data']['nome'] ?>" required>
                        </div>
                        <div class="col-md-10 col-12">
                            <button type="submit" class="btn btn-success btn-sm confirm-action" data-message="Tem certeza que deseja inserir essa profissão?" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm confirm-action" data-message="Tem certeza que deseja apagar essa profissão?" name="btn_apagar"><i class="bi bi-floppy-fill"></i> Apagar</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>