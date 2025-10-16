<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="p-4" style="min-width: 300px; max-width: 430px; width: 100%; background: transparent; margin-top: -200px;">
        <img src="<?php BASE_URL ?>/img/logo_white.png" class="card-img-top mx-auto d-block mb-3" alt="Logo" style="width: 100px; height: auto;">

        <h4 class="text-center mb-2 text-white">Mandato Digital</h4>
        <p class="card-text text-center text-white">Gestão de gabinete</p>
        <?php

        $loginController = new \App\Controllers\LoginController();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_logar'])) {

            $dados = [
                'login' => $_POST['login'],
                'senha' => $_POST['senha'],
            ];

            $result = $loginController->Logar($dados);

            if ($result['status'] == 'not_found') {
                echo '<div class="alert alert-primary rounded-pill px-4 py-2 custom-alert mb-2" data-timeout="3" role="alert">Usuário não encontrado!</div>';
            } else if ($result['status'] == 'wrong_password') {
                echo '<div class="alert alert-danger rounded-pill px-4 py-2 custom-alert mb-2" data-timeout="3" role="alert">Senha incorreta!</div>';
            } else if ($result['status'] == 'deactived') {
                echo '<div class="alert alert-info rounded-pill px-4 py-2 custom-alert mb-2" data-timeout="3" role="alert">Usuário ou gabinete desativado! Contate o gestor do sistema.</div>';
            } else if ($result['status'] == 'server_error') {
                echo '<div class="alert alert-danger rounded-pill px-4 py-2 custom-alert mb-2" data-timeout="3" role="alert">Erro interno do servidor | ' . $result['error_id'] . '</div>';
            } else if ($result['status'] == 'success') {
                header('location: ?secao=home');
            }
        }

        ?>

        <form method="post" class="mb-3" enctype="application/x-www-form-urlencoded">
            <div class="mb-2">
                <input type="text" class="form-control rounded-pill px-4 py-2" name="login" value="jairojeffersont@gmail.com" placeholder="E-mail ou telefone" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control rounded-pill px-4 py-2" name="senha" value="intell01" placeholder="Senha" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn rounded-pill px-3 py-2" name="btn_logar" style="background-color: #00265eff; color: #fff; border: none;">
                    <i class="bi bi-door-open-fill"></i> Entrar
                </button>
            </div>
        </form>

        <div class="d-flex justify-content-center mt-3 mb-3">
            <p class="card-text text-white mb-0 me-3" style="cursor: pointer;"><a href="?secao=recuperar-senha" style="color: white;"> Esqueci minha senha</a></p>
            <p class="card-text text-white mb-0" style="cursor: pointer;"><a href="?secao=cadastro" style="color: white;"> Cadastre seu gabinete</a></p>
        </div>

        <p class="card-text text-center text-white copyright">© <?php echo date('Y'); ?> | Just Solutions. Todos os direitos reservados.</p>


    </div>
</div>