<?php

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\UsuarioController;

ob_start();

require './src/Middleware/VerificaLogado.php';

require_once './vendor/autoload.php';

$usuarioController = new UsuarioController();
$gabineteController = new GabineteController();

$buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $_SESSION['usuario_id']);
$buscaGabinete = $gabineteController->buscaGabinete($buscaUsuario['dados']['usuario_gabinete']);

$configPath = dirname(__DIR__, 3) . '/src/Configs/config.php';
$config = require $configPath;

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <?php
            if ($buscaUsuario['dados']['usuario_gestor']) {
                include 'area-gestor.php';
            } else {
                include 'area-usuario.php';
            }
            ?>
        </div>
    </div>
</div>