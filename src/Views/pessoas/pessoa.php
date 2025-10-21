<?php

use App\Controllers\OrgaoController;
use App\Controllers\PessoaController;
use App\Helpers\Slugfy;

include('../src/Views/includes/verificaLogado.php');

$id = $_GET['id'] ?: '';
$buscaPessoa = PessoaController::buscarPessoa($id);

if ($buscaPessoa['status'] != 'success') {
    header('location: ?secao=pessoas');
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
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao loading-modal" href="?secao=pessoas" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body card-profile p-2">
                    <div class="row align-items-center">

                        <!-- FOTO -->
                        <div class="col-auto">
                            <div class="foto-perfil">
                                <img src="<?php echo !empty($buscaPessoa['data']['foto']) ? $buscaPessoa['data']['foto'] : '/img/not_found.jpg' ?>" alt="Foto de Perfil" class="img-fluid rounded">
                            </div>
                        </div>

                        <!-- INFORMAÇÕES -->
                        <div class="col">
                            <h3 class="card-title mb-1"> <?= $buscaPessoa['data']['nome'] ?></h3>
                            <p class="mb-0"><strong>Email:</strong> <?php echo !empty($buscaPessoa['data']['email']) ? $buscaPessoa['data']['email'] : 'Não informado' ?></p>
                            <p class="mb-0"><strong>Telefone:</strong> <?php echo !empty($buscaPessoa['data']['telefone']) ? $buscaPessoa['data']['telefone'] : 'Não informado' ?></p>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-1">
                    <a class="btn btn-danger btn-sm custom-nav barra_navegacao"
                        href="<?php echo !empty($buscaPessoa['data']['instagram']) ? 'https://www.instagram.com/' . Slugfy::slug($buscaPessoa['data']['instagram']) . '/' : '#' ?>"
                        <?php echo !empty($buscaPessoa['data']['instagram']) ? 'target="_blank"' : '' ?>
                        role="button">
                        <i class="bi bi-instagram"></i> Instagram
                    </a>

                    <a class="btn btn-info text-white btn-sm custom-nav barra_navegacao"
                        href="<?php echo !empty($buscaPessoa['data']['facebook']) ? 'https://www.facebook.com/' . Slugfy::slug($buscaPessoa['data']['facebook']) . '/' : '#' ?>"
                        <?php echo !empty($buscaPessoa['data']['facebook']) ? 'target="_blank"' : '' ?>
                        role="button">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>

                    <a class="btn btn-success btn-sm custom-nav barra_navegacao"
                        href="<?php echo !empty($buscaPessoa['data']['telefone']) ? 'https://wa.me/' . preg_replace('/\D/', '', $buscaPessoa['data']['telefone']) : '#' ?>"
                        <?php echo !empty($buscaPessoa['data']['telefone']) ? 'target="_blank"' : '' ?>
                        role="button">
                        <i class="bi bi-whatsapp"></i> Whatsapp
                    </a>
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=imprimir-pessoa" role="button"><i class="bi bi-printer"></i> Imprimir</a>

                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'nome'                   => $_POST['nome'],
                            'email'                  => $_POST['email'],
                            'telefone'               => $_POST['telefone'],
                            'data_nascimento'        => $_POST['aniversario'],
                            'estado'                 => $_POST['estado'],
                            'cidade'                 => $_POST['cidade'],
                            'tipo_id'                => $_POST['tipo'],
                            'profissao'              => $_POST['profissao'],
                            'orgao_id'               => $_POST['orgao'],
                            'partido'                => $_POST['partido'],
                            'importancia'            => $_POST['importancia'],
                            'instagram'              => $_POST['instagram'],
                            'facebook'               => $_POST['facebook'],
                            'sexo'                   => $_POST['sexo'],
                            'informacoes_adicionais' => $_POST['informacoes']
                        ];

                        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                            $dados['foto'] = $_FILES['foto'];
                        }

                        $result = PessoaController::atualizarPessoa($id, $dados);

                        if ($result['status'] == 'conflict') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            header('Location: ?secao=pessoa&id=' . $id);
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_apagar'])) {

                        $result = PessoaController::apagarPessoa($id);

                        if ($result['status'] == 'not_permitted') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            header('location: ?secao=pessoas');
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        } else if ($result['status'] == 'tamanho_maximo_excedido' || $result['status'] == 'formato_nao_permitido') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">Tamanho da foto excedido ou formato não permitido.</div>';
                        }
                    }


                    ?>
                    <form class="row g-2 form_custom " id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-5 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome" value="<?php echo $buscaPessoa['data']['nome'] ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="email" placeholder="Email" value="<?php echo $buscaPessoa['data']['email'] ?>">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="telefone" placeholder="Telefone (somente números)" value="<?php echo $buscaPessoa['data']['telefone'] ?>" data-mask="(00) 00000-0000" maxlength="15">
                        </div>
                        <div class="col-md-1 col-6">
                            <input type="text" class="form-control form-control-sm" name="aniversario" placeholder="Aniversário (dd/mm)" data-mask="00/00" maxlength="5" value="<?php echo $buscaPessoa['data']['data_nascimento'] ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="sexo" data-selected="<?php echo $buscaPessoa['data']['sexo'] ?>" required>
                                <option value="Não informado" <?= ($buscaPessoa['data']['sexo'] == 'Não informado') ? 'selected' : '' ?>>Gênero não informado</option>
                                <option value="Masculino" <?= ($buscaPessoa['data']['sexo'] == 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                                <option value="Feminino" <?= ($buscaPessoa['data']['sexo'] == 'Feminino') ? 'selected' : '' ?>>Feminino</option>
                                <option value="Outro" <?= ($buscaPessoa['data']['sexo'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm estado" name="estado" data-selected="<?php echo $buscaPessoa['data']['estado'] ?>" required>
                                <option value="" selected>UF</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm municipio" name="cidade" data-selected="<?php echo $buscaPessoa['data']['cidade'] ?>" required>
                                <option value="" selected>Município</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm partidos" name="partido" data-selected="<?php echo $buscaPessoa['data']['partido'] ?>">
                                <option value="" selected>Partido não informado</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="instagram" value="<?php echo $buscaPessoa['data']['instagram'] ?>" placeholder="Instagram">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="facebook" value="<?php echo $buscaPessoa['data']['facebook'] ?>" placeholder="Facebook">
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="importancia" required>
                                <option value="Não informado" <?= ($buscaPessoa['data']['importancia'] == 'Não informado') ? 'selected' : '' ?>>Selecione a importância</option>
                                <option value="Baixa" <?= ($buscaPessoa['data']['importancia'] == 'Baixa') ? 'selected' : '' ?>>🟢 Baixa</option>
                                <option value="Media" <?= ($buscaPessoa['data']['importancia'] == 'Media') ? 'selected' : '' ?>>🟡 Média</option>
                                <option value="Alta" <?= ($buscaPessoa['data']['importancia'] == 'Alta') ? 'selected' : '' ?>>🔴 Alta</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-12">
                            <div class="input-group input-group-sm">
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="1">Sem tipo definido</option>
                                    <?php
                                    $buscaTipo = PessoaController::listarTiposPessoas($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaTipo['status'] == 'success') {
                                        foreach ($buscaTipo['data'] as $t) {
                                            if ($buscaPessoa['data']['tipo_id'] == $t['id']) {
                                                echo '<option value="' . $t['id'] . '" selected>' . $t['nome'] . '</option>';
                                            } else {
                                                echo '<option value="' . $t['id'] . '">' . $t['nome'] . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                                <a href="?secao=tipos-pessoas" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir um novo tipo de órgão?" title="Gerenciar Tipos de Órgãos">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2 col-12">
                            <div class="input-group input-group-sm">
                                <select class="form-select" name="profissao" required>
                                    <option value="1">Profissão não informada</option>
                                    <?php
                                    $buscaProfissao = PessoaController::listarProfissoes($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaProfissao['status'] == 'success') {
                                        foreach ($buscaProfissao['data'] as $p) {
                                            if ($buscaPessoa['data']['profissao'] == $p['id']) {
                                                echo '<option value="' . $p['id'] . '" selected>' . $p['nome'] . '</option>';
                                            } else {
                                                echo '<option value="' . $p['id'] . '">' . $p['nome'] . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                                <a href="?secao=profissoes" class="btn btn-primary confirm-action loading-modal" data-message="Tem certeza que deseja inserir uma nova profissão?" title="Gerenciar Tipos de Órgãos">
                                    <i class="bi bi-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2 col-12">
                            <div class="input-group input-group-sm">
                                <select class="form-select" name="orgao" required>
                                    <option value="1">Órgão não informado</option>
                                    <?php
                                    $buscaOrgao = OrgaoController::listarOrgaos($_SESSION['usuario']['gabinete_id'], 'ASC', 'nome', 1000);
                                    if ($buscaOrgao['status'] == 'success') {
                                        foreach ($buscaOrgao['data'] as $o) {
                                            if ($buscaPessoa['data']['profissao'] == $o['id']) {
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
                        <div class="col-md-3 col-12">
                            <input type="file" class="form-control form-control-sm" name="foto">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes dessa pessoa"><?php echo $buscaPessoa['data']['informacoes_adicionais'] ?></textarea>
                        </div>

                        <div class="col-md-4 col-6">
                            <button type="submit" class="btn btn-success btn-sm confirm-action loading-modal" data-message="Tem certeza que deseja atualizar essa pessoa?" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm confirm-action loading-modal" data-message="Tem certeza que deseja apagar essa pessoa?" name="btn_apagar"><i class="bi bi-floppy-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>