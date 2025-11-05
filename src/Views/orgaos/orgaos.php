<?php

use App\Controllers\GabineteController;
use App\Controllers\OrgaoController;
use App\Controllers\UsuarioController;

include('../src/Views/includes/verificaLogado.php');

$buscaGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id'])['data']['estado'];

$itens = $_GET['itens'] ?? 10;
$pagina = $_GET['pagina'] ?? 1;
$ordem = $_GET['ordem'] ?? 'ASC';
$ordenarPor = $_GET['ordenarPor'] ?? 'nome';
$estado = $_GET['estado'] ?? $buscaGabinete;
$cidade = $_GET['cidade'] ?? '';
$tipoGet = $_GET['tipo'] ?? '';
$busca = $_GET['busca'] ?? '';

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body custom-card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header custom-card-header px-2 py-1 text-white">
                    Órgãos/entidades
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar os tipos de órgãos e entidades, garantindo a organização correta dessas informações no sistema.</p>

                    <p class="card-text mb-0">Os campos <b>nome, estado e muncípio</b> são <b>obrigatórios</b>.</p>
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
                            'tipo_id'        => $_POST['tipo'],
                            'site'        => $_POST['site'],
                            'instagram'   => $_POST['instagram'],
                            'facebook'    => $_POST['facebook'],
                            'informacoes_adicionais' => $_POST['informacoes'],
                            'gabinete_id' => $_SESSION['usuario']['gabinete_id'],
                            'usuario_id' => $_SESSION['usuario']['id'],
                        ];

                        $result = OrgaoController::novoOrgao($dados);

                        if ($result['status'] == 'conflict') {
                            echo '<div class="alert alert-info px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'server_error') {
                            echo '<div class="alert alert-danger px-2 py-1 custom-alert mb-2" data-timeout="3" role="alert">' . $result['message'] . ' | ' . $result['error_id'] . '</div>';
                        }
                    }


                    ?>
                    <form class="row g-2 form_custom " id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-5 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome" required>
                        </div>
                        <div class="col-md-4 col-6">
                            <input type="text" class="form-control form-control-sm" name="email" placeholder="Email">
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="telefone" placeholder="Telefone (somente números)" data-mask="(00) 00000-0000" maxlength="15">
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm estado" name="estado" required>
                                <option value="" selected>UF</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm municipio" name="cidade" required>
                                <option value="" selected>Município</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-12">
                            <div class="input-group input-group-sm">
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="1">Sem tipo definido</option>
                                    <?php
                                    $buscaTipo = OrgaoController::listarTiposOrgaos($_SESSION['usuario']['gabinete_id']);
                                    if ($buscaTipo['status'] == 'success') {
                                        foreach ($buscaTipo['data'] as $t) {
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
                            <input type="text" class="form-control form-control-sm" name="site" placeholder="Site">
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="instagram" placeholder="Instagram">
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="facebook" placeholder="Facebook">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes desse órgão"></textarea>
                        </div>
                        <div class="col-md-4 col-6">
                            <button type="submit" class="btn btn-success btn-sm confirm-action" data-message="Tem certeza que deseja inserir esse órgão?" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <form class="row g-2 form_custom mb-0" id="form_busca" method="GET" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-2 col-6">
                            <input type="hidden" name="secao" value="orgaos" />
                            <select class="form-select form-select-sm" name="ordenarPor" required>
                                <option value="nome" <?= ($ordenarPor == 'nome') ? 'selected' : '' ?>>Ordenar por | Nome</option>
                                <option value="estado" <?= ($ordenarPor == 'estado') ? 'selected' : '' ?>>Ordenar por | Estado</option>
                                <option value="cidade" <?= ($ordenarPor == 'cidade') ? 'selected' : '' ?>>Ordenar por | Cidade</option>
                                <option value="tipo_id" <?= ($ordenarPor == 'tipo_id') ? 'selected' : '' ?>>Ordenar por | Tipo</option>
                                <option value="created_at" <?= ($ordenarPor == 'created_at') ? 'selected' : '' ?>>Ordenar por | Criação</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="ordem" required>
                                <option value="ASC" <?= ($ordem == 'ASC') ? 'selected' : '' ?>>Ordem Crescente</option>
                                <option value="DESC" <?= ($ordem == 'DESC') ? 'selected' : '' ?>>Ordem Decrescente</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="itens" required>
                                <option value="5" <?= ($itens == 5) ? 'selected' : '' ?>>5 itens</option>
                                <option value="10" <?= ($itens == 10) ? 'selected' : '' ?>>10 itens</option>
                                <option value="25" <?= ($itens == 25) ? 'selected' : '' ?>>25 itens</option>
                                <option value="50" <?= ($itens == 50) ? 'selected' : '' ?>>50 itens</option>
                                <option value="100" <?= ($itens == 100) ? 'selected' : '' ?>>100 itens</option>
                            </select>
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
                            <select class="form-select form-select-sm" name="tipo">
                                <option value="" <?= ($tipoGet == '') ? 'selected' : '' ?>>Todos os tipos</option>
                                <option value="1" <?= ($tipoGet == '1') ? 'selected' : '' ?>>Sem tipo definido</option>
                                <?php

                                if ($buscaTipo['status'] == 'success') {
                                    foreach ($buscaTipo['data'] as $t) {
                                        $sel = ($tipoGet == $t['id']) ? 'selected' : '';
                                        echo '<option value="' . $t['id'] . '" ' . $sel . '>' . $t['nome'] . '</option>';
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-10">
                            <input type="text" class="form-control form-control-sm" name="busca" placeholder="Buscar..." value="<?= htmlspecialchars($busca) ?>">
                        </div>
                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm loading-modal"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <div class="table-responsive mb-2">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">UF/Município</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Criado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $buscaOrgao = OrgaoController::listarOrgaos($_SESSION['usuario']['gabinete_id'], $ordem, $ordenarPor, $itens, $pagina, $estado, $cidade, $tipoGet, $busca);

                                if ($buscaOrgao['status'] == 'success') {
                                    foreach ($buscaOrgao['data'] as $orgao) {
                                        $usuario = UsuarioController::buscarUsuario($orgao['usuario_id'])['data']['nome'];
                                        $tipo = OrgaoController::buscarTipoOrgao($orgao['tipo_id']);

                                        $nomeTipo = ($tipo['status'] === 'success') ? $tipo['data']['nome'] : 'Sem tipo definido';

                                        echo '<tr>
                                                <td nowrap><a href="?secao=orgao&id=' . $orgao['id'] . '">' . $orgao['nome'] . '</a></td>
                                                <td nowrap>' . $orgao['cidade'] . '/' . $orgao['estado'] . '</td>
                                                <td nowrap>' . $nomeTipo . '</td>
                                                <td nowrap>' . $usuario . ' | ' . date('d/m - H:i', strtotime($orgao['created_at'])) . '</td>
                                            </tr>';
                                    }
                                } else if ($buscaOrgao['status'] == 'empty') {
                                    echo '<tr><td colspan="4">' . $buscaOrgao['message'] . '</td></tr>';
                                } else if ($buscaOrgao['status'] == 'server_error') {
                                    echo '<tr><td colspan="4">' . $buscaOrgao['message'] . ' | ' . $buscaOrgao['error_id'] . '</td></tr>';
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                    <?php
                    $total_paginas = $buscaOrgao['total_pagina'] ?? 0;

                    $max_links = 5;

                    $start = max(1, $pagina - floor($max_links / 2));
                    $end = min($total_paginas, $start + $max_links - 1);

                    $start = max(1, $end - $max_links + 1);

                    ?>
                    <ul class="pagination mb-0 custom-pagination">
                        <!-- Primeiro -->
                        <li class="page-item <?php if ($pagina == 1) echo 'disabled'; ?>">
                            <a class="page-link loading-modal" href="?secao=orgaos&pagina=1&itens=<?= $itens ?>&ordem=<?= $ordem ?>&ordenarPor=<?= $ordenarPor ?>&estado=<?= $estado ?>&cidade=<?= $cidade ?>&tipo=<?= $tipoGet ?>&busca=<?= urlencode($busca) ?>">Primeiro</a>
                        </li>

                        <!-- Números de página -->
                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?php if ($pagina == $i) echo 'active'; ?>">
                                <a class="page-link loading-modal" href="?secao=orgaos&pagina=<?= $i ?>&itens=<?= $itens ?>&ordem=<?= $ordem ?>&ordenarPor=<?= $ordenarPor ?>&estado=<?= $estado ?>&cidade=<?= $cidade ?>&tipo=<?= $tipoGet ?>&busca=<?= urlencode($busca) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Último -->
                        <li class="page-item <?php if ($pagina == $total_paginas) echo 'disabled'; ?>">
                            <a class="page-link loading-modal" href="?secao=orgaos&pagina=<?= $total_paginas ?>&itens=<?= $itens ?>&ordem=<?= $ordem ?>&ordenarPor=<?= $ordenarPor ?>&estado=<?= $estado ?>&cidade=<?= $cidade ?>&tipo=<?= $tipoGet ?>&busca=<?= urlencode($busca) ?>">Último</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>