<?php

use App\Helpers\GetData;

include('../src/Views/includes/verificaLogado.php');

$id = $_GET['id'] ?? '';

$url = 'https://dadosabertos.camara.leg.br/api/v2/deputados/' . $id;

$buscaDep = GetData::getJson($url);

if (!isset($buscaDep['data']['dados'])) {
    header('location: ?secao=deputados');
}


?>


<div class="d-flex" id="wrapper">
    <?php include '../src/Views/base/sidebar.php'; ?>

    <div id="page-content-wrapper">
        <?php include '../src/Views/base/top_menu.php'  ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body custom-card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao loading-modal" href="?secao=deputados" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
           

            <div class="card mb-2">
                <div class="card-body card-profile py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0"> <?= $buscaDep['data']['dados']['ultimoStatus']['nome'] ?> - <?= $buscaDep['data']['dados']['ultimoStatus']['siglaPartido'] ?>/<?= $buscaDep['data']['dados']['ultimoStatus']['siglaUf'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body p-3">
                    <?php $gabinete = $buscaDep['data']['dados']['ultimoStatus']['gabinete'] ?? []; ?>

                    <p class="mb-1">
                        <i class="bi bi-envelope"></i> | <a href="mailto:<?= $gabinete['email'] ?>"><?= !empty($gabinete['email']) ? $gabinete['email'] : 'Não informado' ?></a>
                    </p>

                    <p class="mb-1">
                        <i class="bi bi-telephone"></i> | <a href="tel:+5561<?= $gabinete['telefone'] ?>"><?= !empty($gabinete['telefone']) ? $gabinete['telefone'] : 'Não informado' ?></a>
                    </p>

                    <p class="mb-0">
                        <i class="bi bi-geo-alt"></i> | <?= !empty($gabinete['predio']) ? 'Anexo ' . $gabinete['predio'] . ' - Gabinete  ' . $gabinete['sala'] : 'Não informado' ?>
                    </p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header custom-card-header-no-bg bg-primary px-2 py-1 text-white">
                    Comissões, secretarias, órgãos...
                </div>
                <div class="card-body custom-card-body p-2">
                    <div class="accordion accordion-flush mb-0" id="accordionFlushExample">
                        <?php
                        $buscaComissoes = GetData::getJson('https://dadosabertos.camara.leg.br/api/v2/deputados/' . $id . '/orgaos?ordem=ASC&ordenarPor=dataInicio');

                        if (isset($buscaComissoes['data']['dados'])) {
                            $comissoesAgrupadas = [];

                            // agrupa por idOrgao
                            foreach ($buscaComissoes['data']['dados'] as $comissao) {
                                $id = $comissao['idOrgao'];
                                if (!isset($comissoesAgrupadas[$id])) {
                                    $comissoesAgrupadas[$id] = [
                                        'siglaOrgao' => $comissao['siglaOrgao'],
                                        'nomePublicacao' => $comissao['nomePublicacao'],
                                        'nomeOrgao' => $comissao['nomeOrgao'],
                                        'funcoes' => []
                                    ];
                                }
                                $comissoesAgrupadas[$id]['funcoes'][] = [
                                    'titulo' => $comissao['titulo'],
                                    'dataInicio' => $comissao['dataInicio'],
                                    'dataFim' => $comissao['dataFim']
                                ];
                            }

                            // exibe no accordion
                            foreach ($comissoesAgrupadas as $idOrgao => $comissao) {
                                $titulo = !empty($comissao['nomePublicacao']) ? $comissao['nomePublicacao'] : $comissao['nomeOrgao'];

                                echo '<div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#flush-collapse' . $idOrgao . '" 
                                                aria-expanded="false" 
                                                aria-controls="flush-collapse' . $idOrgao . '">
                                                ' . $comissao['siglaOrgao'] . ' - ' . $titulo . '
                                            </button>
                                        </h2>
                                        <div id="flush-collapse' . $idOrgao . '" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">';

                                foreach ($comissao['funcoes'] as $funcao) {
                                    echo '<p class="mb-0"><b>' . $funcao['titulo'] . '</b> </p>';
                                }

                                echo '      <p class="mb-0 mt-1 text-muted"  style="font-size: 1.1em"><b>' . $comissao['nomeOrgao'] . '</b></p>
                                            </div>
                                        </div>
                                    </div>';
                            }
                        }

                        ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>