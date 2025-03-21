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

$anoGet = $_GET['ano'] ?? 2024;
$ordenarPorGet = $_GET['ordenarPor'] ?? 'NM_URNA_CANDIDATO';
$ordem = $_GET['ordem'] ?? 'asc';

$anosEleicoes = [2022, 2018, 2014, 2010, 2006, 2002];

if (!in_array($anoGet, $anosEleicoes)) {
    $anoGet = 2022;
}

$eleicoesController = new EleicoesController($anoGet, 'AP');



$cargoGet = $_GET['cargo'] ?? 'Deputado Federal';


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
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-newspaper"></i> Eleições Gerais de <?php echo $anoGet ?></div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, você pode consultar resultados de uma eleição.</p>
                    <p class="card-text mb-0">As informações são de responsabilidade do Tribunal Superior Eleitoral (TSE).</p>
                </div>
            </div>

            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-2 col-6">
                                    <input type="hidden" name="secao" value="resultado-eleicoes" />
                                    <select class="form-select form-select-sm" name="cargo" required>
                                        <?php
                                        foreach ($eleicoesController->getCargos() as $cargo) {
                                            if ($cargoGet == $cargo) {
                                                echo '<option value="' . $cargo . '" selected>' . $cargo . '</option>';
                                            } else {
                                                echo '<option value="' . $cargo . '">' . $cargo . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>

                                </div>
                                <div class="col-md-1 col-6">
                                    <select class="form-select form-select-sm" name="ano" required>
                                        <?php
                                        foreach ($eleicoesController->getAnos() as $anos) {
                                            if (in_array($anos, $anosEleicoes)) {
                                                if ($anoGet == $anos) {
                                                    echo '<option value="' . $anos . '" selected>' . $anos . '</option>';
                                                } else {
                                                    echo '<option value="' . $anos . '">' . $anos . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-1 col-6">
                                    <select class="form-select form-select-sm" name="ordenarPor" required>
                                        <option value="NM_URNA_CANDIDATO" <?php echo ($ordenarPorGet == 'NM_URNA_CANDIDATO') ? 'selected' : ''; ?>>Por nome</option>
                                        <option value="QT_VOTOS_NOMINAIS_VALIDOS" <?php echo ($ordenarPorGet == 'QT_VOTOS_NOMINAIS_VALIDOS') ? 'selected' : ''; ?>>Por votos</option>
                                    </select>
                                </div>
                                <div class="col-md-1 col-6">
                                    <select class="form-select form-select-sm" name="ordem" required>
                                        <option value="asc" <?php echo ($ordem == 'asc') ? 'selected' : ''; ?>>Crescente</option>
                                        <option value="desc" <?php echo ($ordem == 'desc') ? 'selected' : ''; ?>>Decrescente</option>
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
                    <div class="table-responsive mb-0">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Candidato</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Votos</th>
                                    <th scope="col">Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $busca = $eleicoesController->votacaoNominal($cargoGet, $ordenarPorGet, $ordem);

                                foreach ($busca as $resultado) {
                                    echo '<tr>';
                                    echo '<td>' . $resultado['NM_URNA_CANDIDATO'] . '</td>';
                                    echo '<td>' . $resultado['DS_CARGO'] . '</td>';
                                    echo '<td>' . (isset($resultado['QT_VOTOS_NOMINAIS_VALIDOS']) ? $resultado['QT_VOTOS_NOMINAIS_VALIDOS'] : $resultado['QT_VOTOS_NOMINAIS']) . '</td>';
                                    echo '<td>' . $resultado['DS_SIT_TOT_TURNO'] . '</td>';
                                    echo '</tr>';
                                }

                                ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>