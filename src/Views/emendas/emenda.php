<?php

use App\Controllers\EmendaController;
use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

$id = $_GET['id'] ?? '';
$buscaEmenda = EmendaController::buscarEmenda($id);

if ($buscaEmenda['status'] != 'success') {
    header('location: ?secao=emendas');
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
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=emendas" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header custom-card-header px-2 py-1 text-white">
                    Editar emenda
                </div>

                <div class="card-body custom-card-body p-2">

                    <?php

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {
                        $dadosEmenda = [

                            'descricao' => $_POST['descricao'],
                            'valor' => str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor']),
                            'estado' => $_POST['estado'],
                            'cidade' => $_POST['cidade'],
                            'tipo_id' => $_POST['tipo'],
                            'area_id' => $_POST['area'],
                            'situacao_id' => $_POST['situacao'],
                            'informacoes' => $_POST['informacoes']
                        ];

                        $result = EmendaController::atualizarEmenda($id, $dadosEmenda);

                        if ($result['status'] == 'success') {
                            $buscaEmenda = EmendaController::buscarEmenda($id);
                            echo '<div class="alert alert-success px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_apagar'])) {

                        $result = EmendaController::apagarEmenda($id);



                        if ($result['status'] == 'success') {
                            header('location: ?secao=emendas');
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }

                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-1 col-3">
                            <input type="text" class="form-control form-control-sm" name="ano" data-mask="0000" placeholder="Ano" value="<?php echo $buscaEmenda['data']['ano'] ?>" disabled>
                        </div>
                        <div class="col-md-1 col-9">
                            <input type="text" class="form-control form-control-sm" name="numero" data-mask="000000000" value="<?php echo $buscaEmenda['data']['numero'] ?>" placeholder="Número da emenda" disabled>
                        </div>
                        <div class="col-md-10 col-12">
                            <input type="text" class="form-control form-control-sm" name="descricao" value="<?php echo $buscaEmenda['data']['descricao'] ?>" placeholder="Descricao simplificada (objeto, projeto...)" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="valor" placeholder="Valor (R$)" value="<?php echo $buscaEmenda['data']['valor'] ?>" required>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm estado" name="estado" data-selected="<?php echo $buscaEmenda['data']['estado'] ?>" required>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm municipio" name="cidade" data-selected="<?php echo $buscaEmenda['data']['cidade'] ?>">
                                <option value="">Selecione o município</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="input-group input-group-sm">
                                <select class="form-select form-select-sm" name="tipo" required>
                                    <?php
                                    $buscaTipo = EmendaController::listarTiposdeEmendas($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaTipo['status'] == 'success') {
                                        foreach ($buscaTipo['data'] as $tipo) {
                                            $selected = ($buscaEmenda['data']['tipo_id'] == $tipo['id']) ? 'selected' : '';
                                            echo '<option value="' . $tipo['id'] . '" ' . $selected . '>' . $tipo['nome'] . '</option>';
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
                                    <option value="1" <?= ($buscaEmenda['data']['area_id'] == 1 ? 'selected' : '') ?>>Área não definida</option>
                                    <?php
                                    $buscaArea = EmendaController::listarAreasDeEmendas($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaArea['status'] == 'success') {
                                        foreach ($buscaArea['data'] as $area) {
                                            $selected = ($buscaEmenda['data']['area_id'] == $area['id']) ? 'selected' : '';
                                            echo '<option value="' . $area['id'] . '" ' . $selected . '>' . $area['nome'] . '</option>';
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
                                    <?php
                                    $buscaSituacao = EmendaController::listarSituacoesdeEmendas($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaSituacao['status'] == 'success') {
                                        foreach ($buscaSituacao['data'] as $situacao) {
                                            $selected = ($buscaEmenda['data']['situacao_id'] == $situacao['id']) ? 'selected' : '';
                                            echo '<option value="' . $situacao['id'] . '" ' . $selected . '>' . $situacao['nome'] . '</option>';
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
                            <textarea class="form-control form-control-sm" id="tinymce" name="informacoes" rows="5" placeholder="Informações importantes dessa emenda"><?php echo $buscaEmenda['data']['informacoes'] ?></textarea>
                        </div>
                        <div class="col-md-1 col-12">
                            <button type="submit" class="btn btn-success btn-sm confirm-action" data-message="Tem certeza que deseja atualizar essa emenda?" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm confirm-action" data-message="Tem certeza que deseja apagar essa emenda?" name="btn_apagar"><i class="bi bi-floppy-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>