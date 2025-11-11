<?php

use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

if($_SESSION['usuario']['tipo_usuario_id'] != '1'){
    header('Location: ?secao=home');
}

$buscaGabinetes = GabineteController::listarGabinetes();


?>


<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid">
            home admin
        </div>
    </div>
</div>