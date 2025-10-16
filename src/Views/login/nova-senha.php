
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="p-4" style="min-width: 300px; max-width: 430px; width: 100%; background: transparent; margin-top: -200px;">
            <img src="<?php BASE_URL ?>/img/logo_white.png" class="card-img-top mx-auto d-block mb-3" alt="Logo" style="width: 100px; height: auto;">

            <h4 class="text-center mb-2 text-white">Mandato Digital</h4>
            <p class="card-text text-center text-white"><b>Criar nova senha</b></p>
            <?php

            use App\Controllers\LoginController;

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {

                $token = $_GET['token'];

                $result = LoginController::novaSenha($token, $_POST['senha']);

                if ($result['status'] == 'success') {
                    echo '<div class="alert alert-success rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Senha atualizada com sucesso!</div>';
                } else if ($result['status'] == 'not_found') {
                    echo '<div class="alert alert-info rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Token inválido ou não enviado</div>';
                } else if ($result['status'] == 'server_error') {
                    echo '<div class="alert alert-danger rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Erro interno do servidor | ' . $result['error_id'] . '</div>';
                }
            }

            ?>

            <form method="post" class="mb-3" enctype="application/x-www-form-urlencoded">
                <div class="mb-2">
                    <input type="password" class="form-control rounded-pill px-4 py-2" name="senha" placeholder="Nova senha" required>
                </div>

                <div class="d-flex">
                    <a href="?secao-login" class="btn rounded-pill px-3 py-2 w-50 me-2"
                        style="background-color: #063a68ff; color: #fff; border: none;">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>

                    <button type="submit" class="btn rounded-pill px-3 py-2 w-50  confirm-action" data-message="Deseja salvar essa senha?" name="btn_salvar"
                        style="background-color: #0da02aff; color: #fff; border: none;">
                        <i class="bi bi-floppy-fill"></i> Salvar
                    </button>
                </div>
            </form>

            <p class="card-text text-center text-white copyright">© <?php echo date('Y'); ?> | Just Solutions. Todos os direitos reservados.</p>


        </div>
    </div>