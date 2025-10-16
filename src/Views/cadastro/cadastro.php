<?php

use App\Controllers\CadastroController;
use App\Controllers\GabineteController;

?>


<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="p-4" style="min-width: 300px; max-width: 480px; width: 100%; background: transparent; margin-top: -200px;">
        <img src="<?php BASE_URL ?>/img/logo_white.png" class="card-img-top mx-auto d-block mb-3" alt="Logo" style="width: 100px; height: auto;">

        <h4 class="text-center mb-2 text-white">Mandato Digital</h4>
        <p class="card-text text-center text-white">Cadastre seu gabinete</p>

        <?php

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_salvar'])) {

            if ($_POST['usuario_senha'] == $_POST['usuario_senha2']) {

                $dadosGabinete = [
                    'nome' => $_POST['gabinete_nome'],
                    'estado' => $_POST['gabinete_estado'],
                    'tipo_gabinete_id' => $_POST['gabinete_tipo'],
                ];

                $dadosUsuarios = [
                    'nome' => $_POST['usuario_nome'],
                    'email' => $_POST['usuario_email'],
                    'senha' => $_POST['usuario_senha'],
                    'telefone' => $_POST['usuario_telefone']
                ];
                $result = CadastroController::novoCadastro($dadosGabinete, $dadosUsuarios);

                if ($result['status'] == 'duplicated' && $result['type'] == 'usuario') {
                    echo '<div class="alert alert-info rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Usuário já cadastrado</div>';
                } else if ($result['status'] == 'duplicated' && $result['type'] == 'gabinete') {
                    echo '<div class="alert alert-info rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Gabinete já cadastrado</div>';
                } else if ($result['status'] == 'server_error') {
                    echo '<div class="alert alert-danger rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Erro interno do servidor | ' . $result['error_id'] . '</div>';
                } else if ($result['status'] == 'success') {
                    echo '<div class="alert alert-success rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Cadastro feito com sucesso!</div>';
                }
            } else {
                echo '<div class="alert alert-danger rounded-pill px-4 py-2 custom-alert mb-3" data-timeout="3" role="alert">Senhas não conferem.</div>';
            }
        }
        ?>


        <form class="row g-2" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-12 mb-0">
                <input type="text" class="form-control rounded-pill px-4 py-2" name="usuario_nome" placeholder="Nome" required>
            </div>
            <div class="col-12 mb-0">
                <input type="email" class="form-control rounded-pill px-4 py-2" name="usuario_email" placeholder="Email" required>
            </div>
            <div class="col-md-6 col-6 mb-0">
                <input type="password" class="form-control rounded-pill px-4 py-2" name="usuario_senha" placeholder="Senha" required>
            </div>
            <div class="col-md-6 col-6 mb-0">
                <input type="password" class="form-control rounded-pill px-4 py-2" name="usuario_senha2" placeholder="Confirma a senha">
            </div>
            <div class="col-md-12 col-12 mb-0">
                <input type="text" class="form-control rounded-pill px-4 py-2" name="usuario_telefone" placeholder="Telefone (com DDD)" data-mask="(00) 00000-0000" maxlength="15" required>
            </div>
            <div class="col-md-6 col-6 mb-0">
                <select class="form-select rounded-pill px-4 py-2 form-select-custom" name="gabinete_tipo">
                    <option selected>Tipo do Gabinete</option>
                    <?php
                    $buscaTipo = GabineteController::listarTiposGabinetes();
                    if ($buscaTipo['status'] == 'success') {
                        foreach ($buscaTipo['data'] as $tipo) {
                            echo '<option value="' . $tipo['id'] . '">' . $tipo['nome'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 col-6 mb-0">
                <select class="form-select rounded-pill px-4 py-2 form-select-custom" id="estado" name="gabinete_estado">
                    <option selected>Escolha o estado</option>
                </select>
            </div>
            <div class="col-12 mb-2">
                <input type="text" class="form-control rounded-pill px-4 py-2" name="gabinete_nome" placeholder="Nome do deputado, senador...">
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
                <button type="submit" name="btn_salvar" class="btn rounded-pill px-3 py-2 w-50 me-2 confirm-action" data-message="Todos os dados estão corretos?" style="background-color: #00265eff; color: #fff; border: none;">
                    <i class="bi bi-floppy-fill"></i> Salvar
                </button>
                <a href="?secao=login" class="btn rounded-pill px-3 py-2 w-50 ms-2 btn-secondary" style="background-color: #09872fff; color: #fff; border: none;"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>
        </form>
        <p class="card-text text-center text-white copyright">© <?php echo date('Y'); ?> | Just Solutions. Todos os direitos reservados.</p>
    </div>
</div>