<?php

use App\Helpers\GetData;
use App\Controllers\GabineteController;


include('../src/Views/includes/verificaLogado.php');

$buscaGabinete = GabineteController::buscarGabinete($_SESSION['usuario']['gabinete_id']);

$legislatura = '57';
$estado = $buscaGabinete['data']['estado'];
$partido = $buscaGabinete['data']['partido'];

$params = [
    'ordem' => 'ASC',
    'ordenarPor' => 'nome',
    'idLegislatura' => $legislatura
];

if (!empty($estado)) $params['siglaUf'] = $estado;
if (!empty($partido)) $params['siglaPartido'] = $partido;

$url = 'https://dadosabertos.camara.leg.br/api/v2/deputados?' . http_build_query($params);

$buscaDep = GetData::getJson($url);

print_r($buscaDep);


?>


<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid">
            home
        </div>
    </div>
</div>