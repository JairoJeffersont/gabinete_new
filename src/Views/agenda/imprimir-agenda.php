<?php

ob_start();

use GabineteMvc\Controllers\AgendaController;
use GabineteMvc\Controllers\GabineteController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$agendaController = new AgendaController();

$gabineteController = new GabineteController();
$buscaGabinete = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);

$dataGet = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
$tipoGet = (isset($_GET['tipo']) && $_GET['tipo'] !== 'null') ? $_GET['tipo'] : null;
$situacaoGet = (isset($_GET['situacao']) && $_GET['situacao'] !== 'null') ? $_GET['situacao'] : null;

?>


<style>
    @media print {

        body {
            background-color: rgb(255, 255, 255) !important;
            background-image: none !important;
        }

        @page {
            margin: 15mm;
            size: A4 landscape;
        }

        body {
            background-color: rgb(255, 255, 255) !important;
            background-image: none !important;
        }


    }
</style>

<div class="container-fluid p-2">
    <div class="row">
        <div class="col-12 text-center mb-1">
            <h5>Gabinete <?php echo $buscaGabinete['dados']['gabinete_nome'] ?></h5>
        </div>
        <div class="col-12 text-center mb-5">
            <h6>Agenda de compromissos - <?php echo date('d/m/Y', strtotime($dataGet)) ?></h6>
        </div>
        <div class="col-12 mb-1">
            <div class="card mb-2 border-0">
                <div class="list-group border-0" style="font-size: 0.8em;">
                    <?php
                    $buscaAgendas = $agendaController->listarAgendas($dataGet, $tipoGet, $situacaoGet, $_SESSION['usuario_gabinete']);

                    if ($buscaAgendas['status'] == 'success') {
                        foreach ($buscaAgendas['dados'] as $agenda) {
                            echo ' <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-2" style="font-size: 1.6em;">' . date('H:i', strtotime($agenda['agenda_data'])) . ' | ' . $agenda['agenda_titulo'] . '</h5>
                                        </div>
                                        <p class="mb-2"><b>' . $agenda['agenda_situacao_nome'] . '</b></p>
                                        <p class="mb-1">' . $agenda['agenda_informacoes'] . '</p>
                                        <small class="text-body-secondary">' . $agenda['agenda_local'] . ' - ' . $agenda['agenda_estado'] . '</small>
                                    </a>';
                        }
                    } else {
                        echo '<p class="card-text">Nenhuma agenda para o dia <b>' . date('d/m', strtotime($dataGet)) . '</b></p>';
                    }
                    ?>


                </div>


            </div>

        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();

        window.onafterprint = function() {
            window.close();
        };
    };
</script>