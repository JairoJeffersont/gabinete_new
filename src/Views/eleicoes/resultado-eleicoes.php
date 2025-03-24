<?php

use GabineteMvc\Controllers\EleicoesController;
use GabineteMvc\Controllers\GabineteController;

ob_start();

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$gabineteController = new GabineteController();
$buscaGab = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);

$estadoDep = $buscaGab['dados']['gabinete_estado_autoridade'];
$nomeDep = strtoupper($buscaGab['dados']['gabinete_nome']);
$gabineteTipo = $buscaGab['dados']['gabinete_tipo'];

$anoCorrente = date('Y');
$anosEleicoes = [2024, 2022, 2020, 2018, 2016, 2014, 2012, 2010, 2008, 2006, 2004, 2002, 2000];

$anoGet = $_GET['ano'] ?? $anoCorrente;

$anoGet = (int) $anoGet;

if (!in_array($anoGet, $anosEleicoes)) {
    foreach ($anosEleicoes as $ano) {
        if ($ano <= $anoCorrente) {
            $anoGet = $ano;
            break;
        }
    }
}

$eleicoesController = new EleicoesController($anoGet, $estadoDep);

?>


<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">

            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-newspaper"></i> Eleições de <?php echo $anoGet ?> | <?php echo $buscaGab['dados']['gabinete_nome'] ?></div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, você pode consultar os votos do gabinete em uma eleição. Serão mostrados todos os votos nominais válidos</p>
                    <p class="card-text mb-0">As informações são de responsabilidade do Tribunal Superior Eleitoral (TSE).</p>
                </div>
            </div>

            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <input type="hidden" name="secao" value="resultado-eleicoes" />
                                <div class="col-md-1 col-3">

                                    <select class="form-select form-select-sm" name="ano" required>
                                        <?php

                                        foreach ($anosEleicoes as $ano) {
                                            if ($anoGet == $ano) {
                                                echo '<option value="' . $ano . '" selected>' . $ano . '</option>';
                                            } else {
                                                echo '<option value="' . $ano . '">' . $ano . '</option>';
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-1 col-2">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body p-2">
                    <ul class="list-group">
                        <?php
                        $busca = $eleicoesController->getTotalVotos($nomeDep);

                        $totalVotosCargo = $busca['total_votos'];

                        if (empty($busca) || empty($busca['dados'])) {
                            echo '<li class="list-group-item">Não participou dessa eleição</li>';
                        } else {
                            $resultadoAgrupado = [];
                            $totalVotosGeral = 0;
                            $totalVotosCargo = $busca['total_votos'] ?? 1; // Evita divisão por zero

                            foreach ($busca['dados'] as $votos) {
                                $municipio = $votos['NM_MUNICIPIO'];
                                $cargo = $votos['DS_CARGO'];

                                $quantidadeVotos = $votos['QT_VOTOS_NOMINAIS_VALIDOS'] ?? $votos['QT_VOTOS_NOMINAIS'] ?? 0;

                                $situacao = $votos['DS_SIT_TOT_TURNO'];

                                if (!isset($resultadoAgrupado[$municipio])) {
                                    $resultadoAgrupado[$municipio] = [
                                        'QT_VOTOS' => 0,
                                        'DS_SIT_TOT_TURNO' => $situacao
                                    ];
                                }

                                $resultadoAgrupado[$municipio]['QT_VOTOS'] += $quantidadeVotos;
                                $totalVotosGeral += $quantidadeVotos;
                            }

                            uasort($resultadoAgrupado, function ($a, $b) {
                                return $b['QT_VOTOS'] <=> $a['QT_VOTOS']; 
                            });

                            foreach ($resultadoAgrupado as $municipio => $dados) {
                                $percentual = ($dados['QT_VOTOS'] / $totalVotosCargo) * 100;
                                echo '<li class="list-group-item"><b>' . $municipio . ':</b> '
                                    . number_format($dados['QT_VOTOS'], 0, ',', '.')
                                    . ' (' . number_format($percentual, 2, ',', '.') . '%)</li>';
                            }

                            echo '<li class="list-group-item list-group-item-info"><b>Total de votos:</b> '
                                . number_format($totalVotosGeral, 0, ',', '.') . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>