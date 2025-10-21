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