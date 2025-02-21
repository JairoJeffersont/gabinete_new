<?php

ob_start();

require './src/Middleware/VerificaLogado.php';
require_once './vendor/autoload.php';



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
            if ($_SESSION['usuario_gestor']) {
                include 'area-gestor.php';
            } else {
                include 'area-usuario.php';
            }
            ?>
        </div>
    </div>
</div>