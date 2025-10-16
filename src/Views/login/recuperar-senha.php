    <?php

    use App\Controllers\LoginController;
    ?>


    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="p-4" style="min-width: 300px; max-width: 430px; width: 100%; background: transparent; margin-top: -200px;">
            <img src="<?php BASE_URL ?>/img/logo_white.png" class="card-img-top mx-auto d-block mb-3" alt="Logo" style="width: 100px; height: auto;">

            <h4 class="text-center mb-2 text-white">Mandato Digital</h4>
            <p class="card-text text-center text-white"><b>Recuperar senha</b></p>
            <?php


            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_enviar'])) {
                $result = LoginController::recuperarSenha($_POST['email']);

                if ($result['status'] == 'success') {
                    echo '<div class="alert alert-success rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Email de recuperação enviado com sucesso</div>';
                } else if ($result['status'] == 'not_found') {
                    echo '<div class="alert alert-primary rounded-pill px-4 py-2 custom-alert mb-2" data-timeout="3" role="alert">Usuário não encontrado!</div>';
                } else {
                    echo '<div class="alert alert-danger rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Erro interno do servidor | ' . $result['error_id'] . '</div>';
                }
            }

            ?>

            <form method="post" class="mb-3" enctype="application/x-www-form-urlencoded">
                <div class="mb-2">
                    <input type="email" class="form-control rounded-pill px-4 py-2" name="email" value="jairojeffersont@gmail.com" placeholder="E-mail" required>
                </div>

                <div class="d-flex">
                    <a href="?secao=login" class="btn rounded-pill px-3 py-2 w-50 me-2"
                        style="background-color: #063a68ff; color: #fff; border: none;">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>

                    <button type="submit" class="btn rounded-pill px-3 py-2 w-50 confirm-action" data-message="Deseja enviar o email de recuperação de senha?" name="btn_enviar"
                        style="background-color: #0da02aff; color: #fff; border: none;">
                        <i class="bi bi-forward-fill"></i> Enviar senha
                    </button>
                </div>
            </form>

            <p class="card-text text-center text-white copyright">© <?php echo date('Y'); ?> | Just Solutions. Todos os direitos reservados.</p>


        </div>
    </div>