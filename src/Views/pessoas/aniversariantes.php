<?php include('../src/Views/includes/verificaLogado.php');

use App\Controllers\PessoaController;
use App\Controllers\GabineteController;

$estadoGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id'])['data']['estado'];

$flag = $_GET['flag'] ?? 'dia';
$estado = $_GET['estado'] ?? $estadoGabinete;

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
                    Aniversariantes
                </div>
                <div class="card-body custom-card-body p-2">
                    <p class="card-text mb-0">Nesta seção, é possível ver todos os aniversariantes do dia ou do mês.</p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <form class="row g-2 form_custom mb-0" id="form_busca" method="GET" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-1 col-5">
                            <input type="hidden" name="secao" value="aniversariantes" />
                            <select class="form-select form-select-sm estado" data-selected="<?php echo $estado; ?>" name="estado">
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="flag">
                                <option value="dia" <?= ($flag == 'dia') ? 'selected' : '' ?>>Aniversariantes do dia</option>
                                <option value="mes" <?= ($flag == 'mes') ? 'selected' : '' ?>>Aniversariantes do mês</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-1">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body custom-card-body p-2">
                    <ul class="list-group">
                        <?php

                        $buscaAniversariantes = PessoaController::aniversariantes($_SESSION['usuario']['gabinete_id'], $flag, $estado);

                        if ($buscaAniversariantes['status'] == 'success') {
                            foreach ($buscaAniversariantes['data'] as $aniversario) {
                                echo '<li class="list-group-item d-flex align-items-center">
                                         <img src="' . (!empty($aniversario['foto']) ? $aniversario['foto'] : '/img/not_found.jpg') . '" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                         <div>
                                             <strong><a href="?secao=pessoa&id=' . $aniversario['id'] . '">' . $aniversario['nome'] . ' - ' . $aniversario['data_nascimento'] . '</a></strong><br>
                                             <span class="text-muted">' . $aniversario['email'] . '</span><br>
                                             <span>' . $aniversario['telefone'] . '</span>
                                         </div>
                                     </li>';
                            }
                        } else {
                            echo '<li class="list-group-item text-start text-muted">Nenhum aniversariante encontrado.</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>