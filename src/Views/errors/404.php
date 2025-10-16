<?php include('../src/Views/includes/verificaLogado.php'); ?>

<div class="d-flex" id="wrapper">
    <?php include('../src/Views/base/sidebar.php'); ?>
    <div id="page-content-wrapper">
        <?php include('../src/Views/base/top_menu.php') ?>
        <div class="container-fluid">
            <div class="container vh-100 d-flex justify-content-center align-items-center bg-transparent">
                <div class="card text-center shadow-lg border-0 p-4 rounded-4" style="max-width: 480px;">
                    <h3 class="fw-bold text-warning mb-2">404</h3>
                    <h5 class="mb-3">Não encontrado</h5>
                    <p class="text-muted mb-4">
                        A secão que você está procurando não existe.
                    </p>
                    <a href="?secao=home" class="btn btn-warning btn-sm w-100 rounded-3">
                        Voltar para o início
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>