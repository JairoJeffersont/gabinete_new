<?php

ob_start();

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\ProposicaoController;
use GabineteMvc\Controllers\NotaTecnicaController;
use GabineteMvc\Middleware\Utils;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$gabineteController = new GabineteController();
$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();
$utils = new Utils();

$gabinete = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$tipoGabinete = $gabineteController->buscaTipoGabinete($gabinete['dados']['gabinete_tipo']);

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
            <?php

            if ($gabinete['dados']['gabinete_tipo'] == 2) {
                include 'proposicoesDep.php';
            } else if ($gabinete['dados']['gabinete_tipo'] == 7) {
                include 'proposicoesSenado.php';
            }else{
                include 'proposicoesGeral.php';
            }

            ?>

        </div>
    </div>
</div>