<?php

use App\Controllers\GabineteController;

include('../src/Views/includes/verificaLogado.php');

$buscaGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id'])['data'];

if ($buscaGabinete['tipo_gabinete_id'] == '3') {
    include 'proposicoesCD.php';
}
