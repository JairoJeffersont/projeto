<?php

use App\Controllers\DocumentoController;
use App\Controllers\OrgaoController;
use App\Controllers\PessoaController;

include('../src/Views/includes/verificaLogado.php');

$id = $_GET['id'] ?: '';

$buscaOrgao = OrgaoController::buscarOrgao($id);

if ($buscaOrgao['status'] != 'success') {
    header('location: ?secao=orgaos');
}

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body custom-card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao loading-modal" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao loading-modal" href="?secao=orgaos" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body card-profile py-1 px-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-1"> <?= $buscaOrgao['data']['nome'] ?></h3>
                            <p class="mb-0"><strong>Email:</strong> <?php echo !empty($buscaOrgao['data']['email']) ? $buscaOrgao['data']['email'] : 'Não informado' ?></p>
                            <p class="mb-2"><strong>Telefone:</strong> <?php echo !empty($buscaOrgao['data']['telefone']) ? $buscaOrgao['data']['telefone'] : 'Não informado' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'nome'        => $_POST['nome'],
                            'email'       => $_POST['email'],
                            'telefone'    => $_POST['telefone'],
                            'estado'      => $_POST['estado'],
                            'cidade'      => $_POST['cidade'],
                            'tipo_id'     => $_POST['tipo'],
                            'site'        => $_POST['site'],
                            'instagram'   => $_POST['instagram'],
                            'facebook'    => $_POST['facebook'],
                            'informacoes_adicionais' => $_POST['informacoes']
                        ];

                        $result = OrgaoController::atualizarOrgao($id, $dados);

                        if ($result['status'] == 'conflict') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            $buscaOrgao = OrgaoController::buscarOrgao($id);
                            header('Location: ?secao=orgao&id=' . $id);

                            echo '<div class="alert alert-success px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'forbidden') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_apagar'])) {

                        $result = OrgaoController::apagarOrgao($id);

                        if ($result['status'] == 'not_permitted' || $result['status'] == 'forbidden') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            header('location: ?secao=orgaos');
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }


                    ?>
                    <form class="row g-2 form_custom " id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-5 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome" value="<?php echo $buscaOrgao['data']['nome']; ?>" required>
                        </div>
                        <div class="col-md-4 col-6">
                            <input type="text" class="form-control form-control-sm" name="email" placeholder="Email" value="<?php echo $buscaOrgao['data']['email']; ?>">
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="telefone" value="<?php echo $buscaOrgao['data']['telefone']; ?>" placeholder="Telefone (somente números)" data-mask="(00) 00000-0000" maxlength="15">
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm estado" name="estado" data-selected="<?php echo $buscaOrgao['data']['estado']; ?>" required>
                                <option value="" selected>UF</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm municipio" name="cidade" data-selected="<?php echo $buscaOrgao['data']['cidade']; ?>" required>
                                <option value="" selected>Município</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-12">
                            <div class="input-group input-group-sm">
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="1">Sem tipo definido</option>
                                    <?php
                                    $buscaTipo = OrgaoController::listarTiposOrgaos($_SESSION['usuario']['gabinete_id']);
                                    foreach ($buscaTipo['data'] as $t) {
                                        if ($buscaOrgao['data']['tipo_id'] == $t['id']) {
                                            echo '<option value="' . $t['id'] . '" selected>' . $t['nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $t['id'] . '">' . $t['nome'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <a href="?secao=tipos-orgaos" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir um novo tipo de órgão?" title="Gerenciar Tipos de Órgãos">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="site" value="<?php echo $buscaOrgao['data']['site']; ?>" placeholder="Site">
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="instagram" value="<?php echo $buscaOrgao['data']['instagram']; ?>" placeholder="Instagram">
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="facebook" value="<?php echo $buscaOrgao['data']['facebook']; ?>" placeholder="Facebook">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes desse órgão"><?php echo $buscaOrgao['data']['informacoes_adicionais']; ?></textarea>
                        </div>
                        <div class="col-md-4 col-6">
                            <button type="submit" class="btn btn-success btn-sm confirm-action" data-message="Tem certeza que deseja atualizar esse órgão?" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm confirm-action" data-message="Tem certeza que deseja apagar esse órgão?" name="btn_apagar"><i class="bi bi-floppy-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-header custom-card-header-no-bg  bg-primary px-2 py-1 text-white">
                    Pessoas desse órgão/entidade
                </div>
                <div class="card-body custom-card-body p-1">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">UF/Município</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscaPessoas = PessoaController::listarPessoas($_SESSION['usuario']['gabinete_id'], 'asc', 'nome', 1000, 1, '', '', '', $buscaOrgao['data']['id']);
                                if ($buscaPessoas['status'] == 'success') {
                                    foreach ($buscaPessoas['data'] as $pessoa) {
                                        $tipo = PessoaController::buscarTipoPessoa($pessoa['tipo_id']);
                                        $nomeTipo = ($tipo['status'] === 'success') ? $tipo['data']['nome'] : 'Sem tipo definido';
                                        echo '<tr>
                                                    <td><a href="?secao=pessoa&id=' . $pessoa['id'] . '">' . $pessoa['nome'] . '</a></td>
                                                    <td>' . $nomeTipo . '</td>
                                                    <td>' . $pessoa['cidade'] . '/' . $pessoa['estado'] . '</td>
                                                  <tr>
                                            ';
                                    }
                                } else if ($buscaPessoas['status'] == 'empty') {
                                    echo '<tr><td colspan="4">' . $buscaPessoas['message'] . '</td></tr>';
                                } else if ($buscaPessoas['status'] == 'server_error') {
                                    echo '<tr><td colspan="4">' . $buscaPessoas['message'] . ' | ' . $buscaPessoas['error_id'] . '</td></tr>';
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-2 ">
                <div class="card-header custom-card-header-no-bg  bg-secondary px-2 py-1 text-white">
                    Documentos desse órgão/entidade
                </div>
                <div class="card-body custom-card-body p-1">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $buscarDoc = DocumentoController::listarDocumentos($_SESSION['usuario']['gabinete_id'], null, null, null, $buscaOrgao['data']['id']);
                                if ($buscarDoc['status'] == 'success') {
                                    foreach ($buscarDoc['data'] as $doc) {
                                        echo '<tr>
                                                <td><a href="?secao=documento&id=' . $doc['id'] . '">' . $doc['titulo'] . '</a></td>
                                              <tr>
                                            ';
                                    }
                                } else if ($buscarDoc['status'] == 'empty') {
                                    echo '<tr><td colspan="4">' . $buscarDoc['message'] . '</td></tr>';
                                } else if ($buscarDoc['status'] == 'server_error') {
                                    echo '<tr><td colspan="4">' . $buscarDoc['message'] . ' | ' . $buscarDoc['error_id'] . '</td></tr>';
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