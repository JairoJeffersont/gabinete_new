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


?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <?php

            if (empty($buscaGabinete['dados']['gabinete_endereco']) || empty($buscaGabinete['dados']['gabinete_municipio']) || empty($buscaGabinete['dados']['gabinete_email']) || empty($buscaGabinete['dados']['gabinete_telefone'])) {
                include 'config-inicial.php';
            }

            ?>
        </div>
    </div>
</div>