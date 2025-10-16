<?php



$sessionHelper = new \App\Helpers\SessionHelper();

$verificaSessao = $sessionHelper::validarSessao();

if (!$verificaSessao) {
    header('Location: ?secao=login');
}
